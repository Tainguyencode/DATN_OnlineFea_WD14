<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptRegrade;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestion;
use App\Models\QuizVersionQuestionInvalidation;
use App\Models\User;
use App\Models\UserPoint;
use App\Services\QuizAttemptRegradeService;
use App\Services\QuizAttemptResultService;
use App\Services\QuizAttemptService;
use App\Services\QuizQuestionInvalidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class QuizQuestionInvalidationRegradeTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalidating_an_incorrect_answer_removes_it_from_the_denominator(): void
    {
        $fixture = $this->fixture();
        $attempt = $this->completedAttempt($fixture, 6);
        $invalidation = $this->activeInvalidation($fixture, 6);

        app(QuizAttemptRegradeService::class)->processInvalidation($invalidation);

        $attempt->refresh();
        $audit = QuizAttemptRegrade::query()->where('quiz_attempt_id', $attempt->id)->sole();
        $this->assertSame(6, $attempt->score);
        $this->assertSame(9, $attempt->total_score);
        $this->assertSame(66.67, (float) $attempt->percent);
        $this->assertFalse($attempt->passed);
        $this->assertSame(6, $audit->original_score);
        $this->assertSame(6, $audit->recalculated_score);
        $this->assertSame(6, $audit->effective_score);
        $this->assertSame(9, $audit->effective_total_score);
        $this->assertSame(9, app(QuizAttemptRegradeService::class)->calculate($attempt)['total_score']);
    }

    public function test_invalidating_an_incorrect_answer_can_improve_a_passing_result(): void
    {
        $fixture = $this->fixture();
        $attempt = $this->completedAttempt($fixture, 7);
        $invalidation = $this->activeInvalidation($fixture, 7);

        app(QuizAttemptRegradeService::class)->processInvalidation($invalidation);

        $attempt->refresh();
        $this->assertSame(7, $attempt->score);
        $this->assertSame(9, $attempt->total_score);
        $this->assertSame(77.78, (float) $attempt->percent);
        $this->assertTrue($attempt->passed);
    }

    public function test_invalidating_a_correct_answer_preserves_the_previous_passing_result(): void
    {
        $fixture = $this->fixture();
        $attempt = $this->completedAttempt($fixture, 7);
        $invalidation = $this->activeInvalidation($fixture, 0);

        app(QuizAttemptRegradeService::class)->processInvalidation($invalidation);

        $attempt->refresh();
        $audit = QuizAttemptRegrade::query()->where('quiz_attempt_id', $attempt->id)->sole();
        $this->assertSame(6, $audit->recalculated_score);
        $this->assertSame(9, $audit->recalculated_total_score);
        $this->assertFalse($audit->recalculated_passed);
        $this->assertSame(7, $audit->effective_score);
        $this->assertSame(10, $audit->effective_total_score);
        $this->assertSame(70.0, (float) $audit->effective_percent);
        $this->assertTrue($audit->effective_passed);
        $this->assertTrue($attempt->passed);
        $this->assertSame(70.0, (float) $attempt->percent);

        $result = app(QuizAttemptResultService::class)->forLearner(
            $fixture['course'],
            $fixture['lesson'],
            $fixture['student'],
            $attempt,
        );
        $this->assertTrue(collect($result['questions'])->contains('is_excluded', true));
        $this->assertSame($attempt->id, $result['regrade']->quiz_attempt_id);

        $this->actingAs($fixture['student'])
            ->get(route('learn.lessons.quiz.result', [$fixture['course'], $fixture['lesson'], $attempt]))
            ->assertOk()
            ->assertSee('Câu hỏi đã bị hủy')
            ->assertSee('Kết quả cũ được giữ nguyên để không ảnh hưởng người học');
    }

    public function test_regrade_is_scoped_to_the_exact_version_mapping_and_keeps_answers_and_snapshot(): void
    {
        $fixture = $this->fixture();
        $attempt = $this->completedAttempt($fixture, 6);
        $answerCount = $attempt->attemptAnswers()->count();
        $snapshot = $attempt->presentation_order;
        $invalidation = $this->activeInvalidation($fixture, 6);

        app(QuizAttemptRegradeService::class)->processInvalidation($invalidation);

        $attempt->refresh();
        $this->assertSame($answerCount, $attempt->attemptAnswers()->count());
        $this->assertSame($snapshot, $attempt->presentation_order);
        $this->assertSame('completed', $attempt->status);
        $this->assertNotNull($attempt->completed_at);
        $this->assertSame($fixture['version']->id, $attempt->quiz_version_id);
        $this->assertSame($fixture['mappings'][6]->question_version_id, $attempt->attemptAnswers()->where('question_id', $fixture['mappings'][6]->question_id)->value('question_version_id'));
    }

    public function test_an_invalidation_does_not_touch_an_attempt_on_another_quiz_version(): void
    {
        $fixture = $this->fixture();
        $v2 = QuizVersion::create([
            'quiz_id' => $fixture['quiz']->id,
            'version' => 2,
            'title' => 'Invalidation quiz V2',
            'pass_score' => $fixture['version']->pass_score,
            'time_limit_minutes' => 10,
            'status' => QuizVersion::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
        $v2Mappings = [];
        foreach ($fixture['mappings'] as $index => $mapping) {
            $v2Mappings[$index] = QuizVersionQuestion::create([
                'quiz_version_id' => $v2->id,
                'question_id' => $mapping->question_id,
                'question_version_id' => $mapping->question_version_id,
                'sort_order' => $mapping->sort_order,
            ]);
        }

        $v1Attempt = $this->completedAttempt($fixture, 6);
        $v2Attempt = $this->completedAttempt($fixture, 6, $v2, $v2Mappings);
        $invalidation = $this->activeInvalidation($fixture, 6);

        app(QuizAttemptRegradeService::class)->processInvalidation($invalidation);

        $this->assertSame(9, $v1Attempt->fresh()->total_score);
        $this->assertSame(10, $v2Attempt->fresh()->total_score);
        $this->assertSame(0, QuizAttemptRegrade::where('quiz_attempt_id', $v2Attempt->id)->count());
    }

    public function test_multiple_approved_invalidations_are_deterministic_and_audited_once_each(): void
    {
        $fixture = $this->fixture();
        $attempt = $this->completedAttempt($fixture, 7);
        $first = $this->activeInvalidation($fixture, 7);
        app(QuizAttemptRegradeService::class)->processInvalidation($first);

        $second = $this->activeInvalidation($fixture, 8);
        app(QuizAttemptRegradeService::class)->processInvalidation($second);

        $attempt->refresh();
        $this->assertSame(2, QuizAttemptRegrade::where('quiz_attempt_id', $attempt->id)->count());
        $this->assertSame(7, $attempt->score);
        $this->assertSame(8, $attempt->total_score);
        $this->assertSame(87.5, (float) $attempt->percent);
        $this->assertTrue($attempt->passed);
    }

    public function test_in_progress_submit_keeps_the_snapshot_and_marks_the_invalidated_question(): void
    {
        $fixture = $this->fixture();
        $invalidation = $this->activeInvalidation($fixture, 9);
        $attempt = QuizAttempt::create([
            'user_id' => $fixture['student']->id,
            'quiz_id' => $fixture['quiz']->id,
            'quiz_version_id' => $fixture['version']->id,
            'status' => 'in_progress',
            'presentation_order' => ['version' => 1, 'question_ids' => $fixture['version']->questionMappings()->pluck('question_id')->all()],
            'started_at' => now(),
        ]);
        $snapshot = $attempt->presentation_order;
        $answers = [$fixture['mappings'][9]->question_id => $fixture['options'][9]['correct']->id];

        $submission = app(QuizAttemptService::class)->submit(
            $fixture['course'],
            $fixture['lesson'],
            $fixture['student'],
            $attempt->id,
            $answers,
        );

        $this->assertTrue($submission['graded']['questions'][$fixture['mappings'][9]->question_id]['is_excluded']);
        $this->assertSame(9, $submission['graded']['total_score']);
        $this->assertSame($snapshot, $submission['attempt']->presentation_order);
        $this->assertCount(1, $submission['attempt']->attemptAnswers()->where('question_id', $fixture['mappings'][9]->question_id)->get());
        $this->assertSame($invalidation->id, QuizVersionQuestionInvalidation::active()->whereKey($invalidation->id)->value('id'));
    }

    public function test_request_requires_the_course_owner_and_admin_approval(): void
    {
        $fixture = $this->fixture();
        $service = app(QuizQuestionInvalidationService::class);
        $otherInstructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);

        $this->expectException(HttpException::class);
        $service->request($fixture['mappings'][0], $otherInstructor, 'Invalid mapping reason');
    }

    public function test_admin_approval_starts_regrade_and_duplicate_requests_are_rejected(): void
    {
        $fixture = $this->fixture();
        $service = app(QuizQuestionInvalidationService::class);
        $pending = $service->request($fixture['mappings'][6], $fixture['instructor'], 'Correctness issue reported');

        $this->assertSame(QuizVersionQuestionInvalidation::STATUS_PENDING, $pending->status);

        $approved = $service->approve($pending, $fixture['admin']);
        $this->assertFalse($approved['queued']);
        $this->assertSame(QuizVersionQuestionInvalidation::STATUS_ACTIVE, $approved['invalidation']->status);

        $this->expectException(ValidationException::class);
        $service->request($fixture['mappings'][6], $fixture['instructor'], 'Second request');
    }

    public function test_regrade_reconciles_positive_quiz_progress_without_replaying_quiz_xp(): void
    {
        $fixture = $this->fixture(passScore: 75);
        $attempt = $this->completedAttempt($fixture, 7);
        $invalidation = $this->activeInvalidation($fixture, 7);

        app(QuizQuestionInvalidationService::class)->processApproved($invalidation);

        $progress = LessonProgress::query()
            ->where('user_id', $fixture['student']->id)
            ->where('lesson_id', $fixture['lesson']->id)
            ->first();
        $this->assertTrue((bool) $progress?->is_completed);
        $this->assertSame(100.0, (float) $progress?->progress_percent);
        $this->assertSame(0, UserPoint::query()->where('user_id', $fixture['student']->id)->where('source', 'quiz_completed')->count());
        $this->assertTrue($attempt->fresh()->passed);
    }

    /** @return array<string, mixed> */
    private function fixture(int $passScore = 70): array
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $category = Category::create(['name' => 'Invalidation category '.uniqid(), 'slug' => 'invalidation-'.uniqid(), 'status' => true]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Invalidation course '.uniqid(),
            'slug' => 'invalidation-course-'.uniqid(),
            'description' => 'Course for invalid question tests',
            'price' => 0,
            'language' => 'vi',
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
            'certificate_enabled' => false,
        ]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Section', 'sort_order' => 0]);
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Invalidation quiz',
            'type' => Lesson::TYPE_QUIZ,
            'sort_order' => 1,
            'status' => Lesson::STATUS_PUBLISHED,
            'is_required' => true,
        ]);
        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => 'Invalidation quiz',
            'description' => null,
            'pass_score' => $passScore,
            'time_limit_minutes' => 10,
            'is_active' => true,
        ]);
        $version = QuizVersion::create([
            'quiz_id' => $quiz->id,
            'version' => 1,
            'title' => 'Invalidation quiz V1',
            'description' => null,
            'pass_score' => $passScore,
            'time_limit_minutes' => 10,
            'status' => QuizVersion::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
        $quiz->update(['current_published_version_id' => $version->id]);

        $mappings = [];
        $options = [];
        foreach (range(0, 9) as $index) {
            $question = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => 'Question '.$index,
                'type' => QuizQuestion::TYPE_SINGLE,
                'points' => 1,
                'sort_order' => $index,
            ]);
            $questionVersion = QuestionVersion::create([
                'question_id' => $question->id,
                'version' => 1,
                'question' => 'Question '.$index.' V1',
                'type' => QuizQuestion::TYPE_SINGLE,
                'points' => 1,
                'status' => QuestionVersion::STATUS_PUBLISHED,
                'published_at' => now(),
            ]);
            $correct = QuizOption::create([
                'quiz_question_id' => $question->id,
                'question_version_id' => $questionVersion->id,
                'option_text' => 'Correct',
                'is_correct' => true,
                'sort_order' => 0,
            ]);
            $wrong = QuizOption::create([
                'quiz_question_id' => $question->id,
                'question_version_id' => $questionVersion->id,
                'option_text' => 'Wrong',
                'is_correct' => false,
                'sort_order' => 1,
            ]);
            $mappings[$index] = QuizVersionQuestion::create([
                'quiz_version_id' => $version->id,
                'question_id' => $question->id,
                'question_version_id' => $questionVersion->id,
                'sort_order' => $index,
            ]);
            $options[$index] = compact('correct', 'wrong');
        }

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        return compact('instructor', 'admin', 'student', 'course', 'lesson', 'quiz', 'version', 'mappings', 'options');
    }

    private function completedAttempt(array $fixture, int $correctCount, ?QuizVersion $version = null, ?array $mappings = null): QuizAttempt
    {
        $version ??= $fixture['version'];
        $mappings ??= $fixture['mappings'];
        $answers = [];
        $attempt = QuizAttempt::create([
            'user_id' => $fixture['student']->id,
            'quiz_id' => $fixture['quiz']->id,
            'quiz_version_id' => $version->id,
            'status' => 'completed',
            'score' => $correctCount,
            'total_score' => 10,
            'percent' => $correctCount * 10,
            'passed' => $correctCount * 10 >= $version->pass_score,
            'answers' => [],
            'presentation_order' => ['version' => 1, 'question_ids' => $version->questionMappings()->pluck('question_id')->all()],
            'started_at' => now()->subMinute(),
            'completed_at' => now(),
        ]);

        foreach ($mappings as $index => $mapping) {
            $isCorrect = $index < $correctCount;
            $answerId = $isCorrect ? $fixture['options'][$index]['correct']->id : $fixture['options'][$index]['wrong']->id;
            $answers[$mapping->question_id] = [$answerId];
            $attempt->attemptAnswers()->create([
                'question_id' => $mapping->question_id,
                'question_version_id' => $mapping->question_version_id,
                'answer_id' => $answerId,
                'is_correct' => $isCorrect,
            ]);
        }

        $attempt->update(['answers' => $answers]);

        return $attempt->fresh();
    }

    private function activeInvalidation(array $fixture, int $mappingIndex): QuizVersionQuestionInvalidation
    {
        return QuizVersionQuestionInvalidation::create([
            'quiz_version_question_id' => $fixture['mappings'][$mappingIndex]->id,
            'status' => QuizVersionQuestionInvalidation::STATUS_ACTIVE,
            'requested_by' => $fixture['instructor']->id,
            'invalidated_by' => $fixture['admin']->id,
            'reviewed_by' => $fixture['admin']->id,
            'invalidated_at' => now(),
            'reviewed_at' => now(),
            'reason' => 'Confirmed invalid question',
        ]);
    }
}
