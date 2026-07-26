<?php

namespace App\CentralLogics;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Akedly OTP gateway (V1.0 REST API).
 *
 * Unlike the gateways in SMS_module, Akedly is OTP-as-a-service: it generates the
 * code, delivers it over its own channel fallback chain (WhatsApp -> Telegram ->
 * SMS -> Email) and verifies it on its servers. We never see the code, so instead
 * of storing a token we store the returned transaction id and hand the user's
 * input back to Akedly at verify time.
 *
 * V1.0 sunsets 2026-12-30. V1.2 adds a Proof-of-Work + Turnstile challenge that
 * has to be solved client side; when we move, only this class should change.
 *
 * @see https://docs.akedly.io/authentication/v1
 */
class AkedlyGateway
{
    const BASE_URL = 'https://api.akedly.io/api/v1';
    const TIMEOUT = 10;

    public static function is_active(): bool
    {
        $config = self::get_settings('akedly');

        return isset($config) && $config['status'] == 1
            && !empty($config['api_key']) && !empty($config['pipeline_id']);
    }

    /**
     * Create a transaction and activate it, which sends the OTP.
     *
     * @return array{response: string, transaction_req_id: string|null, main_transaction_id: string|null, channels: array}
     */
    public static function send($receiver): array
    {
        $failed = ['response' => 'error', 'transaction_req_id' => null, 'main_transaction_id' => null, 'channels' => []];

        if (!self::is_active()) {
            return $failed;
        }

        $config = self::get_settings('akedly');

        try {
            $create = Http::timeout(self::TIMEOUT)
                ->post(self::BASE_URL . '/transactions', [
                    'APIKey' => $config['api_key'],
                    'pipelineID' => $config['pipeline_id'],
                    'verificationAddress' => [
                        'phoneNumber' => self::normalize_phone($receiver),
                    ],
                    'digits' => 6,
                ]);

            $main_transaction_id = data_get($create->json(), 'data.transactionID');

            if (!$create->successful() || !$main_transaction_id) {
                info('Akedly create transaction failed: ' . $create->body());
                return $failed;
            }

            $activate = Http::timeout(self::TIMEOUT)
                ->post(self::BASE_URL . '/transactions/activate/' . $main_transaction_id, []);

            $transaction_req_id = data_get($activate->json(), 'data._id');

            if (!$activate->successful() || !$transaction_req_id) {
                info('Akedly activate transaction failed: ' . $activate->body());
                return $failed;
            }

            return [
                'response' => 'success',
                'transaction_req_id' => $transaction_req_id,
                'main_transaction_id' => $main_transaction_id,
                'channels' => data_get($activate->json(), 'channels', []),
            ];
        } catch (\Exception $exception) {
            info('Akedly send exception: ' . $exception->getMessage());
            return $failed;
        }
    }

    /**
     * Verify the user's OTP against Akedly.
     *
     * @return string 'success' | 'invalid' | 'error'
     */
    public static function verify($transaction_req_id, $otp): string
    {
        if (empty($transaction_req_id)) {
            return 'error';
        }

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->post(self::BASE_URL . '/transactions/verify/' . $transaction_req_id, [
                    'otp' => (string) $otp,
                ]);

            if ($response->successful() && data_get($response->json(), 'status') === 'success') {
                return 'success';
            }

            // 403 is a wrong code; anything else (expired, not found, outage) is an error
            // so the caller can tell "try again" apart from "that code was wrong".
            if ($response->status() === 403) {
                return 'invalid';
            }

            info('Akedly verify failed: ' . $response->body());
            return 'error';
        } catch (\Exception $exception) {
            info('Akedly verify exception: ' . $exception->getMessage());
            return 'error';
        }
    }

    /**
     * Resolve an OTP check against either Akedly or the locally stored token.
     *
     * Returns the matching row so callers can keep using it exactly as they did
     * with their old `where(['phone' => ..., 'token' => ...])->first()` lookup,
     * or null when the code does not match.
     *
     * @param  string  $table  'phone_verifications' or 'password_resets'
     */
    public static function match_otp(string $table, $phone, $otp)
    {
        if (empty($phone) || $otp === null || $otp === '') {
            return null;
        }

        $row = DB::table($table)->where('phone', $phone)->first();

        if (!$row) {
            return null;
        }

        if (!empty($row->akedly_transaction_req_id)) {
            return self::verify($row->akedly_transaction_req_id, $otp) === 'success' ? $row : null;
        }

        return (string) $row->token === (string) $otp ? $row : null;
    }

    /**
     * Akedly requires E.164; phones are stored in mixed formats across this codebase.
     *
     * Only formatting noise (spaces, dashes, brackets) and an international "00"
     * prefix are handled. A bare local number such as "01551234567" carries no
     * country code, so we pass it through as-is and let Akedly reject it rather
     * than guessing a country and silently texting the wrong person.
     */
    public static function normalize_phone($phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', (string) $phone);

        if (substr($phone, 0, 1) === '+') {
            return $phone;
        }

        if (substr($phone, 0, 2) === '00') {
            return '+' . substr($phone, 2);
        }

        return $phone;
    }

    public static function get_settings($name)
    {
        $config = DB::table('addon_settings')->where('key_name', $name)
            ->where('settings_type', 'sms_config')->first();

        if (isset($config) && !is_null($config->live_values)) {
            return json_decode($config->live_values, true);
        }
        return null;
    }
}
