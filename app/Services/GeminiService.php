<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GeminiService (dùng qua OpenRouter)
 *
 * Gửi ảnh lên OpenRouter API (sử dụng các model Gemini) và nhận kết quả kiểm duyệt.
 */
class GeminiService
{
    private const API_URL = 'https://openrouter.ai/api/v1/chat/completions';

    /**
     * Danh sách model thử trên OpenRouter (ưu tiên các bản free của Gemini)
     */
    private const MODELS = [
        'google/gemini-3.5-flash',
        'google/gemini-3.1-flash-image',
        'google/gemini-3.1-flash-lite-image',
        'google/gemini-3-pro-image',
        'google/gemini-3.1-flash-lite',
        'google/gemini-flash-latest',
    ];

    public function analyzeImage(string $imagePath): array
    {
        // 1. Kiểm tra file tồn tại
        if (! file_exists($imagePath)) {
            return ['error' => "File không tồn tại: {$imagePath}"];
        }

        // 2. Đọc & encode ảnh sang Base64
        $imageData = base64_encode(file_get_contents($imagePath));
        $mimeType = mime_content_type($imagePath) ?: 'image/jpeg';

        // Lấy API key (đã trỏ sang OPENROUTER_API_KEY trong config)
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            return ['error' => 'Chưa cấu hình OPENROUTER_API_KEY trong .env'];
        }

