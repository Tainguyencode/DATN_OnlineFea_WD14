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
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestion;
use App\Models\User;
use App\Services\QuizContentService;
use App\Services\QuizAttemptService;
use App\Services\QuizAttemptResultService;
use App\Services\QuizService;
use App\Services\RoleSyncService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class QuizReviewTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        session()->start();
        $this->withHeader('X-CSRF-TOKEN', session()->token());
        app(RoleSyncService::class)->ensurePrimaryRolesExist();
    }

    public function test_student_can_review_own_quiz_attempt(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);

        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);
        $this->approveTeachingField($instructor, $category);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học Laravel Master',
            'slug' => 'khoa-hoc-laravel-master',
            'price' => 0,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Chương 1', 'sort_order' => 1]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Bài Quiz kiểm tra kiến thức',
            'type' => 'quiz',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => 'Bài Quiz 1',
            'pass_score' => 70,
            'is_active' => true,
        ]);

        $question = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Laravel là gì?',
            'type' => 'single',
            'points' => 10,
            'explanation' => 'Laravel là một PHP Web Framework nổi tiếng.',
            'sort_order' => 1,
        ]);

        $optA = QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text' => 'Một PHP Framework',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        $optB = QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text' => 'Một ngôn ngữ lập trình',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        $version = $this->publishQuizVersion($quiz);

        $attempt = QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'quiz_version_id' => $version->id,
            'status' => QuizAttempt::STATUS_COMPLETED,
            'termination_reason' => QuizAttempt::REASON_SUBMITTED,
            'score' => 10,
            'total_score' => 10,
            'percent' => 100,
            'passed' => true,
            'answers' => [$question->id => [$optA->id]],
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $attempt->attemptAnswers()->create([
            'question_id' => $question->id,
            'answer_id' => $optA->id,
            'is_correct' => true,
        ]);

        // Access review as student
        $response = $this->actingAs($student)->get(route('learn.lessons.quiz.attempts.show', [$course->slug, $lesson, $attempt]));

        $response->assertStatus(200);
        $response->assertSee('Xem lại kết quả bài Quiz');
        $response->assertSee('Laravel là gì?');
        $response->assertSee('Một PHP Framework');
        $response->assertSee('ĐẠT YÊU CẦU');
        $response->assertSee('Đáp án đúng và lời giải sẽ được hiển thị khi bạn đã sử dụng hết số lượt làm bài.');
        $response->assertDontSee('Laravel là một PHP Web Framework nổi tiếng.');
    }

    public function test_student_cannot_review_other_students_quiz_attempt(): void
    {
        $studentA = User::factory()->create(['role' => 'student']);
        $studentB = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);

        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);
        $this->approveTeachingField($instructor, $category);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học Vue',
            'slug' => 'khoa-hoc-vue',
            'status' => 'published',
            'is_published' => true,
        ]);

        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Chương 1', 'sort_order' => 1]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz Vue',
            'type' => 'quiz',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => 'Quiz 1',
            'pass_score' => 70,
            'is_active' => true,
        ]);

        $question = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Vue là gì?',
            'type' => 'single',
            'points' => 10,
            'sort_order' => 1,
        ]);
        QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text' => 'Một JavaScript framework',
            'is_correct' => true,
            'sort_order' => 1,
        ]);
        $version = $this->publishQuizVersion($quiz);

        $attemptA = QuizAttempt::create([
            'user_id' => $studentA->id,
            'quiz_id' => $quiz->id,
            'quiz_version_id' => $version->id,
            'score' => 10,
            'total_score' => 10,
            'percent' => 100,
            'passed' => true,
            'answers' => [],
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        // Student B tries to access Student A's attempt
        $response = $this->actingAs($studentB)->get(route('learn.lessons.quiz.attempts.show', [$course->slug, $lesson, $attemptA]));
        $response->assertStatus(403);
    }

    public function test_instructor_can_review_student_quiz_attempt(): void
    {
        $student = User::factory()->create(['role' => 'student', 'name' => 'Nguyễn Văn Học Viên']);
        $instructor = User::factory()->create(['role' => 'instructor', 'name' => 'Thầy Giáo', 'instructor_status' => 'approved']);

        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);
        $this->approveTeachingField($instructor, $category);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học React',
            'slug' => 'khoa-hoc-react',
            'status' => 'published',
            'is_published' => true,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Chương 1', 'sort_order' => 1]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz React',
            'type' => 'quiz',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => 'React Quiz Test',
            'pass_score' => 80,
            'is_active' => true,
        ]);

        $question = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'JSX là gì?',
            'type' => 'single',
            'points' => 10,
            'explanation' => 'JSX là JavaScript XML.',
            'sort_order' => 1,
        ]);

        $optA = QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text' => 'Cú pháp mở rộng của JavaScript',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        $optB = QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text' => 'Một database',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        $version = $this->publishQuizVersion($quiz);

        $attempt = QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'quiz_version_id' => $version->id,
            'score' => 10,
            'total_score' => 10,
            'percent' => 100,
            'passed' => true,
            'answers' => [$question->id => [$optA->id]],
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $attempt->attemptAnswers()->create([
            'question_id' => $question->id,
            'answer_id' => $optA->id,
            'is_correct' => true,
        ]);

        // Instructor reviews student attempt
        $response = $this->actingAs($instructor)->get(route('instructor.courses.students.quiz-attempt', [$course, $student, $quiz, $attempt]));

        $response->assertStatus(200);
        $response->assertSee('Nguyễn Văn Học Viên');
        $response->assertSee('React Quiz Test');
        $response->assertSee('JSX là gì?');
        $response->assertSee('Cú pháp mở rộng của JavaScript');
        $response->assertSee('Học viên chọn đúng');
        $response->assertSee('JSX là JavaScript XML.');
    }

    public function test_other_instructor_cannot_review_quiz_attempt_of_unowned_course(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructorA = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);
        $instructorB = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);

        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);
        $this->approveTeachingField($instructorA, $category);

        $course = Course::create([
            'instructor_id' => $instructorA->id,
            'category_id' => $category->id,
            'title' => 'Khóa học Node',
            'slug' => 'khoa-hoc-node',
            'status' => 'published',
            'is_published' => true,
        ]);

        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Chương 1', 'sort_order' => 1]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz Node',
            'type' => 'quiz',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => 'Quiz Node',
            'pass_score' => 70,
            'is_active' => true,
        ]);

        $attempt = QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'score' => 10,
            'total_score' => 10,
            'percent' => 100,
            'passed' => true,
            'answers' => [],
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        // Instructor B (not owner) tries to review
        $response = $this->actingAs($instructorB)->get(route('instructor.courses.students.quiz-attempt', [$course, $student, $quiz, $attempt]));
        $response->assertStatus(403);
    }

    public function test_restricted_review_displays_wrong_selection_without_correct_answer_or_explanation(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);

        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);
        $this->approveTeachingField($instructor, $category);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học JS',
            'slug' => 'khoa-hoc-js',
            'status' => 'published',
            'is_published' => true,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Chương 1', 'sort_order' => 1]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz JS',
            'type' => 'quiz',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => 'JS Quiz',
            'pass_score' => 80,
            'is_active' => true,
        ]);

        $question = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'typeof null trong JS là gì?',
            'type' => 'single',
            'points' => 10,
            'explanation' => 'Đây là một bug lịch sử trong JS trả về object.',
            'sort_order' => 1,
        ]);

        $optA = QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text' => 'object',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        $optB = QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text' => 'null',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        $version = $this->publishQuizVersion($quiz);

        // Student chose wrong answer optB
        $attempt = QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'quiz_version_id' => $version->id,
            'status' => QuizAttempt::STATUS_COMPLETED,
            'termination_reason' => QuizAttempt::REASON_SUBMITTED,
            'score' => 0,
            'total_score' => 10,
            'percent' => 0,
            'passed' => false,
            'answers' => [$question->id => [$optB->id]],
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $attempt->attemptAnswers()->create([
            'question_id' => $question->id,
            'answer_id' => $optB->id,
            'is_correct' => false,
        ]);

        $response = $this->actingAs($student)->get(route('learn.lessons.quiz.attempts.show', [$course->slug, $lesson, $attempt]));

        $response->assertStatus(200);
        $response->assertSee('CHƯA ĐẠT');
        $response->assertSee('SAI');
        $response->assertSee('Bạn chọn sai');
        $response->assertDontSee('✓ Đáp án đúng', false);
        $response->assertDontSee('Đây là một bug lịch sử trong JS trả về object.');
    }

    public function test_student_submit_ajax_requires_started_attempt(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);

        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);
        $this->approveTeachingField($instructor, $category);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học AJAX Quiz',
            'slug' => 'khoa-hoc-ajax-quiz',
            'status' => 'published',
            'is_published' => true,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Chương 1', 'sort_order' => 1]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz AJAX',
            'type' => 'quiz',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => 'Quiz AJAX',
            'pass_score' => 70,
            'is_active' => true,
        ]);

        $question = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => '1 + 1 = ?',
            'type' => 'single',
            'points' => 10,
            'sort_order' => 1,
        ]);

        $opt = QuizOption::create([
            'quiz_question_id' => $question->id,
            'option_text' => '2',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        $this->publishQuizVersion($quiz);
        $start = $this->actingAs($student)->postJson(route('courses.lessons.quiz.start', [$course, $lesson]));
        $start->assertOk();
        $attemptId = $start->json('attempt.id');

        $response = $this->actingAs($student)->postJson(route('courses.lessons.quiz.submit', [$course, $lesson]), [
            'attempt_id' => $attemptId,
            'answers' => [
                $question->id => $opt->id,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('attempt.review_url', route('learn.lessons.quiz.attempts.show', [$course->slug, $lesson, $attemptId]));
        $response->assertJsonStructure([
            'attempt' => [
                'id',
                'score',
                'total_score',
                'percent',
                'passed',
                'review_url',
            ],
        ]);
    }

    public function test_review_policy_uses_current_limit_and_counts_completed_terminated_and_expired_attempts(): void
    {
        $fixture = $this->reviewFixture(3);
        $completed = $this->persistAttempt($fixture, [], QuizAttempt::STATUS_COMPLETED);
        $this->persistAttempt($fixture, [], QuizAttempt::STATUS_TERMINATED);

        $policy = app(QuizAttemptService::class)->reviewPolicy($completed, $fixture['student']);
        $this->assertSame('restricted', $policy['review_mode']);
        $this->assertSame('attempts_remaining', $policy['review_restriction_reason']);
        $this->assertSame(2, $policy['attempts_used']);
        $this->assertSame(1, $policy['remaining_attempts']);

        $this->persistAttempt($fixture, [], QuizAttempt::STATUS_EXPIRED);
        $policy = app(QuizAttemptService::class)->reviewPolicy($completed->fresh(), $fixture['student']);
        $this->assertSame('full', $policy['review_mode']);
        $this->assertNull($policy['review_restriction_reason']);
        $this->assertSame(3, $policy['attempts_used']);
        $this->assertSame(0, $policy['remaining_attempts']);
    }

    public function test_unlimited_attempts_are_restricted_and_in_progress_review_is_blocked(): void
    {
        $fixture = $this->reviewFixture(null);
        $completed = $this->persistAttempt($fixture, [], QuizAttempt::STATUS_COMPLETED);
        $policy = app(QuizAttemptService::class)->reviewPolicy($completed, $fixture['student']);

        $this->assertSame('restricted', $policy['review_mode']);
        $this->assertTrue($policy['has_remaining_attempts']);
        $this->assertNull($policy['remaining_attempts']);

        $inProgress = $this->persistAttempt($fixture, [], QuizAttempt::STATUS_IN_PROGRESS);
        $this->actingAs($fixture['student'])
            ->get(route('learn.lessons.quiz.attempts.show', [$fixture['course']->slug, $fixture['lesson'], $inProgress]))
            ->assertStatus(409);
    }

    public function test_review_policy_uses_current_published_version_limit_instead_of_attempt_version_limit(): void
    {
        $fixture = $this->reviewFixture(3);
        $attempt = $this->persistAttempt($fixture, []);
        $v2 = QuizVersion::create([
            'quiz_id' => $fixture['quiz']->id,
            'version' => 2,
            'title' => 'Current V2',
            'pass_score' => 70,
            'max_attempts' => 1,
            'status' => QuizVersion::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);
        foreach ($fixture['version']->questionMappings()->orderBy('sort_order')->get() as $mapping) {
            QuizVersionQuestion::create([
                'quiz_version_id' => $v2->id,
                'question_id' => $mapping->question_id,
                'question_version_id' => $mapping->question_version_id,
                'sort_order' => $mapping->sort_order,
            ]);
        }
        $fixture['quiz']->update(['current_published_version_id' => $v2->id]);

        $policy = app(QuizAttemptService::class)->reviewPolicy($attempt->fresh(), $fixture['student']);
        $this->assertSame(1, $policy['max_attempts']);
        $this->assertSame(0, $policy['remaining_attempts']);
        $this->assertSame('full', $policy['review_mode']);
        $this->assertSame($fixture['version']->id, $attempt->fresh()->quiz_version_id);
    }

    public function test_review_fails_closed_when_attempt_has_no_bound_quiz_version(): void
    {
        $fixture = $this->reviewFixture(2);
        $attempt = QuizAttempt::create([
            'user_id' => $fixture['student']->id,
            'quiz_id' => $fixture['quiz']->id,
            'quiz_version_id' => null,
            'status' => QuizAttempt::STATUS_COMPLETED,
            'score' => 0,
            'total_score' => 0,
            'percent' => 0,
            'passed' => false,
            'answers' => [],
            'started_at' => now()->subMinute(),
            'completed_at' => now(),
        ]);

        $this->actingAs($fixture['student'])
            ->get(route('learn.lessons.quiz.attempts.show', [$fixture['course']->slug, $fixture['lesson'], $attempt]))
            ->assertStatus(409);
    }

    public function test_restricted_review_filters_single_multiple_and_unanswered_option_correctness(): void
    {
        $fixture = $this->reviewFixture(2);
        $questions = $fixture['questions'];
        $attempt = $this->persistAttempt($fixture, [
            $questions['single_wrong']->id => $questions['single_wrong']->options->firstWhere('is_correct', false)->id,
            $questions['single_correct']->id => $questions['single_correct']->options->firstWhere('is_correct', true)->id,
            $questions['multiple']->id => [
                $questions['multiple']->options->where('is_correct', true)->first()->id,
                $questions['multiple']->options->firstWhere('is_correct', false)->id,
            ],
        ]);
        $policy = app(QuizAttemptService::class)->reviewPolicy($attempt, $fixture['student']);
        $review = app(QuizService::class)->buildAttemptReview($attempt, $policy);

        $this->assertSame('restricted', $review['review_mode']);
        foreach ($review['questions'] as $question) {
            $this->assertArrayNotHasKey('correct_ids', $question);
            $this->assertArrayNotHasKey('explanation', $question);
            foreach ($question['options'] as $option) {
                $this->assertArrayNotHasKey('is_correct', $option);
                if (! $option['is_selected']) {
                    $this->assertArrayNotHasKey('selected_correct', $option);
                }
            }
        }

        $singleWrong = collect($review['questions'])->firstWhere('id', $questions['single_wrong']->id);
        $wrongSelection = collect($singleWrong['options'])->firstWhere('is_selected', true);
        $this->assertFalse($wrongSelection['selected_correct']);
        $this->assertArrayNotHasKey('selected_correct', collect($singleWrong['options'])->firstWhere('id', $questions['single_wrong']->options->firstWhere('is_correct', true)->id));

        $singleCorrect = collect($review['questions'])->firstWhere('id', $questions['single_correct']->id);
        $this->assertTrue(collect($singleCorrect['options'])->firstWhere('is_selected', true)['selected_correct']);

        $unanswered = collect($review['questions'])->firstWhere('id', $questions['unanswered']->id);
        $this->assertTrue($unanswered['is_unanswered']);

        $multiple = collect($review['questions'])->firstWhere('id', $questions['multiple']->id);
        $selectedResults = collect($multiple['options'])->where('is_selected', true)->pluck('selected_correct')->sort()->values()->all();
        $this->assertSame([false, true], $selectedResults);
        $unselectedCorrectId = $questions['multiple']->options->where('is_correct', true)->pluck('id')
            ->first(fn ($id) => ! in_array($id, $multiple['selected_ids'], true));
        $this->assertArrayNotHasKey('selected_correct', collect($multiple['options'])->firstWhere('id', $unselectedCorrectId));

        $historicalResult = app(QuizAttemptResultService::class)->forLearner(
            $fixture['course'],
            $fixture['lesson'],
            $fixture['student'],
            $attempt,
        );
        $this->assertSame('restricted', $historicalResult['review_mode']);
        foreach ($historicalResult['questions'] as $question) {
            $this->assertArrayNotHasKey('explanation', $question);
            foreach ($question['options'] as $option) {
                $this->assertArrayNotHasKey('is_correct', $option);
            }
        }

        $this->actingAs($fixture['student'])
            ->get(route('learn.lessons.quiz.attempts.show', [$fixture['course']->slug, $fixture['lesson'], $attempt]))
            ->assertOk()
            ->assertSee('Bạn chọn đúng')
            ->assertSee('Bạn chọn sai')
            ->assertSee('CHƯA TRẢ LỜI')
            ->assertDontSee('Review secret explanation')
            ->assertDontSee('✓ Đáp án đúng', false);
    }

    public function test_exhausted_terminated_attempt_remains_restricted_without_answer_key(): void
    {
        $fixture = $this->reviewFixture(3);
        $questions = $fixture['questions'];
        $wrongId = $questions['single_wrong']->options->firstWhere('is_correct', false)->id;

        $this->persistAttempt($fixture, []);
        $this->persistAttempt($fixture, []);
        $terminated = $this->persistAttempt($fixture, [
            $questions['single_wrong']->id => $wrongId,
        ], QuizAttempt::STATUS_TERMINATED);

        $policy = app(QuizAttemptService::class)->reviewPolicy($terminated, $fixture['student']);
        $this->assertSame('restricted', $policy['review_mode']);
        $this->assertSame('abnormal_end', $policy['review_restriction_reason']);
        $this->assertSame(3, $policy['attempts_used']);
        $this->assertSame(0, $policy['remaining_attempts']);
        $this->assertFalse($policy['has_remaining_attempts']);

        $review = app(QuizService::class)->buildAttemptReview($terminated, $policy);
        $this->assertSame('restricted', $review['review_mode']);
        $this->assertSame('abnormal_end', $review['review_restriction_reason']);

        foreach ($review['questions'] as $question) {
            $this->assertArrayNotHasKey('correct_ids', $question);
            $this->assertArrayNotHasKey('explanation', $question);

            foreach ($question['options'] as $option) {
                $this->assertArrayNotHasKey('is_correct', $option);
                if (! $option['is_selected']) {
                    $this->assertArrayNotHasKey('selected_correct', $option);
                }
            }
        }

        $wrongQuestion = collect($review['questions'])->firstWhere('id', $questions['single_wrong']->id);
        $selectedWrong = collect($wrongQuestion['options'])->firstWhere('is_selected', true);
        $unselectedCorrect = collect($wrongQuestion['options'])->firstWhere('id', $questions['single_wrong']->options->firstWhere('is_correct', true)->id);
        $this->assertFalse($selectedWrong['selected_correct']);
        $this->assertArrayNotHasKey('selected_correct', $unselectedCorrect);

        $unanswered = collect($review['questions'])->firstWhere('id', $questions['unanswered']->id);
        $this->assertTrue($unanswered['is_unanswered']);

        $this->actingAs($fixture['student'])
            ->get(route('learn.lessons.quiz.attempts.show', [$fixture['course']->slug, $fixture['lesson'], $terminated]))
            ->assertOk()
            ->assertSee('Lượt làm bài này kết thúc bất thường nên đáp án đúng và lời giải không được hiển thị.')
            ->assertSee('Bạn chọn sai')
            ->assertSee('CHƯA TRẢ LỜI')
            ->assertDontSee('✓ Đáp án đúng', false)
            ->assertDontSee('Review secret explanation');
    }

    public function test_exhausted_expired_attempt_remains_restricted(): void
    {
        $fixture = $this->reviewFixture(1);
        $expired = $this->persistAttempt($fixture, [], QuizAttempt::STATUS_EXPIRED);

        $policy = app(QuizAttemptService::class)->reviewPolicy($expired, $fixture['student']);

        $this->assertSame('restricted', $policy['review_mode']);
        $this->assertSame('abnormal_end', $policy['review_restriction_reason']);
        $this->assertSame(0, $policy['remaining_attempts']);

        $review = app(QuizService::class)->buildAttemptReview($expired, $policy);
        foreach ($review['questions'] as $question) {
            $this->assertArrayNotHasKey('correct_ids', $question);
            $this->assertArrayNotHasKey('explanation', $question);
        }
    }

    public function test_last_attempt_termination_json_is_restricted_and_contains_no_answer_key(): void
    {
        $fixture = $this->reviewFixture(1);
        $question = $fixture['questions']['single_wrong'];
        $wrongId = $question->options->firstWhere('is_correct', false)->id;
        $start = $this->actingAs($fixture['student'])
            ->postJson(route('courses.lessons.quiz.start', [$fixture['course'], $fixture['lesson']]))
            ->assertOk();

        $response = $this->actingAs($fixture['student'])->postJson(
            route('courses.lessons.quiz.terminate', [$fixture['course'], $fixture['lesson']]),
            [
                'attempt_id' => $start->json('attempt.id'),
                'reason' => QuizAttempt::REASON_TAB_SWITCH,
                'answers' => [$question->id => $wrongId],
            ],
        )->assertOk()
            ->assertJsonPath('terminated', true)
            ->assertJsonPath('review_mode', 'restricted')
            ->assertJsonPath('attempts_count', 1)
            ->assertJsonPath('remaining_attempts', 0);

        $this->assertFalse($this->arrayContainsKeyRecursive($response->json(), 'correct_ids'));
        $this->assertFalse($this->arrayContainsKeyRecursive($response->json(), 'explanation'));
        $this->assertFalse($this->arrayContainsKeyRecursive($response->json(), 'is_correct'));
    }

    public function test_instructor_and_admin_receive_full_review_for_terminated_attempt(): void
    {
        $fixture = $this->reviewFixture(1);
        $question = $fixture['questions']['single_wrong'];
        $terminated = $this->persistAttempt($fixture, [
            $question->id => $question->options->firstWhere('is_correct', false)->id,
        ], QuizAttempt::STATUS_TERMINATED);
        $admin = User::factory()->create(['role' => 'admin']);

        foreach ([$fixture['instructor'], $admin] as $staff) {
            $policy = app(QuizAttemptService::class)->reviewPolicy($terminated->fresh(), $staff);
            $this->assertSame('full', $policy['review_mode']);
            $this->assertNull($policy['review_restriction_reason']);
        }

        $this->actingAs($fixture['instructor'])
            ->get(route('instructor.courses.students.quiz-attempt', [
                $fixture['course'],
                $fixture['student'],
                $fixture['quiz'],
                $terminated,
            ]))
            ->assertOk()
            ->assertSee('✓ Đáp án đúng', false)
            ->assertSee('Review secret explanation');

        $this->actingAs($admin)
            ->get(route('learn.lessons.quiz.attempts.show', [$fixture['course']->slug, $fixture['lesson'], $terminated]))
            ->assertOk()
            ->assertSee('✓ Đáp án đúng', false)
            ->assertSee('Review secret explanation');
    }

    public function test_submit_json_is_sanitized_while_retry_remains_and_full_after_limit(): void
    {
        $fixture = $this->reviewFixture(1);
        $question = $fixture['questions']['single_wrong'];
        $start = $this->actingAs($fixture['student'])->postJson(route('courses.lessons.quiz.start', [$fixture['course'], $fixture['lesson']]));
        $attemptId = $start->json('attempt.id');

        $fullResponse = $this->actingAs($fixture['student'])->postJson(route('courses.lessons.quiz.submit', [$fixture['course'], $fixture['lesson']]), [
            'attempt_id' => $attemptId,
            'answers' => [$question->id => $question->options->firstWhere('is_correct', false)->id],
        ])->assertOk()->assertJsonPath('review_mode', 'full')->assertJsonPath('remaining_attempts', 0);
        $this->assertTrue($this->arrayContainsKeyRecursive($fullResponse->json('graded'), 'correct_ids'));

        $fixture = $this->reviewFixture(2);
        $question = $fixture['questions']['single_wrong'];
        $start = $this->actingAs($fixture['student'])->postJson(route('courses.lessons.quiz.start', [$fixture['course'], $fixture['lesson']]));
        $attemptId = $start->json('attempt.id');
        $answers = [$question->id => $question->options->firstWhere('is_correct', false)->id];

        $restricted = $this->actingAs($fixture['student'])->postJson(route('courses.lessons.quiz.submit', [$fixture['course'], $fixture['lesson']]), [
            'attempt_id' => $attemptId,
            'answers' => $answers,
        ])->assertOk()->assertJsonPath('review_mode', 'restricted')->assertJsonPath('remaining_attempts', 1);
        $this->assertFalse($this->arrayContainsKeyRecursive($restricted->json(), 'correct_ids'));
        $this->assertFalse($this->arrayContainsKeyRecursive($restricted->json(), 'explanation'));

        $learnResponse = $this->actingAs($fixture['student'])->postJson(route('learn.lessons.quiz.submit', [$fixture['course']->slug, $fixture['lesson']]), [
            'attempt_id' => $attemptId,
            'answers' => $answers,
        ])->assertOk()->assertJsonPath('review_mode', 'restricted');
        $this->assertFalse($this->arrayContainsKeyRecursive($learnResponse->json(), 'correct_ids'));
    }

    public function test_full_review_exposes_correct_answers_and_explanation_to_exhausted_learner_and_admin(): void
    {
        $fixture = $this->reviewFixture(1);
        $question = $fixture['questions']['single_wrong'];
        $attempt = $this->persistAttempt($fixture, [
            $question->id => $question->options->firstWhere('is_correct', false)->id,
        ]);
        $policy = app(QuizAttemptService::class)->reviewPolicy($attempt, $fixture['student']);
        $this->assertSame('full', $policy['review_mode']);
        $this->assertNull($policy['review_restriction_reason']);

        $learnerResponse = $this->actingAs($fixture['student'])
            ->get(route('learn.lessons.quiz.attempts.show', [$fixture['course']->slug, $fixture['lesson'], $attempt]))
            ->assertOk()
            ->assertSee('✓ Đáp án đúng', false)
            ->assertSee('Review secret explanation');

        $this->assertStringNotContainsString('Đáp án đúng và lời giải sẽ được hiển thị', $learnerResponse->getContent());
        $this->actingAs($fixture['student'])
            ->get(route('learn.lessons.quiz.result', [$fixture['course']->slug, $fixture['lesson'], $attempt]))
            ->assertOk()
            ->assertSee('Dap an dung')
            ->assertSee('Review secret explanation');

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get(route('learn.lessons.quiz.attempts.show', [$fixture['course']->slug, $fixture['lesson'], $attempt]))
            ->assertOk()
            ->assertSee('✓ Đáp án đúng', false)
            ->assertSee('Review secret explanation');
    }

    /** @return array<string, mixed> */
    private function reviewFixture(?int $maxAttempts): array
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);
        $parent = Category::create(['name' => 'Review Parent', 'slug' => 'review-parent-'.uniqid(), 'status' => true]);
        $category = Category::create(['name' => 'Review Child', 'slug' => 'review-child-'.uniqid(), 'parent_id' => $parent->id, 'status' => true]);
        $this->approveTeachingField($instructor, $category);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Restricted Review Course',
            'slug' => 'restricted-review-'.uniqid(),
            'status' => 'published',
            'is_published' => true,
        ]);
        Enrollment::create(['user_id' => $student->id, 'course_id' => $course->id, 'status' => 'active']);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Review section', 'sort_order' => 1]);
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Review quiz lesson',
            'type' => 'quiz',
            'status' => 'published',
            'sort_order' => 1,
        ]);
        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => 'Restricted Review Quiz',
            'pass_score' => 70,
            'max_attempts' => $maxAttempts,
            'is_active' => true,
        ]);

        $questions = [];
        foreach ([
            'single_wrong' => QuizQuestion::TYPE_SINGLE,
            'multiple' => QuizQuestion::TYPE_MULTIPLE,
            'unanswered' => QuizQuestion::TYPE_SINGLE,
            'single_correct' => QuizQuestion::TYPE_SINGLE,
        ] as $key => $type) {
            $question = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => 'Review '.$key,
                'type' => $type,
                'points' => 10,
                'explanation' => 'Review secret explanation '.$key,
                'sort_order' => count($questions),
            ]);
            QuizOption::create(['quiz_question_id' => $question->id, 'option_text' => $key.' correct A', 'is_correct' => true, 'sort_order' => 0]);
            QuizOption::create(['quiz_question_id' => $question->id, 'option_text' => $key.' correct B', 'is_correct' => $type === QuizQuestion::TYPE_MULTIPLE, 'sort_order' => 1]);
            QuizOption::create(['quiz_question_id' => $question->id, 'option_text' => $key.' wrong', 'is_correct' => false, 'sort_order' => 2]);
            $questions[$key] = $question;
        }

        $version = $this->publishQuizVersion($quiz, $maxAttempts);
        foreach ($questions as $key => $question) {
            $questions[$key] = $question->fresh('options');
        }

        return compact('student', 'instructor', 'course', 'lesson', 'quiz', 'version', 'questions');
    }

    private function approveTeachingField(User $instructor, Category $category): void
    {
        $profile = InstructorProfile::create([
            'user_id' => $instructor->id,
            'category_id' => $category->id,
        ]);
        $profile->teachingCategories()->attach($category->id, [
            'is_primary' => true,
            'approval_status' => InstructorTeachingField::STATUS_APPROVED,
        ]);
    }

    private function persistAttempt(array $fixture, array $answers, string $status = QuizAttempt::STATUS_COMPLETED): QuizAttempt
    {
        $graded = app(QuizService::class)->grade($fixture['version'], $answers);
        $attempt = QuizAttempt::create([
            'user_id' => $fixture['student']->id,
            'quiz_id' => $fixture['quiz']->id,
            'quiz_version_id' => $fixture['version']->id,
            'status' => $status,
            'termination_reason' => $status === QuizAttempt::STATUS_TERMINATED
                ? QuizAttempt::REASON_TAB_SWITCH
                : ($status === QuizAttempt::STATUS_EXPIRED ? QuizAttempt::REASON_TIME_EXPIRED : QuizAttempt::REASON_SUBMITTED),
            'score' => $graded['score'],
            'total_score' => $graded['total_score'],
            'percent' => $graded['percent'],
            'passed' => $graded['passed'],
            'answers' => $graded['answers'],
            'started_at' => now()->subMinute(),
            'completed_at' => $status === QuizAttempt::STATUS_IN_PROGRESS ? null : now(),
        ]);

        if ($status !== QuizAttempt::STATUS_IN_PROGRESS) {
            foreach ($graded['questions'] as $questionId => $result) {
                $selectedIds = $result['selected_ids'] ?: [null];
                foreach ($selectedIds as $answerId) {
                    $attempt->attemptAnswers()->create([
                        'question_id' => (int) $questionId,
                        'question_version_id' => (int) $result['question_version_id'],
                        'answer_id' => $answerId,
                        'is_correct' => (bool) $result['is_correct'],
                    ]);
                }
            }
        }

        return $attempt->fresh(['quiz', 'quizVersion', 'attemptAnswers']);
    }

    private function arrayContainsKeyRecursive(mixed $value, string $needle): bool
    {
        if (! is_array($value)) {
            return false;
        }

        if (array_key_exists($needle, $value)) {
            return true;
        }

        foreach ($value as $child) {
            if ($this->arrayContainsKeyRecursive($child, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function publishQuizVersion(Quiz $quiz, ?int $maxAttempts = null): QuizVersion
    {
        $questions = $quiz->questions()->with('options')->orderBy('sort_order')->get();
        for ($sortOrder = $questions->count(); $sortOrder < QuizContentService::MIN_QUESTIONS; $sortOrder++) {
            $question = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => "Bổ sung câu hỏi {$sortOrder}",
                'type' => 'single',
                'points' => 1,
                'sort_order' => $sortOrder,
            ]);
            QuizOption::create([
                'quiz_question_id' => $question->id,
                'option_text' => 'Đáp án đúng',
                'is_correct' => true,
                'sort_order' => 0,
            ]);
        }
        $questions = $quiz->questions()->with('options')->orderBy('sort_order')->get();

        foreach ($questions as $question) {
            for ($sortOrder = $question->options->count(); $sortOrder < QuizContentService::MIN_OPTIONS; $sortOrder++) {
                QuizOption::create([
                    'quiz_question_id' => $question->id,
                    'option_text' => "Đáp án bổ sung {$question->id}-{$sortOrder}",
                    'is_correct' => false,
                    'sort_order' => $sortOrder,
                ]);
            }
        }
        $questions = $quiz->questions()->with('options')->orderBy('sort_order')->get();

        $version = QuizVersion::create([
            'quiz_id' => $quiz->id,
            'version' => 1,
            'title' => $quiz->title,
            'pass_score' => $quiz->pass_score,
            'max_attempts' => $maxAttempts,
            'status' => QuizVersion::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        foreach ($questions as $index => $question) {
            $questionVersion = QuestionVersion::create([
                'question_id' => $question->id,
                'version' => 1,
                'question' => $question->question,
                'type' => $question->type,
                'points' => $question->points,
                'explanation' => $question->explanation,
                'status' => QuestionVersion::STATUS_PUBLISHED,
                'published_at' => now(),
            ]);
            $question->options()->update(['question_version_id' => $questionVersion->id]);
            QuizVersionQuestion::create([
                'quiz_version_id' => $version->id,
                'question_id' => $question->id,
                'question_version_id' => $questionVersion->id,
                'sort_order' => $index,
            ]);
        }

        $quiz->update(['current_published_version_id' => $version->id]);

        return $version;
    }
}
