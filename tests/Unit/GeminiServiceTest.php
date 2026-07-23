<?php

namespace Tests\Unit;

use App\Services\Ai\GeminiService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.lesson_ai.api_key' => 'test-key',
            'services.lesson_ai.model' => 'gemini-3.5-flash-lite',
            'services.lesson_ai.base_url' => 'https://generativelanguage.googleapis.com/v1beta',
            'services.lesson_ai.timeout' => 10,
        ]);
    }

    public function test_missing_api_key_is_detected_before_http_call(): void
    {
        config(['services.lesson_ai.api_key' => '']);

        Http::fake();

        $result = app(GeminiService::class)->generateText('hello');

        $this->assertSame('missing_api_key', $result['code']);
        Http::assertNothingSent();
    }

    public function test_maps_quota_exceeded_from_http_429(): void
    {
        Http::fake([
            '*' => Http::response([
                'error' => [
                    'code' => 429,
                    'message' => 'Resource exhausted',
                    'status' => 'RESOURCE_EXHAUSTED',
                ],
            ], 429),
        ]);

        $result = app(GeminiService::class)->generateText('hello');

        $this->assertSame('quota_exceeded', $result['code']);
        $this->assertStringContainsString('hết hạn mức', $result['error']);
    }

    public function test_maps_invalid_model_from_http_404(): void
    {
        Http::fake([
            '*' => Http::response([
                'error' => [
                    'code' => 404,
                    'message' => 'models/gemini-3.5-flash-lite is not found',
                    'status' => 'NOT_FOUND',
                ],
            ], 404),
        ]);

        $result = app(GeminiService::class)->generateText('hello');

        $this->assertSame('invalid_model', $result['code']);
        $this->assertStringContainsString('gemini-3.5-flash-lite', $result['error']);
    }

    public function test_maps_safety_block_from_prompt_feedback(): void
    {
        Http::fake([
            '*' => Http::response([
                'promptFeedback' => [
                    'blockReason' => 'SAFETY',
                ],
                'candidates' => [],
            ], 200),
        ]);

        $result = app(GeminiService::class)->generateText('hello');

        $this->assertSame('content_blocked', $result['code']);
    }

    public function test_maps_max_tokens_finish_reason(): void
    {
        Http::fake([
            '*' => Http::response([
                'candidates' => [
                    [
                        'finishReason' => 'MAX_TOKENS',
                        'content' => ['parts' => [['text' => '']]],
                    ],
                ],
            ], 200),
        ]);

        $result = app(GeminiService::class)->generateText('hello');

        $this->assertSame('response_truncated', $result['code']);
    }

    public function test_returns_text_on_success(): void
    {
        Http::fake([
            '*' => Http::response([
                'candidates' => [
                    [
                        'finishReason' => 'STOP',
                        'content' => [
                            'parts' => [
                                ['text' => "```json\n{\"ok\":true}\n```"],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $result = app(GeminiService::class)->generateText('hello');

        $this->assertSame('{"ok":true}', $result['text']);
        $this->assertSame('gemini-3.5-flash-lite', $result['model']);
        $this->assertArrayNotHasKey('error', $result);
    }
}
