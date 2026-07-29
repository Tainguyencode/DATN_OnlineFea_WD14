<?php

namespace App\Support;

class SensitiveData
{
    public static function maskEmail(string $email): string
    {
        if (! str_contains($email, '@')) {
            return '***';
        }

        [$local, $domain] = explode('@', $email, 2);

        if ($local === '') {
            return '***@'.$domain;
        }

        if (strlen($local) <= 4) {
            $maskedLocal = substr($local, 0, 1).str_repeat('*', max(1, strlen($local) - 1));
        } else {
            $maskedLocal = substr($local, 0, 2).str_repeat('*', strlen($local) - 4).substr($local, -2);
        }

        return $maskedLocal.'@'.$domain;
    }
}
