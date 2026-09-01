<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Discussion;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Services\ContentUpdateService;
use App\Services\LearningProgressService;
use App\Services\QuizContentService;
use App\Services\QuizVersioningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LearningProgressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        session()->start();
        $this->withHeader('X-CSRF-TOKEN', session()->token());
    }

    public function test_student_without_enrollment_cannot_update_progress(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'watched_seconds' => 100,
                'duration_seconds' => 300,
            ])
            ->assertForbidden();
    }

    public function test_enrolled_student_can_update_progress(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'watched_seconds' => 240,
                'duration_seconds' => 300,
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['lesson_progress', 'course_progress', 'lesson_completed', 'course_completed']);
    }

    public function test_video_progress_saves_last_position_without_completing_from_seek(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();
        $this->enroll($student, $course);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'last_position_seconds' => 295,
                'furthest_position_seconds' => 295,
                'played_seconds' => 0,
                'video_duration_seconds' => 300,
                'client_updated_at' => now()->toIso8601String(),
            ])
            ->assertOk()
            ->assertJsonPath('lesson_completed', false);

        $progress = LessonProgress::where('user_id', $student->id)->where('lesson_id', $lesson->id)->firstOrFail();

        $this->assertSame(295, $progress->last_position_seconds);
        $this->assertSame(0, $progress->watched_seconds);
        $this->assertFalse($progress->is_completed);
    }

    public function test_furthest_position_does_not_decrease(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();
        $this->enroll($student, $course);

        LessonProgress::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'watched_seconds' => 120,
            'duration_seconds' => 300,
            'last_position_seconds' => 120,
            'furthest_position_seconds' => 120,
            'progress_percent' => 40,
            'last_watched_at' => now()->subSeconds(20),
            'last_client_updated_at' => now()->subSeconds(20),
        ]);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'last_position_seconds' => 60,
                'furthest_position_seconds' => 60,
                'played_seconds' => 5,
                'video_duration_seconds' => 300,
                'client_updated_at' => now()->toIso8601String(),
            ])
            ->assertOk();

        $this->assertSame(120, LessonProgress::first()->furthest_position_seconds);
    }

    public function test_video_reaches_ninety_percent_after_played_time(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse(['required_video_percent' => 90]);
        $lesson = $course->lessons->first();
        $this->enroll($student, $course);

        LessonProgress::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'watched_seconds' => 260,
            'duration_seconds' => 300,
            'last_position_seconds' => 260,
            'furthest_position_seconds' => 260,
            'progress_percent' => 86.67,
            'last_watched_at' => now()->subSeconds(20),
            'last_client_updated_at' => now()->subSeconds(20),
        ]);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'last_position_seconds' => 270,
                'furthest_position_seconds' => 270,
                'played_seconds' => 10,
                'video_duration_seconds' => 300,
                'client_updated_at' => now()->toIso8601String(),
            ])
            ->assertOk()
            ->assertJsonPath('lesson_completed', true);

        $this->assertTrue(LessonProgress::first()->is_completed);
    }

    public function test_completed_lesson_is_not_reverted_by_later_progress(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();
        $this->enroll($student, $course);

        LessonProgress::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'watched_seconds' => 300,
            'duration_seconds' => 300,
            'last_position_seconds' => 300,
            'furthest_position_seconds' => 300,
            'progress_percent' => 100,
            'is_completed' => true,
            'completed_at' => now()->subMinute(),
            'last_watched_at' => now()->subMinute(),
            'last_client_updated_at' => now()->subMinute(),
        ]);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'last_position_seconds' => 10,
                'furthest_position_seconds' => 10,
                'played_seconds' => 0,
                'video_duration_seconds' => 300,
                'client_updated_at' => now()->toIso8601String(),
            ])
            ->assertOk()
            ->assertJsonPath('lesson_completed', true);
    }

    public function test_stale_request_does_not_overwrite_newer_progress(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();
        $this->enroll($student, $course);

        LessonProgress::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'watched_seconds' => 100,
            'duration_seconds' => 300,
            'last_position_seconds' => 100,
            'furthest_position_seconds' => 100,
            'progress_percent' => 33.33,
            'last_watched_at' => now(),
            'last_client_updated_at' => now(),
        ]);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'last_position_seconds' => 20,
                'furthest_position_seconds' => 20,
                'played_seconds' => 10,
                'video_duration_seconds' => 300,
                'client_updated_at' => now()->subMinute()->toIso8601String(),
            ])
            ->assertStatus(409);

        $this->assertSame(100, LessonProgress::first()->last_position_seconds);
    }

    public function test_document_lesson_can_be_manually_completed(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $course->courseSections->first()->id,
            'title' => 'Doc',
            'type' => 'document',
            'content' => 'Read me',
            'sort_order' => 2,
            'is_required' => true,
            'status' => 'published',
        ]);
        $this->enroll($student, $course);
        Cache::put('reading-start:'.$student->id.':'.$lesson->id, now()->subSeconds(30)->timestamp, now()->addMinute());

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'completed' => true,
            ])
            ->assertOk()
            ->assertJsonPath('lesson_completed', true);
    }

    public function test_failed_quiz_attempt_is_completed_but_lesson_remains_incomplete(): void
    {
        [$student, $course, $lesson, $quiz, , $wrongAnswers] = $this->publishedQuizLesson();
        $attempt = $this->startQuizAttempt($student, $course, $lesson, $quiz);

        $this->submitQuizAttempt($student, $course, $lesson, $attempt, $wrongAnswers)
            ->assertOk()
            ->assertJsonPath('attempt.passed', false)
            ->assertJsonPath('lesson_completed', false);

        $this->assertSame('completed', $attempt->fresh()->status);
        $this->assertFalse($attempt->fresh()->passed);
        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'is_completed' => false,
        ]);
    }

    public function test_passed_quiz_attempt_completes_lesson(): void
    {
        [$student, $course, $lesson, $quiz, $correctAnswers] = $this->publishedQuizLesson();
        $attempt = $this->startQuizAttempt($student, $course, $lesson, $quiz);

        $this->submitQuizAttempt($student, $course, $lesson, $attempt, $correctAnswers)
            ->assertOk()
            ->assertJsonPath('attempt.passed', true)
            ->assertJsonPath('lesson_completed', true);

        $this->assertSame('completed', $attempt->fresh()->status);
        $this->assertTrue($attempt->fresh()->passed);
        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'is_completed' => true,
        ]);
    }

    public function test_fail_then_pass_completes_quiz_lesson(): void
    {
        [$student, $course, $lesson, $quiz, $correctAnswers, $wrongAnswers] = $this->publishedQuizLesson();
        $failedAttempt = $this->startQuizAttempt($student, $course, $lesson, $quiz);
        $this->submitQuizAttempt($student, $course, $lesson, $failedAttempt, $wrongAnswers)
            ->assertJsonPath('lesson_completed', false);

        $passedAttempt = $this->startQuizAttempt($student, $course, $lesson, $quiz);
        $this->submitQuizAttempt($student, $course, $lesson, $passedAttempt, $correctAnswers)
            ->assertJsonPath('lesson_completed', true);

        $this->assertFalse($failedAttempt->fresh()->passed);
        $this->assertTrue($passedAttempt->fresh()->passed);
        $this->assertTrue(LessonProgress::where('user_id', $student->id)->where('lesson_id', $lesson->id)->sole()->is_completed);
    }

    public function test_multiple_failed_quiz_attempts_leave_lesson_incomplete(): void
    {
        [$student, $course, $lesson, $quiz, , $wrongAnswers] = $this->publishedQuizLesson();

        foreach (range(1, 2) as $index) {
            $attempt = $this->startQuizAttempt($student, $course, $lesson, $quiz);
            $this->submitQuizAttempt($student, $course, $lesson, $attempt, $wrongAnswers)
                ->assertJsonPath('lesson_completed', false);
        }

        $this->assertSame(2, QuizAttempt::where('user_id', $student->id)->where('quiz_id', $quiz->id)->where('status', 'completed')->count());
        $this->assertFalse(LessonProgress::where('user_id', $student->id)->where('lesson_id', $lesson->id)->sole()->is_completed);
    }

    public function test_passed_v1_attempt_keeps_lesson_complete_after_v2_activation(): void
    {
        [$student, $course, $lesson, $quiz, $correctAnswers] = $this->publishedQuizLesson();
        $versioning = app(QuizVersioningService::class);
        $v1 = $versioning->currentPublished($quiz);
        $attempt = $this->startQuizAttempt($student, $course, $lesson, $quiz);
        $this->submitQuizAttempt($student, $course, $lesson, $attempt, $correctAnswers)
            ->assertJsonPath('lesson_completed', true);

        $versioning->ensureDraft($quiz->fresh());
        $quiz = $quiz->fresh();
        $draft = $versioning->currentDraft($quiz);
        $update = $versioning->contentUpdateForVersion($quiz, $draft);
        $update->update(['status' => ContentUpdate::STATUS_PENDING, 'submitted_at' => now()]);
        app(ContentUpdateService::class)->applyApprovedUpdate($update->fresh(), User::factory()->create(['role' => 'admin']));
        $v2 = $versioning->currentPublished($quiz->fresh());

        $progress = app(LearningProgressService::class)->recordLessonProgress($student->id, $course, $lesson);

        $this->assertNotSame($v1->id, $v2->id);
        $this->assertSame($v1->id, $attempt->fresh()->quiz_version_id);
        $this->assertTrue($progress['lesson_completed']);
        $this->assertTrue(LessonProgress::where('user_id', $student->id)->where('lesson_id', $lesson->id)->sole()->is_completed);
    }

    public function test_course_stays_incomplete_after_quiz_failure_and_completes_after_pass(): void
    {
        [$student, $course, $lesson, $quiz, $correctAnswers, $wrongAnswers] = $this->publishedQuizLesson();
        $video = $course->fresh('lessons')->lessons->firstWhere('type', Lesson::TYPE_VIDEO);
        app(LearningProgressService::class)->recordLessonProgress($student->id, $course, $video, 300, 300, true);

        $failedAttempt = $this->startQuizAttempt($student, $course, $lesson, $quiz);
        $this->submitQuizAttempt($student, $course, $lesson, $failedAttempt, $wrongAnswers)
            ->assertJsonPath('lesson_completed', false);
        $this->assertSame(Enrollment::STATUS_ACTIVE, Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->sole()->status);

        $passedAttempt = $this->startQuizAttempt($student, $course, $lesson, $quiz);
        $this->submitQuizAttempt($student, $course, $lesson, $passedAttempt, $correctAnswers)
            ->assertJsonPath('lesson_completed', true);
        $this->assertSame(Enrollment::STATUS_COMPLETED, Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->sole()->status);
    }

    public function test_double_submit_failed_attempt_does_not_complete_lesson(): void
    {
        [$student, $course, $lesson, $quiz, , $wrongAnswers] = $this->publishedQuizLesson();
        $attempt = $this->startQuizAttempt($student, $course, $lesson, $quiz);
        $this->submitQuizAttempt($student, $course, $lesson, $attempt, $wrongAnswers)
            ->assertJsonPath('lesson_completed', false);

        $this->submitQuizAttempt($student, $course, $lesson, $attempt, $wrongAnswers)
            ->assertJsonPath('lesson_completed', false);

        $this->assertSame(1, LessonProgress::where('user_id', $student->id)->where('lesson_id', $lesson->id)->count());
        $this->assertFalse(LessonProgress::where('user_id', $student->id)->where('lesson_id', $lesson->id)->sole()->is_completed);
    }

    public function test_double_submit_passed_attempt_does_not_duplicate_lesson_progress(): void
    {
        [$student, $course, $lesson, $quiz, $correctAnswers] = $this->publishedQuizLesson();
        $attempt = $this->startQuizAttempt($student, $course, $lesson, $quiz);
        $this->submitQuizAttempt($student, $course, $lesson, $attempt, $correctAnswers)
            ->assertJsonPath('lesson_completed', true);
        $completedAt = LessonProgress::where('user_id', $student->id)->where('lesson_id', $lesson->id)->sole()->completed_at?->toIso8601String();

        $this->submitQuizAttempt($student, $course, $lesson, $attempt, $correctAnswers)
            ->assertJsonPath('lesson_completed', true);

        $progress = LessonProgress::where('user_id', $student->id)->where('lesson_id', $lesson->id)->sole();
        $this->assertSame(1, LessonProgress::where('user_id', $student->id)->where('lesson_id', $lesson->id)->count());
        $this->assertSame($completedAt, $progress->completed_at?->toIso8601String());
    }

    public function test_progress_does_not_exceed_one_hundred_percent(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $result = app(LearningProgressService::class)->recordLessonProgress(
            $student->id,
            $course,
            $lesson,
            9999,
            300,
            false,
        );

        $this->assertLessThanOrEqual(100, $result['lesson_progress']);
    }

    public function test_completed_enrollment_can_still_access_learning(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_COMPLETED,
            'progress_percent' => 100,
            'completed_at' => now(),
            'enrolled_at' => now()->subDays(7),
        ]);

        $this->actingAs($student)
            ->get(route('courses.lessons.show', [$course, $lesson]))
            ->assertOk();

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'watched_seconds' => 120,
                'duration_seconds' => 300,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_course_chat_reply_and_recall_return_json_without_redirecting(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();
        $this->enroll($student, $course);
        $discussion = Discussion::create([
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'user_id' => $student->id,
            'title' => 'Course chat',
            'content' => 'First message',
        ]);

        $replyResponse = $this->actingAs($student)->postJson(
            route('discussions.replies.store', $discussion),
            ['content' => 'AJAX reply', 'lesson_id' => $lesson->id]
        );

        $replyResponse->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('kind', 'reply')
            ->assertJsonPath('data.content', 'AJAX reply');

        $replyId = $replyResponse->json('data.id');
        $this->actingAs($student)
            ->postJson(route('discussions.replies.recall', $replyId))
            ->assertOk()
            ->assertJson(['success' => true, 'kind' => 'reply', 'id' => $replyId]);

        $this->actingAs($student)
            ->getJson(route('discussions.messages', $discussion))
            ->assertOk()
            ->assertJsonPath('data.1.id', $replyId)
            ->assertJsonPath('data.1.is_recalled', true);
    }

    public function test_course_chat_drawer_uses_ajax_handlers_instead_of_native_submission(): void
    {
        $view = file_get_contents(resource_path('views/components/learning/course-chat-drawer.blade.php'));
        $instructorView = file_get_contents(resource_path('views/instructor/discussions/show.blade.php'));
        $script = file_get_contents(resource_path('js/course-chat.js'));

        $this->assertStringContainsString('data-course-chat-send', $view);
        $this->assertStringContainsString('data-course-chat-recall', $view);
        $this->assertStringContainsString('data-course-chat-send', $instructorView);
        $this->assertStringContainsString('data-course-chat-recall', $instructorView);
        $this->assertStringContainsString("event.stopImmediatePropagation()", $script);
        $this->assertStringContainsString('window.Echo.private(`course-discussion.${discussionId}`)', $script);
        $this->assertStringContainsString('window.setInterval(() => syncCourseChat(root), 1500)', $script);
    }

    /** @return array{0: User, 1: Course, 2: Lesson, 3: Quiz, 4: array<int, int>, 5: array<int, int>} */
    private function publishedQuizLesson(): array
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse(['certificate_enabled' => false]);
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $course->courseSections->first()->id,
            'title' => 'Quiz',
            'type' => Lesson::TYPE_QUIZ,
            'content' => 'Quiz',
            'sort_order' => 2,
            'is_required' => true,
            'status' => Lesson::STATUS_PUBLISHED,
        ]);
        $content = app(QuizContentService::class);
        $quiz = $content->getOrCreateForLesson($lesson);
        $content->saveMetadata($lesson, [
            'title' => 'Quiz',
            'pass_score' => 100,
            'description' => null,
            'time_limit_minutes' => null,
            'max_attempts' => null,
        ], false);

        $correctAnswers = [];
        $wrongAnswers = [];
        foreach (range(1, 5) as $index) {
            $question = $content->createQuestion($quiz->fresh(), [
                'question_text' => 'Question '.$index,
                'question_type' => 'single',
                'score' => 1,
                'sort_order' => $index - 1,
            ], [
                ['option_text' => 'Correct '.$index, 'is_correct' => true, 'sort_order' => 0],
                ['option_text' => 'Wrong '.$index, 'is_correct' => false, 'sort_order' => 1],
                ['option_text' => 'Other '.$index, 'is_correct' => false, 'sort_order' => 2],
            ]);
            $correctAnswers[$question->id] = $question->options->firstWhere('is_correct', true)->id;
            $wrongAnswers[$question->id] = $question->options->firstWhere('is_correct', false)->id;
        }

        $quiz->update(['is_active' => true]);
        $versioning = app(QuizVersioningService::class);
        $versioning->publishDraft($quiz->fresh(), $versioning->currentDraft($quiz->fresh()));
        $this->enroll($student, $course);

        return [$student, $course->fresh(), $lesson->fresh(), $quiz->fresh(), $correctAnswers, $wrongAnswers];
    }

    private function startQuizAttempt(User $student, Course $course, Lesson $lesson, Quiz $quiz): QuizAttempt
    {
        $this->actingAs($student)
            ->postJson(route('courses.lessons.quiz.start', [$course, $lesson]))
            ->assertOk();

        return QuizAttempt::where('user_id', $student->id)->where('quiz_id', $quiz->id)->latest('id')->firstOrFail();
    }

    private function submitQuizAttempt(User $student, Course $course, Lesson $lesson, QuizAttempt $attempt, array $answers)
    {
        return $this->actingAs($student)->postJson(route('courses.lessons.quiz.submit', [$course, $lesson]), [
            'attempt_id' => $attempt->id,
            'answers' => $answers,
        ]);
    }

    private function publishedCourse(array $attributes = []): Course
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
        ]);
        $course = Course::create(array_merge([
            'instructor_id' => $instructor->id,
            'category_id' => Category::create(['name' => 'Test', 'slug' => 'test-'.uniqid()])->id,
            'title' => 'Published',
            'slug' => 'published-'.uniqid(),
            'short_description' => 'Short',
            'description' => 'Desc',
            'thumbnail' => 't.png',
            'price' => 0,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
        ], $attributes));

        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'S1', 'sort_order' => 1]);
        Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'L1',
            'type' => 'video',
            'video_url' => 'https://example.com/v.mp4',
            'duration_seconds' => 300,
            'content' => 'content',
            'sort_order' => 1,
            'is_required' => true,
            'status' => 'published',
        ]);

        return $course->fresh(['lessons', 'courseSections.lessons']);
    }

    private function enroll(User $student, Course $course): Enrollment
    {
        return Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);
    }
}
