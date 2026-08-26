<?php

namespace Tests\Feature;

use App\Models\AiChatMessage;
use App\Models\AiConversation;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonAiSummary;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Services\Ai\GeminiService;
use App\Services\Ai\LessonAiService;
use App\Services\Ai\LessonContextService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class LessonAiTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrolled_student_can_get_summary(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $this->mockGeminiJson([
            'summary' => 'Tóm tắt ngắn về Laravel MVC.',
            'key_points' => ['Routing', 'Controller'],
            'takeaways' => ['Hiểu vòng đời request'],
        ]);

        $this->actingAs($student)
            ->getJson(route('courses.lessons.ai-summary', [$course, $lesson]).'?generate=1')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('cached', false)
            ->assertJsonPath('summary', 'Tóm tắt ngắn về Laravel MVC.')
            ->assertJsonPath('key_points.0', 'Routing')
            ->assertJsonPath('takeaways.0', 'Hiểu vòng đời request')
            ->assertJsonStructure(['source_hash', 'has_source']);

        $this->assertDatabaseHas('lesson_ai_summaries', [
            'lesson_id' => $lesson->id,
            'summary' => 'Tóm tắt ngắn về Laravel MVC.',
        ]);
    }

    public function test_summary_is_cached_and_gemini_not_called_again(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();
        $hash = app(LessonContextService::class)->sourceHash($lesson);

        LessonAiSummary::query()->create([
            'lesson_id' => $lesson->id,
            'summary' => 'Bản đã lưu',
            'key_points' => [
                'main_points' => ['Ý 1'],
                'takeaways' => ['Nhớ 1'],
            ],
            'source_hash' => $hash,
            'model' => 'mock',
            'generated_at' => now(),
        ]);

        $gemini = Mockery::mock(GeminiService::class);
        $gemini->shouldNotReceive('generateText');
        $this->app->instance(GeminiService::class, $gemini);

        $this->actingAs($student)
            ->getJson(route('courses.lessons.ai-summary', [$course, $lesson]).'?generate=1')
            ->assertOk()
            ->assertJsonPath('cached', true)
            ->assertJsonPath('summary', 'Bản đã lưu');
    }

    public function test_unenrolled_student_receives_forbidden(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
        [$course, $lesson] = $this->publishedCourseWithLesson();

        $this->actingAs($student)
            ->getJson(route('courses.lessons.ai-summary', [$course, $lesson]))
            ->assertForbidden()
            ->assertJsonPath('code', 'forbidden');
    }

    public function test_lesson_not_belonging_to_course_is_rejected(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();
        [$otherCourse] = $this->publishedCourseWithLesson('Other content for mismatch test.');

        $this->actingAs($student)
            ->getJson(route('courses.lessons.ai-summary', [$otherCourse, $lesson]))
            ->assertNotFound()
            ->assertJsonPath('code', 'lesson_mismatch');
    }

    public function test_source_hash_changes_when_lesson_content_changes(): void
    {
        [, , $lesson] = $this->enrolledLessonSetup('Nội dung A đủ dài để tạo hash.');
        $service = app(LessonContextService::class);
        $before = $service->sourceHash($lesson);

        $lesson->update(['content' => 'Nội dung B đã thay đổi hoàn toàn và đủ dài.']);
        $after = $service->sourceHash($lesson->fresh());

        $this->assertNotSame($before, $after);
    }

    public function test_missing_source_content_returns_friendly_message(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup('short');
        $lesson->update(['content' => 'abc', 'title' => 'T']);

        $this->actingAs($student)
            ->getJson(route('courses.lessons.ai-summary', [$course, $lesson->fresh()]).'?generate=1')
            ->assertStatus(422)
            ->assertJsonPath('code', 'no_source');
    }

    public function test_gemini_exception_does_not_return_server_error(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $gemini = Mockery::mock(GeminiService::class);
        $gemini->shouldReceive('generateText')
            ->once()
            ->andReturn(['error' => 'Dịch vụ AI tạm thời không khả dụng.', 'code' => 'ai_error']);
        $this->app->instance(GeminiService::class, $gemini);

        $this->actingAs($student)
            ->getJson(route('courses.lessons.ai-summary', [$course, $lesson]).'?generate=1')
            ->assertStatus(503)
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 'ai_error');
    }

    public function test_quota_exceeded_returns_429_with_exact_code(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $gemini = Mockery::mock(GeminiService::class);
        $gemini->shouldReceive('generateText')
            ->once()
            ->andReturn([
                'error' => 'Gemini đã hết hạn mức (quota).',
                'code' => 'quota_exceeded',
            ]);
        $this->app->instance(GeminiService::class, $gemini);

        $this->actingAs($student)
            ->getJson(route('courses.lessons.ai-summary', [$course, $lesson]).'?generate=1')
            ->assertStatus(429)
            ->assertJsonPath('code', 'quota_exceeded')
            ->assertJsonPath('success', false);
    }

    public function test_invalid_model_returns_503_with_exact_code(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $gemini = Mockery::mock(GeminiService::class);
        $gemini->shouldReceive('generateText')
            ->once()
            ->andReturn([
                'error' => 'Model Gemini không tồn tại.',
                'code' => 'invalid_model',
            ]);
        $this->app->instance(GeminiService::class, $gemini);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-explain', [$course, $lesson]), [
                'question' => 'Giải thích phần này?',
            ])
            ->assertStatus(503)
            ->assertJsonPath('code', 'invalid_model');
    }

    public function test_content_blocked_returns_422_with_exact_code(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $gemini = Mockery::mock(GeminiService::class);
        $gemini->shouldReceive('generateText')
            ->once()
            ->andReturn([
                'error' => 'Nội dung bị chặn bởi bộ lọc an toàn.',
                'code' => 'content_blocked',
            ]);
        $this->app->instance(GeminiService::class, $gemini);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-explain', [$course, $lesson]), [
                'question' => 'Giải thích phần này?',
            ])
            ->assertStatus(422)
            ->assertJsonPath('code', 'content_blocked');
    }

    public function test_empty_or_too_long_question_is_validated(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-explain', [$course, $lesson]), ['question' => ''])
            ->assertStatus(422);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-explain', [$course, $lesson]), [
                'question' => str_repeat('a', 1001),
            ])
            ->assertStatus(422);
    }

    public function test_explain_endpoint_is_throttled(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();
        $this->mockGeminiText('Câu trả lời mẫu.');

        $lastStatus = 200;
        for ($i = 0; $i < 12; $i++) {
            $lastStatus = $this->actingAs($student)
                ->postJson(route('courses.lessons.ai-explain', [$course, $lesson]), [
                    'question' => "Câu hỏi số {$i}",
                ])
                ->status();

            if ($lastStatus === 429) {
                break;
            }
        }

        $this->assertSame(429, $lastStatus);
    }

    public function test_ai_context_excludes_quiz_answers(): void
    {
        [, , $lesson] = $this->enrolledLessonSetup('Nội dung bài video Laravel về routing.');
        $quiz = Quiz::query()->create([
            'lesson_id' => $lesson->id,
            'title' => 'Quiz 1',
            'pass_score' => 50,
        ]);
        $question = QuizQuestion::query()->create([
            'quiz_id' => $quiz->id,
            'question' => 'Câu hỏi bí mật?',
            'type' => 'single',
            'sort_order' => 1,
        ]);
        QuizOption::query()->create([
            'quiz_question_id' => $question->id,
            'option_text' => 'Đáp án đúng bí mật XYZ',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        $context = app(LessonAiService::class)->buildContext($lesson->fresh());

        $this->assertStringContainsString('Nội dung bài video Laravel về routing.', $context);
        $this->assertStringNotContainsString('Đáp án đúng bí mật XYZ', $context);
        $this->assertStringNotContainsString('Câu hỏi bí mật?', $context);
    }

    public function test_missing_api_key_returns_friendly_json(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $gemini = Mockery::mock(GeminiService::class);
        $gemini->shouldReceive('generateText')
            ->once()
            ->andReturn([
                'error' => 'Chưa cấu hình GEMINI_API_KEY trong .env',
                'code' => 'missing_api_key',
            ]);
        $this->app->instance(GeminiService::class, $gemini);

        $this->actingAs($student)
            ->getJson(route('courses.lessons.ai-summary', [$course, $lesson]).'?generate=1')
            ->assertStatus(503)
            ->assertJsonPath('code', 'missing_api_key');
    }

    public function test_explain_returns_frontend_json_shape(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();
        $this->mockGeminiText('Giải thích ngắn gọn.');

        $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-explain', [$course, $lesson]), [
                'question' => 'Ý chính là gì?',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('question', 'Ý chính là gì?')
            ->assertJsonPath('answer', 'Giải thích ngắn gọn.')
            ->assertJsonStructure(['message']);
    }

    public function test_unverified_student_cannot_use_ai(): void
    {
        $student = User::factory()->unverified()->create(['role' => 'student']);
        [$course, $lesson] = $this->publishedCourseWithLesson();

        Enrollment::query()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        $this->actingAs($student)
            ->getJson(route('courses.lessons.ai-summary', [$course, $lesson]))
            ->assertStatus(403);
    }

    public function test_guest_cannot_call_ai_chat(): void
    {
        [$course, $lesson] = $this->publishedCourseWithLesson();

        $this->postJson(route('courses.lessons.ai-chat', [$course, $lesson]), [
            'message' => 'MVC là gì?',
        ])->assertUnauthorized();
    }

    public function test_unenrolled_student_cannot_call_ai_chat(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
        [$course, $lesson] = $this->publishedCourseWithLesson();

        $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-chat', [$course, $lesson]), [
                'message' => 'MVC là gì?',
            ])
            ->assertForbidden()
            ->assertJsonPath('code', 'forbidden');
    }

    public function test_enrolled_student_can_chat_with_ai(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();
        $this->mockGeminiText('MVC là mô hình Model, View và Controller.');

        $response = $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-chat', [$course, $lesson]), [
                'message' => 'MVC là gì?',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'MVC là mô hình Model, View và Controller.')
            ->assertJsonStructure(['conversation_id', 'answer']);

        $conversationId = $response->json('conversation_id');

        $this->assertDatabaseHas('ai_conversations', [
            'id' => $conversationId,
            'user_id' => $student->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ]);
        $this->assertDatabaseHas('ai_chat_messages', [
            'conversation_id' => $conversationId,
            'role' => 'user',
            'content' => 'MVC là gì?',
        ]);
        $this->assertDatabaseHas('ai_chat_messages', [
            'conversation_id' => $conversationId,
            'role' => 'assistant',
            'content' => 'MVC là mô hình Model, View và Controller.',
        ]);
    }

    public function test_enrolled_student_can_load_saved_ai_chat_history(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $conversation = AiConversation::query()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ]);
        AiChatMessage::query()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'role' => 'user',
            'content' => 'MVC là gì?',
        ]);
        AiChatMessage::query()->create([
            'conversation_id' => $conversation->id,
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'role' => 'assistant',
            'content' => 'MVC gồm Model, View và Controller.',
        ]);

        $this->actingAs($student)
            ->getJson(route('courses.lessons.ai-chat.history', [$course, $lesson]))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('conversation_id', $conversation->id)
            ->assertJsonPath('messages.0.role', 'user')
            ->assertJsonPath('messages.0.content', 'MVC là gì?')
            ->assertJsonPath('messages.1.role', 'assistant')
            ->assertJsonPath('messages.1.content', 'MVC gồm Model, View và Controller.');
    }

    public function test_ai_chat_history_is_scoped_to_current_user_and_lesson(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();
        $otherStudent = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
        $lessonB = Lesson::query()->create([
            'course_id' => $course->id,
            'section_id' => $lesson->section_id,
            'title' => 'Lesson B',
            'type' => 'video',
            'video_url' => 'https://example.com/b.mp4',
            'duration_seconds' => 120,
            'content' => 'Nội dung bài B đủ dài để kiểm tra lịch sử riêng.',
            'sort_order' => 2,
            'is_required' => true,
            'status' => 'published',
        ]);

        $ownConversation = AiConversation::query()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ]);
        AiChatMessage::query()->create([
            'conversation_id' => $ownConversation->id,
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'role' => 'user',
            'content' => 'Tin nhắn của tôi.',
        ]);

        $otherConversation = AiConversation::query()->create([
            'user_id' => $otherStudent->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ]);
        AiChatMessage::query()->create([
            'conversation_id' => $otherConversation->id,
            'user_id' => $otherStudent->id,
            'lesson_id' => $lesson->id,
            'role' => 'user',
            'content' => 'Tin nhắn của người khác.',
        ]);

        $lessonBConversation = AiConversation::query()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'lesson_id' => $lessonB->id,
        ]);
        AiChatMessage::query()->create([
            'conversation_id' => $lessonBConversation->id,
            'user_id' => $student->id,
            'lesson_id' => $lessonB->id,
            'role' => 'user',
            'content' => 'Tin nhắn lesson B.',
        ]);

        $response = $this->actingAs($student)
            ->getJson(route('courses.lessons.ai-chat.history', [$course, $lesson]))
            ->assertOk()
            ->assertJsonPath('conversation_id', $ownConversation->id);

        $contents = collect($response->json('messages'))->pluck('content')->all();

        $this->assertContains('Tin nhắn của tôi.', $contents);
        $this->assertNotContains('Tin nhắn của người khác.', $contents);
        $this->assertNotContains('Tin nhắn lesson B.', $contents);
    }

    public function test_ai_chat_rejects_empty_or_too_long_message(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-chat', [$course, $lesson]), ['message' => '   '])
            ->assertStatus(422)
            ->assertJsonPath('code', 'validation');

        $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-chat', [$course, $lesson]), [
                'message' => str_repeat('a', 2001),
            ])
            ->assertStatus(422)
            ->assertJsonPath('code', 'validation');
    }

    public function test_student_cannot_use_another_users_ai_conversation(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();
        $otherStudent = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $otherConversation = AiConversation::query()->create([
            'user_id' => $otherStudent->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ]);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-chat', [$course, $lesson]), [
                'message' => 'Cho mình ví dụ.',
                'conversation_id' => $otherConversation->id,
            ])
            ->assertForbidden()
            ->assertJsonPath('code', 'forbidden');
    }

    public function test_ai_chat_conversation_cannot_be_reused_for_another_lesson(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();
        $lessonB = Lesson::query()->create([
            'course_id' => $course->id,
            'section_id' => $lesson->section_id,
            'title' => 'Lesson B',
            'type' => 'video',
            'video_url' => 'https://example.com/b.mp4',
            'duration_seconds' => 120,
            'content' => 'Nội dung bài B đủ dài để kiểm tra hội thoại riêng.',
            'sort_order' => 2,
            'is_required' => true,
            'status' => 'published',
        ]);

        $conversation = AiConversation::query()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
        ]);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-chat', [$course, $lessonB]), [
                'message' => 'Tiếp tục giải thích.',
                'conversation_id' => $conversation->id,
            ])
            ->assertNotFound()
            ->assertJsonPath('code', 'conversation_mismatch');
    }

    public function test_ai_chat_context_contains_course_lesson_and_saved_summary(): void
    {
        [, $course, $lesson] = $this->enrolledLessonSetup('Nội dung bài học Laravel về middleware đủ dài.');

        LessonAiSummary::query()->create([
            'lesson_id' => $lesson->id,
            'summary' => 'Middleware lọc request trước khi vào controller.',
            'key_points' => ['main_points' => ['Middleware']],
            'source_hash' => app(LessonContextService::class)->sourceHash($lesson),
            'model' => 'mock',
            'generated_at' => now(),
        ]);

        $context = app(LessonAiService::class)->buildContext($lesson->fresh(), $course, includeAiSummary: true);

        $this->assertStringContainsString('Khóa học: Course AI', $context);
        $this->assertStringContainsString('Mô tả khóa học:', $context);
        $this->assertStringContainsString('Tiêu đề bài học: Lesson AI', $context);
        $this->assertStringContainsString('Nội dung bài học Laravel về middleware', $context);
        $this->assertStringContainsString('Middleware lọc request trước khi vào controller.', $context);
    }

    public function test_ai_chat_allows_general_learning_question_without_lesson_source(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup('abc');
        $this->mockGeminiText('Java và PHP khác nhau ở hệ sinh thái, runtime và cách triển khai.');

        $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-chat', [$course, $lesson]), [
                'message' => 'Java khác PHP ở đâu?',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Java và PHP khác nhau ở hệ sinh thái, runtime và cách triển khai.');
    }

    public function test_ai_chat_passes_recent_conversation_history_to_provider(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $gemini = Mockery::mock(GeminiService::class);
        $gemini->shouldReceive('generateText')
            ->once()
            ->withArgs(fn (string $prompt, array $options): bool => str_contains($prompt, 'Học viên: Dependency Injection là gì?'))
            ->andReturn(['text' => 'DI là cách đưa phụ thuộc từ bên ngoài vào đối tượng.', 'model' => 'mock-gemini']);
        $gemini->shouldReceive('generateText')
            ->once()
            ->withArgs(fn (string $prompt, array $options): bool => str_contains($prompt, 'AI: DI là cách đưa phụ thuộc từ bên ngoài vào đối tượng.')
                && str_contains($prompt, 'Học viên: Giải thích dễ hơn.'))
            ->andReturn(['text' => 'Hiểu đơn giản, class không tự tạo thứ nó cần mà được truyền vào.', 'model' => 'mock-gemini']);
        $this->app->instance(GeminiService::class, $gemini);

        $first = $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-chat', [$course, $lesson]), [
                'message' => 'Dependency Injection là gì?',
            ])
            ->assertOk();

        $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-chat', [$course, $lesson]), [
                'message' => 'Giải thích dễ hơn.',
                'conversation_id' => $first->json('conversation_id'),
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Hiểu đơn giản, class không tự tạo thứ nó cần mà được truyền vào.');
    }

    public function test_ai_chat_timeout_returns_friendly_error(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $gemini = Mockery::mock(GeminiService::class);
        $gemini->shouldReceive('generateText')
            ->once()
            ->andReturn(['error' => 'provider timeout detail', 'code' => 'timeout']);
        $this->app->instance(GeminiService::class, $gemini);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-chat', [$course, $lesson]), [
                'message' => 'Tóm tắt bài.',
            ])
            ->assertStatus(503)
            ->assertJsonPath('code', 'timeout')
            ->assertJsonPath('message', 'AI đang phản hồi chậm. Vui lòng thử lại.');
    }

    public function test_ai_chat_provider_error_does_not_return_unexpected_500(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $gemini = Mockery::mock(GeminiService::class);
        $gemini->shouldReceive('generateText')
            ->once()
            ->andReturn(['error' => 'Provider unavailable', 'code' => 'ai_error']);
        $this->app->instance(GeminiService::class, $gemini);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-chat', [$course, $lesson]), [
                'message' => 'Cho mình ví dụ.',
            ])
            ->assertStatus(503)
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 'ai_error');
    }

    public function test_ai_chat_response_does_not_leak_provider_secret(): void
    {
        [$student, $course, $lesson] = $this->enrolledLessonSetup();

        $gemini = Mockery::mock(GeminiService::class);
        $gemini->shouldReceive('generateText')
            ->once()
            ->andReturn([
                'error' => 'Invalid key sk-test-secret-value',
                'code' => 'invalid_api_key',
            ]);
        $this->app->instance(GeminiService::class, $gemini);

        $response = $this->actingAs($student)
            ->postJson(route('courses.lessons.ai-chat', [$course, $lesson]), [
                'message' => 'AI có hoạt động không?',
            ])
            ->assertStatus(503)
            ->assertJsonPath('code', 'invalid_api_key');

        $this->assertStringNotContainsString('sk-test-secret-value', $response->getContent());
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function mockGeminiJson(array $payload): void
    {
        $gemini = Mockery::mock(GeminiService::class);
        $gemini->shouldReceive('generateText')
            ->andReturn([
                'text' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'model' => 'mock-gemini',
            ]);
        $this->app->instance(GeminiService::class, $gemini);
    }

    private function mockGeminiText(string $text): void
    {
        $gemini = Mockery::mock(GeminiService::class);
        $gemini->shouldReceive('generateText')
            ->andReturn(['text' => $text, 'model' => 'mock-gemini']);
        $this->app->instance(GeminiService::class, $gemini);
    }

    /**
     * @return array{0: User, 1: Course, 2: Lesson}
     */
    private function enrolledLessonSetup(string $content = 'Nội dung bài học Laravel căn bản về routing và MVC đủ dài.'): array
    {
        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
        [$course, $lesson] = $this->publishedCourseWithLesson($content);

        Enrollment::query()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        return [$student, $course, $lesson];
    }

    /**
     * @return array{0: Course, 1: Lesson}
     */
    private function publishedCourseWithLesson(string $content = 'Nội dung bài học mẫu đủ dài để tóm tắt AI.'): array
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $course = Course::query()->create([
            'instructor_id' => $instructor->id,
            'category_id' => Category::query()->create(['name' => 'AI', 'slug' => 'ai-'.uniqid()])->id,
            'title' => 'Course AI',
            'slug' => 'course-ai-'.uniqid(),
            'short_description' => 'Short',
            'description' => 'Desc',
            'thumbnail' => 't.png',
            'price' => 0,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
        ]);

        $section = CourseSection::query()->create([
            'course_id' => $course->id,
            'title' => 'Section 1',
            'sort_order' => 1,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Lesson AI',
            'type' => 'video',
            'video_url' => 'https://example.com/v.mp4',
            'duration_seconds' => 300,
            'content' => $content,
            'sort_order' => 1,
            'is_required' => true,
            'status' => 'published',
        ]);

        return [$course, $lesson];
    }
}
