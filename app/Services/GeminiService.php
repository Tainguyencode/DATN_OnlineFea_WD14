<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service Tương tác với Trí tuệ nhân tạo Google Gemini API (GeminiService)
 * 
 * Chức năng chính:
 * 1. `generateText`: Sinh văn bản tóm tắt bài học, trả lời câu hỏi AI Trợ lý học tập (Learning AI Assistant).
 * 2. `analyzeImage`: Phân tích hình ảnh / khung hình cắt từ video bài giảng bằng Gemini Multimodal Vision để phát hiện dấu hiệu vi phạm nội dung:
 *    - Bạo lực (violence), Người lớn (adult), Vũ khí (weapon).
 *    - Logo các nền tảng (TikTok, YouTube, Facebook, Instagram) & Watermark bản quyền.
 *    - Đánh giá mức độ rủi ro bản quyền (copyright_risk: none, low, medium, high).
 */
class GeminiService
{
    /** URL API Google Generative AI */
    private const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/';
    
    /** Model Gemini mặc định sử dụng */
    private const DEFAULT_MODEL = 'gemini-3.5-flash';

    /**
     * Sinh phản hồi văn bản từ Gemini cho trợ lý AI bài học (Tóm tắt, Giải đáp thắc mắc).
     *
     * @param string $prompt Câu lệnh yêu cầu truyền tới AI
     * @param array{max_tokens?: int, temperature?: float, timeout?: int} $options Cấu hình tùy chọn
     * @return array{text?: string, error?: string, _model_used?: string} Mảng chứa kết quả văn bản hoặc thông báo lỗi
     */
    public function generateText(string $prompt, array $options = []): array
    {
        $apiKey = $this->apiKey();

        if ($apiKey === null) {
            return ['error' => 'Chưa cấu hình GEMINI_API_KEY trong .env'];
        }

        $maxTokens = (int) ($options['max_tokens'] ?? 400);
        $temperature = (float) ($options['temperature'] ?? 0.3);
        $timeout = (int) ($options['timeout'] ?? 45);
        
        $model = self::DEFAULT_MODEL;

        $body = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => $temperature,
                'maxOutputTokens' => $maxTokens,
            ],
        ];

        try {
            $url = self::API_URL . $model . ':generateContent?key=' . $apiKey;

            $response = Http::timeout($timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $body);

            if (in_array($response->status(), [400, 402, 403, 429, 502, 503, 404], true)) {
                $msg = $response->json('error.message') ?? $response->body();
                Log::warning("Google Gemini text skip [{$model}]", ['status' => $response->status()]);
                return ['error' => "[{$model}] {$response->status()}: {$msg}"];
            }

            if ($response->failed()) {
                $msg = $response->json('error.message') ?? $response->body();
                Log::error("Google Gemini text error [{$model}]", ['status' => $response->status()]);
                return ['error' => "[{$model}] {$response->status()}: {$msg}"];
            }

            $rawText = (string) ($response->json('candidates.0.content.parts.0.text') ?? '');
            $clean = trim($rawText);
            $clean = preg_replace('/^```(?:\w+)?\s*/i', '', $clean) ?? $clean;
            $clean = preg_replace('/\s*```\s*$/i', '', $clean) ?? $clean;
            $clean = trim($clean);

            if ($clean === '') {
                return ['error' => "[{$model}] empty response"];
            }

            return [
                'text' => $clean,
                '_model_used' => $model,
            ];
        } catch (ConnectionException $e) {
            Log::error('Google Gemini text connection error', ['exception' => $e::class]);
            throw $e;
        }
    }

    /**
     * Phân tích khung hình ảnh cắt từ Video bài giảng bằng AI Gemini Multimodal Vision để tự động kiểm duyệt nội dung.
     * 
     * @param string $imagePath Đường dẫn file ảnh thực tế trên ổ đĩa
     * @return array Kết quả dạng mảng JSON [violence, adult, weapon, tiktok_logo, youtube_logo, copyright_risk, summary, reason, confidence]
     */
    public function analyzeImage(string $imagePath): array
    {
        // 1. Kiểm tra file ảnh có tồn tại trên hệ thống không
        if (! file_exists($imagePath)) {
            return ['error' => "File không tồn tại: {$imagePath}"];
        }

        // 2. Đọc & mã hóa ảnh sang chuỗi Base64
        $imageData = base64_encode(file_get_contents($imagePath));
        $mimeType = mime_content_type($imagePath) ?: 'image/jpeg';

        $apiKey = $this->apiKey();

        if ($apiKey === null) {
            return ['error' => 'Chưa cấu hình GEMINI_API_KEY trong .env'];
        }

        // 3. Chuẩn bị prompt mẫu định hình phản hồi dạng JSON nghiêm ngặt cho Gemini
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

        $model = self::DEFAULT_MODEL;

        $body = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt,
                        ],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $imageData,
                            ],
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'maxOutputTokens' => 1024,
                'responseMimeType' => 'application/json',
            ],
        ];

        try {
            $url = self::API_URL . $model . ':generateContent?key=' . $apiKey;

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $body);

            if (in_array($response->status(), [400, 402, 403, 429, 502, 503, 404])) {
                $msg = $response->json('error.message') ?? $response->body();
                Log::warning("Google Gemini skip [{$model}]", ['status' => $response->status(), 'msg' => $msg]);
                return ['error' => "[{$model}] {$response->status()}: {$msg}"];
            }

            if ($response->failed()) {
                $msg = $response->json('error.message') ?? $response->body();
                Log::error("Google Gemini error [{$model}]", ['status' => $response->status(), 'msg' => $msg]);
                return ['error' => "[{$model}] {$response->status()}: {$msg}"];
            }

            // 5. Lấy chuỗi phản hồi text dạng JSON từ Gemini
            $rawText = $response->json('candidates.0.content.parts.0.text') ?? '';

            // 6. Làm sạch các thẻ markdown (```json ... ```)
            $clean = trim($rawText);
            $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean);
            $clean = preg_replace('/\s*```\s*$/i', '', $clean);
            $clean = trim($clean);

            // 7. Parse chuỗi JSON thành mảng PHP
            $result = json_decode($clean, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Google Gemini JSON parse failed', ['raw' => $rawText, 'model' => $model]);

                return ['error' => 'Không parse được JSON từ Google Gemini.', 'raw' => $rawText];
            }

            $result['_model_used'] = $model;

            return $result;

        } catch (ConnectionException $e) {
            Log::error('Google Gemini connection error', ['msg' => $e->getMessage()]);
            return ['error' => 'Connection error: '.$e->getMessage()];
        }
    }

    /**
     * Trích xuất API Key hợp lệ từ cấu hình `config/services.php`.
     * 
     * @return string|null Chuỗi API Key hoặc null nếu chưa cài đặt
     */
    private function apiKey(): ?string
    {
        $key = config('services.gemini.api_key');

        if (! is_string($key) || trim($key) === '') {
            return null;
        }

        return trim($key);
    }
}
