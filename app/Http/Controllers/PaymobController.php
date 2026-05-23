<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Traits\Processor;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\PaymentRequest;
use App\CentralLogics\OrderLogic;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class PaymobController extends Controller
{
    use Processor;

    private $config_values;

    private PaymentRequest $payment;
    private $user;

    // Credentials are loaded from admin config (business-settings → payment-method → Paymob).
    private const PAYMOB_BASE_URL    = 'https://accept.paymob.com';

    public function __construct(PaymentRequest $payment, User $user)
    {
        $config = $this->payment_config('paymob_accept', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
        }
        $this->payment = $payment;
        $this->user = $user;
    }

    private function publicKey(): string
    {
        return $this->config_values->public_key ?? '';
    }

    private function secretKey(): string
    {
        return $this->config_values->secret_key ?? '';
    }

    protected function cURL($url, $json)
    {
        // Create curl resource
        $ch = curl_init($url);

        // Request headers
        $headers = array();
        $headers[] = 'Content-Type: application/json';

        // Return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // $output contains the output string
        $output = curl_exec($ch);

        // Close curl resource to free up system resources
        curl_close($ch);
        return json_decode($output);
    }

    protected function GETcURL($url)
    {
        // Create curl resource
        $ch = curl_init($url);

        // Request headers
        $headers = array();
        $headers[] = 'Content-Type: application/json';

        // Return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // $output contains the output string
        $output = curl_exec($ch);

        // Close curl resource to free up system resources
        curl_close($ch);
        return json_decode($output);
    }

    public function credit(Request $request)
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

        session()->put('payment_id', $data->id);

        $payer = json_decode($data['payer_information']);

        try {
            $clientSecret = $this->createIntention($data, $payer);
        } catch (\Exception $exception) {
            \Log::error('Paymob Unified Checkout intention error: ' . $exception->getMessage());
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_404), 200);
        }

        return Redirect::away(
            self::PAYMOB_BASE_URL . '/unifiedcheckout/?publicKey=' . $this->publicKey()
            . '&clientSecret=' . $clientSecret
        );
    }

    private function createIntention($payment_data, $payer)
    {
        $amountCents = (int) round($payment_data->payment_amount * 100);

        $firstName = $payer->name ?? 'Customer';
        $lastName  = $payer->name ?? 'Customer';
        if (!empty($payer->name) && str_contains($payer->name, ' ')) {
            $parts = explode(' ', trim($payer->name), 2);
            $firstName = $parts[0];
            $lastName  = $parts[1] ?? $parts[0];
        }

        $payload = [
            'amount'             => $amountCents,
            'currency'           => $payment_data->currency_code ?? 'EGP',
            'payment_methods'    => ['card'],
            'items'              => [[
                'name'   => 'Order ' . $payment_data->id,
                'amount' => $amountCents,
                'description' => 'Payment ID: ' . $payment_data->id,
                'quantity' => 1,
            ]],
            'billing_data' => [
                'first_name'   => $firstName,
                'last_name'    => $lastName,
                'phone_number' => $payer->phone ?? 'NA',
                'email'        => $payer->email ?? 'no-email@example.com',
                'country'      => 'EG',
            ],
            'customer' => [
                'first_name'   => $firstName,
                'last_name'    => $lastName,
                'email'        => $payer->email ?? 'no-email@example.com',
            ],
            'extras' => [
                'payment_id' => (string) $payment_data->id,
            ],
            'special_reference' => (string) $payment_data->id,
        ];

        $ch = curl_init(self::PAYMOB_BASE_URL . '/v1/intention/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Token ' . $this->secretKey(),
        ]);
        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $response = json_decode($output);

        if ($httpCode >= 200 && $httpCode < 300 && isset($response->client_secret)) {
            return $response->client_secret;
        }

        \Log::error('Paymob intention failed', ['http' => $httpCode, 'response' => $output]);
        throw new \Exception('Failed to create Paymob intention: ' . $output);
    }

    public function callback(Request $request)
    {
        $data = $request->all();
        ksort($data);
        $hmac = $data['hmac'] ?? '';
        $array = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order',
            'owner',
            'pending',
            'source_data_pan',
            'source_data_sub_type',
            'source_data_type',
            'success',
        ];
        $connectedString = '';
        foreach ($data as $key => $element) {
            if (in_array($key, $array)) {
                $connectedString .= $element;
            }
        }
        $secret = $this->config_values->hmac ?? '';
        $hased = hash_hmac('sha512', $connectedString, $secret);

        $success = ($data['success'] ?? '') === 'true' || ($data['success'] ?? '') === true;

        if ($hased !== $hmac) {
            \Log::warning('Paymob callback HMAC mismatch', [
                'expected' => $hased,
                'received' => $hmac,
                'payment_id' => session('payment_id'),
            ]);
        }

        if ($hased == $hmac && $success) {

            $this->payment::where(['id' => session('payment_id')])->update([
                'payment_method' => 'paymob_accept',
                'is_paid' => 1,
                'transaction_id' => session('payment_id'),
            ]);

            $payment_data = $this->payment::where(['id' => session('payment_id')])->first();

            if (isset($payment_data) && function_exists($payment_data->success_hook)) {
                call_user_func($payment_data->success_hook, $payment_data);
            }
            return $this->payment_response($payment_data,'success');
        }
        $payment_data = $this->payment::where(['id' => session('payment_id')])->first();
        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }
        return $this->payment_response($payment_data,'fail');
    }
}

