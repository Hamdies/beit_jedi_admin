<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Traits\Processor;
use Illuminate\Http\Request;
use App\Models\PaymentRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class GeideaPaymentController extends Controller
{
    use Processor;

    private $config_values;

    private PaymentRequest $payment;

    public function __construct(PaymentRequest $payment)
    {
        $config = $this->payment_config('geidea', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
        }
        $this->payment = $payment;
    }

    private function publicKey(): string
    {
        return $this->config_values->public_key ?? '';
    }

    private function apiPassword(): string
    {
        return $this->config_values->api_password ?? '';
    }

    private function apiBaseUrl(): string
    {
        return rtrim($this->config_values->api_base_url ?? 'https://api.merchant.geidea.net', '/');
    }

    private function hppBaseUrl(): string
    {
        $map = [
            'https://api.merchant.geidea.net' => 'https://www.merchant.geidea.net/hpp/checkout/?',
            'https://api.merchant.geidea.ae' => 'https://payments.geidea.ae/hpp/checkout/?',
            'https://api.ksamerchant.geidea.net' => 'https://www.ksamerchant.geidea.net/hpp/checkout/?',
            'https://api.gd-pprod-infra.net' => 'https://www.gd-pprod-infra.net/hpp/checkout/?',
            'https://api.staging.geidea.ae' => 'https://www.staging.geidea.ae/hpp/checkout/?',
        ];

        return $map[$this->apiBaseUrl()] ?? (str_replace('//api.', '//www.', $this->apiBaseUrl()) . '/hpp/checkout/?');
    }

    // signature = base64(HMAC-SHA256("{publicKey}{amount 2dp}{currency}{merchantReferenceId}{timestamp}", apiPassword))
    private function signature(float $amount, string $currency, string $merchantReferenceId, string $timestamp): string
    {
        $amountStr = number_format($amount, 2, '.', '');
        $data = $this->publicKey() . $amountStr . $currency . $merchantReferenceId . $timestamp;

        return base64_encode(hash_hmac('sha256', $data, $this->apiPassword(), true));
    }

    private function apiCall(string $method, string $url, ?array $body = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->publicKey() . ':' . $this->apiPassword()),
        ]);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output);
    }

    public function pay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }

        $data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if (!isset($data)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }

        session()->put('geidea_payment_id', $data->id);

        $amount = round((float) $data->payment_amount, 2);
        $currency = strtoupper($data->currency_code ?? 'EGP');
        $timestamp = date('m/d/Y h:i:s A');

        $response = $this->apiCall('POST', $this->apiBaseUrl() . '/payment-intent/api/v2/direct/session', [
            'amount' => $amount,
            'currency' => $currency,
            'timestamp' => $timestamp,
            'merchantReferenceID' => (string) $data->id,
            'signature' => $this->signature($amount, $currency, (string) $data->id, $timestamp),
            'callbackUrl' => route('geidea.callback'),
            'returnUrl' => route('geidea.return'),
            'language' => 'en',
            'paymentOperation' => 'Pay',
        ]);

        if (!isset($response->session->id)) {
            Log::error('Geidea session creation failed', ['response' => json_encode($response)]);
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_404), 200);
        }

        return Redirect::away($this->hppBaseUrl() . $response->session->id);
    }

    // Fetch the order from Geidea's API — payloads/query strings are never trusted.
    private function verifiedGeideaOrder(?string $geideaOrderId): ?object
    {
        if (empty($geideaOrderId)) {
            return null;
        }
        $response = $this->apiCall('GET', $this->apiBaseUrl() . '/pgw/api/v1/order/' . $this->publicKey() . '/' . $geideaOrderId);

        return $response->order ?? null;
    }

    private function isPaid(?object $geideaOrder): bool
    {
        return isset($geideaOrder) && in_array(strtolower($geideaOrder->detailedStatus ?? $geideaOrder->status ?? ''), ['paid', 'captured']);
    }

    // Browser redirect back from the hosted checkout page (webview flow).
    public function returnPage(Request $request)
    {
        $geideaOrder = $this->verifiedGeideaOrder($request->input('orderId') ?? $request->input('order_id'));
        $reference = $geideaOrder->merchantReferenceId ?? session('geidea_payment_id');

        $payment_data = $this->payment::where(['id' => $reference])->first()
            ?? $this->payment::where(['id' => session('geidea_payment_id')])->first();

        if (!isset($payment_data)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }

        if ($this->isPaid($geideaOrder) && !$payment_data->is_paid) {
            $this->markPaymentRequestPaid($payment_data, $geideaOrder);
            return $this->payment_response($payment_data, 'success');
        }

        if ($payment_data->is_paid) {
            return $this->payment_response($payment_data, 'success');
        }

        if (function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }
        return $this->payment_response($payment_data, 'fail');
    }

    // Server-to-server webhook from Geidea. Handles both the webview flow
    // (merchantReferenceId = PaymentRequest uuid) and the native mobile SDK
    // flow (merchantReferenceId = app order id).
    public function callback(Request $request)
    {
        $geideaOrderId = $request->input('order.id') ?? $request->input('orderId') ?? $request->input('order_id');
        $geideaOrder = $this->verifiedGeideaOrder($geideaOrderId);

        if (!isset($geideaOrder)) {
            Log::warning('Geidea callback could not verify order', ['payload' => $request->all()]);
            return response()->json(['status' => 'ignored'], 200);
        }

        $reference = $geideaOrder->merchantReferenceId ?? null;

        if ($reference && preg_match('/^[0-9a-fA-F-]{36}$/', $reference)) {
            $payment_data = $this->payment::where(['id' => $reference])->first();
            if (isset($payment_data) && !$payment_data->is_paid && $this->isPaid($geideaOrder)) {
                $this->markPaymentRequestPaid($payment_data, $geideaOrder);
            }
            return response()->json(['status' => 'ok'], 200);
        }

        if ($reference && ctype_digit((string) $reference)) {
            $this->settleAppOrder((int) $reference, $geideaOrder);
            return response()->json(['status' => 'ok'], 200);
        }

        Log::warning('Geidea callback with unknown merchantReferenceId', ['reference' => $reference]);
        return response()->json(['status' => 'ignored'], 200);
    }

    private function markPaymentRequestPaid(PaymentRequest $payment_data, object $geideaOrder): void
    {
        $this->payment::where(['id' => $payment_data->id])->update([
            'payment_method' => 'geidea',
            'is_paid' => 1,
            'transaction_id' => $geideaOrder->orderId ?? $geideaOrder->id ?? $payment_data->id,
        ]);
        $payment_data->refresh();

        if (function_exists($payment_data->success_hook)) {
            call_user_func($payment_data->success_hook, $payment_data);
        }
    }

    // Payment made through the Geidea native SDK inside the customer app:
    // the app passes the order id as merchantReferenceID.
    private function settleAppOrder(int $orderId, object $geideaOrder): void
    {
        $order = Order::find($orderId);
        if (!isset($order) || $order->payment_status == 'paid') {
            return;
        }

        if (!$this->isPaid($geideaOrder)) {
            Log::info('Geidea app-order callback with unpaid status', ['order_id' => $orderId]);
            return;
        }

        $due = round($order->order_amount - $order->partially_paid_amount, 2);
        $paidAmount = round((float) ($geideaOrder->amount ?? 0), 2);
        if ($paidAmount + 0.01 < $due) {
            Log::warning('Geidea paid amount is less than order due amount', [
                'order_id' => $orderId, 'paid' => $paidAmount, 'due' => $due,
            ]);
            return;
        }

        $order->transaction_reference = $geideaOrder->orderId ?? $geideaOrder->id ?? null;
        $order->save();

        order_place((object) [
            'attribute_id' => $order->id,
            'payment_method' => 'geidea',
        ]);
    }
}
