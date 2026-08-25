<?php

namespace App\Services\Ai;

use App\Models\AiChatMessage;
use App\Models\AiConversation;
use App\Models\Course;
use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LearningPathAiService
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Lấy hoặc khởi tạo cuộc hội thoại AI Lộ trình cho User / Session.
     */
    public function getOrCreateConversation(?User $user, ?string $sessionId, ?int $learningPathId = null): AiConversation
    {
        $query = AiConversation::where('scope', 'learning_path');

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('session_id', $sessionId);
        }

        $conversation = $query->latest('last_activity_at')->first();

        if (! $conversation) {
            $conversation = AiConversation::create([
                'user_id' => $user?->id,
                'session_id' => $user ? null : $sessionId,
                'scope' => 'learning_path',
                'title' => 'Tư vấn lộ trình học tập',
                'learning_path_id' => $learningPathId,
                'context_data' => [
                    'onboarding_completed' => false,
                    'profile' => [],
                    'current_roadmap' => null,
                    'pending_topic_change' => false,
                    'pending_new_topic' => null,
                ],
                'last_activity_at' => now(),
            ]);
        } elseif ($learningPathId && $conversation->learning_path_id !== $learningPathId) {
            $conversation->update([
                'learning_path_id' => $learningPathId,
                'last_activity_at' => now(),
            ]);
        }

        return $conversation;
    }

    /**
     * Khởi tạo lại cuộc trò chuyện mới.
     */
    public function resetConversation(?User $user, ?string $sessionId, ?int $learningPathId = null): AiConversation
    {
        return AiConversation::create([
            'user_id' => $user?->id,
            'session_id' => $user ? null : $sessionId,
            'scope' => 'learning_path',
            'title' => 'Tư vấn lộ trình mới',
            'learning_path_id' => $learningPathId,
            'context_data' => [
                'onboarding_completed' => false,
                'profile' => [],
                'current_roadmap' => null,
                'pending_topic_change' => false,
                'pending_new_topic' => null,
            ],
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Xử lý tin nhắn người dùng gửi đến AI Tư Vấn Lộ Trình.
     *
     * @param  array{age?: int, field?: string, goal?: string, level?: string, existing_knowledge?: string, weekly_hours?: string, duration?: string, target_role?: string}  $onboardingData
     * @return array{success: bool, response_type: string, message: string, roadmap?: array, matched_courses?: array, related_learning_path?: array, is_topic_switch?: bool, new_topic?: string, conversation_id: int}
     */
    public function processMessage(AiConversation $conversation, string $userPrompt, ?array $onboardingData = []): array
    {
        $cleanPrompt = trim($userPrompt);
        $onboardingData = $onboardingData ?? [];
        $contextData = $conversation->context_data ?? [];
        $profile = $contextData['profile'] ?? [];
        $pendingTopicChange = (bool) ($contextData['pending_topic_change'] ?? false);

        // 1. Cập nhật profile từ onboardingData nếu có
        if (! empty($onboardingData)) {
            $profile = array_merge($profile, array_filter($onboardingData, fn ($v) => ! is_null($v) && $v !== ''));
            $contextData['onboarding_completed'] = true;
        }

        // 2. Xử lý phản hồi xác nhận đổi chủ đề từ người dùng
        $isConfirmingSwitch = str_contains(mb_strtolower($cleanPrompt), 'đồng ý') || 
                              str_contains(mb_strtolower($cleanPrompt), 'tạo lộ trình mới') ||
                              str_starts_with(mb_strtolower($cleanPrompt), 'có,');
        
        $isDismissingSwitch = str_contains(mb_strtolower($cleanPrompt), 'giữ lộ trình cũ') || 
                              str_contains(mb_strtolower($cleanPrompt), 'tiếp tục lộ trình hiện tại');

        if ($pendingTopicChange && ($isConfirmingSwitch || $isDismissingSwitch)) {
            $this->resolvePendingConfirmations($conversation, $isConfirmingSwitch ? 'resolved' : 'dismissed');

            $newTopic = $contextData['pending_new_topic'] ?? 'chủ đề mới';
            $contextData['pending_topic_change'] = false;
            $contextData['pending_new_topic'] = null;

            if ($isDismissingSwitch) {
                $dismissMsg = 'Đã giữ nguyên lộ trình hiện tại của bạn. Bạn muốn tìm hiểu thêm thông tin gì về lộ trình này?';
                
                AiChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $conversation->user_id,
                    'learning_path_id' => $conversation->learning_path_id,
                    'role' => 'user',
                    'content' => $cleanPrompt,
                ]);

                AiChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $conversation->user_id,
                    'learning_path_id' => $conversation->learning_path_id,
                    'role' => 'assistant',
                    'content' => $dismissMsg,
                ]);

                $conversation->update([
                    'context_data' => $contextData,
                    'last_activity_at' => now(),
                ]);

                return [
                    'success' => true,
                    'response_type' => 'general_advice',
                    'message' => $dismissMsg,
                    'conversation_id' => $conversation->id,
                ];
            }

            // Nếu đồng ý đổi: Reset roadmap cũ và xây dựng roadmap cho topic mới
            $profile['desired_field'] = $newTopic;
            $profile['target_role'] = $newTopic;
            $contextData['current_roadmap'] = null;
            $contextData['profile'] = $profile;
            $conversation->title = 'Lộ trình: ' . $newTopic;
            $conversation->current_topic = $newTopic;
            $cleanPrompt = "Hãy xây dựng một lộ trình học tập chi tiết, bài bản từ đầu cho {$newTopic}.";
        }

        // 3. Lấy thông tin Learning Path hiện tại nếu người dùng đang ở trang /learning-paths/{slug}
        $currentLearningPath = null;
        if ($conversation->learning_path_id) {
            $currentLearningPath = LearningPath::with(['courses' => fn ($q) => $q->published()])->find($conversation->learning_path_id);
        }

        // 4. Lấy lịch sử tin nhắn gần nhất
        $history = $conversation->messages()
            ->latest('id')
            ->take(12)
            ->get()
            ->reverse()
            ->values();

        // 5. Kiểm tra Scope & Xử lý Prompt với Gemini AI
        $systemInstruction = $this->buildSystemInstruction($profile, $contextData['current_roadmap'] ?? null, $currentLearningPath, $pendingTopicChange);
        $fullPrompt = $this->buildFullPrompt($systemInstruction, $history, $cleanPrompt);

        // Lưu tin nhắn của User
        AiChatMessage::create([
            'conversation_id' => $conversation->id,
            'user_id' => $conversation->user_id,
            'learning_path_id' => $conversation->learning_path_id,
            'role' => 'user',
            'content' => $cleanPrompt,
        ]);

        // Gọi Gemini
        $aiResult = $this->geminiService->generateText($fullPrompt, [
            'json' => true,
            'temperature' => 0.2,
            'max_output_tokens' => 3500,
        ]);

        if (isset($aiResult['error'])) {
            Log::error('LearningPathAiService error from Gemini', ['error' => $aiResult['error']]);

            $errorMsg = 'AI tạm thời chưa thể phản hồi. Vui lòng thử lại sau giây lát hoặc gửi lại câu hỏi.';
            AiChatMessage::create([
                'conversation_id' => $conversation->id,
                'user_id' => $conversation->user_id,
                'learning_path_id' => $conversation->learning_path_id,
                'role' => 'assistant',
                'content' => $errorMsg,
            ]);

            return [
                'success' => false,
                'response_type' => 'error',
                'message' => $errorMsg,
                'conversation_id' => $conversation->id,
            ];
        }

        $parsedJson = $this->parseGeminiResponse($aiResult['text'] ?? '');

        // 6. Xử lý các loại phản hồi của AI:
        // A. Ngoài phạm vi (out_of_scope)
        if (($parsedJson['type'] ?? '') === 'out_of_scope') {
            $refusalText = 'AI Lộ trình chỉ hỗ trợ tư vấn lộ trình học tập và lựa chọn khóa học trên FEA. Bạn hãy cho tôi biết ngành, mục tiêu hoặc trình độ hiện tại để tôi hỗ trợ.';

            AiChatMessage::create([
                'conversation_id' => $conversation->id,
                'user_id' => $conversation->user_id,
                'learning_path_id' => $conversation->learning_path_id,
                'role' => 'assistant',
                'content' => $refusalText,
            ]);

            $conversation->update(['last_activity_at' => now()]);

            return [
                'success' => true,
                'response_type' => 'out_of_scope',
                'message' => $refusalText,
                'conversation_id' => $conversation->id,
            ];
        }

        // B. Phát hiện đổi chủ đề (topic_switch_confirmation) - Chỉ hỏi khi chưa có pending confirmation
        if (($parsedJson['type'] ?? '') === 'topic_switch_confirmation' && ! $pendingTopicChange) {
            $newTopic = trim($parsedJson['new_topic'] ?? 'chủ đề mới');
            $switchMsg = $parsedJson['message'] ?? "Bạn đang tìm hiểu lộ trình hiện tại. Bạn có muốn chuyển sang xây dựng lộ trình mới cho {$newTopic} không?";

            $contextData['pending_topic_change'] = true;
            $contextData['pending_new_topic'] = $newTopic;

            AiChatMessage::create([
                'conversation_id' => $conversation->id,
                'user_id' => $conversation->user_id,
                'learning_path_id' => $conversation->learning_path_id,
                'role' => 'assistant',
                'content' => $switchMsg,
                'metadata' => [
                    'type' => 'topic_switch_confirmation',
                    'new_topic' => $newTopic,
                    'status' => 'pending',
                ],
            ]);

            $conversation->update([
                'context_data' => $contextData,
                'last_activity_at' => now(),
            ]);

            return [
                'success' => true,
                'response_type' => 'topic_switch_confirmation',
                'message' => $switchMsg,
                'is_topic_switch' => true,
                'new_topic' => $newTopic,
                'conversation_id' => $conversation->id,
            ];
        }

        // C. Phản hồi giải đáp / tư vấn thông thường (general_advice)
        if (($parsedJson['type'] ?? '') === 'general_advice') {
            $adviceMsg = $parsedJson['message'] ?? '';

            if (! empty($parsedJson['extracted_profile'])) {
                $profile = array_merge($profile, array_filter($parsedJson['extracted_profile']));
                $contextData['profile'] = $profile;
            }

            AiChatMessage::create([
                'conversation_id' => $conversation->id,
                'user_id' => $conversation->user_id,
                'learning_path_id' => $conversation->learning_path_id,
                'role' => 'assistant',
                'content' => $adviceMsg,
            ]);

            $conversation->update([
                'context_data' => $contextData,
                'last_activity_at' => now(),
            ]);

            return [
                'success' => true,
                'response_type' => 'general_advice',
                'message' => $adviceMsg,
                'conversation_id' => $conversation->id,
            ];
        }

        // D. Xây dựng hoặc điều chỉnh Roadmap (learning_roadmap)
        $roadmap = $parsedJson['roadmap'] ?? $parsedJson;
        $adviceMsg = $parsedJson['message'] ?? ($roadmap['overview'] ?? 'Dưới đây là lộ trình học tập chi tiết được AI xây dựng riêng cho bạn:');

        if (! empty($parsedJson['extracted_profile'])) {
            $profile = array_merge($profile, array_filter($parsedJson['extracted_profile']));
        }
        $contextData['profile'] = $profile;
        $contextData['current_roadmap'] = $roadmap;
        $contextData['pending_topic_change'] = false;
        $contextData['pending_new_topic'] = null;

        if (! empty($roadmap['title'])) {
            $conversation->title = 'Lộ trình: '.$roadmap['title'];
            $conversation->current_topic = $roadmap['topic'] ?? $roadmap['title'];
        }

        // BƯỚC 2: Đối chiếu Roadmap với Database thực tế của FEA với THRESHOLD VÀ DOMAIN FILTER NGHIÊM NGẶT
        $matchResults = $this->matchCoursesFromDatabase($roadmap, $profile);
        $relatedLearningPath = $this->findMatchingLearningPath($roadmap, $profile);

        $metadata = [
            'type' => 'learning_roadmap',
            'roadmap' => $roadmap,
            'matched_courses' => $matchResults,
            'related_learning_path' => $relatedLearningPath,
        ];

        AiChatMessage::create([
            'conversation_id' => $conversation->id,
            'user_id' => $conversation->user_id,
            'learning_path_id' => $conversation->learning_path_id,
            'role' => 'assistant',
            'content' => $adviceMsg,
            'metadata' => $metadata,
        ]);

        $conversation->update([
            'context_data' => $contextData,
            'last_activity_at' => now(),
        ]);

        return [
            'success' => true,
            'response_type' => 'learning_roadmap',
            'message' => $adviceMsg,
            'roadmap' => $roadmap,
            'matched_courses' => $matchResults,
            'related_learning_path' => $relatedLearningPath,
            'conversation_id' => $conversation->id,
        ];
    }

    /**
     * Đánh dấu các tin nhắn xác nhận chuyển chủ đề cũ đã được xử lý để không lặp lại nút bấm.
     */
    protected function resolvePendingConfirmations(AiConversation $conversation, string $status = 'resolved'): void
    {
        $messages = $conversation->messages()->whereNotNull('metadata')->get();
        foreach ($messages as $msg) {
            $meta = $msg->metadata;
            if (is_array($meta) && ($meta['type'] ?? '') === 'topic_switch_confirmation') {
                $meta['status'] = $status;
                $msg->update(['metadata' => $meta]);
            }
        }
    }

    /**
     * BƯỚC 2: Tìm kiếm khóa học thực tế từ Database FEA dựa trên Domain & Relevance Score.
     * TUYỆT ĐỐI KHÔNG GỢI Ý KHÓA HỌC SAI CHUYÊN NGÀNH.
     */
    public function matchCoursesFromDatabase(array $roadmap, array $profile = []): array
    {
        $stages = $roadmap['stages'] ?? [];
        $roadmapDomain = $this->detectDomain($roadmap, $profile);

        // Lấy danh sách toàn bộ khóa học published trên FEA để so khớp
        $allPublishedCourses = Course::published()
            ->with([
                'category:id,name,slug',
                'instructor:id,name,avatar',
            ])
            ->withCount('lessons')
            ->get();

        $matchedStages = [];
        $matchedCourseIds = [];
        $totalMatchedCoursesCount = 0;

        foreach ($stages as $stageIndex => $stage) {
            $stageNumber = $stage['stage'] ?? ($stageIndex + 1);
            $stageTitle = $stage['title'] ?? "Giai đoạn {$stageNumber}";
            $skills = is_array($stage['skills'] ?? null) ? $stage['skills'] : [];
            $topics = is_array($stage['topics_to_learn'] ?? null) ? $stage['topics_to_learn'] : [];

            $stageCourseMatches = [];

            foreach ($skills as $skill) {
                $skillQuery = mb_strtolower(trim($skill));
                if (empty($skillQuery)) {
                    continue;
                }

                // Tìm kiếm khóa học đạt ngưỡng điểm và cùng Domain
                $bestCourse = null;
                $highestScore = 0;

                foreach ($allPublishedCourses as $course) {
                    if (in_array($course->id, $matchedCourseIds, true)) {
                        continue;
                    }

                    // 1. Kiểm tra Domain tương thích (Nếu Course là IT mà Roadmap là Business -> Score = 0)
                    $courseDomain = $this->detectCourseDomain($course);
                    if (! $this->areDomainsCompatible($roadmapDomain, $courseDomain)) {
                        continue;
                    }

                    // 2. Tính điểm Relevance Score (0 - 100)
                    $score = $this->calculateRelevanceScore($course, $skill, $stageTitle, $topics);

                    // Ngưỡng tối thiểu nghiêm ngặt (Score >= 55)
                    if ($score >= 55 && $score > $highestScore) {
                        $highestScore = $score;
                        $bestCourse = $course;
                    }
                }

                if ($bestCourse) {
                    $matchedCourseIds[] = $bestCourse->id;
                    $totalMatchedCoursesCount++;
                    $stageCourseMatches[] = [
                        'skill' => $skill,
                        'course_id' => $bestCourse->id,
                        'title' => $bestCourse->title,
                        'slug' => $bestCourse->slug,
                        'url' => route('courses.show', $bestCourse->slug),
                        'thumbnail' => $bestCourse->thumbnail ? asset('storage/'.$bestCourse->thumbnail) : null,
                        'instructor_name' => $bestCourse->instructor?->name ?? 'Giảng viên FEA',
                        'instructor_avatar' => $bestCourse->instructor?->avatar ? asset('storage/'.$bestCourse->instructor->avatar) : null,
                        'price' => $bestCourse->price,
                        'formatted_price' => $bestCourse->is_free ? 'Miễn phí' : number_format($bestCourse->price).' đ',
                        'lessons_count' => $bestCourse->lessons_count ?? 0,
                        'level' => $bestCourse->level,
                        'has_course' => true,
                        'relevance_score' => $highestScore,
                    ];
                } else {
                    // Không có khóa học nào đạt ngưỡng -> Không cố gán khóa học linh tinh
                    $stageCourseMatches[] = [
                        'skill' => $skill,
                        'course_id' => null,
                        'title' => null,
                        'has_course' => false,
                        'message' => 'FEA hiện chưa có khóa học phù hợp trực tiếp cho phần này.',
                    ];
                }
            }

            $matchedStages[] = [
                'stage' => $stageNumber,
                'title' => $stageTitle,
                'skills' => $skills,
                'matches' => $stageCourseMatches,
            ];
        }

        return [
            'has_any_matched_courses' => ($totalMatchedCoursesCount > 0),
            'total_matched_count' => $totalMatchedCoursesCount,
            'stages' => $matchedStages,
        ];
    }

    /**
     * Nhận diện Domain kiến thức của Roadmap (e.g. tech, business, marketing, design, languages...).
     */
    protected function detectDomain(array $roadmap, array $profile = []): string
    {
        $text = mb_strtolower(implode(' ', [
            $roadmap['topic'] ?? '',
            $roadmap['title'] ?? '',
            $roadmap['target_role'] ?? '',
            $profile['desired_field'] ?? '',
            $profile['target_role'] ?? '',
            implode(' ', $roadmap['stages'][0]['skills'] ?? []),
        ]));

        if (Str::contains($text, ['kinh doanh', 'business', 'quản trị', 'doanh nghiệp', 'bán hàng', 'sales', 'đàm phán', 'quản lý', 'tài chính', 'kế toán', 'thương mại'])) {
            return 'business';
        }
        if (Str::contains($text, ['marketing', 'seo', 'content', 'social media', 'quảng cáo', 'branding', 'truyền thông'])) {
            return 'marketing';
        }
        if (Str::contains($text, ['thiết kế', 'design', 'ui/ux', 'ui', 'ux', 'photoshop', 'illustrator', 'figma', 'đồ họa'])) {
            return 'design';
        }
        if (Str::contains($text, ['tiếng anh', 'ngoại ngữ', 'ielts', 'toeic', 'tiếng nhật', 'tiếng hàn', 'english'])) {
            return 'languages';
        }

        return 'tech';
    }

    /**
     * Nhận diện Domain kiến thức của Course.
     */
    protected function detectCourseDomain(Course $course): string
    {
        $text = mb_strtolower(implode(' ', [
            $course->title ?? '',
            $course->category?->name ?? '',
            $course->category?->slug ?? '',
            implode(' ', is_array($course->tags) ? $course->tags : []),
            $course->description ?? '',
        ]));

        if (Str::contains($text, ['kinh doanh', 'business', 'quản trị', 'doanh nghiệp', 'bán hàng', 'sales', 'tài chính', 'kế toán', 'thương mại'])) {
            return 'business';
        }
        if (Str::contains($text, ['marketing', 'seo', 'content', 'social', 'quảng cáo', 'branding', 'truyền thông'])) {
            return 'marketing';
        }
        if (Str::contains($text, ['thiết kế', 'design', 'ui/ux', 'photoshop', 'illustrator', 'figma', 'đồ họa'])) {
            return 'design';
        }
        if (Str::contains($text, ['tiếng anh', 'ngoại ngữ', 'ielts', 'toeic', 'tiếng nhật', 'tiếng hàn'])) {
            return 'languages';
        }

        return 'tech';
    }

    /**
     * So sánh 2 domain có tương thích hay không.
     */
    protected function areDomainsCompatible(string $roadmapDomain, string $courseDomain): bool
    {
        if ($roadmapDomain === $courseDomain) {
            return true;
        }

        // Cho phép giao thoa hợp lý (e.g. Business <-> Marketing)
        if (($roadmapDomain === 'business' && $courseDomain === 'marketing') || ($roadmapDomain === 'marketing' && $courseDomain === 'business')) {
            return true;
        }

        return false;
    }

    /**
     * Tính điểm Relevance Score giữa Course và Skill/Stage (0 - 100).
     */
    protected function calculateRelevanceScore(Course $course, string $skill, string $stageTitle, array $stageTopics): int
    {
        $stopwords = [
            'khoa', 'hoc', 'co', 'ban', 'tu', 'den', 'nang', 'cao', 'cho', 'nguoi',
            'moi', 'bat', 'dau', 'toan', 'dien', 'chuyen', 'sau', 'thuc', 'chien',
            'cac', 'va', 'trong', 've', 'cua', 'ky', 'nang', 'tong', 'quan', 'nen', 'tang',
        ];

        $titleSlug = Str::slug($course->title, ' ');
        $catSlug = Str::slug($course->category?->name ?? '', ' ');
        $skillSlug = Str::slug($skill, ' ');

        // 1. Nếu khớp chính xác cụm từ kỹ năng hoặc Title có kỹ năng
        if (! empty($skillSlug) && Str::contains($titleSlug, $skillSlug)) {
            return 95;
        }

        // Tách các từ khóa có ý nghĩa chuyên môn (loại bỏ stopwords)
        $skillKeywords = array_filter(explode(' ', $skillSlug), fn ($k) => strlen($k) >= 2 && ! in_array($k, $stopwords, true));

        if (empty($skillKeywords)) {
            return 0;
        }

        $matchedKwCount = 0;
        foreach ($skillKeywords as $kw) {
            if (Str::contains($titleSlug, $kw)) {
                $matchedKwCount += 2;
            } elseif (Str::contains($catSlug, $kw)) {
                $matchedKwCount += 1;
            }
        }

        $ratio = count($skillKeywords) > 0 ? ($matchedKwCount / count($skillKeywords)) : 0;

        if ($ratio >= 1.5) {
            return 85;
        }
        if ($ratio >= 1.0) {
            return 70;
        }
        if ($ratio >= 0.5) {
            return 40; // Chưa đủ ngưỡng 55
        }

        return 0;
    }

    /**
     * Tìm xem trên FEA đã có sẵn Learning Path nào tương đồng cao không (Có kiểm tra Domain).
     */
    public function findMatchingLearningPath(array $roadmap, array $profile = []): ?array
    {
        $topic = mb_strtolower($roadmap['topic'] ?? $roadmap['title'] ?? '');
        if (empty($topic)) {
            return null;
        }

        $roadmapDomain = $this->detectDomain($roadmap, $profile);
        $learningPaths = LearningPath::withCount('courses')->get();

        foreach ($learningPaths as $lp) {
            $lpTitle = mb_strtolower($lp->title);
            $lpRole = mb_strtolower($lp->target_role ?? '');

            // Phân loại domain của Learning Path
            $lpText = "{$lpTitle} {$lpRole}";
            $lpDomain = Str::contains($lpText, ['kinh doanh', 'business', 'quản trị', 'bán hàng', 'sales']) ? 'business' :
                       (Str::contains($lpText, ['marketing', 'seo']) ? 'marketing' :
                       (Str::contains($lpText, ['thiết kế', 'design', 'ui/ux']) ? 'design' : 'tech'));

            if (! $this->areDomainsCompatible($roadmapDomain, $lpDomain)) {
                continue;
            }

            if (str_contains($lpTitle, $topic) || str_contains($topic, $lpTitle) || ($lpRole && str_contains($topic, $lpRole))) {
                return [
                    'id' => $lp->id,
                    'title' => $lp->title,
                    'slug' => $lp->slug,
                    'url' => route('learning-paths.show', $lp->slug),
                    'level' => $lp->level,
                    'courses_count' => $lp->courses_count,
                    'target_role' => $lp->target_role,
                ];
            }
        }

        return null;
    }

    /**
     * Xây dựng System Instruction có Scope Lock nghiêm ngặt và yêu cầu Roadmap chi tiết, chuẩn mực.
     */
    protected function buildSystemInstruction(array $profile, ?array $currentRoadmap, ?LearningPath $currentLearningPath, bool $hasPendingTopicChange): string
    {
        $profileSummary = json_encode($profile, JSON_UNESCAPED_UNICODE);
        $roadmapSummary = $currentRoadmap ? json_encode($currentRoadmap, JSON_UNESCAPED_UNICODE) : 'Chưa có roadmap';

        $currentLpContext = 'Người dùng đang ở trang danh sách lộ trình chung.';
        if ($currentLearningPath) {
            $stagesList = [];
            foreach ($currentLearningPath->courses as $c) {
                $stagesList[] = "- Giai đoạn: {$c->pivot->stage_name} | Khóa: {$c->title}";
            }
            $currentLpContext = "Người dùng đang xem chi tiết Lộ trình: '{$currentLearningPath->title}' (Cấp độ: {$currentLearningPath->level}, Mục tiêu: {$currentLearningPath->target_role}).\nCác môn học trong lộ trình này gồm:\n".implode("\n", $stagesList);
        }

        return <<<PROMPT
BẠN LÀ: "AI Tư vấn Lộ trình học tập" của nền tảng giáo dục trực tuyến FEA.

==================================================
PHẠM VI HOẠT ĐỘNG VÀ QUY TẮC BẢO MẬT (SCOPE LOCK):
==================================================
1. BẠN CHỈ ĐƯỢC PHÉP:
- Tư vấn ngành học, mục tiêu nghề nghiệp, kỹ năng cần thiết.
- XÂY DỰNG LỘ TRÌNH HỌC TẬP (ROADMAP) CHI TIẾT, BÀI BẢN VÀ DÀI ĐẦY ĐỦ NỘI DUNG (4 - 6 Giai đoạn).
- Xác định rõ: Mục tiêu giai đoạn, Nội dung chi tiết cần học, Kỹ năng cần đạt, Bài tập thực hành/Dự án thực tế, Thời gian học dự kiến, và Điều kiện chuyển tiếp giữa các giai đoạn.
- Giải thích lý do học, định hướng lộ trình phù hợp với từng người học.

2. BẠN TUYỆT ĐỐI KHÔNG ĐƯỢC LÀM (TỪ CHỐI NGAY):
- KHÔNG làm chatbot tổng quát, không nói chuyện phiếm, đời sống, chính trị, giải trí.
- KHÔNG tự bịa ra ID khóa học hay cố ép roadmap theo các khóa học đang có. Bạn xây dựng Roadmap hoàn toàn độc lập từ tri thức chuyên môn chuẩn mực.
- Nếu người dùng hỏi ngoài phạm vi, TRẢ VỀ JSON type: "out_of_scope" với câu từ chối chuẩn:
"AI Lộ trình chỉ hỗ trợ tư vấn lộ trình học tập và lựa chọn khóa học trên FEA. Bạn hãy cho tôi biết ngành, mục tiêu hoặc trình độ hiện tại để tôi hỗ trợ."

3. QUY TẮC ĐỔI CHỦ ĐỀ (TOPIC SWITCH) vs KỸ NĂNG CÙNG LỘ TRÌNH:
- Khi người dùng hỏi về các kỹ năng con thuộc ngành hiện tại (ví dụ đang ở Lập trình Web mà hỏi HTML, CSS, PHP, Laravel, Docker, Git... hoặc đang ở Kinh doanh mà hỏi Marketing, Sales, Quản trị...): ĐÂY LÀ CÙNG LỘ TRÌNH -> Tiếp tục giải thích hoặc cập nhật lộ trình, KHÔNG hỏi đổi chủ đề.
- Chỉ hỏi `topic_switch_confirmation` khi người dùng chuyển hẳn sang ngành khác (ví dụ: từ Lập trình sang Kinh doanh, từ Kinh doanh sang Thiết kế đồ họa...).

4. YÊU CẦU CHI TIẾT CHO MỖI GIAI ĐOẠN CỦA ROADMAP:
Mỗi giai đoạn trong Roadmap bắt buộc phải có đầy đủ các thông tin sau:
- "stage": Số thứ tự (1, 2, 3, 4, 5, 6...)
- "title": Tên giai đoạn rõ ràng
- "duration": Thời gian ước tính (ví dụ: "4 - 6 tuần (1-2h/ngày)")
- "objective": Mục tiêu cốt lõi của giai đoạn này
- "topics_to_learn": Mảng danh sách 4 - 8 chuyên đề/kiến thức cụ thể cần học trong giai đoạn
- "skills": Mảng danh sách các kỹ năng đạt được
- "practice": Bài tập thực hành hoặc Dự án thực tế cụ thể cần làm
- "transition_criteria": Tiêu chí/điều kiện để người học tự đánh giá đạt yêu cầu trước khi chuyển sang giai đoạn kế tiếp

==================================================
THÔNG TIN NGƯỜI HỌC HIỆN TẠI:
$profileSummary

ROADMAP HIỆN TẠI:
$roadmapSummary

NGỮ CẢNH TRANG HIỆN TẠI:
$currentLpContext

==================================================
QUY ĐỊNH ĐỊNH DẠNG ĐẦU RA (JSON FORMAT):
Bạn PHẢI trả về duy nhất một chuỗi JSON hợp lệ với cấu trúc sau:

Nếu ngoài phạm vi:
{
  "type": "out_of_scope",
  "message": "AI Lộ trình chỉ hỗ trợ tư vấn lộ trình học tập và lựa chọn khóa học trên FEA."
}

Nếu đổi ngành khác:
{
  "type": "topic_switch_confirmation",
  "new_topic": "Tên chủ đề mới",
  "message": "Bạn đang tìm hiểu lộ trình hiện tại. Bạn có muốn chuyển sang xây dựng lộ trình mới cho [Chủ đề mới] không?"
}

Nếu trả lời câu hỏi phụ / tư vấn ngắn:
{
  "type": "general_advice",
  "message": "Nội dung tư vấn chi tiết...",
  "extracted_profile": { ... }
}

Nếu tạo mới hoặc cập nhật Roadmap:
{
  "type": "learning_roadmap",
  "message": "Lời mở đầu định hướng tổng quan lộ trình...",
  "extracted_profile": {
    "desired_field": "Tên ngành",
    "goal": "Mục tiêu",
    "current_level": "Trình độ",
    "target_role": "Vị trí hướng tới",
    "target_duration": "Thời gian"
  },
  "roadmap": {
    "title": "Tên Lộ trình hoàn chỉnh",
    "topic": "Chủ đề chính",
    "goal": "Mục tiêu đạt được",
    "target_role": "Vị trí công việc",
    "estimated_duration": "Tổng thời gian dự kiến",
    "current_level": "Trình độ áp dụng",
    "overview": "Tổng quan triết lý và phương pháp học...",
    "stages": [
      {
        "stage": 1,
        "title": "Tên giai đoạn 1",
        "duration": "4 - 6 tuần",
        "objective": "Mục tiêu cụ thể giai đoạn 1...",
        "topics_to_learn": [
          "1. Kiến thức nền tảng A",
          "2. Khái niệm và nguyên lý B",
          "3. Công cụ và môi trường C",
          "4. Phương pháp luận D"
        ],
        "skills": ["Kỹ năng 1", "Kỹ năng 2", "Kỹ năng 3"],
        "practice": "Thực hiện dự án/bài tập cụ thể...",
        "transition_criteria": "Tự hoàn thành được sản phẩm X hoặc đạt điểm kiểm tra kiến thức Y."
      },
      {
        "stage": 2,
        "title": "Tên giai đoạn 2",
        "duration": "6 - 8 tuần",
        "objective": "Mục tiêu giai đoạn 2...",
        "topics_to_learn": [
          "1. Chuyên đề nâng cao A",
          "2. Kỹ thuật thực thi B",
          "3. Tối ưu hóa C"
        ],
        "skills": ["Kỹ năng 4", "Kỹ năng 5"],
        "practice": "Xây dựng hệ thống/chiến dịch thực tế...",
        "transition_criteria": "Triển khai được quy trình Z thành công."
      }
    ],
    "final_note": "Bạn nên hoàn thành tuần tự từng giai đoạn trên để đạt hiệu quả cao nhất."
  }
}
PROMPT;
    }

    /**
     * Gom system instruction, lịch sử tin nhắn và câu hỏi mới thành full prompt.
     */
    protected function buildFullPrompt(string $systemInstruction, Collection $history, string $userPrompt): string
    {
        $dialogue = [];
        foreach ($history as $msg) {
            $sender = $msg->role === 'user' ? 'Người học' : 'AI Lộ trình';
            $dialogue[] = "{$sender}: {$msg->content}";
        }

        $conversationHistoryText = empty($dialogue) ? 'Chưa có tin nhắn trước đó.' : implode("\n\n", $dialogue);

        return <<<FULLPROMPT
$systemInstruction

==================================================
LỊCH SỬ CUỘC HỘI THOẠI TRƯỚC ĐÓ:
$conversationHistoryText

==================================================
TIN NHẮN MỚI CỦA NGƯỜI HỌC:
$userPrompt

Hãy phân tích và trả về duy nhất chuỗi JSON hợp lệ theo đúng cấu trúc đã quy định:
FULLPROMPT;
    }

    /**
     * Parse JSON từ text trả về của Gemini.
     */
    protected function parseGeminiResponse(string $rawText): array
    {
        $clean = trim($rawText);
        if (str_starts_with($clean, '```json')) {
            $clean = substr($clean, 7);
        } elseif (str_starts_with($clean, '```')) {
            $clean = substr($clean, 3);
        }
        if (str_ends_with($clean, '```')) {
            $clean = substr($clean, 0, -3);
        }
        $clean = trim($clean);

        $data = json_decode($clean, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data;
        }

        // Fallback nếu không parse được JSON chuẩn
        return [
            'type' => 'general_advice',
            'message' => $rawText,
        ];
    }
}
