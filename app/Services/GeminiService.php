<?php

namespace App\Services;

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

    /** Danh sách các Model Gemini hỗ trợ thử lại khi model chính quá tải / 503 */
    private const CANDIDATE_MODELS = [
        'gemini-3.5-flash-lite',
        'gemini-3.5-flash',
        'gemini-3.1-flash-lite',
        'gemini-2.0-flash',
    ];

    /**
     * Sinh phản hồi văn bản từ Gemini cho trợ lý AI bài học (Tóm tắt, Giải đáp thắc mắc).
     *
     * @param  string  $prompt  Câu lệnh yêu cầu truyền tới AI
     * @param  array{max_tokens?: int, temperature?: float, timeout?: int}  $options  Cấu hình tùy chọn
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
        $timeout = (int) ($options['timeout'] ?? 20);

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

        $lastError = null;

        foreach (self::CANDIDATE_MODELS as $model) {
            try {
                $url = self::API_URL.$model.':generateContent?key='.$apiKey;

                $response = Http::withoutVerifying()
                    ->timeout($timeout)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->post($url, $body);

                if ($response->successful()) {
                    $rawText = (string) ($response->json('candidates.0.content.parts.0.text') ?? '');
                    $clean = trim($rawText);
                    $clean = preg_replace('/^```(?:\w+)?\s*/i', '', $clean) ?? $clean;
                    $clean = preg_replace('/\s*```\s*$/i', '', $clean) ?? $clean;
                    $clean = trim($clean);

                    if ($clean !== '') {
                        return [
                            'text' => $clean,
                            '_model_used' => $model,
                        ];
                    }
                }

                $msg = $response->json('error.message') ?? $response->body();
                $lastError = "[{$model}] {$response->status()}: {$msg}";
                Log::warning("Google Gemini text skip [{$model}]", ['status' => $response->status(), 'msg' => $msg]);

            } catch (\Throwable $e) {
                $lastError = "[{$model}] Connection error: ".$e->getMessage();
                Log::warning("Google Gemini text connection error [{$model}]", ['error' => $e->getMessage()]);
            }
        }

        return ['error' => $lastError ?? 'Dịch vụ AI Gemini tạm thời quá tải. Vui lòng thử lại sau.'];
    }

    /**
     * Phân tích khung hình ảnh cắt từ Video bài giảng bằng AI Gemini Multimodal Vision để tự động kiểm duyệt nội dung.
     *
     * @param  string  $imagePath  Đường dẫn file ảnh thực tế trên ổ đĩa
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
  "summary": ""
}
PROMPT;

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

        $lastError = null;

        foreach (self::CANDIDATE_MODELS as $model) {
            try {
                $url = self::API_URL.$model.':generateContent?key='.$apiKey;

                $response = Http::withoutVerifying()
                    ->timeout(25)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->post($url, $body);

                if ($response->successful()) {
                    $rawText = (string) ($response->json('candidates.0.content.parts.0.text') ?? '');
                    $result = $this->parseJsonFromText($rawText);

                    if (! is_array($result)) {
                        Log::warning('Google Gemini JSON parse failed', ['raw' => $rawText, 'model' => $model]);

                        $result = [
                            'violence' => false,
                            'adult' => false,
                            'weapon' => false,
                            'tiktok_logo' => false,
                            'youtube_logo' => false,
                            'facebook_logo' => false,
                            'instagram_logo' => false,
                            'watermark' => false,
                            'copyright_risk' => 'none',
                            'confidence' => 0.8,
                            'summary' => 'Frame bình thường.',
                            'reason' => '',
                            '_raw_text' => $rawText,
                        ];
                    }

                    $result['_model_used'] = $model;

                    return $result;
                }

                $msg = $response->json('error.message') ?? $response->body();
                $lastError = "[{$model}] {$response->status()}: {$msg}";
                Log::warning("Google Gemini image skip [{$model}]", ['status' => $response->status(), 'msg' => $msg]);

            } catch (\Throwable $e) {
                $lastError = "[{$model}] Connection error: ".$e->getMessage();
                Log::warning("Google Gemini image connection error [{$model}]", ['error' => $e->getMessage()]);
            }
        }

        return ['error' => $lastError ?? 'Tất cả model Gemini đều tạm thời quá tải. Vui lòng thử lại sau.'];
    }

    /**
     * Phân tích sự phù hợp giữa nội dung video và danh mục / chuyên ngành của khóa học.
     *
     * @param  array<string>  $framePaths  Danh sách đường dẫn các frame đã cắt
     * @param  array{category_name?: string, parent_category_name?: string, course_title?: string, course_description?: string, lesson_title?: string, lesson_content?: string}  $context  Thông tin ngữ cảnh bài học & khóa học
     * @return array{status: string, confidence: float, reason: string, detected_topics: list<string>, _model_used?: string}
     */
    public function analyzeCategoryMatch(array $framePaths, array $context): array
    {
        $apiKey = $this->apiKey();
        if ($apiKey === null) {
            return [
                'status' => 'Cần Admin kiểm tra',
                'confidence' => 0.5,
                'reason' => 'Chưa cấu hình GEMINI_API_KEY trong .env để AI phân tích danh mục.',
                'detected_topics' => [],
            ];
        }

        $validFrames = array_values(array_filter($framePaths, fn ($p) => is_string($p) && file_exists($p)));
        if (empty($validFrames)) {
            return [
                'status' => 'Cần Admin kiểm tra',
                'confidence' => 0.5,
                'reason' => 'Không tìm thấy khung hình video để kiểm tra danh mục.',
                'detected_topics' => [],
            ];
        }

        $selectedFrames = [];
        $totalFrames = count($validFrames);
        if ($totalFrames <= 3) {
            $selectedFrames = $validFrames;
        } else {
            $selectedFrames = [
                $validFrames[0],
                $validFrames[(int) floor($totalFrames / 2)],
                $validFrames[$totalFrames - 1],
            ];
        }

        $categoryName = $context['category_name'] ?? 'Không xác định';
        $parentCategoryName = $context['parent_category_name'] ?? '';
        $courseTitle = $context['course_title'] ?? '';
        $courseDescription = $context['course_description'] ?? '';
        $lessonTitle = $context['lesson_title'] ?? '';
        $lessonContent = $context['lesson_content'] ?? '';

        $prompt = <<<PROMPT
Bạn là hệ thống AI phân tích và kiểm tra sự phù hợp giữa nội dung video bài giảng với danh mục/ngành học của khóa học trên nền tảng giáo dục trực tuyến.

NGỮ CẢNH KHÓA HỌC & BÀI HỌC:
- Danh mục khóa học yêu cầu: {$categoryName}
- Ngành học cha (nếu có): {$parentCategoryName}
- Tên khóa học: {$courseTitle}
- Mô tả khóa học: {$courseDescription}
- Tên bài học: {$lessonTitle}
- Nội dung/mô tả bài học: {$lessonContent}

NHIỆM VỤ CỦA BẠN:
1. Quan sát kỹ các hình ảnh (frame) được cắt từ video bài giảng đính kèm và kết hợp với tiêu đề/nội dung bài học.
2. Nhận diện các chủ đề, công nghệ, kiến thức, lĩnh vực cụ thể xuất hiện trong video (detected_topics).
3. Đánh giá xem nội dung thực tế trong video có PHÙ HỢP với "Danh mục khóa học yêu cầu: {$categoryName}" hay không.

QUY TẮC PHÂN LOẠI TRẠNG THÁI (status):
- "Phù hợp": Khi nội dung video rõ ràng thuộc về hoặc bổ trợ trực tiếp cho danh mục "{$categoryName}".
- "Không phù hợp": Khi nội dung video hoàn toàn thuộc về lĩnh vực khác không liên quan (ví dụ: danh mục là Lập trình Web nhưng video dạy nấu ăn, ẩm thực, thời trang, thể thao, game không liên quan...).
- "Cần Admin kiểm tra": Khi nội dung có tính giao thoa, nội dung chung chung (ví dụ slide giới thiệu chung, màn hình desktop chưa rõ nội dung), hoặc không đủ bằng chứng hình ảnh để khẳng định chắc chắn.

QUY TẮC ĐỘ TIN CẬY (confidence):
- confidence là số thực từ 0.0 đến 1.0 (ví dụ: 0.95 tương đương 95%).
- Nếu không chắc chắn, hãy đặt confidence từ 0.5 đến 0.65 và chọn status "Cần Admin kiểm tra".

QUY TẮC NHẬN XÉT (reason):
- Viết 1-2 câu tiếng Việt nhận xét khách quan, nêu rõ tại sao phù hợp hoặc không phù hợp, nhắc đến tên danh mục "{$categoryName}".

QUAN TRỌNG:
- Trả về DUY NHẤT một chuỗi JSON hợp lệ. Không giải thích thêm. Không dùng markdown ```json.
- Định dạng JSON bắt buộc:
{
  "status": "Phù hợp",
  "confidence": 0.95,
  "reason": "Nội dung video trình bày các kiến thức phù hợp với danh mục {$categoryName}.",
  "detected_topics": ["Chủ đề 1", "Chủ đề 2"]
}
PROMPT;

        $parts = [
            ['text' => $prompt],
        ];

        foreach ($selectedFrames as $framePath) {
            $imageData = base64_encode(file_get_contents($framePath));
            $mimeType = mime_content_type($framePath) ?: 'image/jpeg';
            $parts[] = [
                'inline_data' => [
                    'mime_type' => $mimeType,
                    'data' => $imageData,
                ],
            ];
        }

        $body = [
            'contents' => [
                ['parts' => $parts],
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'maxOutputTokens' => 1024,
                'responseMimeType' => 'application/json',
            ],
        ];

        foreach (self::CANDIDATE_MODELS as $model) {
            try {
                $url = self::API_URL.$model.':generateContent?key='.$apiKey;

                $response = Http::withoutVerifying()
                    ->timeout(30)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->post($url, $body);

                if ($response->successful()) {
                    $rawText = (string) ($response->json('candidates.0.content.parts.0.text') ?? '');
                    $result = $this->parseJsonFromText($rawText);

                    if (is_array($result) && isset($result['status'])) {
                        $statusRaw = trim((string) $result['status']);
                        $status = match (mb_strtolower($statusRaw, 'UTF-8')) {
                            'phù hợp', 'phu hop', 'match', 'matched', 'suitable' => 'Phù hợp',
                            'không phù hợp', 'khong phu hop', 'mismatch', 'mismatched', 'unsuitable' => 'Không phù hợp',
                            default => 'Cần Admin kiểm tra',
                        };

                        $confidence = isset($result['confidence']) ? (float) $result['confidence'] : 0.8;
                        if ($confidence > 1.0 && $confidence <= 100.0) {
                            $confidence = round($confidence / 100.0, 2);
                        }

                        $detectedTopics = is_array($result['detected_topics'] ?? null)
                            ? array_values(array_filter(array_map('strval', $result['detected_topics'])))
                            : [];

                        $reason = trim((string) ($result['reason'] ?? ''));
                        if ($reason === '') {
                            $reason = $status === 'Phù hợp'
                                ? "Nội dung video phù hợp với danh mục {$categoryName}."
                                : ($status === 'Không phù hợp'
                                    ? "Nội dung video không phù hợp với danh mục {$categoryName}."
                                    : "Chưa đủ bằng chứng để xác định chắc chắn độ phù hợp với danh mục {$categoryName}.");
                        }

                        return [
                            'status' => $status,
                            'confidence' => $confidence,
                            'reason' => $reason,
                            'detected_topics' => $detectedTopics,
                            '_model_used' => $model,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Google Gemini category match connection error [{$model}]", ['error' => $e->getMessage()]);
            }
        }

        return [
            'status' => 'Cần Admin kiểm tra',
            'confidence' => 0.5,
            'reason' => "AI chưa thể đưa ra kết luận chắc chắn cho danh mục {$categoryName}. Admin vui lòng kiểm tra thêm.",
            'detected_topics' => [],
        ];
    }

    /**
     * Tách và parse JSON từ chuỗi phản hồi của Gemini một cách linh hoạt.
     */
    private function parseJsonFromText(string $rawText): ?array
    {
        $clean = trim($rawText);

        if ($clean === '') {
            return null;
        }

        // 1. Thử parse trực tiếp
        $decoded = json_decode($clean, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // 2. Tìm khối code markdown ```json ... ``` ở bất kỳ đâu trong text
        if (preg_match('/```(?:json)?\s*(\{[\s\S]*?\})\s*```/i', $clean, $matches)) {
            $decoded = json_decode(trim($matches[1]), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        // 3. Trích xuất khoảng giữa dấu { đầu tiên và } cuối cùng
        $firstBrace = strpos($clean, '{');
        $lastBrace = strrpos($clean, '}');

        if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
            $jsonCandidate = substr($clean, $firstBrace, $lastBrace - $firstBrace + 1);

            $decoded = json_decode($jsonCandidate, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            // 4. Xóa dấu phẩy thừa (trailing comma) trước } hoặc ]
            $sanitized = preg_replace('/,\s*([\}\]])/', '$1', $jsonCandidate);
            $decoded = json_decode($sanitized, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
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
