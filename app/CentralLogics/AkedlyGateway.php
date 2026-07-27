<?php

namespace App\CentralLogics;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Akedly OTP gateway (V1.2 REST API).
 *
 * Unlike the gateways in SMS_module, Akedly is OTP-as-a-service: it generates the
 * code, delivers it over its own channel fallback chain (WhatsApp -> Telegram ->
 * SMS -> Email) and verifies it on its servers. We never see the code, so instead
 * of storing a token we store the returned transaction id and hand the user's
 * input back to Akedly at verify time.
 *
 * V1.2 gates sending behind a Proof-of-Work challenge, which is mandatory and
 * cannot be turned off per pipeline. PoW is only a hash puzzle, so we solve it
 * here on the server rather than pushing it into the mobile apps. Cloudflare
 * Turnstile genuinely needs a browser, so the pipeline must have Turnstile
 * DISABLED for this server-to-server flow to work.
 *
 * Flow: GET /challenge -> solve nonce -> POST /send -> POST /verify
 *
 * @see https://docs.akedly.io/authentication/v1-2
 */
class AkedlyGateway
{
    const BASE_URL = 'https://api.akedly.io/api/v1.2/transactions';
    const TIMEOUT = 10;

    /** Safety cap so a misconfigured difficulty can never hang a login request. */
    const POW_MAX_ITERATIONS = 5000000;

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
            $challenge = Http::timeout(self::TIMEOUT)
                ->get(self::BASE_URL . '/challenge', [
                    'APIKey' => $config['api_key'],
                    'pipelineID' => $config['pipeline_id'],
                ]);

            if (!$challenge->successful()) {
                info('Akedly challenge failed: ' . $challenge->body());
                return $failed;
            }

            $data = $challenge->json('data') ?? [];

            // Turnstile needs a real browser. If the pipeline still has it on we
            // cannot complete the send from the server, so fail loudly here rather
            // than sending a request we know Cloudflare will reject.
            if (data_get($data, 'turnstile.required')) {
                info('Akedly: Turnstile is enabled on this pipeline; disable it for server-to-server OTP.');
                return $failed;
            }

            $payload = [
                'APIKey' => $config['api_key'],
                'pipelineID' => $config['pipeline_id'],
                'verificationAddress' => [
                    'phoneNumber' => self::normalize_phone($receiver),
                ],
                'digits' => 6,
            ];

            if (data_get($data, 'challengeRequired')) {
                $nonce = self::solve_pow(data_get($data, 'challenge'), (int) data_get($data, 'difficulty', 4));

                if ($nonce === null) {
                    info('Akedly: could not solve PoW challenge within the iteration cap.');
                    return $failed;
                }

                $payload['powSolution'] = [
                    'challengeToken' => data_get($data, 'challengeToken'),
                    'nonce' => $nonce,
                ];
            }

            $send = Http::timeout(self::TIMEOUT)
                ->post(self::BASE_URL . '/send', $payload);

            $transaction_req_id = data_get($send->json(), 'data.transactionReqID');

            if (!$send->successful() || !$transaction_req_id) {
                info('Akedly send failed: ' . $send->body());
                return $failed;
            }

            return [
                'response' => 'success',
                'transaction_req_id' => $transaction_req_id,
                'main_transaction_id' => data_get($send->json(), 'data.transactionID'),
                'channels' => data_get($send->json(), 'data.channels', []),
            ];
        } catch (\Exception $exception) {
            info('Akedly send exception: ' . $exception->getMessage());
            return $failed;
        }
    }

    /**
     * Solve the Proof-of-Work challenge: find a nonce where the hex digest of
     * sha256("<challenge>:<nonce>") begins with `difficulty` zeros.
     *
     * Difficulty is set per pipeline; at the "Light" setting this costs a few
     * milliseconds. The iteration cap keeps a bad setting from stalling a login.
     */
    public static function solve_pow($challenge, int $difficulty): ?int
    {
        if (empty($challenge) || $difficulty < 1) {
            return null;
        }

        $prefix = str_repeat('0', $difficulty);

        for ($nonce = 0; $nonce < self::POW_MAX_ITERATIONS; $nonce++) {
            if (strncmp(hash('sha256', $challenge . ':' . $nonce), $prefix, $difficulty) === 0) {
                return $nonce;
            }
        }

        return null;
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
                ->post(self::BASE_URL . '/verify', [
                    'transactionReqID' => $transaction_req_id,
                    'otp' => (string) $otp,
                ]);

            if ($response->successful() && data_get($response->json(), 'status') === 'success') {
                return 'success';
            }

            // A wrong code is a normal outcome; expiry/outage is not, so the caller
            // can tell "that code was wrong" apart from "something broke".
            if (data_get($response->json(), 'code') === 'INVALID_OTP' || $response->status() === 403) {
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
