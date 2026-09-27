<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\InstructorProfile;
use App\Models\InstructorTeachingField;
use App\Models\Lesson;
use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestion;
use App\Models\User;
use App\Services\QuizContentService;
use App\Services\QuizVersioningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizVersionAwareGradingTest extends TestCase
{
    use RefreshDatabase;

    public function test_v1_attempt_grades_v1_and_persists_v1_history_after_current_pointer_moves_to_v2(): void
    {
        [$student, $course, $lesson, $quiz, $v1] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);
        $v2 = $this->switchToV2($quiz, $v1, changeCorrectAnswer: true, firstQuestionPoints: 1, passScore: 100);

        $this->submit($student, $course, $lesson, $attempt, $this->correctAnswers($v1))
            ->assertOk()
            ->assertJsonPath('attempt.quiz_version_id', $v1->id)
            ->assertJsonPath('attempt.score', 14)
            ->assertJsonPath('attempt.total_score', 14)
            ->assertJsonPath('attempt.passed', true);

        $attempt = $attempt->fresh();
        $this->assertSame($v1->id, $attempt->quiz_version_id);
        $this->assertSame('completed', $attempt->status);
        $this->assertSame(14, $attempt->score);
        $this->assertSame(14, $attempt->total_score);
        $this->assertSame(100.0, (float) $attempt->percent);
        $this->assertTrue($attempt->passed);

        $v1->load('questionMappings.questionVersion.options');
        $v2->load('questionMappings.questionVersion.options');
        $v1First = $v1->questionMappings->first()->questionVersion;
        $v2First = $v2->questionMappings->first()->questionVersion;
        $this->assertNotSame($v1First->id, $v2First->id);
        $this->assertNotSame(
            $v1First->options->firstWhere('is_correct', true)->id,
            $v2First->options->firstWhere('is_correct', true)->id,
        );

        foreach ($v1->questionMappings as $mapping) {
            $answer = QuizAttemptAnswer::query()
                ->where('quiz_attempt_id', $attempt->id)
                ->where('question_id', $mapping->question_id)
                ->sole();

            $this->assertSame($mapping->question_version_id, $answer->question_version_id);
            $this->assertContains($answer->answer_id, $mapping->questionVersion->options->pluck('id')->all());
        }
    }

    public function test_v1_attempt_uses_v1_correct_answer_when_v2_changes_the_correct_option(): void
    {
        [$student, $course, $lesson, $quiz, $v1] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);
        $v2 = $this->switchToV2($quiz, $v1, changeCorrectAnswer: true);
        $v1->load('questionMappings.questionVersion.options');
        $v2->load('questionMappings.questionVersion.options');
        $v1First = $v1->questionMappings->first();
        $v2First = $v2->questionMappings->first();
        $v1CorrectId = $v1First->questionVersion->options->firstWhere('is_correct', true)->id;

        $this->submit($student, $course, $lesson, $attempt, [$v1First->question_id => $v1CorrectId])
            ->assertOk()
            ->assertJsonPath('graded.questions.0.is_correct', true);

        $answer = QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)->where('question_id', $v1First->question_id)->sole();
        $this->assertTrue($answer->is_correct);
        $this->assertSame($v1First->question_version_id, $answer->question_version_id);
        $this->assertSame($v1CorrectId, $answer->answer_id);
        $this->assertNotContains($v1CorrectId, $v2First->questionVersion->options->where('is_correct', true)->pluck('id')->all());
    }

    public function test_v1_attempt_uses_v1_points_and_pass_score_when_v2_changes_them(): void
    {
        [$student, $course, $lesson, $quiz, $v1] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);
        $this->switchToV2($quiz, $v1, changeCorrectAnswer: false, firstQuestionPoints: 1, passScore: 100);
        $v1->load('questionMappings.questionVersion.options');
        $first = $v1->questionMappings->first();
        $correctId = $first->questionVersion->options->firstWhere('is_correct', true)->id;

        $this->submit($student, $course, $lesson, $attempt, [$first->question_id => $correctId])
            ->assertOk()
            ->assertJsonPath('attempt.score', 10)
            ->assertJsonPath('attempt.total_score', 14)
            ->assertJsonPath('attempt.percent', 71.43)
            ->assertJsonPath('attempt.passed', true)
            ->assertJsonPath('attempt.pass_score', 70);

        $attempt = $attempt->fresh();
        $this->assertSame(10, $attempt->score);
        $this->assertSame(14, $attempt->total_score);
        $this->assertSame(71.43, (float) $attempt->percent);
        $this->assertTrue($attempt->passed);
    }

    public function test_v2_option_injected_into_v1_attempt_is_ignored_and_saved_as_unanswered(): void
    {
        [$student, $course, $lesson, $quiz, $v1] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);
        $v2 = $this->switchToV2($quiz, $v1, changeCorrectAnswer: true);
        $v1->load('questionMappings.questionVersion.options');
        $v2->load('questionMappings.questionVersion.options');
        $v1First = $v1->questionMappings->first();
        $v2First = $v2->questionMappings->first();
        $v2OnlyOption = $v2First->questionVersion->options->firstWhere('is_correct', true);

        $this->submit($student, $course, $lesson, $attempt, [$v1First->question_id => $v2OnlyOption->id])->assertOk();

        $answer = QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)->where('question_id', $v1First->question_id)->sole();
        $this->assertSame($v1First->question_version_id, $answer->question_version_id);
        $this->assertNull($answer->answer_id);
        $this->assertFalse($answer->is_correct);
    }

    public function test_cross_question_option_injection_is_ignored_and_saved_as_unanswered(): void
    {
        [$student, $course, $lesson, , $v1] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);
        $v1->load('questionMappings.questionVersion.options');
        $first = $v1->questionMappings->first();
        $second = $v1->questionMappings->get(1);
        $foreignOptionId = $second->questionVersion->options->firstWhere('is_correct', true)->id;

        $this->submit($student, $course, $lesson, $attempt, [$first->question_id => $foreignOptionId])->assertOk();

        $answer = QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)->where('question_id', $first->question_id)->sole();
        $this->assertSame($first->question_version_id, $answer->question_version_id);
        $this->assertNull($answer->answer_id);
        $this->assertFalse($answer->is_correct);
    }

    public function test_multiple_choice_requires_the_exact_correct_set_and_stores_one_row_per_selected_option(): void
    {
        [$student, $course, $lesson, , $v1] = $this->publishedQuiz(QuizQuestion::TYPE_MULTIPLE);
        $attempt = $this->startAttempt($student, $course, $lesson);
        $v1->load('questionMappings.questionVersion.options');
        $first = $v1->questionMappings->first();
        $correctIds = $first->questionVersion->options->where('is_correct', true)->pluck('id')->all();

        $this->submit($student, $course, $lesson, $attempt, [$first->question_id => array_reverse($correctIds)])
            ->assertOk()
            ->assertJsonPath('graded.questions.0.is_correct', true);

        $answers = QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)->where('question_id', $first->question_id)->get();
        $this->assertCount(2, $answers);
        $this->assertSame($correctIds, $answers->pluck('answer_id')->sort()->values()->all());
        $this->assertTrue($answers->every(fn (QuizAttemptAnswer $answer): bool => $answer->is_correct));
    }

    public function test_true_false_grading_uses_the_bound_question_version(): void
    {
        [$student, $course, $lesson, , $v1] = $this->publishedQuiz(QuizQuestion::TYPE_TRUE_FALSE);
        $attempt = $this->startAttempt($student, $course, $lesson);
        $v1->load('questionMappings.questionVersion.options');
        $first = $v1->questionMappings->first();
        $correctId = $first->questionVersion->options->firstWhere('is_correct', true)->id;

        $this->submit($student, $course, $lesson, $attempt, [$first->question_id => $correctId])
            ->assertOk()
            ->assertJsonPath('graded.questions.0.is_correct', true);

        $answer = QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)->where('question_id', $first->question_id)->sole();
        $this->assertSame($first->question_version_id, $answer->question_version_id);
        $this->assertTrue($answer->is_correct);
    }

    public function test_unanswered_questions_score_zero_and_persist_null_answer_rows(): void
    {
        [$student, $course, $lesson, , $v1] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);

        $this->submit($student, $course, $lesson, $attempt, [])
            ->assertOk()
            ->assertJsonPath('attempt.score', 0)
            ->assertJsonPath('attempt.percent', 0)
            ->assertJsonPath('attempt.passed', false);

        $v1->load('questionMappings');
        $answers = QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)->get();
        $this->assertCount($v1->questionMappings->count(), $answers);
        $this->assertTrue($answers->every(fn (QuizAttemptAnswer $answer): bool => $answer->answer_id === null && ! $answer->is_correct));
        $this->assertSame(
            $v1->questionMappings->pluck('question_version_id')->sort()->values()->all(),
            $answers->pluck('question_version_id')->sort()->values()->all(),
        );
    }

    public function test_double_submit_returns_the_completed_attempt_without_duplicate_rows_or_regrading(): void
    {
        [$student, $course, $lesson, $quiz, $v1] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);
        $v1->load('questionMappings.questionVersion.options');
        $first = $v1->questionMappings->first();
        $correctId = $first->questionVersion->options->firstWhere('is_correct', true)->id;

        $this->submit($student, $course, $lesson, $attempt, [$first->question_id => $correctId])->assertOk();
        $completed = $attempt->fresh();
        $rowCount = QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)->count();
        $this->switchToV2($quiz, $v1, changeCorrectAnswer: true, firstQuestionPoints: 1, passScore: 100);

        $this->submit($student, $course, $lesson, $attempt, $this->correctAnswers($v1))
            ->assertOk()
            ->assertJsonPath('attempt.score', 10)
            ->assertJsonPath('attempt.total_score', 14)
            ->assertJsonPath('attempt.quiz_version_id', $v1->id);

        $attempt->refresh();
        $this->assertSame($completed->score, $attempt->score);
        $this->assertSame($completed->total_score, $attempt->total_score);
        $this->assertSame($completed->percent, $attempt->percent);
        $this->assertSame($rowCount, QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)->count());
    }

    public function test_other_learner_cannot_submit_an_attempt_they_do_not_own(): void
    {
        [$student, $course, $lesson] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);
        $other = User::factory()->create(['role' => 'student']);
        $this->enroll($other, $course);

        $this->submit($other, $course, $lesson, $attempt, [])->assertForbidden();

        $this->assertSame('in_progress', $attempt->fresh()->status);
        $this->assertSame(0, QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)->count());
    }

    public function test_submit_rejects_an_attempt_from_a_different_course_context(): void
    {
        [$student, $course, $lesson] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);
        $otherCourse = $this->publishedCourse('Other quiz course');
        $this->enroll($student, $otherCourse);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.quiz.submit', [$otherCourse, $lesson]), ['attempt_id' => $attempt->id, 'answers' => []])
            ->assertNotFound();

        $this->assertSame('in_progress', $attempt->fresh()->status);
    }

    /** @return array{0: User, 1: Course, 2: Lesson, 3: Quiz, 4: QuizVersion} */
    private function publishedQuiz(string $firstQuestionType = QuizQuestion::TYPE_SINGLE): array
    {
        $course = $this->publishedCourse('Version-aware grading course');
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $course->courseSections->first()->id,
            'title' => 'Quiz lesson',
            'type' => Lesson::TYPE_QUIZ,
            'sort_order' => 1,
            'status' => Lesson::STATUS_PUBLISHED,
        ]);
        $content = app(QuizContentService::class);
        $quiz = $content->getOrCreateForLesson($lesson);
        $content->saveMetadata($lesson, [
            'title' => 'Quiz V1',
            'description' => null,
            'pass_score' => 70,
            'time_limit_minutes' => 10,
            'max_attempts' => null,
        ], false);

        foreach (range(1, 5) as $index) {
            $type = $index === 1 ? $firstQuestionType : QuizQuestion::TYPE_SINGLE;
            $content->createQuestion($quiz->fresh(), [
                'question_text' => 'V1 question '.$index,
                'question_type' => $type,
                'score' => $index === 1 ? 10 : 1,
                'sort_order' => $index - 1,
            ], $this->optionsFor($type));
        }

        $quiz->update(['is_active' => true]);
        $versioning = app(QuizVersioningService::class);
        $versioning->publishDraft($quiz->fresh(), $versioning->currentDraft($quiz->fresh()));
        $student = User::factory()->create(['role' => 'student']);
        $this->enroll($student, $course);

        return [
            $student,
            $course->fresh(),
            $lesson->fresh(),
            $quiz->fresh(),
            $versioning->currentPublished($quiz->fresh())->fresh('questionMappings.questionVersion.options'),
        ];
    }

    private function switchToV2(
        Quiz $quiz,
        QuizVersion $v1,
        bool $changeCorrectAnswer = false,
        int $firstQuestionPoints = 10,
        int $passScore = 70,
    ): QuizVersion {
        $v1->load('questionMappings.questionVersion.options');
        $v2 = QuizVersion::create([
            'quiz_id' => $quiz->id,
            'version' => 2,
            'title' => 'Quiz V2',
            'description' => 'Updated version',
            'pass_score' => $passScore,
            'time_limit_minutes' => $v1->time_limit_minutes,
            'max_attempts' => $v1->max_attempts,
            'status' => QuizVersion::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        foreach ($v1->questionMappings as $index => $mapping) {
            $questionVersionId = $mapping->question_version_id;

            if ($index === 0) {
                $source = $mapping->questionVersion;
                $replacement = QuestionVersion::create([
                    'question_id' => $mapping->question_id,
                    'version' => (int) $source->version + 1,
                    'question' => 'V2 '.$source->question,
                    'type' => $source->type,
                    'points' => $firstQuestionPoints,
                    'explanation' => $source->explanation,
                    'status' => QuestionVersion::STATUS_PUBLISHED,
                    'published_at' => now(),
                ]);

                foreach ($source->options as $optionIndex => $option) {
                    QuizOption::create([
                        'quiz_question_id' => $mapping->question_id,
                        'question_version_id' => $replacement->id,
                        'option_text' => $option->option_text,
                        'is_correct' => $changeCorrectAnswer && $source->type !== QuizQuestion::TYPE_MULTIPLE
                            ? $optionIndex === 1
                            : $option->is_correct,
                        'sort_order' => $option->sort_order,
                    ]);
                }

                $questionVersionId = $replacement->id;
            }

            QuizVersionQuestion::create([
                'quiz_version_id' => $v2->id,
                'question_id' => $mapping->question_id,
                'question_version_id' => $questionVersionId,
                'sort_order' => $mapping->sort_order,
            ]);
        }

        $v1->update(['status' => QuizVersion::STATUS_SUPERSEDED]);
        $quiz->update(['current_published_version_id' => $v2->id]);

        return $v2->fresh('questionMappings.questionVersion.options');
    }

    private function startAttempt(User $student, Course $course, Lesson $lesson): QuizAttempt
    {
        $this->actingAs($student)->postJson(route('courses.lessons.quiz.start', [$course, $lesson]))->assertOk();

        return QuizAttempt::where('user_id', $student->id)->where('quiz_id', $lesson->quiz->id)->sole();
    }

    private function submit(User $student, Course $course, Lesson $lesson, QuizAttempt $attempt, array $answers)
    {
        return $this->actingAs($student)->postJson(route('courses.lessons.quiz.submit', [$course, $lesson]), [
            'attempt_id' => $attempt->id,
            'answers' => $answers,
        ]);
    }

    /** @return array<int, int|array<int, int>> */
    private function correctAnswers(QuizVersion $version): array
    {
        $version->load('questionMappings.questionVersion.options');

        return $version->questionMappings->mapWithKeys(function (QuizVersionQuestion $mapping): array {
            $correctIds = $mapping->questionVersion->options->where('is_correct', true)->pluck('id')->map(fn ($id): int => (int) $id)->all();

            return [$mapping->question_id => $mapping->questionVersion->type === QuizQuestion::TYPE_MULTIPLE
                ? $correctIds
                : $correctIds[0]];
        })->all();
    }

    /** @return array<int, array<string, mixed>> */
    private function optionsFor(string $type): array
    {
        if ($type === QuizQuestion::TYPE_MULTIPLE) {
            return [
                ['option_text' => 'A', 'is_correct' => true, 'sort_order' => 0],
                ['option_text' => 'B', 'is_correct' => true, 'sort_order' => 1],
                ['option_text' => 'C', 'is_correct' => false, 'sort_order' => 2],
            ];
        }

        if ($type === QuizQuestion::TYPE_TRUE_FALSE) {
            return [
                ['identity' => 'TRUE', 'is_correct' => true],
                ['identity' => 'FALSE', 'is_correct' => false],
            ];
        }

        return [
            ['option_text' => 'Correct', 'is_correct' => true, 'sort_order' => 0],
            ['option_text' => 'Wrong', 'is_correct' => false, 'sort_order' => 1],
            ['option_text' => 'Other', 'is_correct' => false, 'sort_order' => 2],
        ];
    }

    private function publishedCourse(string $title): Course
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);
        $category = Category::create(['name' => $title.' category', 'slug' => str($title)->slug().'-'.uniqid(), 'status' => true]);
        $profile = InstructorProfile::create(['user_id' => $instructor->id, 'category_id' => $category->id]);
        $profile->teachingCategories()->attach($category->id, [
            'is_primary' => true,
            'approval_status' => InstructorTeachingField::STATUS_APPROVED,
        ]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => $title,
            'slug' => str($title)->slug().'-'.uniqid(),
            'description' => 'Description',
            'price' => 0,
            'language' => 'vi',
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
        ]);
        CourseSection::create(['course_id' => $course->id, 'title' => 'Section', 'sort_order' => 0]);

        return $course->fresh(['courseSections']);
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
