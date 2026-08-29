<?php

namespace Tests\Feature;

use App\Exceptions\HistoricalQuizDeletionException;
use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
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
use App\Services\HistoricalQuizDeletionGuard;
use App\Services\QuizContentService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizHistoricalForeignKeyHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_quiz_with_an_attempt_cannot_be_hard_deleted(): void
    {
        [, , , $quiz] = $this->historicalQuiz();

        $this->expectException(QueryException::class);
        $quiz->delete();
    }

    public function test_version_question_and_selected_option_are_immutable_when_referenced_by_history(): void
    {
        [, , , $quiz, $question, $quizVersion, $questionVersion, $option] = $this->historicalQuiz();

        foreach ([$quizVersion, $questionVersion, $option] as $model) {
            try {
                $model->delete();
                $this->fail('Historical quiz record was deleted.');
            } catch (QueryException) {
                // The database, rather than a controller convention, protects historical records.
            }
        }

        $legacyQuestion = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Legacy question',
            'type' => 'single',
            'points' => 1,
        ]);
        QuizAttemptAnswer::create([
            'quiz_attempt_id' => $quiz->attempts()->sole()->id,
            'question_id' => $legacyQuestion->id,
            'answer_id' => null,
            'is_correct' => false,
        ]);

        try {
            $legacyQuestion->delete();
            $this->fail('Question deletion cascaded into attempt answers.');
        } catch (QueryException) {
            $this->assertDatabaseHas('quiz_attempt_answers', ['question_id' => $legacyQuestion->id]);
        }
    }

    public function test_unreferenced_draft_question_can_still_be_cleaned_up(): void
    {
        [, , , $quiz] = $this->historicalQuiz();
        $draftQuestion = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Draft-only question',
            'type' => 'single',
            'points' => 1,
        ]);

        app(QuizContentService::class)->deleteQuestion($draftQuestion);

        $this->assertDatabaseMissing('quiz_questions', ['id' => $draftQuestion->id]);
    }

    public function test_legacy_null_answer_id_is_still_valid(): void
    {
        [, , , $quiz, $question, $quizVersion, $questionVersion] = $this->historicalQuiz();
        $attempt = $quiz->attempts()->sole();

        QuizAttemptAnswer::create([
            'quiz_attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'question_version_id' => $questionVersion->id,
            'answer_id' => null,
            'is_correct' => false,
        ]);

        $this->assertDatabaseHas('quiz_attempt_answers', [
            'quiz_attempt_id' => $attempt->id,
            'answer_id' => null,
        ]);
        $this->assertSame($quizVersion->id, $attempt->fresh()->quiz_version_id);
    }

    public function test_approved_lesson_delete_fails_safely_and_keeps_history(): void
    {
        [$course, $lesson] = $this->historicalQuiz();
        $admin = User::factory()->create(['role' => 'admin']);
        $update = ContentUpdate::create([
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => ContentUpdate::ACTION_DELETE,
            'course_id' => $course->id,
            'entity_id' => $lesson->id,
            'payload' => [],
            'status' => ContentUpdate::STATUS_PENDING,
            'created_by' => $course->instructor_id,
            'submitted_at' => now(),
        ]);

        try {
            app(ContentUpdateService::class)->applyApprovedUpdate($update, $admin);
            $this->fail('Approved historical lesson deletion was applied.');
        } catch (HistoricalQuizDeletionException $exception) {
            $this->assertSame('Không thể xóa nội dung này vì đã có lịch sử làm bài của học viên.', $exception->getMessage());
        }

        $this->assertDatabaseHas('lessons', ['id' => $lesson->id]);
        $this->assertSame(ContentUpdate::STATUS_PENDING, $update->fresh()->status);
        $this->assertDatabaseCount('quiz_attempts', 1);
        $this->assertDatabaseCount('quiz_attempt_answers', 1);
    }

    public function test_section_and_course_preflight_reject_published_quiz_without_attempts(): void
    {
        [$course, , $section, $quiz] = $this->historicalQuiz();
        QuizAttemptAnswer::query()->delete();
        QuizAttempt::query()->delete();

        $guard = app(HistoricalQuizDeletionGuard::class);

        foreach ([
            fn () => $guard->assertSectionCanBeHardDeleted($section),
            fn () => $guard->assertCourseCanBeHardDeleted($course),
        ] as $assertDeletionAllowed) {
            try {
                $assertDeletionAllowed();
                $this->fail('Published quiz content was allowed to be deleted.');
            } catch (HistoricalQuizDeletionException $exception) {
                $this->assertSame(HistoricalQuizDeletionGuard::MESSAGE, $exception->getMessage());
            }
        }

        $this->assertDatabaseHas('quizzes', ['id' => $quiz->id]);
    }

    /** @return array{Course, Lesson, CourseSection, Quiz, QuizQuestion, QuizVersion, QuestionVersion, QuizOption} */
    private function historicalQuiz(): array
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);
        $category = Category::create(['name' => 'History '.uniqid(), 'slug' => 'history-'.uniqid(), 'status' => true]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Historical quiz course',
            'slug' => 'historical-quiz-'.uniqid(),
            'description' => 'Description',
            'price' => 0,
            'language' => 'vi',
            'status' => Course::STATUS_DRAFT,
        ]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Section', 'sort_order' => 0]);
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz lesson',
            'type' => Lesson::TYPE_QUIZ,
            'status' => Lesson::STATUS_DRAFT,
        ]);
        $quiz = Quiz::create(['lesson_id' => $lesson->id, 'title' => 'Quiz', 'pass_score' => 70]);
        $question = QuizQuestion::create(['quiz_id' => $quiz->id, 'question' => 'Question', 'type' => 'single', 'points' => 1]);
        $questionVersion = QuestionVersion::create([
            'question_id' => $question->id,
            'version' => 1,
            'question' => 'Question',
            'type' => 'single',
            'points' => 1,
            'status' => QuestionVersion::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
        $quizVersion = QuizVersion::create([
            'quiz_id' => $quiz->id,
            'version' => 1,
            'title' => 'Quiz V1',
            'pass_score' => 70,
            'status' => QuizVersion::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
        QuizVersionQuestion::create([
            'quiz_version_id' => $quizVersion->id,
            'question_id' => $question->id,
            'question_version_id' => $questionVersion->id,
            'sort_order' => 0,
        ]);
        $option = QuizOption::create([
            'quiz_question_id' => $question->id,
            'question_version_id' => $questionVersion->id,
            'option_text' => 'Correct option',
            'is_correct' => true,
        ]);
        $attempt = QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'quiz_version_id' => $quizVersion->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        QuizAttemptAnswer::create([
            'quiz_attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'question_version_id' => $questionVersion->id,
            'answer_id' => $option->id,
            'is_correct' => true,
        ]);

        return [$course, $lesson, $section, $quiz, $question, $quizVersion, $questionVersion, $option];
    }
}
