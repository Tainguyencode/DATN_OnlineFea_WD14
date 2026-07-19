<?php

namespace Tests\Feature;

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
