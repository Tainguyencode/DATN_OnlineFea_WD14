<?php

namespace Tests\Feature;

use App\Enums\ReviewStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseFeedbackReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_review(): void
    {
        $course = $this->course();

        $this->post(route('courses.reviews.store', $course), $this->payload())
            ->assertRedirect(route('login'));
    }

    public function test_student_without_enrollment_cannot_create_review(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->course();

        $this->actingAs($student)->post(route('courses.reviews.store', $course), $this->payload())->assertForbidden();
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_student_must_start_learning_before_reviewing(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $this->enroll($student, $course, 0);

        $this->actingAs($student)->post(route('courses.reviews.store', $course), $this->payload())->assertForbidden();
    }

    public function test_enrolled_student_can_create_pending_review(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $this->enroll($student, $course);

        $this->actingAs($student)
            ->post(route('courses.reviews.store', $course), $this->payload())
            ->assertRedirect(route('courses.show', $course->slug).'#reviews');

        $this->assertDatabaseHas('reviews', [
            'course_id' => $course->id,
            'user_id' => $student->id,
            'rating' => 5,
            'status' => 'pending',
            'verified_purchase' => true,
        ]);
        $this->assertSame(0, $course->fresh()->rating_count);
    }

    public function test_rating_content_and_html_are_validated(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $this->enroll($student, $course);

        $this->actingAs($student)->post(route('courses.reviews.store', $course), ['rating' => 0, 'comment' => 'quá ngắn'])->assertSessionHasErrors(['rating', 'comment']);
        $this->actingAs($student)->post(route('courses.reviews.store', $course), ['rating' => 6, 'comment' => '<script>alert(1)</script> nội dung dài'])->assertSessionHasErrors(['rating', 'comment']);
    }

    public function test_unique_index_and_service_prevent_duplicate_review(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $this->enroll($student, $course);

        $this->actingAs($student)->post(route('courses.reviews.store', $course), $this->payload())->assertSessionHasNoErrors();
        $this->actingAs($student)->post(route('courses.reviews.store', $course), $this->payload(4))->assertSessionHasErrors('rating');
        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_owner_can_update_review_and_reply_is_preserved(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $this->enroll($student, $course);
        $review = $this->review($student, $course, ReviewStatus::Approved, 5);
        \App\Models\Review::create([
            'user_id' => $course->instructor_id,
            'course_id' => $course->id,
            'parent_id' => $review->id,
            'rating' => null,
            'comment' => 'Cảm ơn bạn',
            'status' => ReviewStatus::Approved->value,
        ]);

        $this->actingAs($student)->put(route('courses.reviews.update', [$course, $review]), $this->payload(3))->assertSessionHasNoErrors();

        $review->refresh();
        $this->assertSame(3, $review->rating);
        $this->assertSame(ReviewStatus::Pending, $review->status);
        $this->assertSame('Cảm ơn bạn', $review->replies->first()->comment);
        $this->assertSame(0, $course->fresh()->rating_count);
    }

    public function test_student_cannot_update_another_students_review_or_mismatched_course(): void
    {
        $owner = User::factory()->create(['role' => 'student']);
        $attacker = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $otherCourse = $this->course();
        $this->enroll($owner, $course);
        $this->enroll($attacker, $course);
        $review = $this->review($owner, $course);

        $this->actingAs($attacker)->put(route('courses.reviews.update', [$course, $review]), $this->payload(2))->assertForbidden();
        $this->actingAs($owner)->put(route('courses.reviews.update', [$otherCourse, $review]), $this->payload(2))->assertNotFound();
    }

    public function test_owner_can_delete_review_but_other_student_cannot(): void
    {
        $owner = User::factory()->create(['role' => 'student']);
        $other = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $this->enroll($owner, $course);
        $review = $this->review($owner, $course, ReviewStatus::Approved);
        app(ReviewService::class)->syncCourseRating($course->id);

        $this->actingAs($other)->delete(route('courses.reviews.destroy', [$course, $review]))->assertForbidden();
        $this->actingAs($owner)->delete(route('courses.reviews.destroy', [$course, $review]))->assertSessionHasNoErrors();
        $this->assertSoftDeleted('reviews', ['id' => $review->id]);
        $this->assertSame(0, $course->fresh()->rating_count);
    }

    public function test_course_owner_can_reply_and_other_instructor_cannot(): void
    {
        $course = $this->course();
        $student = User::factory()->create(['role' => 'student']);
        $otherInstructor = User::factory()->create(['role' => 'instructor']);
        $review = $this->review($student, $course, ReviewStatus::Approved);

        $this->actingAsTwoFactorVerified($otherInstructor)
            ->post(route('instructor.reviews.reply', $review), ['comment' => 'Không hợp lệ'])
            ->assertForbidden();

        $this->actingAsTwoFactorVerified($course->instructor)
            ->post(route('instructor.reviews.reply', $review), ['comment' => 'Cảm ơn phản hồi của bạn'])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('reviews', [
            'parent_id' => $review->id,
            'user_id' => $course->instructor_id,
            'comment' => 'Cảm ơn phản hồi của bạn',
        ]);
    }

    public function test_admin_approval_updates_average_and_public_visibility(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = $this->course();
        $first = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Pending, 5);
        $second = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Pending, 3);

        $this->actingAsTwoFactorVerified($admin)->patch(route('admin.student-reviews.approve', $first))->assertSessionHasNoErrors();
        $this->actingAsTwoFactorVerified($admin)->patch(route('admin.student-reviews.approve', $second))->assertSessionHasNoErrors();

        $course->refresh();
        $this->assertSame(2, $course->rating_count);
        $this->assertSame('4.00', $course->rating_avg);
        $response = $this->get(route('courses.show', $course->slug))->assertOk();
        $this->assertCount(2, $response->viewData('reviews'));
        $this->assertSame(1, $response->viewData('ratingDistribution')[5]);
        $this->assertSame(1, $response->viewData('ratingDistribution')[3]);
    }

    public function test_rejected_hidden_and_deleted_reviews_do_not_affect_rating(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = $this->course();
        $approved = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Approved, 5);
        $hidden = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Approved, 1);
        app(ReviewService::class)->syncCourseRating($course->id);
        $this->assertSame('3.00', $course->fresh()->rating_avg);

        $this->actingAsTwoFactorVerified($admin)->patch(route('admin.student-reviews.hide', $hidden), ['moderation_note' => 'Nội dung vi phạm'])->assertSessionHasNoErrors();
        $this->assertSame('5.00', $course->fresh()->rating_avg);

        $rejected = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Rejected, 1);
        app(ReviewService::class)->delete($approved);
        $this->assertSame(0, $course->fresh()->rating_count);
        $this->assertSame(ReviewStatus::Rejected, $rejected->status);
    }

    public function test_pending_review_is_not_public_and_escaped_output_blocks_xss(): void
    {
        $course = $this->course();
        $pending = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Pending, 5, ['comment' => 'Nội dung đang chờ duyệt']);
        $approved = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Approved, 4, ['comment' => '<script>alert(1)</script>']);
        app(ReviewService::class)->syncCourseRating($course->id);

        $response = $this->get(route('courses.show', $course->slug))->assertOk();
        $this->assertCount(1, $response->viewData('reviews'));
        $response->assertDontSee($pending->comment)->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $this->assertSame($approved->id, $response->viewData('reviews')->first()->id);
    }

    public function test_helpful_toggle_is_unique_reversible_and_cannot_be_self_marked(): void
    {
        $owner = User::factory()->create(['role' => 'student']);
        $viewer = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $review = $this->review($owner, $course, ReviewStatus::Approved);

        $this->actingAs($owner)->post(route('reviews.helpful.toggle', $review))->assertForbidden();
        $this->actingAs($viewer)->post(route('reviews.helpful.toggle', $review))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('review_helpful', 1);
        $this->assertSame(1, $review->fresh()->helpful_count);
        $this->actingAs($viewer)->post(route('reviews.helpful.toggle', $review))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('review_helpful', 0);
        $this->assertSame(0, $review->fresh()->helpful_count);
    }

    public function test_latest_rating_filter_and_helpful_sort_are_course_scoped(): void
    {
        $course = $this->course();
        $otherCourse = $this->course();
        $oldHelpful = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Approved, 5, ['helpful_count' => 10, 'created_at' => now()->subDay()]);
        $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Approved, 3, ['helpful_count' => 0]);
        $this->review(User::factory()->create(['role' => 'student']), $otherCourse, ReviewStatus::Approved, 5, ['helpful_count' => 99]);

        $response = $this->get(route('courses.show', ['slug' => $course->slug, 'review_rating' => 5, 'review_sort' => 'helpful']))->assertOk();
        $reviews = $response->viewData('reviews');
        $this->assertCount(1, $reviews);
        $this->assertSame($oldHelpful->id, $reviews->first()->id);
    }

    public function test_refunded_or_revoked_student_cannot_update_but_review_is_retained(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $enrollment = $this->enroll($student, $course);
        $review = $this->review($student, $course, ReviewStatus::Approved);
        $enrollment->update(['status' => 'refunded']);

        $this->actingAs($student)->put(route('courses.reviews.update', [$course, $review]), $this->payload(2))->assertForbidden();
        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'deleted_at' => null]);
    }

    public function test_student_instructor_and_admin_review_dashboards_render(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $review = $this->review($student, $course, ReviewStatus::Pending);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAsTwoFactorVerified($student)->get(route('student.reviews.index'))->assertOk();
        $this->actingAsTwoFactorVerified($course->instructor)->get(route('instructor.reviews.index'))->assertOk();
        $this->actingAsTwoFactorVerified($admin)->get(route('admin.student-reviews.index'))->assertOk();
        $this->actingAsTwoFactorVerified($admin)->get(route('admin.student-reviews.show', $review))->assertOk();
    }

    public function test_non_admin_cannot_access_review_moderation(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAsTwoFactorVerified($student)
            ->get(route('admin.student-reviews.index'))
            ->assertRedirect($student->dashboardUrl());
    }

    private function payload(int $rating = 5): array
    {
        return ['rating' => $rating, 'comment' => 'Nội dung đánh giá chi tiết và hữu ích.'];
    }

    private function course(): Course
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $category = Category::query()->create(['name' => 'Danh mục '.uniqid(), 'slug' => 'category-'.uniqid()]);

        return Course::query()->create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học '.uniqid(),
            'slug' => 'course-'.uniqid(),
            'short_description' => 'Mô tả ngắn',
            'description' => 'Nội dung khóa học',
            'price' => 0,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
        ])->load('instructor');
    }

    private function enroll(User $student, Course $course, float $progress = 10): Enrollment
    {
        return Enrollment::query()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'progress_percent' => $progress,
            'enrolled_at' => now(),
            'last_accessed_at' => $progress > 0 ? now() : null,
        ]);
    }

    private function review(User $student, Course $course, ReviewStatus $status = ReviewStatus::Pending, int $rating = 5, array $extra = []): Review
    {
        return Review::query()->create(array_merge([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'rating' => $rating,
            'comment' => 'Đánh giá đủ dài cho bài kiểm thử.',
            'status' => $status->value,
            'verified_purchase' => true,
        ], $extra));
    }

    private function actingAsTwoFactorVerified(User $user): static
    {
        $user->forceFill(['email_verified_at' => now(), 'is_active' => true])->save();

        return $this->actingAs($user)->withSession(['two_factor_passed_at' => now()->timestamp]);
    }
}
