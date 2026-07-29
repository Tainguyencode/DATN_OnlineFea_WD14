<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env(
            'GOOGLE_REDIRECT_URI',
            env('APP_URL').'/auth/google/callback'
        ),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env(
            'FACEBOOK_REDIRECT_URI',
            env('APP_URL').'/auth/facebook/callback'
        ),
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URI', env('APP_URL').'/auth/github/callback'),
    ],

    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect' => env('MICROSOFT_REDIRECT_URI', env('APP_URL').'/auth/microsoft/callback'),
    ],
    // Admin video moderation (OpenRouter).
    'gemini' => [
        'api_key' => env('OPENROUTER_API_KEY', env('GEMINI_API_KEY')),
        'timeout' => (int) env('GEMINI_TIMEOUT', 45),
    ],
    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),
    ],

    // Lesson AI explain/summary (Google Gemini API).
    'lesson_ai' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-3.5-flash-lite'),
        // Comma-separated fallbacks used when primary model is unavailable or out of quota.
        'fallback_models' => array_values(array_filter(array_map(
            static fn (string $model): string => trim($model),
            explode(',', (string) env(
                'GEMINI_FALLBACK_MODELS',
                'gemini-flash-lite-latest,gemini-3.1-flash-lite,gemini-3.5-flash,gemini-flash-latest'
            ))
        ))),
        'timeout' => (int) env('GEMINI_TIMEOUT', 45),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
    ],

];
