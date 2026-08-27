<?php

namespace Tests\Feature;

use App\Enums\CourseReviewStatus;
use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use App\Models\User;
use App\Services\ContentUpdateService;
use App\Services\CourseReviewService;
use App\Services\CourseSubmissionValidator;
use App\Services\QuizContentService;
use App\Services\QuizService;
use App\Services\QuizVersioningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class QuizVersionedAuthoringTest extends TestCase
{
    use RefreshDatabase;

    private QuizContentService $content;

    private QuizVersioningService $versioning;

    protected function setUp(): void
    {
        parent::setUp();

        $this->content = app(QuizContentService::class);
        $this->versioning = app(QuizVersioningService::class);
    }

    public function test_published_metadata_edit_creates_and_reuses_one_structurally_shared_draft(): void
    {
        [$instructor, $course, $lesson, $quiz, $questions] = $this->publishedQuiz();
        $published = $this->versioning->currentPublished($quiz);
        $publishedQuestionVersionIds = $published->questionMappings()->pluck('question_version_id')->all();

        $this->actingAs($instructor)
            ->get(route('instructor.courses.lessons.quiz.show', [$course, $lesson]))
            ->assertOk()
            ->assertSee('Đang xem V1 — Đã xuất bản');
        $this->assertSame(1, $quiz->versions()->count());
        $this->assertNull($quiz->fresh()->current_draft_version_id);

        $this->content->saveMetadata($quiz->lesson, [
            'title' => 'Quiz V2 title',
            'description' => 'Draft description',
            'pass_score' => 80,
            'time_limit_minutes' => 30,
            'max_attempts' => 2,
        ], true);
        $draft = $this->versioning->currentDraft($quiz->fresh());

        $this->content->saveMetadata($quiz->lesson, [
            'title' => 'Quiz V2 title again',
            'description' => 'Draft description',
            'pass_score' => 80,
            'time_limit_minutes' => 30,
            'max_attempts' => 2,
        ], true);

        $this->assertSame(2, $draft->version);
        $this->assertSame(2, $quiz->versions()->count());
        $this->assertSame($draft->id, $quiz->fresh()->current_draft_version_id);
        $this->assertSame('Quiz V1', $published->fresh()->title);
        $this->assertSame('Quiz V2 title again', $draft->fresh()->title);
        $this->assertSame($publishedQuestionVersionIds, $draft->questionMappings()->pluck('question_version_id')->all());
        $this->assertSame(5, $questions->count());
    }

    public function test_question_clone_on_write_and_repeated_edit_keep_published_content_immutable(): void
    {
        [$instructor, , , $quiz, $questions] = $this->publishedQuiz();
        $question = $questions[1];
        $published = $this->versioning->currentPublished($quiz);
        $q1v1 = $published->questionMappings()->where('question_id', $questions[0]->id)->value('question_version_id');
        $q2v1 = $published->questionMappings()->where('question_id', $question->id)->value('question_version_id');

        $this->actingAs($instructor);
        $this->content->updateQuestion($question, [
            'question_text' => 'Question 2 — draft edit',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'score' => 2,
            'sort_order' => 1,
            'explanation' => 'Draft explanation',
        ]);
        $draft = $this->versioning->currentDraft($quiz->fresh());
        $q2v2 = $draft->questionMappings()->where('question_id', $question->id)->value('question_version_id');

        $this->content->updateQuestion($question->fresh(), [
            'question_text' => 'Question 2 — second draft edit',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'score' => 3,
            'sort_order' => 1,
            'explanation' => 'Draft explanation',
        ]);

        $this->assertSame($q1v1, $draft->questionMappings()->where('question_id', $questions[0]->id)->value('question_version_id'));
        $this->assertNotSame($q2v1, $q2v2);
        $this->assertSame($q2v2, $draft->questionMappings()->where('question_id', $question->id)->value('question_version_id'));
        $this->assertSame('Question 2', QuestionVersion::findOrFail($q2v1)->question);
        $this->assertSame('Question 2 — second draft edit', QuestionVersion::findOrFail($q2v2)->question);
        $this->assertSame(2, $question->versions()->count());
    }

    public function test_option_clone_preserves_old_ids_and_true_false_clone_stays_exactly_two_options(): void
    {
        [$instructor, , , $quiz, $questions] = $this->publishedQuiz(true);
        $question = $questions[0];
        $published = $this->versioning->currentPublished($quiz);
        $oldVersion = $published->questionMappings()->where('question_id', $question->id)->firstOrFail()->questionVersion;
        $oldOptionIds = $oldVersion->options()->pluck('id')->all();
        $selected = $oldVersion->options()->orderBy('sort_order')->get()[1];

        $this->actingAs($instructor);
        $this->content->updateOptions(
            $question,
            $oldVersion->options->mapWithKeys(fn ($option) => [
                $option->id => ['answer_text' => $option->option_text, 'sort_order' => $option->sort_order],
            ])->all(),
            [],
            [$selected->id],
        );

        $draft = $this->versioning->currentDraft($quiz->fresh());
        $newVersion = $draft->questionMappings()->where('question_id', $question->id)->firstOrFail()->questionVersion;
        $newOptionIds = $newVersion->options()->pluck('id')->all();

        $this->assertNotSame($oldVersion->id, $newVersion->id);
        $this->assertCount(2, $oldOptionIds);
        $this->assertCount(2, $newOptionIds);
        $this->assertSame([], array_values(array_intersect($oldOptionIds, $newOptionIds)));
        $this->assertSame(['Đúng', 'Sai'], $newVersion->options()->pluck('option_text')->all());
        $this->assertTrue($oldVersion->options()->orderBy('sort_order')->firstOrFail()->is_correct);
        $this->assertFalse($oldVersion->options()->orderBy('sort_order')->get()[1]->is_correct);
        $this->assertTrue($newVersion->options()->orderBy('sort_order')->get()[1]->is_correct);
    }

    public function test_reorder_remove_and_add_change_only_draft_composition_where_expected(): void
    {
        [$instructor, , , $quiz, $questions] = $this->publishedQuiz();
        $published = $this->versioning->currentPublished($quiz);
        $questionVersionCount = QuestionVersion::count();

        $this->actingAs($instructor);
        $third = $questions[2];
        $this->content->updateQuestion($third, [
            'question_text' => 'Question 3',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'score' => 1,
            'sort_order' => 0,
            'explanation' => null,
        ]);
        $draft = $this->versioning->currentDraft($quiz->fresh());

        $this->assertSame($questionVersionCount, QuestionVersion::count());
        $this->assertSame(
            [$third->id, $questions[0]->id, $questions[1]->id, $questions[3]->id, $questions[4]->id],
            $draft->questionMappings()->pluck('question_id')->all(),
        );

        $removed = $questions[1];
        $this->content->deleteQuestion($removed);
        $added = $this->content->createQuestion($quiz->fresh(), [
            'question_text' => 'Question 6',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'score' => 1,
            'sort_order' => 4,
        ], [
            ['option_text' => 'A6', 'is_correct' => true, 'sort_order' => 0],
            ['option_text' => 'B6', 'is_correct' => false, 'sort_order' => 1],
            ['option_text' => 'C6', 'is_correct' => false, 'sort_order' => 2],
        ]);

        $this->assertTrue($published->questionMappings()->where('question_id', $removed->id)->exists());
        $this->assertFalse($draft->questionMappings()->where('question_id', $removed->id)->exists());
        $this->assertFalse($published->questionMappings()->where('question_id', $added->id)->exists());
        $this->assertTrue($draft->questionMappings()->where('question_id', $added->id)->exists());
        $this->assertDatabaseHas('quiz_questions', ['id' => $removed->id]);
    }

    public function test_pointer_historical_and_cross_quiz_mutation_guards_reject_unsafe_targets(): void
    {
        [$instructor, , , $quiz, $questions] = $this->publishedQuiz();
        [, , , $otherQuiz] = $this->draftQuiz('Other quiz');
        $published = $this->versioning->currentPublished($quiz);
        $publishedQuestionVersion = $published->questionMappings()->firstOrFail()->questionVersion;

        try {
            $this->versioning->assertVersionBelongsToQuiz($quiz, $this->versioning->currentDraft($otherQuiz));
            $this->fail('A cross-quiz version pointer was accepted.');
        } catch (ValidationException) {
            $this->assertTrue(true);
        }

        foreach ([$published, $publishedQuestionVersion] as $immutable) {
            try {
                $immutable instanceof QuizVersion
                    ? $this->versioning->assertMutableQuizVersion($immutable)
                    : $this->versioning->assertMutableQuestionVersion($immutable, $published);
                $this->fail('An immutable version was accepted as mutable.');
            } catch (ValidationException) {
                $this->assertTrue(true);
            }
        }

        $this->actingAs($instructor);
        $oldOption = $publishedQuestionVersion->options()->firstOrFail();
        $this->content->updateOption($oldOption, [
            'answer_text' => 'First draft change',
            'is_correct' => true,
        ]);

        $this->expectException(ValidationException::class);
        $this->content->updateOption($oldOption->fresh(), [
            'answer_text' => 'Stale historical mutation',
            'is_correct' => true,
        ]);
    }

    public function test_quiz_content_update_approval_activates_v2_and_rejected_candidate_reuses_v2(): void
    {
        [$instructor, $course, , $quiz, $questions] = $this->publishedQuiz();
        $admin = User::factory()->create(['role' => 'admin']);
        $publishedId = $quiz->current_published_version_id;

        $this->actingAs($instructor);
        $this->content->updateQuestion($questions[0], [
            'question_text' => 'Candidate question',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'score' => 1,
            'sort_order' => 0,
            'explanation' => null,
        ]);
        $draft = $this->versioning->currentDraft($quiz->fresh());
        $update = $this->versioning->contentUpdateForVersion($quiz->fresh(), $draft);
        $update->update(['status' => ContentUpdate::STATUS_PENDING, 'submitted_at' => now()]);

        app(ContentUpdateService::class)->applyApprovedUpdate($update->fresh(), $admin);

        $this->assertSame($draft->id, $quiz->fresh()->current_published_version_id);
        $this->assertNull($quiz->fresh()->current_draft_version_id);
        $this->assertSame(QuizVersion::STATUS_SUPERSEDED, QuizVersion::findOrFail($publishedId)->status);
        $this->assertSame(QuizVersion::STATUS_PUBLISHED, $draft->fresh()->status);
        $this->assertTrue($update->fresh()->isApproved());
        $this->assertFalse((bool) data_get($update->fresh()->payload, 'activation_deferred'));

        [$instructor2, $course2, , $quiz2, $questions2] = $this->publishedQuiz(false, 'Rejected candidate');
        $this->actingAs($instructor2);
        $this->content->updateQuestion($questions2[0], [
            'question_text' => 'Rejected V2',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'score' => 1,
            'sort_order' => 0,
            'explanation' => null,
        ]);
        $draft2 = $this->versioning->currentDraft($quiz2->fresh());
        $update2 = $this->versioning->contentUpdateForVersion($quiz2->fresh(), $draft2);
        $update2->update(['status' => ContentUpdate::STATUS_PENDING]);
        app(ContentUpdateService::class)->rejectUpdate($update2->fresh(), $admin, 'Cần chỉnh sửa câu hỏi.');

        $this->content->updateQuestion($questions2[0]->fresh(), [
            'question_text' => 'Corrected V2',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'score' => 1,
            'sort_order' => 0,
            'explanation' => null,
        ]);

        $this->assertSame(2, $quiz2->versions()->count());
        $this->assertSame($draft2->id, $quiz2->fresh()->current_draft_version_id);
        $this->assertTrue($update2->fresh()->isDraft());
        $this->assertSame(Course::STATUS_REJECTED_UPDATE, $course2->fresh()->status);
    }

    public function test_first_course_approval_publishes_draft_v1_atomically_for_future_learner_reads(): void
    {
        [$instructor, $course, , $quiz] = $this->draftQuiz('First publication');
        $admin = User::factory()->create(['role' => 'admin']);
        $course->update(['status' => Course::STATUS_PENDING, 'submission_count' => 1]);
        CourseReview::create([
            'course_id' => $course->id,
            'submission_number' => 1,
            'status' => CourseReviewStatus::Pending,
            'submitted_at' => now(),
        ]);
        $checklist = collect(config('course.admin_review_checklist'))
            ->mapWithKeys(fn ($label, $key) => [$key => true])
            ->all();

        app(CourseReviewService::class)->approve($course->fresh(), $admin, $checklist, true);

        $quiz->refresh();
        $published = $this->versioning->currentPublished($quiz);
        $this->assertSame(1, $published->version);
        $this->assertSame(QuizVersion::STATUS_PUBLISHED, $published->status);
        $this->assertNull($quiz->current_draft_version_id);
        $this->assertTrue($published->questionVersions->every(
            fn (QuestionVersion $version): bool => $version->status === QuestionVersion::STATUS_PUBLISHED,
        ));
        $this->assertSame(Course::STATUS_PUBLISHED, $course->fresh()->status);
        $this->assertSame($instructor->id, $course->instructor_id);
    }

    public function test_learner_projection_and_grading_ignore_coexisting_draft_options(): void
    {
        [$instructor, , , $quiz, $questions] = $this->publishedQuiz();
        $question = $questions[0];
        $published = $this->versioning->currentPublished($quiz);
        $publishedMapping = $published->questionMappings()->where('question_id', $question->id)->firstOrFail();
        $oldCorrect = $publishedMapping->questionVersion->options()->where('is_correct', true)->firstOrFail();

        $this->actingAs($instructor);
        $this->content->updateOption($oldCorrect, [
            'answer_text' => 'Draft-only answer text',
            'is_correct' => true,
        ]);

        $projected = $this->versioning->projectVersion($quiz->fresh(), $published->fresh());
        $projectedQuestion = $projected->questions->firstWhere('id', $question->id);
        $graded = app(QuizService::class)->grade($quiz->fresh(), [$question->id => $oldCorrect->id]);

        $this->assertFalse($projectedQuestion->options->contains('option_text', 'Draft-only answer text'));
        $this->assertSame(3, $projectedQuestion->options->count());
        $this->assertTrue($graded['questions'][$question->id]['is_correct']);
        $this->assertSame($published->id, $quiz->fresh()->current_published_version_id);
    }

    public function test_course_readiness_uses_invalid_draft_candidate_while_v1_remains_valid_for_learners(): void
    {
        [$instructor, $course, , $quiz, $questions] = $this->publishedQuiz();
        $published = $this->versioning->currentPublished($quiz);
        $publishedValidation = $this->content->validateQuizVersion($published);
        $this->assertTrue($publishedValidation['is_complete']);

        $this->actingAs($instructor);
        $this->content->deleteQuestion($questions[0]);
        $draft = $this->versioning->currentDraft($quiz->fresh());
        $this->assertFalse($this->content->validateQuizVersion($draft)['is_complete']);

        $item = collect(app(CourseSubmissionValidator::class)->validate($course->fresh())->items())
            ->firstWhere('key', CourseSubmissionValidator::KEY_QUIZ_CONTENT);

        $this->assertFalse($item['passed']);
        $this->assertStringContainsString('chưa đủ 5 câu hỏi', $item['message']);
        $this->assertSame($published->id, $quiz->fresh()->current_published_version_id);
    }

    public function test_instructor_routes_reject_cross_owner_question_and_option_ids(): void
    {
        [, , , $quiz, $questions] = $this->draftQuiz('Owned quiz');
        $intruder = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $question = $questions[0];
        $option = $question->options->first();

        $this->actingAs($intruder)
            ->post(route('instructor.quizzes.questions.store', $quiz), [
                'question_text' => 'IDOR',
                'question_type' => QuizQuestion::TYPE_SINGLE,
                'score' => 1,
            ])
            ->assertForbidden();
        $this->actingAs($intruder)
            ->put(route('instructor.quiz-questions.update', $question), [
                'question_text' => 'IDOR',
                'question_type' => QuizQuestion::TYPE_SINGLE,
                'score' => 1,
            ])
            ->assertForbidden();
        $this->actingAs($intruder)
            ->put(route('instructor.quiz-answers.update', $option), [
                'answer_text' => 'IDOR option',
                'is_correct' => true,
            ])
            ->assertForbidden();
    }

    public function test_pending_review_freezes_the_exact_candidate_in_service_and_manager_ui(): void
    {
        [$instructor, $course, $lesson, $quiz, $questions] = $this->publishedQuiz();
        $this->actingAs($instructor);
        $this->content->updateQuestion($questions[0], [
            'question_text' => 'Pending candidate',
            'question_type' => QuizQuestion::TYPE_SINGLE,
            'score' => 1,
            'sort_order' => 0,
            'explanation' => null,
        ]);
        $draft = $this->versioning->currentDraft($quiz->fresh());
        $update = $this->versioning->contentUpdateForVersion($quiz->fresh(), $draft);
        $update->update(['status' => ContentUpdate::STATUS_PENDING, 'submitted_at' => now()]);

        try {
            $this->content->updateQuestion($questions[0]->fresh(), [
                'question_text' => 'Must not mutate pending content',
                'question_type' => QuizQuestion::TYPE_SINGLE,
                'score' => 1,
                'sort_order' => 0,
                'explanation' => null,
            ]);
            $this->fail('A pending Quiz candidate was mutated.');
        } catch (ValidationException) {
            $this->assertSame('Pending candidate', $draft->questionMappings()->firstOrFail()->questionVersion->fresh()->question);
        }

        $this->get(route('instructor.courses.lessons.quiz.show', [$course, $lesson]))
            ->assertOk()
            ->assertSee('Đang chờ duyệt')
            ->assertSee('tạm thời không thể chỉnh sửa');
    }

    /**
     * @return array{0: User, 1: Course, 2: Lesson, 3: Quiz, 4: Collection<int, QuizQuestion>}
     */
    private function publishedQuiz(bool $firstQuestionTrueFalse = false, string $title = 'Published quiz'): array
    {
        [$instructor, $course, $lesson, $quiz, $questions] = $this->draftQuiz($title, $firstQuestionTrueFalse);
        $quiz->update(['is_active' => true]);
        $this->versioning->publishDraft($quiz->fresh(), $this->versioning->currentDraft($quiz->fresh()));
        $course->update([
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
        ]);
        $lesson->update(['status' => Lesson::STATUS_PUBLISHED]);

        return [$instructor, $course->fresh(), $lesson->fresh(), $quiz->fresh(), $questions];
    }

    /**
     * @return array{0: User, 1: Course, 2: Lesson, 3: Quiz, 4: Collection<int, QuizQuestion>}
     */
    private function draftQuiz(string $title, bool $firstQuestionTrueFalse = false): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $category = Category::create([
            'name' => 'Quiz category '.uniqid(),
            'slug' => 'quiz-category-'.uniqid(),
            'status' => true,
        ]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => $title,
            'slug' => str($title)->slug().'-'.uniqid(),
            'description' => 'Description',
            'price' => 0,
            'language' => 'vi',
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
        ]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Section', 'sort_order' => 0]);
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz lesson',
            'type' => Lesson::TYPE_QUIZ,
            'sort_order' => 0,
            'status' => Lesson::STATUS_DRAFT,
        ]);
        $quiz = $this->content->getOrCreateForLesson($lesson);
        $this->content->saveMetadata($lesson, [
            'title' => 'Quiz V1',
            'description' => 'Initial version',
            'pass_score' => 70,
            'time_limit_minutes' => null,
            'max_attempts' => null,
        ], false);
        $questions = collect();

        for ($index = 1; $index <= 5; $index++) {
            $type = $firstQuestionTrueFalse && $index === 1
                ? QuizQuestion::TYPE_TRUE_FALSE
                : QuizQuestion::TYPE_SINGLE;
            $options = $type === QuizQuestion::TYPE_TRUE_FALSE
                ? []
                : [
                    ['option_text' => 'A'.$index, 'is_correct' => true, 'sort_order' => 0],
                    ['option_text' => 'B'.$index, 'is_correct' => false, 'sort_order' => 1],
                    ['option_text' => 'C'.$index, 'is_correct' => false, 'sort_order' => 2],
                ];
            $questions->push($this->content->createQuestion($quiz->fresh(), [
                'question_text' => 'Question '.$index,
                'question_type' => $type,
                'score' => 1,
                'sort_order' => $index - 1,
            ], $options));
        }

        return [$instructor, $course, $lesson, $quiz->fresh(), $questions];
    }
}
