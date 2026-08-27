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
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestion;
use App\Models\User;
use App\Services\QuizAttemptService;
use App\Services\QuizContentService;
use App\Services\QuizVersioningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizAttemptStartResumeTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_binds_the_current_published_version_and_second_start_resumes_it(): void
    {
        [$student, $course, $lesson, $quiz, $v1] = $this->publishedQuiz();

        $this->actingAs($student)->postJson(route('courses.lessons.quiz.start', [$course, $lesson]))->assertOk()
            ->assertJsonPath('attempt.quiz_version_id', $v1->id);
        $first = QuizAttempt::firstOrFail();

        $this->actingAs($student)->postJson(route('courses.lessons.quiz.start', [$course, $lesson]))->assertOk()
            ->assertJsonPath('attempt.id', $first->id);

        $this->assertDatabaseCount('quiz_attempts', 1);
        $this->assertSame('in_progress', $first->fresh()->status);
        $this->assertNull($first->fresh()->completed_at);
    }

    public function test_resume_stays_on_v1_after_current_pointer_switches_to_v2(): void
    {
        [$student, $course, $lesson, $quiz, $v1] = $this->publishedQuiz();
        $this->actingAs($student)->postJson(route('courses.lessons.quiz.start', [$course, $lesson]));
        $v2 = $this->publishedV2($quiz, $v1);

        $this->actingAs($student)->postJson(route('courses.lessons.quiz.start', [$course, $lesson]))->assertOk()
            ->assertJsonPath('attempt.quiz_version_id', $v1->id);

        $attempt = QuizAttempt::firstOrFail();
        $projected = app(QuizAttemptService::class)->projectQuiz($attempt);
        $this->assertSame($v1->id, $attempt->quiz_version_id);
        $this->assertSame('Quiz V1', $projected->title);
        $this->assertNotSame($v2->id, $attempt->quiz_version_id);
    }

    public function test_completed_attempt_allows_a_new_attempt_on_the_new_current_version(): void
    {
        [$student, $course, $lesson, $quiz, $v1] = $this->publishedQuiz();
        $this->actingAs($student)->postJson(route('courses.lessons.quiz.start', [$course, $lesson]));
        $first = QuizAttempt::firstOrFail();
        $first->update(['status' => 'completed', 'completed_at' => now()]);
        $v2 = $this->publishedV2($quiz, $v1);

        $this->actingAs($student)->postJson(route('courses.lessons.quiz.start', [$course, $lesson]))->assertOk()
            ->assertJsonPath('attempt.quiz_version_id', $v2->id);

        $this->assertDatabaseCount('quiz_attempts', 2);
    }

    public function test_start_ignores_an_arbitrary_quiz_version_id_and_refresh_preserves_started_at(): void
    {
        [$student, $course, $lesson, $quiz, $v1] = $this->publishedQuiz();
        $v2 = $this->publishedV2($quiz, $v1);

        $this->actingAs($student)->postJson(route('courses.lessons.quiz.start', [$course, $lesson]), ['quiz_version_id' => $v1->id])->assertOk()
            ->assertJsonPath('attempt.quiz_version_id', $v2->id);
        $attempt = QuizAttempt::firstOrFail();
        $startedAt = $attempt->started_at->toIso8601String();

        $this->actingAs($student)->postJson(route('courses.lessons.quiz.start', [$course, $lesson]), ['quiz_version_id' => $v1->id])->assertOk()
            ->assertJsonPath('attempt.id', $attempt->id);
        $this->assertSame($startedAt, $attempt->fresh()->started_at->toIso8601String());
    }

    public function test_attempt_projection_uses_only_its_bound_version_questions_and_options(): void
    {
        [$student, $course, $lesson, $quiz, $v1] = $this->publishedQuiz();
        $this->actingAs($student)->postJson(route('courses.lessons.quiz.start', [$course, $lesson]));
        $attempt = QuizAttempt::firstOrFail();

        app(QuizVersioningService::class)->ensureDraft($quiz->fresh());
        $draft = app(QuizVersioningService::class)->currentDraft($quiz->fresh());
        $mapping = $draft->questionMappings()->firstOrFail();
        $draftQuestionVersion = QuestionVersion::create([
            'question_id' => $mapping->question_id,
            'version' => 2,
            'question' => 'Draft V2 question must not be shown',
            'type' => $mapping->questionVersion->type,
            'points' => $mapping->questionVersion->points,
            'status' => QuestionVersion::STATUS_DRAFT,
        ]);
        $mapping->update(['question_version_id' => $draftQuestionVersion->id]);

        $projected = app(QuizAttemptService::class)->projectQuiz($attempt);
        $this->assertSame($v1->id, $attempt->quiz_version_id);
        $this->assertStringNotContainsString('Draft V2 question must not be shown', $projected->questions->first()->question);
        $this->assertCount(3, $projected->questions->first()->options);
    }

    public function test_start_rejects_cross_course_lesson_mismatch_and_other_user_cannot_resume_owner_attempt(): void
    {
        [$student, $course, $lesson] = $this->publishedQuiz();
        $other = User::factory()->create(['role' => 'student']);
        $this->enroll($other, $course);
        $otherCourse = $this->publishedCourse('Other course');

        $this->actingAs($student)->postJson(route('courses.lessons.quiz.start', [$course, $lesson]))->assertOk();
        $this->actingAs($student)->postJson(route('courses.lessons.quiz.start', [$otherCourse, $lesson]))->assertNotFound();
        $this->actingAs($other)->postJson(route('courses.lessons.quiz.start', [$course, $lesson]))->assertOk();

        $this->assertSame(2, QuizAttempt::count());
        $this->assertSame(1, QuizAttempt::where('user_id', $student->id)->count());
        $this->assertSame(1, QuizAttempt::where('user_id', $other->id)->count());
    }

    /** @return array{0: User, 1: Course, 2: Lesson, 3: Quiz, 4: QuizVersion} */
    private function publishedQuiz(): array
    {
        $course = $this->publishedCourse('Attempt course');
        $lesson = Lesson::create(['course_id' => $course->id, 'section_id' => $course->courseSections->first()->id, 'title' => 'Quiz lesson', 'type' => Lesson::TYPE_QUIZ, 'sort_order' => 1, 'status' => Lesson::STATUS_PUBLISHED]);
        $content = app(QuizContentService::class);
        $quiz = $content->getOrCreateForLesson($lesson);
        $content->saveMetadata($lesson, ['title' => 'Quiz V1', 'description' => null, 'pass_score' => 70, 'time_limit_minutes' => 10, 'max_attempts' => null], false);
        foreach (range(1, 5) as $index) {
            $content->createQuestion($quiz->fresh(), ['question_text' => 'V1 question '.$index, 'question_type' => 'single', 'score' => 1, 'sort_order' => $index - 1], [
                ['option_text' => 'Correct '.$index, 'is_correct' => true, 'sort_order' => 0],
                ['option_text' => 'Wrong '.$index, 'is_correct' => false, 'sort_order' => 1],
                ['option_text' => 'Other '.$index, 'is_correct' => false, 'sort_order' => 2],
            ]);
        }
        $quiz->update(['is_active' => true]);
        app(QuizVersioningService::class)->publishDraft($quiz->fresh(), app(QuizVersioningService::class)->currentDraft($quiz->fresh()));
        $student = User::factory()->create(['role' => 'student']);
        $this->enroll($student, $course);

        return [$student, $course->fresh(), $lesson->fresh(), $quiz->fresh(), app(QuizVersioningService::class)->currentPublished($quiz->fresh())];
    }

    private function publishedV2(Quiz $quiz, QuizVersion $v1): QuizVersion
    {
        $v2 = QuizVersion::create([
            'quiz_id' => $quiz->id, 'version' => 2, 'title' => 'Quiz V2', 'pass_score' => $v1->pass_score,
            'time_limit_minutes' => $v1->time_limit_minutes, 'max_attempts' => $v1->max_attempts,
            'status' => QuizVersion::STATUS_PUBLISHED, 'published_at' => now(),
        ]);
        foreach ($v1->questionMappings as $mapping) {
            QuizVersionQuestion::create(['quiz_version_id' => $v2->id, 'question_id' => $mapping->question_id, 'question_version_id' => $mapping->question_version_id, 'sort_order' => $mapping->sort_order]);
        }
        $quiz->update(['current_published_version_id' => $v2->id]);

        return $v2;
    }

    private function publishedCourse(string $title): Course
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);
        $category = Category::create(['name' => $title.' category', 'slug' => str($title)->slug().'-'.uniqid(), 'status' => true]);
        $course = Course::create(['instructor_id' => $instructor->id, 'category_id' => $category->id, 'title' => $title, 'slug' => str($title)->slug().'-'.uniqid(), 'description' => 'Description', 'price' => 0, 'language' => 'vi', 'status' => Course::STATUS_PUBLISHED, 'is_published' => true, 'published_at' => now()]);
        CourseSection::create(['course_id' => $course->id, 'title' => 'Section', 'sort_order' => 0]);

        return $course->fresh(['courseSections']);
    }

    private function enroll(User $student, Course $course): void
    {
        Enrollment::create(['user_id' => $student->id, 'course_id' => $course->id, 'status' => Enrollment::STATUS_ACTIVE, 'enrolled_at' => now()]);
    }
}
