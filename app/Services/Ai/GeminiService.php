<?php

namespace App\Services\Ai;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

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

        $model = (string) config('services.lesson_ai.model', 'gemini-3.5-flash-lite');
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
                ->connectTimeout(min(15, $timeout))
                ->acceptJson()
                ->post($url.'?key='.urlencode($apiKey), $payload);
        } catch (ConnectionException $exception) {
            $message = $exception->getMessage();

            Log::warning('Lesson AI Gemini connection failed.', [
                'exception' => $exception::class,
                'model' => $model,
                'reason' => $this->connectionReason($message),
            ]);

            return [
                'error' => $this->connectionErrorMessage($message),
                'code' => $this->connectionErrorCode($message),
            ];
        } catch (Throwable $exception) {
            Log::warning('Lesson AI Gemini unexpected client failure.', [
                'exception' => $exception::class,
                'model' => $model,
                'message' => $exception->getMessage(),
            ]);

            return [
                'error' => 'Không gọi được dịch vụ AI. Vui lòng thử lại sau.',
                'code' => 'ai_error',
            ];
        }

        if ($response->failed()) {
            $mapped = $this->mapHttpFailure($response->status(), $response->json(), $model);

            Log::warning('Lesson AI Gemini request failed.', [
                'status' => $response->status(),
                'model' => $model,
                'code' => $mapped['code'],
                'api_status' => data_get($response->json(), 'error.status'),
                'api_message' => $this->truncate((string) data_get($response->json(), 'error.message', '')),
            ]);

            return $mapped;
        }

        $body = $response->json() ?? [];

        $blocked = $this->mapBlockedOrEmptyCandidate($body);
        if ($blocked !== null) {
            Log::warning('Lesson AI Gemini returned no usable text.', [
                'model' => $model,
                'code' => $blocked['code'],
                'finish_reason' => data_get($body, 'candidates.0.finishReason'),
                'block_reason' => data_get($body, 'promptFeedback.blockReason'),
            ]);

            return $blocked;
        }

        $text = (string) (
            data_get($body, 'candidates.0.content.parts.0.text')
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

    /**
     * @param  array<string, mixed>|null  $json
     * @return array{error: string, code: string}
     */
    private function mapHttpFailure(int $status, ?array $json, string $model): array
    {
        $apiStatus = strtoupper((string) data_get($json, 'error.status', ''));
        $apiMessage = trim((string) data_get($json, 'error.message', ''));
        $lowerMessage = strtolower($apiMessage);

        if ($status === 401 || $status === 403 || $apiStatus === 'PERMISSION_DENIED' || $apiStatus === 'UNAUTHENTICATED') {
            $hint = $apiStatus === 'UNAUTHENTICATED'
                ? ' Google từ chối xác thực key này (UNAUTHENTICATED). Hãy tạo key mới tại https://aistudio.google.com/apikey, dán vào GEMINI_API_KEY, lưu file, rồi chạy php artisan config:clear.'
                : ' Hãy kiểm tra GEMINI_API_KEY trong .env.';

            return [
                'error' => 'Khóa API Gemini không hợp lệ hoặc chưa được cấp quyền.'.$hint,
                'code' => 'missing_api_key',
            ];
        }

        if ($status === 429 || $apiStatus === 'RESOURCE_EXHAUSTED') {
            return [
                'error' => 'Gemini đã hết hạn mức (quota). Hãy đợi vài phút, đổi model, hoặc tạo API key mới tại Google AI Studio.',
                'code' => 'quota_exceeded',
            ];
        }

        if (
            $status === 404
            || $apiStatus === 'NOT_FOUND'
            || str_contains($lowerMessage, 'is not found')
            || (str_contains($lowerMessage, 'model') && str_contains($lowerMessage, 'not found'))
        ) {
            return [
                'error' => "Model Gemini \"{$model}\" không tồn tại hoặc không khả dụng. Hãy kiểm tra GEMINI_MODEL trong .env (ví dụ gemini-3.5-flash-lite).",
                'code' => 'invalid_model',
            ];
        }

        if ($status === 400 || $apiStatus === 'INVALID_ARGUMENT') {
            if (str_contains($lowerMessage, 'api key') || str_contains($lowerMessage, 'api_key')) {
                return [
                    'error' => 'Khóa API Gemini không hợp lệ. Hãy kiểm tra GEMINI_API_KEY trong .env.',
                    'code' => 'missing_api_key',
                ];
            }

            return [
                'error' => $apiMessage !== ''
                    ? 'Yêu cầu AI không hợp lệ: '.$this->truncate($apiMessage, 180)
                    : 'Yêu cầu gửi tới Gemini không hợp lệ. Hãy kiểm tra model/cấu hình rồi thử lại.',
                'code' => 'invalid_request',
            ];
        }

        if ($status >= 500 || in_array($apiStatus, ['UNAVAILABLE', 'INTERNAL', 'DEADLINE_EXCEEDED'], true)) {
            return [
                'error' => 'Dịch vụ Gemini đang gián đoạn. Vui lòng thử lại sau.',
                'code' => 'ai_unavailable',
            ];
        }

        return [
            'error' => $apiMessage !== ''
                ? 'Lỗi Gemini: '.$this->truncate($apiMessage, 180)
                : 'Dịch vụ AI tạm thời không khả dụng. Vui lòng thử lại sau.',
            'code' => 'ai_error',
        ];
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array{error: string, code: string}|null
     */
    private function mapBlockedOrEmptyCandidate(array $body): ?array
    {
        $blockReason = strtoupper((string) data_get($body, 'promptFeedback.blockReason', ''));
        if ($blockReason !== '' && $blockReason !== 'BLOCK_REASON_UNSPECIFIED') {
            return [
                'error' => 'Nội dung bài học bị Gemini chặn bởi bộ lọc an toàn ('.$blockReason.'). Hãy rút gọn hoặc chỉnh nội dung rồi thử lại.',
                'code' => 'content_blocked',
            ];
        }

        $finishReason = strtoupper((string) data_get($body, 'candidates.0.finishReason', ''));
        $text = trim((string) data_get($body, 'candidates.0.content.parts.0.text', ''));

        if ($text !== '') {
            return null;
        }

        if (in_array($finishReason, ['SAFETY', 'BLOCKLIST', 'PROHIBITED_CONTENT'], true)) {
            return [
                'error' => 'Phản hồi AI bị chặn bởi bộ lọc an toàn. Hãy đổi câu hỏi hoặc nội dung bài rồi thử lại.',
                'code' => 'content_blocked',
            ];
        }

        if ($finishReason === 'MAX_TOKENS') {
            return [
                'error' => 'Phản hồi AI bị cắt vì quá dài. Hãy thử lại với câu hỏi ngắn hơn.',
                'code' => 'response_truncated',
            ];
        }

        if ($finishReason === 'RECITATION') {
            return [
                'error' => 'AI không thể trả lời do nội dung trùng nguồn có bản quyền.',
                'code' => 'content_blocked',
            ];
        }

        $candidates = data_get($body, 'candidates');
        if ($candidates === null || $candidates === []) {
            return [
                'error' => 'AI không trả về nội dung. Vui lòng thử lại.',
                'code' => 'empty_response',
            ];
        }

        if ($finishReason !== '' && $finishReason !== 'STOP' && $finishReason !== 'FINISH_REASON_UNSPECIFIED') {
            return [
                'error' => "AI dừng phản hồi (lý do: {$finishReason}). Vui lòng thử lại.",
                'code' => 'empty_response',
            ];
        }

        return null;
    }

    private function connectionReason(string $message): string
    {
        $lower = strtolower($message);

        if (str_contains($lower, 'cacert') || str_contains($lower, 'certificate')) {
            return 'ssl_cacert';
        }

        if (str_contains($lower, 'timed out') || str_contains($lower, 'timeout')) {
            return 'timeout';
        }

        if (str_contains($lower, 'could not resolve') || str_contains($lower, 'getaddrinfo') || str_contains($lower, 'name or service not known')) {
            return 'dns';
        }

        return 'connection';
    }

    private function connectionErrorCode(string $message): string
    {
        $reason = $this->connectionReason($message);

        return match ($reason) {
            'ssl_cacert' => 'ssl_error',
            'timeout' => 'timeout',
            'dns' => 'connection_error',
            default => 'connection_error',
        };
    }

    private function connectionErrorMessage(string $message): string
    {
        return match ($this->connectionReason($message)) {
            'ssl_cacert' => 'Máy chủ thiếu chứng chỉ SSL (cacert.pem). Hãy kiểm tra cấu hình PHP/Laragon rồi thử lại.',
            'timeout' => 'Kết nối AI bị quá thời gian chờ. Vui lòng thử lại sau.',
            'dns' => 'Không phân giải được tên miền Gemini. Hãy kiểm tra kết nối mạng rồi thử lại.',
            default => 'Không kết nối được dịch vụ AI. Kiểm tra mạng hoặc cấu hình SSL rồi thử lại.',
        };
    }

    private function truncate(string $text, int $limit = 220): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $limit - 1)).'…';
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
