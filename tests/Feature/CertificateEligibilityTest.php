<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Notifications\CertificateIssuedNotification;
use App\Services\CourseCompletionService;
use App\Services\LearningProgressService;
use App\Services\RoleSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateEligibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(RoleSyncService::class)->ensurePrimaryRolesExist();
    }

    public function test_certificate_is_not_issued_if_videos_or_quizzes_are_incomplete(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor']);

        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Laravel Advanced',
            'slug' => 'laravel-advanced',
            'price' => 199000,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
            'certificate_enabled' => true,
        ]);

        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Chương 1', 'sort_order' => 1]);

        // Video lesson 1
        $video1 = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Video 1',
            'type' => 'video',
            'video_url' => 'https://example.com/1.mp4',
            'duration_seconds' => 100,
            'sort_order' => 1,
            'is_required' => true,
        ]);

        // Video lesson 2
        $video2 = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Video 2',
            'type' => 'video',
            'video_url' => 'https://example.com/2.mp4',
            'duration_seconds' => 100,
            'sort_order' => 2,
            'is_required' => true,
        ]);

        // Quiz lesson
        $quizLesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz 1',
            'type' => 'quiz',
            'sort_order' => 3,
            'is_required' => true,
        ]);

        $quiz = Quiz::create([
            'lesson_id' => $quizLesson->id,
            'title' => 'Quiz trắc nghiệm 1',
            'pass_score' => 80,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress_percent' => 0,
            'enrolled_at' => now(),
        ]);

        // 1. Xem Video 1
        app(LearningProgressService::class)->recordLessonProgress(
            $student->id,
            $course,
            $video1,
            100,
            100,
            true
        );

        // Kiểm tra chưa được cấp chứng chỉ
        $this->assertDatabaseMissing('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
        $this->assertNull($enrollment->fresh()->completed_at);
    }

    public function test_certificate_is_issued_only_after_completing_all_videos_and_quizzes(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor']);

        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Laravel Advanced 2',
            'slug' => 'laravel-advanced-2',
            'price' => 199000,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
            'certificate_enabled' => true,
        ]);

        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Chương 1', 'sort_order' => 1]);

        // Video lesson
        $video = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Video 1',
            'type' => 'video',
            'video_url' => 'https://example.com/1.mp4',
            'duration_seconds' => 100,
            'sort_order' => 1,
            'is_required' => true,
        ]);

        // Quiz lesson
        $quizLesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz 1',
            'type' => 'quiz',
            'sort_order' => 2,
            'is_required' => true,
        ]);

        $quiz = Quiz::create([
            'lesson_id' => $quizLesson->id,
            'title' => 'Quiz trắc nghiệm 1',
            'pass_score' => 80,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress_percent' => 0,
            'enrolled_at' => now(),
        ]);

        // 1. Xem Video -> Chưa có chứng chỉ vì chưa làm Quiz
        app(LearningProgressService::class)->recordLessonProgress(
            $student->id,
            $course,
            $video,
            100,
            100,
            true
        );

        $this->assertDatabaseMissing('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);

        // 2. Làm Quiz nhưng trượt -> Vẫn chưa có chứng chỉ
        QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'score' => 50,
            'passed' => false,
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        // Kích hoạt cập nhật progress để kiểm tra completion
        app(LearningProgressService::class)->recordLessonProgress(
            $student->id,
            $course,
            $quizLesson,
            0,
            null,
            false
        );

        $this->assertDatabaseMissing('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);

        // 3. Làm Quiz đạt điểm đỗ -> Nhận chứng chỉ thành công!
        Notification::fake();
        Storage::fake('local');

        QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'score' => 90,
            'passed' => true,
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        // Kích hoạt cập nhật progress
        app(LearningProgressService::class)->recordLessonProgress(
            $student->id,
            $course,
            $quizLesson,
            0,
            null,
            true // Hoàn thành bài học quiz
        );

        $this->assertDatabaseHas('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
        $this->assertNotNull($enrollment->fresh()->completed_at);

        $certificate = Certificate::query()
            ->where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        $this->assertNotNull($certificate);
        $this->assertNotEmpty($certificate->file_path);
        Storage::disk('local')->assertExists($certificate->file_path);

        Notification::assertSentTo(
            $student,
            CertificateIssuedNotification::class
        );
    }

    public function test_certificate_is_not_issued_when_certificate_disabled(): void
    {
        Notification::fake();
        Storage::fake('local');

        [$student, $course, $enrollment, $video, $quizLesson, $quiz] = $this->makeCompletableCourse(certificateEnabled: false);

        app(LearningProgressService::class)->recordLessonProgress(
            $student->id,
            $course,
            $video,
            100,
            100,
            true
        );

        QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'score' => 100,
            'passed' => true,
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        app(LearningProgressService::class)->recordLessonProgress(
            $student->id,
            $course,
            $quizLesson,
            0,
            null,
            true
        );

        $this->assertNotNull($enrollment->fresh()->completed_at);
        $this->assertDatabaseMissing('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
        Notification::assertNothingSent();
    }

    public function test_email_failure_does_not_prevent_certificate_issue(): void
    {
        Storage::fake('local');

        [$student, $course, $enrollment, $video, $quizLesson, $quiz] = $this->makeCompletableCourse();

        app(LearningProgressService::class)->recordLessonProgress(
            $student->id,
            $course,
            $video,
            100,
            100,
            true
        );

        QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'score' => 100,
            'passed' => true,
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $this->mock(\Illuminate\Contracts\Notifications\Dispatcher::class, function ($mock) {
            $mock->shouldReceive('send')->andThrow(new \RuntimeException('SMTP down'));
        });

        app(LearningProgressService::class)->recordLessonProgress(
            $student->id,
            $course,
            $quizLesson,
            0,
            null,
            true
        );

        $this->assertNotNull($enrollment->fresh()->completed_at);
        $this->assertDatabaseHas('certificates', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $certificate = Certificate::query()
            ->where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        $this->assertNotNull($certificate);
        $this->assertNotEmpty($certificate->file_path);
        Storage::disk('local')->assertExists($certificate->file_path);
    }

    public function test_duplicate_completion_does_not_create_second_certificate(): void
    {
        Notification::fake();
        Storage::fake('local');

        [$student, $course, $enrollment, $video, $quizLesson, $quiz] = $this->makeCompletableCourse();

        app(LearningProgressService::class)->recordLessonProgress(
            $student->id,
            $course,
            $video,
            100,
            100,
            true
        );

        QuizAttempt::create([
            'user_id' => $student->id,
            'quiz_id' => $quiz->id,
            'score' => 100,
            'passed' => true,
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        app(LearningProgressService::class)->recordLessonProgress(
            $student->id,
            $course,
            $quizLesson,
            0,
            null,
            true
        );

        app(CourseCompletionService::class)->check($enrollment->fresh(), $student->id);
        app(CourseCompletionService::class)->check($enrollment->fresh(), $student->id);

        $this->assertSame(1, Certificate::query()
            ->where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->count());
    }

    public function test_student_can_view_certificate_list_page(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
            'two_factor_enabled' => false,
        ]);
        $instructor = User::factory()->create(['role' => 'instructor']);
        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it-list', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web-list', 'parent_id' => $parentCategory->id, 'status' => true]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'List Course',
            'slug' => 'list-course-cert',
            'price' => 0,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
        ]);

        Certificate::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'certificate_code' => 'FEA-LIST01',
            'issued_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('student.certificates'))
            ->assertOk()
            ->assertSee('FEA-LIST01')
            ->assertSee('List Course');
    }

    public function test_student_can_view_certificate_pdf(): void
    {
        Storage::fake('local');

        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor']);
        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Laravel Advanced PDF',
            'slug' => 'laravel-advanced-pdf',
            'price' => 199000,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
        ]);

        $certificate = Certificate::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'certificate_code' => 'FEA-TESTPDF',
            'issued_at' => now(),
        ]);

        // Trả về 403 nếu khách chưa đăng nhập hoặc đăng nhập tài khoản khác
        $otherStudent = User::factory()->create(['role' => 'student']);
        $this->actingAs($otherStudent)
            ->get(route('student.certificates.pdf', $certificate))
            ->assertStatus(403);

        // Trả về 200 và nội dung PDF nếu đúng chủ sở hữu
        $response = $this->actingAs($student)
            ->get(route('student.certificates.pdf', $certificate));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        $certificate->refresh();
        $this->assertNotEmpty($certificate->file_path);
        Storage::disk('local')->assertExists($certificate->file_path);
    }

    public function test_student_can_re_enroll_completed_course(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor']);
        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Re-enrollable Course',
            'slug' => 're-enrollable-course',
            'price' => 0,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_COMPLETED,
            'completed_at' => now(),
            'progress_percent' => 100,
        ]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'title' => 'Test Lesson',
            'type' => 'video',
            'sort_order' => 1,
        ]);

        LessonProgress::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'completed' => true,
        ]);

        $response = $this->actingAs($student)
            ->post(route('courses.enroll', $course));

        $response->assertRedirect();

        $enrollment = $enrollment->fresh();
        $this->assertEquals(Enrollment::STATUS_ACTIVE, $enrollment->status);
        $this->assertEquals(0, (float) $enrollment->progress_percent);
        $this->assertNull($enrollment->completed_at);

        $this->assertDatabaseMissing('lesson_progress', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }

    /**
     * @return array{0: User, 1: Course, 2: Enrollment, 3: Lesson, 4: Lesson, 5: Quiz}
     */
    private function makeCompletableCourse(bool $certificateEnabled = true): array
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor']);
        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it-'.uniqid(), 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web-'.uniqid(), 'parent_id' => $parentCategory->id, 'status' => true]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Completable Course',
            'slug' => 'completable-'.uniqid(),
            'price' => 0,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
            'certificate_enabled' => $certificateEnabled,
        ]);

        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Chương 1', 'sort_order' => 1]);

        $video = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Video 1',
            'type' => 'video',
            'video_url' => 'https://example.com/1.mp4',
            'duration_seconds' => 100,
            'sort_order' => 1,
            'is_required' => true,
        ]);

        $quizLesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz 1',
            'type' => 'quiz',
            'sort_order' => 2,
            'is_required' => true,
        ]);

        $quiz = Quiz::create([
            'lesson_id' => $quizLesson->id,
            'title' => 'Quiz trắc nghiệm 1',
            'pass_score' => 80,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress_percent' => 0,
            'enrolled_at' => now(),
        ]);

        return [$student, $course, $enrollment, $video, $quizLesson, $quiz];
    }
}
