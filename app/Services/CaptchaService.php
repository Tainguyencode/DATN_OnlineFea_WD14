<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CaptchaService
{
    private const SESSION_KEY = 'auth_captchas';

    /**
     * @return array{token: string, question: string}
     */
    public static function generate(string $purpose): array
    {
        $left = random_int(2, 12);
        $right = random_int(1, 9);
        $token = Str::random(40);

        $captchas = Session::get(self::SESSION_KEY, []);
        $captchas[$token] = [
            'purpose' => $purpose,
            'answer' => (string) ($left + $right),
            'expires_at' => now()->addMinutes(10)->timestamp,
        ];

        Session::put(self::SESSION_KEY, $captchas);

        return [
            'token' => $token,
            'question' => "{$left} + {$right} = ?",
        ];
    }

    public static function verify(?string $token, ?string $answer, string $purpose): bool
    {
        $captchas = Session::get(self::SESSION_KEY, []);
        $captcha = $token ? ($captchas[$token] ?? null) : null;

        if (! $captcha || $captcha['purpose'] !== $purpose || $captcha['expires_at'] < now()->timestamp) {
            return false;
        }

        unset($captchas[$token]);
        Session::put(self::SESSION_KEY, $captchas);

        return hash_equals($captcha['answer'], trim((string) $answer));
    }
}
