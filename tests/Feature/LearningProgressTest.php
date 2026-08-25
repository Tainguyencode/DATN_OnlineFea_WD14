<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Services\LearningProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningProgressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        session()->start();
        $this->withHeader('X-CSRF-TOKEN', session()->token());
    }

    public function test_student_without_enrollment_cannot_update_progress(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'watched_seconds' => 100,
                'duration_seconds' => 300,
            ])
            ->assertForbidden();
    }

    public function test_enrolled_student_can_update_progress(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'watched_seconds' => 240,
                'duration_seconds' => 300,
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['lesson_progress', 'course_progress', 'lesson_completed', 'course_completed']);
    }

    public function test_video_progress_saves_last_position_without_completing_from_seek(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();
        $this->enroll($student, $course);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'last_position_seconds' => 295,
                'furthest_position_seconds' => 295,
                'played_seconds' => 0,
                'video_duration_seconds' => 300,
                'client_updated_at' => now()->toIso8601String(),
            ])
            ->assertOk()
            ->assertJsonPath('lesson_completed', false);

        $progress = LessonProgress::where('user_id', $student->id)->where('lesson_id', $lesson->id)->firstOrFail();

        $this->assertSame(295, $progress->last_position_seconds);
        $this->assertSame(0, $progress->watched_seconds);
        $this->assertFalse($progress->is_completed);
    }

    public function test_furthest_position_does_not_decrease(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();
        $this->enroll($student, $course);

        LessonProgress::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'watched_seconds' => 120,
            'duration_seconds' => 300,
            'last_position_seconds' => 120,
            'furthest_position_seconds' => 120,
            'progress_percent' => 40,
            'last_watched_at' => now()->subSeconds(20),
            'last_client_updated_at' => now()->subSeconds(20),
        ]);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'last_position_seconds' => 60,
                'furthest_position_seconds' => 60,
                'played_seconds' => 5,
                'video_duration_seconds' => 300,
                'client_updated_at' => now()->toIso8601String(),
            ])
            ->assertOk();

        $this->assertSame(120, LessonProgress::first()->furthest_position_seconds);
    }

    public function test_video_reaches_ninety_percent_after_played_time(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse(['required_video_percent' => 90]);
        $lesson = $course->lessons->first();
        $this->enroll($student, $course);

        LessonProgress::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'watched_seconds' => 260,
            'duration_seconds' => 300,
            'last_position_seconds' => 260,
            'furthest_position_seconds' => 260,
            'progress_percent' => 86.67,
            'last_watched_at' => now()->subSeconds(20),
            'last_client_updated_at' => now()->subSeconds(20),
        ]);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'last_position_seconds' => 270,
                'furthest_position_seconds' => 270,
                'played_seconds' => 10,
                'video_duration_seconds' => 300,
                'client_updated_at' => now()->toIso8601String(),
            ])
            ->assertOk()
            ->assertJsonPath('lesson_completed', true);

        $this->assertTrue(LessonProgress::first()->is_completed);
    }

    public function test_completed_lesson_is_not_reverted_by_later_progress(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();
        $this->enroll($student, $course);

        LessonProgress::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'watched_seconds' => 300,
            'duration_seconds' => 300,
            'last_position_seconds' => 300,
            'furthest_position_seconds' => 300,
            'progress_percent' => 100,
            'is_completed' => true,
            'completed_at' => now()->subMinute(),
            'last_watched_at' => now()->subMinute(),
            'last_client_updated_at' => now()->subMinute(),
        ]);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'last_position_seconds' => 10,
                'furthest_position_seconds' => 10,
                'played_seconds' => 0,
                'video_duration_seconds' => 300,
                'client_updated_at' => now()->toIso8601String(),
            ])
            ->assertOk()
            ->assertJsonPath('lesson_completed', true);
    }

    public function test_stale_request_does_not_overwrite_newer_progress(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();
        $this->enroll($student, $course);

        LessonProgress::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'watched_seconds' => 100,
            'duration_seconds' => 300,
            'last_position_seconds' => 100,
            'furthest_position_seconds' => 100,
            'progress_percent' => 33.33,
            'last_watched_at' => now(),
            'last_client_updated_at' => now(),
        ]);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'last_position_seconds' => 20,
                'furthest_position_seconds' => 20,
                'played_seconds' => 10,
                'video_duration_seconds' => 300,
                'client_updated_at' => now()->subMinute()->toIso8601String(),
            ])
            ->assertStatus(409);

        $this->assertSame(100, LessonProgress::first()->last_position_seconds);
    }

    public function test_document_lesson_can_be_manually_completed(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $course->courseSections->first()->id,
            'title' => 'Doc',
            'type' => 'document',
            'content' => 'Read me',
            'sort_order' => 2,
            'is_required' => true,
            'status' => 'published',
        ]);
        $this->enroll($student, $course);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'completed' => true,
            ])
            ->assertOk()
            ->assertJsonPath('lesson_completed', true);
    }

    public function test_quiz_lesson_is_completed_after_submission_even_when_not_passed(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $course->courseSections->first()->id,
            'title' => 'Quiz',
            'type' => 'quiz',
            'content' => 'Quiz',
            'sort_order' => 2,
            'is_required' => true,
            'status' => 'published',
        ]);
        $quiz = Quiz::create(['lesson_id' => $lesson->id, 'title' => 'Quiz', 'pass_score' => 100, 'is_active' => true]);
        $question = QuizQuestion::create(['quiz_id' => $quiz->id, 'question' => 'Q', 'type' => 'single', 'points' => 1]);
        $wrong = QuizOption::create(['quiz_question_id' => $question->id, 'option_text' => 'Wrong', 'is_correct' => false]);
        QuizOption::create(['quiz_question_id' => $question->id, 'option_text' => 'Right', 'is_correct' => true]);
        QuizOption::create(['quiz_question_id' => $question->id, 'option_text' => 'Other', 'is_correct' => false]);

        for ($index = 2; $index <= 5; $index++) {
            $extraQuestion = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => 'Q'.$index,
                'type' => 'single',
                'points' => 1,
                'sort_order' => $index,
            ]);
            QuizOption::create(['quiz_question_id' => $extraQuestion->id, 'option_text' => 'A'.$index, 'is_correct' => true]);
            QuizOption::create(['quiz_question_id' => $extraQuestion->id, 'option_text' => 'B'.$index, 'is_correct' => false]);
            QuizOption::create(['quiz_question_id' => $extraQuestion->id, 'option_text' => 'C'.$index, 'is_correct' => false]);
        }
        $this->enroll($student, $course);

        $this->actingAs($student)
            ->postJson(route('courses.lessons.quiz.submit', [$course, $lesson]), [
                'answers' => [$question->id => $wrong->id],
            ])
            ->assertOk()
            ->assertJsonPath('lesson_completed', true);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'is_completed' => true,
        ]);
    }

    public function test_progress_does_not_exceed_one_hundred_percent(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $result = app(LearningProgressService::class)->recordLessonProgress(
            $student->id,
            $course,
            $lesson,
            9999,
            300,
            false,
        );

        $this->assertLessThanOrEqual(100, $result['lesson_progress']);
    }

    public function test_completed_enrollment_can_still_access_learning(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_COMPLETED,
            'progress_percent' => 100,
            'completed_at' => now(),
            'enrolled_at' => now()->subDays(7),
        ]);

        $this->actingAs($student)
            ->get(route('courses.lessons.show', [$course, $lesson]))
            ->assertOk();

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'watched_seconds' => 120,
                'duration_seconds' => 300,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    private function publishedCourse(array $attributes = []): Course
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
        ]);
        $course = Course::create(array_merge([
            'instructor_id' => $instructor->id,
            'category_id' => Category::create(['name' => 'Test', 'slug' => 'test-'.uniqid()])->id,
            'title' => 'Published',
            'slug' => 'published-'.uniqid(),
            'short_description' => 'Short',
            'description' => 'Desc',
            'thumbnail' => 't.png',
            'price' => 0,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
        ], $attributes));

        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'S1', 'sort_order' => 1]);
        Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'L1',
            'type' => 'video',
            'video_url' => 'https://example.com/v.mp4',
            'duration_seconds' => 300,
            'content' => 'content',
            'sort_order' => 1,
            'is_required' => true,
            'status' => 'published',
        ]);

        return $course->fresh(['lessons', 'courseSections.lessons']);
    }

    private function enroll(User $student, Course $course): Enrollment
    {
        return Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);
    }
}
