<?php

namespace Tests\Feature;

use App\Models\Category;
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
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizVersioningFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_quiz_identity_exposes_explicit_published_and_draft_pointers(): void
    {
        [$user, , $lesson, $quiz] = $this->quizContext();
        $published = $this->quizVersion($quiz, 1, QuizVersion::STATUS_PUBLISHED, $user);
        $draft = $this->quizVersion($quiz, 2, QuizVersion::STATUS_DRAFT, $user);

        $quiz->update([
            'current_published_version_id' => $published->id,
            'current_draft_version_id' => $draft->id,
        ]);
        $quiz->refresh();

        $this->assertTrue($lesson->refresh()->quiz->is($quiz));
        $this->assertSame([$published->id, $draft->id], $quiz->versions->pluck('id')->all());
        $this->assertTrue($quiz->currentPublishedVersion->is($published));
        $this->assertTrue($quiz->publishedVersion->is($published));
        $this->assertTrue($quiz->currentDraftVersion->is($draft));
        $this->assertTrue($quiz->authoringVersion->is($draft));
    }

    public function test_quiz_and_question_versions_are_unique_within_their_identities(): void
    {
        [$user, , , $quiz] = $this->quizContext();
        $this->quizVersion($quiz, 1, QuizVersion::STATUS_DRAFT, $user);

        try {
            $this->quizVersion($quiz, 1, QuizVersion::STATUS_DRAFT, $user);
            $this->fail('Duplicate quiz version was accepted.');
        } catch (QueryException) {
            $this->assertSame(1, $quiz->versions()->count());
        }

        $question = $this->question($quiz, 'Identity question', 0);
        $this->questionVersion($question, 1, 'Question V1');

        try {
            $this->questionVersion($question, 1, 'Duplicate V1');
            $this->fail('Duplicate question version was accepted.');
        } catch (QueryException) {
            $this->assertSame(1, $question->versions()->count());
        }
    }

    public function test_composition_supports_structural_sharing_and_enforces_identity_and_order(): void
    {
        [$user, , , $quiz] = $this->quizContext();
        $quizV1 = $this->quizVersion($quiz, 1, QuizVersion::STATUS_PUBLISHED, $user);
        $quizV2 = $this->quizVersion($quiz, 2, QuizVersion::STATUS_DRAFT, $user);
        $q1 = $this->question($quiz, 'Q1 identity', 0);
        $q2 = $this->question($quiz, 'Q2 identity', 1);
        $q1v1 = $this->questionVersion($q1, 1, 'Q1 V1');
        $q2v1 = $this->questionVersion($q2, 1, 'Q2 V1');
        $q2v2 = $this->questionVersion($q2, 2, 'Q2 V2', QuestionVersion::STATUS_DRAFT);

        $this->mapQuestion($quizV1, $q1, $q1v1, 0);
        $this->mapQuestion($quizV1, $q2, $q2v1, 1);
        $this->mapQuestion($quizV2, $q1, $q1v1, 0);
        $this->mapQuestion($quizV2, $q2, $q2v2, 1);

        $this->assertSame([$q1v1->id, $q2v1->id], $quizV1->questionVersions->pluck('id')->all());
        $this->assertSame([$q1v1->id, $q2v2->id], $quizV2->questionVersions->pluck('id')->all());
        $this->assertSame([$q1->id, $q2->id], $quizV2->questions->pluck('id')->all());
        $this->assertTrue($q1v1->questionIdentity->is($q1));
        $this->assertTrue($q1v1->quizVersions->contains($quizV1));
        $this->assertTrue($q1v1->quizVersions->contains($quizV2));

        try {
            $this->mapQuestion($quizV2, $q2, $q2v1, 2);
            $this->fail('One quiz version accepted two versions of the same question identity.');
        } catch (QueryException) {
            $this->assertSame(2, $quizV2->questionMappings()->count());
        }

        $quizV3 = $this->quizVersion($quiz, 3, QuizVersion::STATUS_DRAFT, $user);
        $this->mapQuestion($quizV3, $q1, $q1v1, 0);

        try {
            $this->mapQuestion($quizV3, $q2, $q2v2, 0);
            $this->fail('Duplicate composition sort order was accepted.');
        } catch (QueryException) {
            $this->assertSame(1, $quizV3->questionMappings()->count());
        }

        $quizV4 = $this->quizVersion($quiz, 4, QuizVersion::STATUS_DRAFT, $user);

        try {
            $this->mapQuestion($quizV4, $q1, $q2v1, 0);
            $this->fail('A question version was mapped to the wrong question identity.');
        } catch (QueryException) {
            $this->assertSame(0, $quizV4->questionMappings()->count());
        }
    }

    public function test_historical_options_attempts_and_answers_bind_to_exact_versions(): void
    {
        [$user, , , $quiz] = $this->quizContext();
        $quizV1 = $this->quizVersion($quiz, 1, QuizVersion::STATUS_PUBLISHED, $user);
        $question = $this->question($quiz, 'Historical question', 0);
        $qv1 = $this->questionVersion($question, 1, 'Historical V1');
        $qv2 = $this->questionVersion($question, 2, 'Draft V2', QuestionVersion::STATUS_DRAFT);
        $oldOption = $this->option($question, $qv1, 'Old answer', true, 0);
        $this->option($question, $qv2, 'New answer', true, 0);
        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'quiz_version_id' => $quizV1->id,
            'status' => 'completed',
            'score' => 1,
            'total_score' => 1,
            'percent' => 100,
            'passed' => true,
            'completed_at' => now(),
        ]);
        $attemptAnswer = QuizAttemptAnswer::create([
            'quiz_attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'question_version_id' => $qv1->id,
            'answer_id' => $oldOption->id,
            'is_correct' => true,
        ]);

        $this->assertTrue($oldOption->questionVersion->is($qv1));
        $this->assertTrue($attempt->quizVersion->is($quizV1));
        $this->assertTrue($attemptAnswer->questionVersion->is($qv1));
        $this->assertTrue($attemptAnswer->answer->is($oldOption));
        $this->assertDatabaseHas('quiz_options', [
            'id' => $oldOption->id,
            'question_version_id' => $qv1->id,
        ]);

        try {
            $quizV1->delete();
            $this->fail('A version referenced by an attempt was deleted.');
        } catch (QueryException) {
            $this->assertDatabaseHas('quiz_attempts', ['id' => $attempt->id]);
        }
    }

    public function test_deleting_unreferenced_draft_version_nulls_its_identity_pointer(): void
    {
        [$user, , , $quiz] = $this->quizContext();
        $draft = $this->quizVersion($quiz, 1, QuizVersion::STATUS_DRAFT, $user);
        $quiz->update(['current_draft_version_id' => $draft->id]);

        $draft->delete();

        $this->assertNull($quiz->refresh()->current_draft_version_id);
    }

    /**
     * @return array{0: User, 1: Course, 2: Lesson, 3: Quiz}
     */
    private function quizContext(): array
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
        ]);
        $category = Category::create([
            'name' => 'Versioning '.uniqid(),
            'slug' => 'versioning-'.uniqid(),
            'status' => true,
        ]);
        $course = Course::create([
            'instructor_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Versioned quiz course',
            'slug' => 'versioned-quiz-'.uniqid(),
            'description' => 'Description',
            'price' => 0,
            'language' => 'vi',
            'status' => Course::STATUS_DRAFT,
        ]);
        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Versioning section',
            'sort_order' => 0,
        ]);
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Versioned quiz lesson',
            'type' => Lesson::TYPE_QUIZ,
            'status' => Lesson::STATUS_DRAFT,
            'sort_order' => 0,
        ]);
        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => 'Quiz identity',
            'pass_score' => 70,
            'is_active' => false,
        ]);

        return [$user, $course, $lesson, $quiz];
    }

    private function quizVersion(Quiz $quiz, int $version, string $status, User $user): QuizVersion
    {
        return QuizVersion::create([
            'quiz_id' => $quiz->id,
            'version' => $version,
            'title' => 'Quiz V'.$version,
            'pass_score' => 70,
            'status' => $status,
            'created_by' => $user->id,
            'published_at' => $status === QuizVersion::STATUS_PUBLISHED ? now() : null,
        ]);
    }

    private function question(Quiz $quiz, string $text, int $sortOrder): QuizQuestion
    {
        return QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => $text,
            'type' => QuizQuestion::TYPE_SINGLE,
            'points' => 1,
            'sort_order' => $sortOrder,
        ]);
    }

    private function questionVersion(
        QuizQuestion $question,
        int $version,
        string $text,
        string $status = QuestionVersion::STATUS_PUBLISHED,
    ): QuestionVersion {
        return QuestionVersion::create([
            'question_id' => $question->id,
            'version' => $version,
            'question' => $text,
            'type' => QuizQuestion::TYPE_SINGLE,
            'points' => 1,
            'status' => $status,
            'published_at' => $status === QuestionVersion::STATUS_PUBLISHED ? now() : null,
        ]);
    }

    private function mapQuestion(
        QuizVersion $quizVersion,
        QuizQuestion $question,
        QuestionVersion $questionVersion,
        int $sortOrder,
    ): QuizVersionQuestion {
        return QuizVersionQuestion::create([
            'quiz_version_id' => $quizVersion->id,
            'question_id' => $question->id,
            'question_version_id' => $questionVersion->id,
            'sort_order' => $sortOrder,
        ]);
    }

    private function option(
        QuizQuestion $question,
        QuestionVersion $questionVersion,
        string $text,
        bool $isCorrect,
        int $sortOrder,
    ): QuizOption {
        return QuizOption::create([
            'quiz_question_id' => $question->id,
            'question_version_id' => $questionVersion->id,
            'option_text' => $text,
            'is_correct' => $isCorrect,
            'sort_order' => $sortOrder,
        ]);
    }
}
