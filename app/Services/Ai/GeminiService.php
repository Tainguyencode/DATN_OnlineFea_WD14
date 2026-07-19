<?php

namespace App\Services\Ai;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Lesson-AI Gemini client (Google Generative Language API).
 * Separated from admin video-moderation GeminiService so providers can diverge.
 */
class GeminiService
{
    /**
     * @param  array{timeout?: int, temperature?: float, max_output_tokens?: int, json?: bool}  $options
     * @return array{text?: string, model?: string, error?: string, code?: string}
     */
    public function generateText(string $prompt, array $options = []): array
    {
        $apiKey = $this->apiKey();

        if ($apiKey === null) {
            return [
                'error' => 'Chưa cấu hình GEMINI_API_KEY trong .env',
                'code' => 'missing_api_key',
            ];
        }

        $model = (string) config('services.lesson_ai.model', 'gemini-2.0-flash');
        $timeout = (int) ($options['timeout'] ?? config('services.lesson_ai.timeout', 45));
        $temperature = (float) ($options['temperature'] ?? 0.3);
        $maxTokens = (int) ($options['max_output_tokens'] ?? 2048);
        $jsonMode = (bool) ($options['json'] ?? false);
        $baseUrl = rtrim((string) config('services.lesson_ai.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');

        $url = "{$baseUrl}/models/{$model}:generateContent";

        $generationConfig = [
            'temperature' => $temperature,
            'maxOutputTokens' => $maxTokens,
        ];

        if ($jsonMode) {
            $generationConfig['responseMimeType'] = 'application/json';
        }

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => $generationConfig,
        ];

        try {
            $response = Http::timeout($timeout)
                ->acceptJson()
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                ])
                ->post($url, $payload);
        } catch (ConnectionException $exception) {
            Log::warning('Lesson AI Gemini connection failed.', [
                'exception' => $exception::class,
                'model' => $model,
            ]);

            return [
                'error' => 'Kết nối AI bị quá thời gian chờ. Vui lòng thử lại sau.',
                'code' => 'timeout',
            ];
        }

        if ($response->failed()) {
            Log::warning('Lesson AI Gemini request failed.', [
                'status' => $response->status(),
                'model' => $model,
            ]);

            if (in_array($response->status(), [401, 403], true)) {
                return [
                    'error' => 'Khóa API Gemini không hợp lệ hoặc chưa được cấp quyền.',
                    'code' => 'missing_api_key',
                ];
            }

            return [
                'error' => 'Dịch vụ AI tạm thời không khả dụng. Vui lòng thử lại sau.',
                'code' => 'ai_error',
            ];
        }

        $text = (string) (
            $response->json('candidates.0.content.parts.0.text')
            ?? ''
        );

        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text) ?? $text;
        $text = preg_replace('/\s*```\s*$/i', '', $text) ?? $text;
        $text = trim($text);

        if ($text === '') {
            return [
                'error' => 'AI không trả về nội dung. Vui lòng thử lại.',
                'code' => 'empty_response',
            ];
        }

        return [
            'text' => $text,
            'model' => $model,
        ];
    }

    private function apiKey(): ?string
    {
        $key = config('services.lesson_ai.api_key');

        if (! is_string($key) || trim($key) === '') {
            return null;
        }

        return trim($key);
    }
}
