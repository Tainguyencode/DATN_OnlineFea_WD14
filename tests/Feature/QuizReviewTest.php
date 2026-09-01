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
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestion;
use App\Models\User;
use App\Services\QuizContentService;
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
        $response->assertSee('Laravel là một PHP Web Framework nổi tiếng.');
    }

    public function test_student_cannot_review_other_students_quiz_attempt(): void
    {
        $studentA = User::factory()->create(['role' => 'student']);
        $studentB = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);

        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);

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
    }

    public function test_other_instructor_cannot_review_quiz_attempt_of_unowned_course(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructorA = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);
        $instructorB = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);

        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);

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

    public function test_review_displays_wrong_answer_and_correct_answer_clearly(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);

        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);

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
        $response->assertSee('Đáp án đúng');
        $response->assertSee('Đây là một bug lịch sử trong JS trả về object.');
    }

    public function test_student_submit_ajax_requires_started_attempt(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);

        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);

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

    private function publishQuizVersion(Quiz $quiz): QuizVersion
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
