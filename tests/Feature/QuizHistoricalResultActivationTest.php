<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
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
use App\Services\ContentUpdateService;
use App\Services\QuizAttemptResultService;
use App\Services\QuizContentService;
use App\Services\QuizVersioningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class QuizHistoricalResultActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_historical_result_renders_v1_question_option_and_correctness_after_v2_activation(): void
    {
        [$student, $course, $lesson, $quiz, $v1] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);
        $this->actingAs($student)
            ->get(route('learn.lessons.quiz.show', [$course->slug, $lesson]))
            ->assertOk()
            ->assertSee('data-math-content', false);
        $this->actingAs($student)
            ->post(route('learn.lessons.quiz.submit', [$course, $lesson]), [
                'attempt_id' => $attempt->id,
                'answers' => $this->correctAnswers($v1),
            ])
            ->assertRedirect(route('learn.lessons.quiz.result', [$course->slug, $lesson, $attempt]));
        $candidate = $this->draftV2($quiz, $v1, 0);
        $this->activate($course, $quiz, $candidate);

        $this->actingAs($student)
            ->get(route('learn.lessons.quiz.result', [$course->slug, $lesson, $attempt]))
            ->assertOk()
            ->assertSee('Ket qua Quiz - Phien ban V1')
            ->assertSee('V1 question 1')
            ->assertSee('V1 Correct 1')
            ->assertSee('Dap an dung')
            ->assertDontSee('V2 question 1')
            ->assertDontSee('V2 option 1-1');

        $this->actingAs($student)
            ->get(route('courses.lessons.quiz.attempts.show', [$course, $lesson, $attempt]))
            ->assertOk()
            ->assertSee('V1 question 1')
            ->assertSee('V1 Correct 1')
            ->assertDontSee('V2 question 1')
            ->assertDontSee('V2 option 1-1');

        $this->assertSame($v1->id, $attempt->fresh()->quiz_version_id);
    }

    public function test_historical_result_marks_latex_content_as_safe_render_targets(): void
    {
        [$student, $course, $lesson, , $v1] = $this->publishedQuiz();
        $v1->load('questionMappings.questionVersion.options');
        $first = $v1->questionMappings->first();
        $questionText = 'Solve \\(x^2 = 4\\) <script>alert(1)</script>';

        $first->questionVersion->update([
            'question' => $questionText,
            'explanation' => 'Use \\(x = 2\\) to verify the answer.',
        ]);
        $first->questionVersion->options->firstOrFail()->update([
            'option_text' => '\\(x = 2\\)',
        ]);

        $attempt = $this->startAttempt($student, $course, $lesson);
        $this->submit($student, $course, $lesson, $attempt, $this->correctAnswers($v1))->assertOk();

        $this->actingAs($student)
            ->get(route('learn.lessons.quiz.result', [$course->slug, $lesson, $attempt]))
            ->assertOk()
            ->assertSee('data-math-content', false)
            ->assertSee('Solve \\(x^2 = 4\\)', false)
            ->assertSee('Use \\(x = 2\\)', false)
            ->assertSee('\\(x = 2\\)', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_historical_result_groups_multiple_answers_and_handles_unanswered_and_missing_legacy_answers(): void
    {
        [$student, $course, $lesson, , $v1] = $this->publishedQuiz(QuizQuestion::TYPE_MULTIPLE);
        $attempt = $this->startAttempt($student, $course, $lesson);
        $v1->load('questionMappings.questionVersion.options');
        $first = $v1->questionMappings->first();
        $correctIds = $first->questionVersion->options->where('is_correct', true)->pluck('id')->all();
        $this->submit($student, $course, $lesson, $attempt, [$first->question_id => $correctIds])->assertOk();

        $result = app(QuizAttemptResultService::class)->forLearner($course, $lesson, $student, $attempt->fresh());
        $questions = array_values($result['questions']);
        $this->assertCount(5, $questions);
        $multipleQuestion = collect($questions)->firstWhere('question', 'V1 question 1');
        $unansweredQuestion = collect($questions)->firstWhere('question', 'V1 question 2');
        $this->assertNotNull($multipleQuestion);
        $this->assertNotNull($unansweredQuestion);
        $this->assertCount(2, collect($multipleQuestion['options'])->where('is_selected', true));
        $this->assertTrue($unansweredQuestion['is_unanswered']);

        QuizAttemptAnswer::where('quiz_attempt_id', $attempt->id)
            ->where('question_version_id', $first->question_version_id)
            ->firstOrFail()
            ->update(['answer_id' => null]);

        $this->actingAs($student)
            ->get(route('learn.lessons.quiz.result', [$course->slug, $lesson, $attempt]))
            ->assertOk()
            ->assertSee('Chua tra loi.')
            ->assertSee('Dap an truoc day khong con kha dung.');
    }

    public function test_learner_cannot_view_another_learners_historical_attempt(): void
    {
        [$student, $course, $lesson, , $v1] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);
        $this->submit($student, $course, $lesson, $attempt, $this->correctAnswers($v1))->assertOk();
        $other = User::factory()->create(['role' => 'student']);
        $this->enroll($other, $course);

        $this->actingAs($other)
            ->get(route('learn.lessons.quiz.result', [$course->slug, $lesson, $attempt]))
            ->assertForbidden();
    }

    public function test_approved_draft_v2_activates_atomically_and_preserves_v1_and_structural_sharing(): void
    {
        [, $course, , $quiz, $v1] = $this->publishedQuiz();
        $v1->load('questionMappings.questionVersion.options');
        $candidate = $this->draftV2($quiz, $v1, 1);
        $candidate->load('questionMappings.questionVersion.options');
        $v1FirstQuestionVersion = $v1->questionMappings->first()->question_version_id;
        $v1ChangedQuestionVersion = $v1->questionMappings->get(1)->question_version_id;
        $v2FirstQuestionVersion = $candidate->questionMappings->first()->question_version_id;
        $v2ChangedQuestionVersion = $candidate->questionMappings->get(1)->question_version_id;
        $update = $this->activate($course, $quiz, $candidate, desiredIsActive: false);

        $quiz->refresh();
        $this->assertSame($candidate->id, $quiz->current_published_version_id);
        $this->assertNull($quiz->current_draft_version_id);
        $this->assertFalse($quiz->is_active);
        $this->assertSame(QuizVersion::STATUS_SUPERSEDED, $v1->fresh()->status);
        $this->assertSame(QuizVersion::STATUS_PUBLISHED, $candidate->fresh()->status);
        $this->assertSame($v1FirstQuestionVersion, $v2FirstQuestionVersion);
        $this->assertNotSame($v1ChangedQuestionVersion, $v2ChangedQuestionVersion);
        $this->assertSame(QuestionVersion::STATUS_PUBLISHED, QuestionVersion::findOrFail($v1FirstQuestionVersion)->status);
        $this->assertSame(QuestionVersion::STATUS_PUBLISHED, QuestionVersion::findOrFail($v1ChangedQuestionVersion)->status);
        $this->assertSame(QuestionVersion::STATUS_PUBLISHED, QuestionVersion::findOrFail($v2ChangedQuestionVersion)->status);
        $this->assertSame('V1 question 2', QuestionVersion::findOrFail($v1ChangedQuestionVersion)->question);
        $this->assertFalse((bool) data_get($update->fresh()->payload, 'activation_deferred'));
    }

    public function test_existing_v1_attempt_resumes_submits_and_renders_v1_after_activation_while_new_attempt_uses_v2(): void
    {
        [$student, $course, $lesson, $quiz, $v1] = $this->publishedQuiz();
        $attempt = $this->startAttempt($student, $course, $lesson);
        $candidate = $this->draftV2($quiz, $v1, 0);
        $this->activate($course, $quiz, $candidate);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.quiz.start', [$course, $lesson]))
            ->assertOk()
            ->assertJsonPath('attempt.id', $attempt->id)
            ->assertJsonPath('attempt.quiz_version_id', $v1->id);
        $this->submit($student, $course, $lesson, $attempt, $this->correctAnswers($v1))->assertOk();

        $this->actingAs($student)
            ->get(route('learn.lessons.quiz.result', [$course->slug, $lesson, $attempt]))
            ->assertOk()
            ->assertSee('Ket qua Quiz - Phien ban V1')
            ->assertSee('V1 question 1');

        $this->actingAs($student)
            ->postJson(route('courses.lessons.quiz.start', [$course, $lesson]))
            ->assertOk()
            ->assertJsonPath('attempt.quiz_version_id', $candidate->id);
    }

    public function test_rejected_or_incomplete_v2_candidate_does_not_activate(): void
    {
        [$instructor, $course, , $quiz, $v1] = $this->publishedQuiz();
        $admin = User::factory()->create(['role' => 'admin']);
        $candidate = $this->draftV2($quiz, $v1, 0);
        $rejected = $this->contentUpdate($course, $quiz, $candidate);
        app(ContentUpdateService::class)->rejectUpdate($rejected, $admin, 'Candidate needs correction.');

        $this->assertSame($v1->id, $quiz->fresh()->current_published_version_id);
        $this->assertSame($candidate->id, $quiz->fresh()->current_draft_version_id);
        $this->assertSame(QuizVersion::STATUS_DRAFT, $candidate->fresh()->status);
        $this->assertTrue($rejected->fresh()->isRejected());

        [$instructor2, $course2, , $quiz2, $v1b] = $this->publishedQuiz();
        $incomplete = $this->draftV2($quiz2, $v1b, 0, incomplete: true);
        $invalidUpdate = $this->contentUpdate($course2, $quiz2, $incomplete);

        try {
            app(ContentUpdateService::class)->applyApprovedUpdate($invalidUpdate, $instructor2);
            $this->fail('An incomplete Quiz candidate was activated.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
        }

        $this->assertSame($v1b->id, $quiz2->fresh()->current_published_version_id);
        $this->assertSame($incomplete->id, $quiz2->fresh()->current_draft_version_id);
        $this->assertSame(QuizVersion::STATUS_DRAFT, $incomplete->fresh()->status);
        $this->assertTrue($invalidUpdate->fresh()->isPending());
    }

    public function test_quiz_activation_is_idempotent(): void
    {
        [$student, $course, , $quiz, $v1] = $this->publishedQuiz();
        $candidate = $this->draftV2($quiz, $v1, 0);
        $admin = User::factory()->create(['role' => 'admin']);
        $update = $this->contentUpdate($course, $quiz, $candidate);

        app(ContentUpdateService::class)->applyApprovedUpdate($update, $admin);
        app(ContentUpdateService::class)->applyApprovedUpdate($update->fresh(), $admin);

        $this->assertSame(2, $quiz->versions()->count());
        $this->assertSame($candidate->id, $quiz->fresh()->current_published_version_id);
        $this->assertNull($quiz->fresh()->current_draft_version_id);
        $this->assertSame(QuizVersion::STATUS_SUPERSEDED, $v1->fresh()->status);
        $this->assertSame(QuizVersion::STATUS_PUBLISHED, $candidate->fresh()->status);
        $this->assertTrue($update->fresh()->isApproved());
    }

    /** @return array{0: User, 1: Course, 2: Lesson, 3: Quiz, 4: QuizVersion} */
    private function publishedQuiz(string $firstQuestionType = QuizQuestion::TYPE_SINGLE): array
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);
        $category = Category::create(['name' => 'Quiz history '.uniqid(), 'slug' => 'quiz-history-'.uniqid(), 'status' => true]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Quiz history course',
            'slug' => 'quiz-history-course-'.uniqid(),
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
            ], $this->optionsFor($type, $index));
        }
        $quiz->update(['is_active' => true]);
        $versioning = app(QuizVersioningService::class);
        $versioning->publishDraft($quiz->fresh(), $versioning->currentDraft($quiz->fresh()));
        $student = User::factory()->create(['role' => 'student']);
        $this->enroll($student, $course);

        return [$student, $course->fresh(), $lesson->fresh(), $quiz->fresh(), $versioning->currentPublished($quiz->fresh())->fresh('questionMappings.questionVersion.options')];
    }

    private function draftV2(Quiz $quiz, QuizVersion $v1, int $changeQuestionIndex, bool $incomplete = false): QuizVersion
    {
        $v1->load('questionMappings.questionVersion.options');
        $candidate = QuizVersion::create([
            'quiz_id' => $quiz->id,
            'version' => 2,
            'title' => 'Quiz V2',
            'description' => 'Updated Quiz',
            'pass_score' => 80,
            'time_limit_minutes' => $v1->time_limit_minutes,
            'max_attempts' => $v1->max_attempts,
            'status' => QuizVersion::STATUS_DRAFT,
        ]);

        foreach ($v1->questionMappings as $index => $mapping) {
            if ($incomplete && $index === $v1->questionMappings->count() - 1) {
                continue;
            }

            $questionVersionId = $mapping->question_version_id;
            if ($index === $changeQuestionIndex) {
                $source = $mapping->questionVersion;
                $replacement = QuestionVersion::create([
                    'question_id' => $mapping->question_id,
                    'version' => (int) $source->version + 1,
                    'question' => 'V2 question '.($index + 1),
                    'type' => $source->type,
                    'points' => (int) $source->points + 1,
                    'explanation' => 'V2 explanation '.($index + 1),
                    'status' => QuestionVersion::STATUS_DRAFT,
                ]);
                foreach ($source->options as $optionIndex => $option) {
                    QuizOption::create([
                        'quiz_question_id' => $mapping->question_id,
                        'question_version_id' => $replacement->id,
                        'option_text' => 'V2 option '.($index + 1).'-'.($optionIndex + 1),
                        'is_correct' => $source->type === QuizQuestion::TYPE_MULTIPLE
                            ? $option->is_correct
                            : $optionIndex === 1,
                        'sort_order' => $option->sort_order,
                    ]);
                }
                $questionVersionId = $replacement->id;
            }

            QuizVersionQuestion::create([
                'quiz_version_id' => $candidate->id,
                'question_id' => $mapping->question_id,
                'question_version_id' => $questionVersionId,
                'sort_order' => $mapping->sort_order,
            ]);
        }

        $quiz->update(['current_draft_version_id' => $candidate->id]);

        return $candidate->fresh('questionMappings.questionVersion.options');
    }

    private function activate(Course $course, Quiz $quiz, QuizVersion $candidate, bool $desiredIsActive = true): ContentUpdate
    {
        $update = $this->contentUpdate($course, $quiz, $candidate, $desiredIsActive);
        $admin = User::factory()->create(['role' => 'admin']);
        app(ContentUpdateService::class)->applyApprovedUpdate($update, $admin);

        return $update->fresh();
    }

    private function contentUpdate(Course $course, Quiz $quiz, QuizVersion $candidate, bool $desiredIsActive = true): ContentUpdate
    {
        return ContentUpdate::create([
            'type' => ContentUpdate::TYPE_QUIZ,
            'action' => ContentUpdate::ACTION_UPDATE,
            'course_id' => $course->id,
            'entity_id' => $quiz->id,
            'payload' => [
                'quiz_id' => $quiz->id,
                'quiz_version_id' => $candidate->id,
                'desired_is_active' => $desiredIsActive,
            ],
            'status' => ContentUpdate::STATUS_PENDING,
            'created_by' => $course->instructor_id,
            'submitted_at' => now(),
        ]);
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

            return [$mapping->question_id => $mapping->questionVersion->type === QuizQuestion::TYPE_MULTIPLE ? $correctIds : $correctIds[0]];
        })->all();
    }

    /** @return array<int, array<string, mixed>> */
    private function optionsFor(string $type, int $index): array
    {
        if ($type === QuizQuestion::TYPE_MULTIPLE) {
            return [
                ['option_text' => 'V1 A'.$index, 'is_correct' => true, 'sort_order' => 0],
                ['option_text' => 'V1 B'.$index, 'is_correct' => true, 'sort_order' => 1],
                ['option_text' => 'V1 C'.$index, 'is_correct' => false, 'sort_order' => 2],
            ];
        }

        return [
            ['option_text' => 'V1 Correct '.$index, 'is_correct' => true, 'sort_order' => 0],
            ['option_text' => 'V1 Wrong '.$index, 'is_correct' => false, 'sort_order' => 1],
            ['option_text' => 'V1 Other '.$index, 'is_correct' => false, 'sort_order' => 2],
        ];
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
