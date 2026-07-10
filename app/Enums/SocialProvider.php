<?php

namespace App\Enums;

enum SocialProvider: string
{
    case Google = 'google';
    case Facebook = 'facebook';

    public function label(): string
    {
        return match ($this) {
            self::Google => 'Google',
            self::Facebook => 'Facebook',
        };
    }

    public static function tryFromName(string $provider): ?self
    {
        return self::tryFrom(strtolower($provider));
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isConfigured(): bool
    {
        $config = config('services.'.$this->value, []);

        return filled($config['client_id'] ?? null) && filled($config['client_secret'] ?? null);
    }

    public static function anyConfigured(): bool
    {
        return self::Google->isConfigured() || self::Facebook->isConfigured();
    }

    public static function configuredProviders(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $provider) => $provider->isConfigured(),
        ));
    }
}
