<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Users often type the local trunk "0" after the country code (+20 01036086000),
 * producing "+2001036086000". That number is not valid E.164, so the OTP lands on
 * the corrected number while lookups use the raw one, and the same person can end
 * up with two accounts. Fix it once here, before any controller reads the phone.
 */
class NormalizePhoneNumber
{
    protected array $fields = ['phone', 'email_or_phone'];

    public function handle(Request $request, Closure $next)
    {
        foreach ($this->fields as $field) {
            $value = $request->input($field);

            if (is_string($value) && $value !== '') {
                $request->merge([$field => self::normalize($value)]);
            }
        }

        return $next($request);
    }

    /** Egypt only: +20 followed by an extra 0 and a 10-digit mobile number (1XXXXXXXXX). */
    public static function normalize(string $phone): string
    {
        $digits = preg_replace('/[\s\-\(\)]/', '', $phone);

        if (preg_match('/^(?:\+|00)?200(1\d{9})$/', $digits, $matches)) {
            return '+20' . $matches[1];
        }

        return $phone;
    }
}