        // 3. Chuẩn bị prompt
        $prompt = <<<'PROMPT'
Bạn là hệ thống AI hỗ trợ kiểm duyệt nội dung video cho một nền tảng học trực tuyến.

Bạn sẽ được cung cấp:
- Một ảnh (frame) được cắt từ video.

Nhiệm vụ của bạn: PHÁT HIỆN DẤU HIỆU, KHÔNG kết luận vi phạm. Quyết định cuối cùng luôn do con người.

Hãy kiểm tra các tiêu chí sau:

1. Bạo lực (violence)
- Có đánh nhau.
- Có máu.
- Có xác chết.
- Có tai nạn nghiêm trọng.
- Có hành vi bạo lực.
Nếu có thì trả về true, ngược lại false.

2. Nội dung người lớn (adult)
- Khỏa thân.
- Nội dung tình dục.
- Nội dung khiêu dâm.
Nếu có thì trả về true, ngược lại false.

3. Vũ khí (weapon)
- Súng.
- Dao.
- Kiếm.
- Các loại vũ khí nguy hiểm.
Nếu có thì trả về true, ngược lại false.

4. Logo hoặc watermark của nền tảng khác
Kiểm tra xem ảnh có xuất hiện:
- Logo TikTok
- Logo YouTube
- Logo Facebook
- Logo Instagram
Nếu phát hiện thì đánh dấu true tương ứng.
Lưu ý: Logo xuất hiện trong bài giảng minh họa cũng tính là phát hiện, nhưng không đồng nghĩa là vi phạm.

5. Watermark
Kiểm tra xem ảnh có chứa watermark hoặc logo của bên thứ ba hay không.
Nếu có thì trả về true.

6. Mức độ dấu hiệu bản quyền cần xem lại (copyright_risk)
Đây KHÔNG phải kết luận vi phạm, chỉ là mức độ AI nghi ngờ cần xem lại:
- none: Không phát hiện bất kỳ dấu hiệu nào liên quan đến nội dung bên thứ ba.
- low: Có logo/watermark xuất hiện thoáng qua hoặc rất nhỏ, có thể chỉ là minh họa.
- medium: Logo hoặc giao diện nền tảng khác xuất hiện khá lâu hoặc rõ ràng, có thể chỉ đang demo.
- high: Watermark hoặc nội dung bên thứ ba chiếm phần lớn khung hình, AI nghi ngờ đây là video phát lại từ nguồn khác – cần admin xác minh.

7. Mô tả nội dung ảnh (summary)
Viết một câu ngắn bằng tiếng Việt mô tả nội dung chính của ảnh.
KHÔNG dùng từ "vi phạm", "ăn cắp", "bị từ chối". Chỉ mô tả những gì AI quan sát thấy.
Ví dụ tốt: "Phát hiện logo YouTube ở góc phải khung hình. Có thể chỉ là video minh họa, admin nên kiểm tra."

8. Giải thích dấu hiệu (reason)
Nếu phát hiện bất kỳ dấu hiệu nào, mô tả ngắn gọn bằng tiếng Việt những gì AI thấy.
KHÔNG kết luận vi phạm. Chỉ mô tả dấu hiệu và gợi ý kiểm tra.
Ví dụ: "Phát hiện logo YouTube tại góc trên bên phải. Gợi ý: Có thể chỉ là video minh họa, admin nên kiểm tra."
Nếu không có gì đáng chú ý thì để chuỗi rỗng.

9. Độ tin cậy
confidence là số thực từ 0 đến 1 thể hiện mức độ tự tin của bạn đối với phát hiện.

=========================
QUAN TRỌNG
=========================
Chỉ trả về JSON hợp lệ. Không giải thích. Không thêm markdown. Không dùng ```json.
JSON phải đúng chính xác định dạng sau:
{
  "timestamp": 0,
  "violence": false,
  "adult": false,
  "weapon": false,
  "tiktok_logo": false,
  "youtube_logo": false,
  "facebook_logo": false,
  "instagram_logo": false,
  "watermark": false,
  "copyright_risk": "none",
  "confidence": 0.98,
  "reason": "",
  "summary": ""
}
PROMPT;

        $lastError = '';

        // 4. Thử từng model trên OpenRouter
        foreach (self::MODELS as $model) {
            $body = [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $prompt,
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mimeType};base64,{$imageData}",
                                ],
                            ],
                        ],
                    ],
                ],
                'temperature' => 0.1,
                'max_tokens' => 500,
                // Định dạng JSON nếu OpenRouter model support
                'response_format' => ['type' => 'json_object'],
            ];

            try {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'Authorization' => 'Bearer '.$apiKey,
                        'HTTP-Referer' => url('/'),
                        'X-Title' => config('app.name'),
                    ])
                    ->post(self::API_URL, $body);

                // Bỏ qua lỗi Rate limit (429), Model unavailable (404/502/503), Invalid model (400) hoặc Thiếu Credit (402)
                if (in_array($response->status(), [400, 402, 403, 429, 502, 503, 404])) {
                    $msg = $response->json('error.message') ?? $response->body();
                    Log::warning("OpenRouter skip [{$model}]", ['status' => $response->status(), 'msg' => $msg]);
                    $lastError = "[{$model}] {$response->status()}: {$msg}";

                    continue;
                }

                if ($response->failed()) {
                    $msg = $response->json('error.message') ?? $response->body();
                    Log::error("OpenRouter error [{$model}]", ['status' => $response->status(), 'msg' => $msg]);

                    $lastError = "[{$model}] {$response->status()}: {$msg}";

                    continue;
                }

                // 5. Lấy text từ OpenAI format
                $rawText = $response->json('choices.0.message.content') ?? '';

                // 6. Làm sạch markdown (```json ... ```)
                $clean = trim($rawText);
                $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean);
                $clean = preg_replace('/\s*```\s*$/i', '', $clean);
                $clean = trim($clean);

                // 7. Parse JSON
                $result = json_decode($clean, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::warning('OpenRouter JSON parse failed', ['raw' => $rawText, 'model' => $model]);

                    return ['error' => 'Không parse được JSON từ OpenRouter.', 'raw' => $rawText];
                }

                $result['_model_used'] = $model; // Ghi chú model nào đã thành công

                return $result;

            } catch (ConnectionException $e) {
                Log::error('OpenRouter connection error', ['msg' => $e->getMessage()]);
                $lastError = 'Connection error: '.$e->getMessage();
            }
        }

        return ['error' => 'Tất cả model OpenRouter đều thất bại. Lỗi cuối: '.$lastError];
    }
}
