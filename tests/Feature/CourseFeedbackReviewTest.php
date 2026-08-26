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

    public function test_enrolled_student_can_create_visible_review_immediately(): void
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
            'status' => ReviewStatus::Visible->value,
            'is_hidden' => false,
            'verified_purchase' => true,
        ]);
        $course->refresh();
        $this->assertSame(1, $course->rating_count);
        $this->assertSame('5.00', $course->rating_avg);

        $response = $this->get(route('courses.show', $course->slug))->assertOk();
        $this->assertSame($student->id, $response->viewData('reviews')->first()->user_id);
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
        $review = $this->review($student, $course, ReviewStatus::Visible, 5);
        Review::create([
            'user_id' => $course->instructor_id,
            'course_id' => $course->id,
            'parent_id' => $review->id,
            'rating' => null,
            'comment' => 'Cảm ơn bạn',
            'status' => ReviewStatus::Visible->value,
        ]);

        $this->actingAs($student)->put(route('courses.reviews.update', [$course, $review]), $this->payload(3))->assertSessionHasNoErrors();

        $review->refresh();
        $this->assertSame(3, $review->rating);
        $this->assertSame(ReviewStatus::Visible, $review->status);
        $this->assertSame('Cảm ơn bạn', $review->replies->first()->comment);
        $this->assertSame(1, $course->fresh()->rating_count);
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
        $review = $this->review($owner, $course, ReviewStatus::Visible);
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
        $otherInstructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $review = $this->review($student, $course, ReviewStatus::Visible);

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

    public function test_admin_can_hide_restore_and_soft_delete_review(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = $this->course();
        $first = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Visible, 5);
        $second = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Visible, 3);
        app(ReviewService::class)->syncCourseRating($course->id);

        $course->refresh();
        $this->assertSame(2, $course->rating_count);
        $this->assertSame('4.00', $course->rating_avg);

        $this->actingAsTwoFactorVerified($admin)
            ->patch(route('admin.student-reviews.hide', $second), ['moderation_note' => 'Không phù hợp'])
            ->assertSessionHasNoErrors();

        $this->assertSame(ReviewStatus::Hidden, $second->fresh()->status);
        $this->assertTrue($second->fresh()->is_hidden);
        $this->assertSame(1, $course->fresh()->rating_count);
        $this->assertSame('5.00', $course->fresh()->rating_avg);

        $this->actingAsTwoFactorVerified($admin)
            ->patch(route('admin.student-reviews.restore', $second))
            ->assertSessionHasNoErrors();

        $this->assertSame(ReviewStatus::Visible, $second->fresh()->status);
        $this->assertFalse($second->fresh()->is_hidden);
        $this->assertSame(2, $course->fresh()->rating_count);
        $this->assertSame('4.00', $course->fresh()->rating_avg);

        $this->actingAsTwoFactorVerified($admin)
            ->delete(route('admin.student-reviews.destroy', $second))
            ->assertRedirect(route('admin.student-reviews.index'));

        $this->assertSoftDeleted('reviews', ['id' => $second->id]);
        $this->assertSame(1, $course->fresh()->rating_count);
        $this->assertSame('5.00', $course->fresh()->rating_avg);

        $response = $this->get(route('courses.show', $course->slug))->assertOk();
        $this->assertCount(1, $response->viewData('reviews'));
        $this->assertSame(1, $response->viewData('ratingDistribution')[5]);
        $this->assertSame(0, $response->viewData('ratingDistribution')[3]);
    }

    public function test_hidden_and_deleted_reviews_do_not_affect_rating(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = $this->course();
        $visible = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Visible, 5);
        $hidden = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Visible, 1);
        app(ReviewService::class)->syncCourseRating($course->id);
        $this->assertSame('3.00', $course->fresh()->rating_avg);

        $this->actingAsTwoFactorVerified($admin)->patch(route('admin.student-reviews.hide', $hidden), ['moderation_note' => 'Nội dung vi phạm'])->assertSessionHasNoErrors();
        $this->assertSame('5.00', $course->fresh()->rating_avg);

        app(ReviewService::class)->delete($visible);
        $this->assertSame(0, $course->fresh()->rating_count);
        $this->assertSame(ReviewStatus::Hidden, $hidden->fresh()->status);
    }

    public function test_hidden_review_is_not_public_and_escaped_output_blocks_xss(): void
    {
        $course = $this->course();
        $hidden = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Hidden, 5, [
            'comment' => 'Nội dung đã ẩn',
            'is_hidden' => true,
        ]);
        $visible = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Visible, 4, ['comment' => '<script>alert(1)</script>']);
        app(ReviewService::class)->syncCourseRating($course->id);

        $response = $this->get(route('courses.show', $course->slug))->assertOk();
        $this->assertCount(1, $response->viewData('reviews'));
        $response->assertDontSee($hidden->comment)->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $this->assertSame($visible->id, $response->viewData('reviews')->first()->id);
    }

    public function test_helpful_toggle_is_unique_reversible_and_cannot_be_self_marked(): void
    {
        $owner = User::factory()->create(['role' => 'student']);
        $viewer = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $review = $this->review($owner, $course, ReviewStatus::Visible);

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
        $oldHelpful = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Visible, 5, ['helpful_count' => 10, 'created_at' => now()->subDay()]);
        $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Visible, 3, ['helpful_count' => 0]);
        $this->review(User::factory()->create(['role' => 'student']), $otherCourse, ReviewStatus::Visible, 5, ['helpful_count' => 99]);

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
        $review = $this->review($student, $course, ReviewStatus::Visible);
        $enrollment->update(['status' => 'refunded']);

        $this->actingAs($student)->put(route('courses.reviews.update', [$course, $review]), $this->payload(2))->assertForbidden();
        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'deleted_at' => null]);
    }

    public function test_student_instructor_and_admin_review_dashboards_render(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $review = $this->review($student, $course, ReviewStatus::Visible);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAsTwoFactorVerified($student)->get(route('student.reviews.index'))->assertOk();
        $this->actingAsTwoFactorVerified($course->instructor)->get(route('instructor.reviews.index'))->assertOk();
        $this->actingAsTwoFactorVerified($admin)->get(route('admin.student-reviews.index'))->assertOk();
        $this->actingAsTwoFactorVerified($admin)->get(route('admin.student-reviews.show', $review))->assertOk();
    }

    public function test_admin_sees_newly_created_visible_review_without_default_filters(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->course();
        $this->enroll($student, $course);
        $comment = 'Admin can see this new student review immediately.';

        $this->actingAs($student)
            ->post(route('courses.reviews.store', $course), [
                'rating' => 5,
                'comment' => $comment,
            ])
            ->assertRedirect(route('courses.show', $course->slug).'#reviews');

        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAsTwoFactorVerified($admin)
            ->get(route('admin.student-reviews.index'))
            ->assertOk()
            ->assertSee($comment);

        $this->assertSame([], $response->viewData('filters'));
        $this->assertSame(1, $response->viewData('reviews')->total());
        $response
            ->assertDontSee('name="instructor_id"', false)
            ->assertDontSee('name="rating"', false)
            ->assertDontSee('name="reply"', false)
            ->assertDontSee('name="date_from"', false)
            ->assertDontSee('name="date_to"', false);
    }

    public function test_admin_review_index_ignores_retired_filters_that_used_to_hide_reviews(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = $this->course();
        $wrongInstructor = User::factory()->create(['role' => 'instructor']);

        for ($i = 0; $i < 16; $i++) {
            $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Visible, 5, [
                'comment' => 'Retired filter visible review '.$i,
            ]);
        }

        $response = $this->actingAsTwoFactorVerified($admin)
            ->get(route('admin.student-reviews.index', [
                'instructor_id' => $wrongInstructor->id,
                'rating' => 1,
                'reply' => 'replied',
                'date_from' => now()->addDays(10)->toDateString(),
                'date_to' => now()->addDays(10)->toDateString(),
                'status' => 'approved',
                'keyword' => '',
                'course_id' => '',
            ]))
            ->assertOk()
            ->assertSee('Retired filter visible review')
            ->assertDontSee('Không có đánh giá phù hợp.');

        $this->assertSame([], $response->viewData('filters'));
        $this->assertSame(16, $response->viewData('reviews')->total());
        $response
            ->assertSee('page=2', false)
            ->assertDontSee('instructor_id=', false)
            ->assertDontSee('rating=', false)
            ->assertDontSee('reply=', false)
            ->assertDontSee('date_from=', false)
            ->assertDontSee('date_to=', false)
            ->assertDontSee('status=approved', false)
            ->assertDontSee('keyword=', false)
            ->assertDontSee('course_id=', false);
    }

    public function test_admin_review_index_filters_visible_and_hidden_reviews(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = $this->course();
        $otherCourse = $this->course();
        $visible = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Visible, 5, [
            'comment' => 'Đánh giá đang hiển thị',
        ]);
        $hidden = $this->review(User::factory()->create(['role' => 'student']), $course, ReviewStatus::Hidden, 1, [
            'comment' => 'Đánh giá đã ẩn',
            'is_hidden' => true,
        ]);
        $other = $this->review(User::factory()->create(['role' => 'student']), $otherCourse, ReviewStatus::Visible, 4, [
            'comment' => 'Đánh giá khóa học khác',
        ]);

        $this->actingAsTwoFactorVerified($admin)
            ->get(route('admin.student-reviews.index'))
            ->assertOk()
            ->assertSee($visible->comment)
            ->assertSee($hidden->comment)
            ->assertSee($other->comment);

        $this->actingAsTwoFactorVerified($admin)
            ->get(route('admin.student-reviews.index', ['status' => ReviewStatus::Visible->value]))
            ->assertOk()
            ->assertSee($visible->comment)
            ->assertDontSee($hidden->comment)
            ->assertSee($other->comment);

        $this->actingAsTwoFactorVerified($admin)
            ->get(route('admin.student-reviews.index', ['status' => ReviewStatus::Hidden->value]))
            ->assertOk()
            ->assertDontSee($visible->comment)
            ->assertSee($hidden->comment)
            ->assertDontSee($other->comment);

        $this->actingAsTwoFactorVerified($admin)
            ->get(route('admin.student-reviews.index', ['course_id' => $course->id]))
            ->assertOk()
            ->assertSee($visible->comment)
            ->assertSee($hidden->comment)
            ->assertDontSee($other->comment);
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
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'email_verified_at' => now(),
        ]);
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

    private function review(User $student, Course $course, ReviewStatus $status = ReviewStatus::Visible, int $rating = 5, array $extra = []): Review
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
