<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestion;
use App\Models\User;
use App\Services\LearningPlayerService;
use App\Services\QuizAttemptPresentationService;
use App\Services\QuizAttemptResultService;
use App\Services\QuizAttemptService;
use App\Services\QuizContentService;
use App\Services\QuizVersioningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizAttemptPresentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_attempt_persists_a_complete_snapshot_and_both_player_projections_use_it(): void
    {
        [$student, $course, $lesson, , $version] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);
        $attempt = $attempt->fresh();
        $snapshot = $attempt->presentation_order;

        $this->assertNotNull($snapshot);
        $this->assertTrue(app(QuizAttemptPresentationService::class)->validateSnapshot($snapshot, $version));

        $expectedQuestionVersionIds = $version->questionMappings
            ->pluck('question_version_id')
            ->map(fn ($id): int => (int) $id)
            ->sort()
            ->values()
            ->all();
        $actualQuestionVersionIds = collect($snapshot['questions'])
            ->pluck('question_version_id')
            ->map(fn ($id): int => (int) $id)
            ->sort()
            ->values()
            ->all();
        $this->assertSame($expectedQuestionVersionIds, $actualQuestionVersionIds);

        foreach ($version->questionMappings as $mapping) {
            $snapshotQuestion = collect($snapshot['questions'])->firstWhere('question_version_id', $mapping->question_version_id);
            $expectedOptionIds = $mapping->questionVersion->options->pluck('id')->map(fn ($id): int => (int) $id)->sort()->values()->all();
            $actualOptionIds = collect($snapshotQuestion['option_ids'])->map(fn ($id): int => (int) $id)->sort()->values()->all();
            $this->assertSame($expectedOptionIds, $actualOptionIds);
        }

        $projected = app(QuizAttemptService::class)->projectQuiz($attempt);
        $projectedQuestionIds = $projected->questions->map(fn ($question): int => (int) $question->authoringVersion->id)->all();
        $projectedOptionIds = $projected->questions->map(fn ($question): array => $question->options->pluck('id')->map(fn ($id): int => (int) $id)->all())->all();
        $this->assertSame(
            collect($snapshot['questions'])->pluck('question_version_id')->map(fn ($id): int => (int) $id)->all(),
            $projectedQuestionIds,
        );
        $this->assertSame(
            collect($snapshot['questions'])->map(fn ($question): array => collect($question['option_ids'])->map(fn ($id): int => (int) $id)->all())->all(),
            $projectedOptionIds,
        );

        $player = app(LearningPlayerService::class)->buildPlayerContext($course->fresh(), $lesson->fresh(), $student, false);
        $contextQuestions = collect($player['quizContext']['questions']);
        $this->assertSame($projectedQuestionIds, $contextQuestions->pluck('id')->map(fn ($id): int => (int) $id)->all());
        $this->assertSame(
            $projectedOptionIds,
            $contextQuestions->map(fn (array $question): array => collect($question['options'])->pluck('id')->map(fn ($id): int => (int) $id)->all())->all(),
        );
    }

    public function test_submit_does_not_mutate_the_snapshot_and_grading_uses_option_ids_not_positions(): void
    {
        [$student, $course, $lesson, , $version] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);
        $before = $attempt->fresh()->presentation_order;

        $response = $this->actingAs($student)->postJson(
            route('courses.lessons.quiz.submit', [$course, $lesson]),
            ['attempt_id' => $attempt->id, 'answers' => $this->correctAnswers($version)],
        );

        $response->assertOk()->assertJsonPath('attempt.passed', true);
        $completed = $attempt->fresh();
        $this->assertSame($before, $completed->presentation_order);
        $this->assertSame($version->questionMappings->sum(fn ($mapping): int => (int) $mapping->questionVersion->points), (int) $completed->score);
    }

    public function test_each_new_attempt_generates_and_persists_its_own_snapshot(): void
    {
        [$student, $course, $lesson, , $version] = $this->publishedQuiz();
        $shuffleCalls = 0;
        $service = new QuizAttemptPresentationService(function (array $items) use (&$shuffleCalls): array {
            $shuffleCalls++;

            return $items;
        });
        $this->app->instance(QuizAttemptPresentationService::class, $service);

        $first = $this->startAttempt($student, $course, $lesson);
        $callsAfterFirst = $shuffleCalls;
        $first->update(['status' => 'completed', 'completed_at' => now()]);
        $second = $this->startAttempt($student, $course, $lesson);

        $this->assertGreaterThan(0, $callsAfterFirst);
        $this->assertGreaterThan($callsAfterFirst, $shuffleCalls);
        $this->assertNotSame($first->id, $second->id);
        $this->assertTrue($service->validateSnapshot($first->fresh()->presentation_order, $version));
        $this->assertTrue($service->validateSnapshot($second->fresh()->presentation_order, $version));
    }

    public function test_double_start_keeps_one_immutable_snapshot_and_legacy_or_invalid_snapshots_fall_back_safely(): void
    {
        [$student, $course, $lesson, , $version] = $this->publishedQuiz();
        $first = $this->startAttempt($student, $course, $lesson);
        $snapshot = $first->presentation_order;

        $this->startAttempt($student, $course, $lesson);
        $this->assertDatabaseCount('quiz_attempts', 1);
        $this->assertSame($snapshot, $first->fresh()->presentation_order);

        $first->update(['presentation_order' => null]);
        $legacyOrder = app(QuizAttemptPresentationService::class)->presentationOrder($first->fresh());
        $this->assertSame(
            $version->questionMappings->pluck('question_version_id')->map(fn ($id): int => (int) $id)->all(),
            collect($legacyOrder['questions'])->pluck('question_version_id')->all(),
        );

        $first->update(['presentation_order' => [
            'version' => 1,
            'questions' => [[
                'question_version_id' => 999999999,
                'option_ids' => [999999998],
            ]],
        ]]);
        $safeOrder = app(QuizAttemptPresentationService::class)->presentationOrder($first->fresh());
        $this->assertTrue(app(QuizAttemptPresentationService::class)->validateSnapshot($safeOrder, $version));
        $this->assertNotContains(999999999, collect($safeOrder['questions'])->pluck('question_version_id')->all());
        $this->assertNotContains(999999998, collect($safeOrder['questions'])->flatMap(fn ($question) => $question['option_ids'])->all());
    }

    public function test_historical_result_uses_the_stored_question_and_option_order(): void
    {
        [$student, $course, $lesson, , $version] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);
        $snapshot = $attempt->presentation_order;
        $this->actingAs($student)->postJson(route('courses.lessons.quiz.submit', [$course, $lesson]), [
            'attempt_id' => $attempt->id,
            'answers' => $this->correctAnswers($version),
        ])->assertOk();

        $result = app(QuizAttemptResultService::class)->forLearner($course, $lesson, $student, $attempt->fresh());
        $this->assertSame(
            collect($snapshot['questions'])->pluck('question_version_id')->all(),
            collect($result['questions'])->map(function (array $question) use ($version): int {
                return (int) $version->questionMappings
                    ->first(fn ($mapping): bool => $mapping->questionVersion->question === $question['question'])
                    ?->question_version_id;
            })->all(),
        );
        foreach ($result['questions'] as $index => $question) {
            $this->assertSame(
                $snapshot['questions'][$index]['option_ids'],
                collect($question['options'])->pluck('id')->map(fn ($id): int => (int) $id)->all(),
            );
        }
    }

    public function test_v1_attempt_keeps_its_snapshot_while_new_attempt_uses_v2_snapshot(): void
    {
        [$student, $course, $lesson, $quiz, $v1] = $this->publishedQuiz();
        $first = $this->startAttempt($student, $course, $lesson);
        $v1Snapshot = $first->presentation_order;
        $v2 = $this->publishedV2($quiz, $v1);

        $resumed = $this->startAttempt($student, $course, $lesson);
        $this->assertSame($first->id, $resumed->id);
        $this->assertSame($v1Snapshot, $resumed->fresh()->presentation_order);
        $this->assertSame($v1->id, $resumed->quiz_version_id);

        $first->update(['status' => 'completed', 'completed_at' => now()]);
        $second = $this->startAttempt($student, $course, $lesson);
        $this->assertSame($v2->id, $second->quiz_version_id);
        $this->assertTrue(app(QuizAttemptPresentationService::class)->validateSnapshot($second->presentation_order, $v2));
        $this->assertSame($v1Snapshot, $first->fresh()->presentation_order);
    }

    /** @return array{0: User, 1: Course, 2: Lesson, 3: Quiz, 4: QuizVersion} */
    private function publishedQuiz(): array
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);
        $category = Category::create(['name' => 'Presentation category '.uniqid(), 'slug' => 'presentation-'.uniqid(), 'status' => true]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Presentation course',
            'slug' => 'presentation-course-'.uniqid(),
            'description' => 'Description',
            'price' => 0,
            'language' => 'vi',
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
        ]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Section', 'sort_order' => 0]);
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz lesson',
            'type' => Lesson::TYPE_QUIZ,
            'sort_order' => 0,
            'status' => Lesson::STATUS_PUBLISHED,
        ]);
        $content = app(QuizContentService::class);
        $quiz = $content->getOrCreateForLesson($lesson);
        $content->saveMetadata($lesson, ['title' => 'Presentation V1', 'description' => null, 'pass_score' => 70, 'time_limit_minutes' => 10, 'max_attempts' => null], false);
        foreach (range(1, 5) as $index) {
            $content->createQuestion($quiz->fresh(), [
                'question_text' => 'Presentation question '.$index,
                'question_type' => 'single',
                'score' => 1,
                'sort_order' => $index - 1,
            ], [
                ['option_text' => 'Correct '.$index, 'is_correct' => true, 'sort_order' => 0],
                ['option_text' => 'Wrong '.$index, 'is_correct' => false, 'sort_order' => 1],
                ['option_text' => 'Other '.$index, 'is_correct' => false, 'sort_order' => 2],
            ]);
        }
        $quiz->update(['is_active' => true]);
        $versioning = app(QuizVersioningService::class);
        $versioning->publishDraft($quiz->fresh(), $versioning->currentDraft($quiz->fresh()));
        $student = User::factory()->create(['role' => 'student']);
        $this->enroll($student, $course);

        return [$student, $course->fresh(), $lesson->fresh(), $quiz->fresh(), $versioning->currentPublished($quiz->fresh())->fresh('questionMappings.questionVersion.options')];
    }

    private function publishedV2(Quiz $quiz, QuizVersion $v1): QuizVersion
    {
        $v1->load('questionMappings.questionVersion.options');
        $source = $v1->questionMappings->first()->questionVersion;
        $replacement = QuestionVersion::create([
            'question_id' => $source->question_id,
            'version' => 2,
            'question' => 'Presentation V2 question 1',
            'type' => $source->type,
            'points' => $source->points,
            'status' => QuestionVersion::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
        foreach ($source->options as $option) {
            QuizOption::create([
                'quiz_question_id' => $source->question_id,
                'question_version_id' => $replacement->id,
                'option_text' => 'Presentation V2 option '.$option->id,
                'is_correct' => $option->is_correct,
                'sort_order' => $option->sort_order,
            ]);
        }
        $v2 = QuizVersion::create([
            'quiz_id' => $quiz->id,
            'version' => 2,
            'title' => 'Presentation V2',
            'pass_score' => $v1->pass_score,
            'time_limit_minutes' => $v1->time_limit_minutes,
            'max_attempts' => $v1->max_attempts,
            'status' => QuizVersion::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
        foreach ($v1->questionMappings as $index => $mapping) {
            QuizVersionQuestion::create([
                'quiz_version_id' => $v2->id,
                'question_id' => $mapping->question_id,
                'question_version_id' => $index === 0 ? $replacement->id : $mapping->question_version_id,
                'sort_order' => $mapping->sort_order,
            ]);
        }
        $quiz->update(['current_published_version_id' => $v2->id]);

        return $v2->fresh('questionMappings.questionVersion.options');
    }

    private function startAttempt(User $student, Course $course, Lesson $lesson): QuizAttempt
    {
        $this->actingAs($student)
            ->postJson(route('courses.lessons.quiz.start', [$course, $lesson]))
            ->assertOk();

        return QuizAttempt::where('user_id', $student->id)->where('quiz_id', $lesson->quiz->id)->latest('id')->firstOrFail();
    }

    /** @return array<int, int> */
    private function correctAnswers(QuizVersion $version): array
    {
        $version->load('questionMappings.questionVersion.options');

        return $version->questionMappings->mapWithKeys(function (QuizVersionQuestion $mapping): array {
            return [$mapping->question_id => (int) $mapping->questionVersion->options->firstWhere('is_correct', true)->id];
        })->all();
    }

    private function enroll(User $student, Course $course): void
    {
        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);
    }
}
