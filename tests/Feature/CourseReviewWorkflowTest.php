<?php

namespace Tests\Feature;

use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use App\Services\CourseReviewService;
use App\Services\CourseValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_access_admin_course_reviews(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);

        $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('admin.course-reviews.index'))
            ->assertRedirect($student->dashboardUrl());
    }

    public function test_instructor_cannot_edit_other_instructors_course(): void
    {
        $owner = User::factory()->create(['role' => 'instructor']);
        $other = User::factory()->create(['role' => 'instructor', 'email_verified_at' => now()]);
        $course = $this->makeDraftCourse($owner);

        $this->actingAs($other)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->put(route('instructor.courses.update', $course), $this->coursePayload())
            ->assertForbidden();
    }

    public function test_incomplete_course_can_be_submitted(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Incomplete',
            'slug' => 'incomplete',
            'price' => 100000,
            'language' => 'vi',
            'status' => CourseStatus::Draft->value,
            'is_published' => false,
            'copyright_agreed' => true,
        ]);

        $validation = app(CourseValidationService::class)->validateForSubmission($course);

        $this->assertTrue($validation['eligible']);
        $this->assertEmpty($validation['errors']);

        $review = app(CourseReviewService::class)->submitForReview($course, $instructor);

        $this->assertEquals(CourseStatus::PendingReview->value, $course->fresh()->status);
        $this->assertEquals(1, $review->submission_number);
    }

    public function test_course_with_enough_content_can_be_submitted(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $course = $this->makeSubmittableCourse($instructor);

        $review = app(CourseReviewService::class)->submitForReview($course, $instructor);

        $this->assertEquals(CourseStatus::PendingReview->value, $course->fresh()->status);
        $this->assertEquals(1, $review->submission_number);
    }

    public function test_admin_can_approve_course(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $instructor = User::factory()->create(['role' => 'instructor']);
        $course = $this->makeSubmittableCourse($instructor);
        app(CourseReviewService::class)->submitForReview($course, $instructor);

        $checklist = collect(config('course.admin_review_checklist'))->mapWithKeys(fn ($l, $k) => [$k => true])->all();

        app(CourseReviewService::class)->approve($course->fresh(), $admin, $checklist, true);

        $this->assertEquals(CourseStatus::Published->value, $course->fresh()->status);
        $this->assertTrue($course->fresh()->is_published);
    }

    public function test_admin_reject_requires_minimum_comment_length(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $instructor = User::factory()->create(['role' => 'instructor']);
        $course = $this->makeSubmittableCourse($instructor);
        app(CourseReviewService::class)->submitForReview($course, $instructor);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        app(CourseReviewService::class)->reject($course->fresh(), $admin, 'short');
    }

    public function test_reject_creates_history_and_instructor_can_see_reason(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $instructor = User::factory()->create(['role' => 'instructor']);
        $course = $this->makeSubmittableCourse($instructor);
        app(CourseReviewService::class)->submitForReview($course, $instructor);

        app(CourseReviewService::class)->reject($course->fresh(), $admin, 'Video bài 2 không có âm thanh rõ ràng.');

        $course->refresh();
        $this->assertEquals(CourseStatus::Rejected->value, $course->status);
        $this->assertStringContainsString('âm thanh', $course->rejectionReasonText());
        $this->assertEquals(1, $course->courseReviews()->where('status', 'rejected')->count());
    }

    public function test_review_history_is_not_overwritten_on_resubmit(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $instructor = User::factory()->create(['role' => 'instructor']);
        $course = $this->makeSubmittableCourse($instructor);

        app(CourseReviewService::class)->submitForReview($course, $instructor);
        app(CourseReviewService::class)->reject($course->fresh(), $admin, 'Cần bổ sung mô tả chi tiết hơn cho bài học.');

        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);

        $this->assertEquals(2, $course->fresh()->courseReviews()->count());
        $this->assertEquals(2, $course->fresh()->submission_count);
    }

    private function makeDraftCourse(User $instructor): Course
    {
        return Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => Category::create(['name' => 'Test', 'slug' => 'test-'.uniqid()])->id,
            'title' => 'Draft Course',
            'slug' => 'draft-course-'.uniqid(),
            'short_description' => 'Short',
            'description' => 'Description long enough',
            'objectives' => 'Learn things',
            'target_audience' => 'Students',
            'requirements' => 'None',
            'thumbnail' => 'thumb.png',
            'price' => 100000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => CourseStatus::Draft->value,
            'is_published' => false,
            'copyright_agreed' => true,
        ]);
    }

    private function makeSubmittableCourse(User $instructor): Course
    {
        $course = $this->makeDraftCourse($instructor);
        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Section 1',
            'sort_order' => 1,
        ]);

        foreach (range(1, 3) as $i) {
            Lesson::create([
                'course_id' => $course->id,
                'section_id' => $section->id,
                'title' => "Lesson {$i}",
                'type' => 'video',
                'video_url' => 'https://example.com/video.mp4',
                'duration_seconds' => 300,
                'content' => 'Lesson content',
                'sort_order' => $i,
                'is_required' => true,
            ]);
        }

        return $course->fresh(['courseSections.lessons']);
    }

    private function coursePayload(): array
    {
        return [
            'title' => 'Updated',
            'price' => 200000,
            'language' => 'vi',
            'level' => 'beginner',
        ];
    }

    public function test_cannot_submit_course_for_review_without_copyright_agreement(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $course = $this->makeSubmittableCourse($instructor);
        $course->update(['copyright_agreed' => false]);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Bạn phải đồng ý với cam kết bản quyền trước khi gửi duyệt.');

        app(CourseReviewService::class)->submitForReview($course, $instructor);
    }

    public function test_controller_validates_copyright_agreement_input(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'email_verified_at' => now()]);
        $course = $this->makeSubmittableCourse($instructor);

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.courses.submit', $course), [])
            ->assertSessionHasErrors('copyright_agreed');
    }
}
