<?php

namespace Tests\Feature;

use App\Jobs\ConvertVideoToHLS;
use App\Models\Assignment;
use App\Models\Cart;
use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Submission;
use App\Models\User;
use App\Models\UserCoupon;
use App\Services\AwsS3UploadService;
use App\Services\MomoService;
use App\Services\PaymentGatewayService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class DefenseTopTenRegressionTest extends TestCase
{
    use DatabaseTransactions;

    private User $student;
    private Course $course;
    private Lesson $video;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        Queue::fake();
        $this->student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $this->course = Course::create([
            'title' => 'Defense regression', 'slug' => (string) Str::uuid(),
            'category_id' => Category::create(['name' => 'Test', 'slug' => (string) Str::uuid()])->id,
            'instructor_id' => $instructor->id, 'price' => 100000,
            'status' => 'published', 'is_published' => true, 'certificate_enabled' => false,
        ]);
        $this->video = Lesson::create([
            'course_id' => $this->course->id, 'title' => 'Video', 'type' => 'video',
            'duration_seconds' => 600, 'duration' => 600, 'status' => 'published', 'is_required' => true,
        ]);
        Enrollment::create(['user_id' => $this->student->id, 'course_id' => $this->course->id, 'status' => 'active']);
        $this->actingAs($this->student);
    }

    public function test_assignment_download_and_retry_reject_a_lesson_from_another_course(): void
    {
        $other = $this->course->replicate();
        $other->slug = (string) Str::uuid();
        $other->save();
        $lesson = Lesson::create(['course_id' => $other->id, 'title' => 'Private', 'type' => 'assignment']);
        foreach (['download' => 'get', 'retry' => 'post'] as $action => $method) {
            $this->$method(route('courses.lessons.assignment.'.$action, [$this->course, $lesson]))->assertNotFound();
        }
        $this->assertDatabaseMissing('assignments', ['lesson_id' => $lesson->id]);
        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_assignment_download_cannot_create_an_assignment_on_a_video(): void
    {
        $this->get(route('courses.lessons.assignment.download', [$this->course, $this->video]))->assertNotFound();
        $this->assertDatabaseMissing('assignments', ['lesson_id' => $this->video->id]);
    }

    public function test_completed_flag_and_end_seek_do_not_grant_video_completion(): void
    {
        $this->postJson(route('courses.lessons.progress', [$this->course, $this->video]), [
            'completed' => true, 'last_position_seconds' => 600, 'played_seconds' => 0,
        ])->assertOk()->assertJsonPath('lesson_completed', false);
        $this->assertSame(0, LessonProgress::where('lesson_id', $this->video->id)->firstOrFail()->watched_seconds);
    }

    public function test_client_duration_and_future_clock_cannot_modify_media_or_inflate_watch_time(): void
    {
        LessonProgress::create([
            'user_id' => $this->student->id, 'course_id' => $this->course->id, 'lesson_id' => $this->video->id,
            'watched_seconds' => 0, 'last_watched_at' => now()->subSeconds(10),
        ]);
        $this->postJson(route('courses.lessons.progress', [$this->course, $this->video]), [
            'video_duration_seconds' => 1, 'last_position_seconds' => 600,
            'played_seconds' => 86400, 'client_updated_at' => now()->addDay()->toIso8601String(),
        ])->assertOk()->assertJsonPath('lesson_completed', false);
        $this->assertSame(600, (int) $this->video->fresh()->duration_seconds);
        $this->assertLessThanOrEqual(11, LessonProgress::where('lesson_id', $this->video->id)->firstOrFail()->watched_seconds);
    }

    public function test_real_heartbeat_can_complete_a_nearly_finished_video(): void
    {
        LessonProgress::create([
            'user_id' => $this->student->id, 'course_id' => $this->course->id, 'lesson_id' => $this->video->id,
            'watched_seconds' => 590, 'last_watched_at' => now()->subSeconds(12),
        ]);
        $this->postJson(route('courses.lessons.progress', [$this->course, $this->video]), [
            'last_position_seconds' => 600, 'played_seconds' => 10,
        ])->assertOk()->assertJsonPath('lesson_completed', true);
    }

    public function test_payos_alias_is_persisted_as_valid_gateway_for_paid_and_free_checkouts(): void
    {
        config(['services.payos.mode' => 'mock']);
        Enrollment::where('user_id', $this->student->id)->delete();
        $cart = Cart::create(['user_id' => $this->student->id]);
        $cart->courses()->attach($this->course->id);
        $this->post(route('student.cart.checkout'), [
            'payment_method' => 'payos', 'course_ids' => [$this->course->id],
        ])->assertRedirect();
        $this->assertDatabaseHas('payments', ['gateway' => 'bank_transfer', 'amount' => 100000]);
        $coupon = $this->coupon(['value' => 100]);
        $this->post(route('student.cart.checkout'), [
            'payment_method' => 'payos', 'course_ids' => [$this->course->id], 'coupon_code' => $coupon->code,
        ])->assertRedirect();
        $this->assertDatabaseHas('payments', ['gateway' => 'bank_transfer', 'amount' => 0, 'status' => 'success']);
    }

    public function test_private_coupon_is_hidden_and_rejected_until_granted(): void
    {
        $coupon = $this->coupon(['is_private' => true]);
        $this->assertFalse(Coupon::availableToUser($this->student->id)->whereKey($coupon->id)->exists());
        $this->postJson(route('student.cart.coupon.apply'), [
            'coupon_code' => $coupon->code, 'course_ids' => [$this->course->id],
        ])->assertJsonPath('success', false);
        UserCoupon::create(['user_id' => $this->student->id, 'coupon_id' => $coupon->id, 'granted_at' => now()]);
        $this->assertTrue($coupon->canBeUsedBy($this->student->id));
        $this->postJson(route('student.cart.coupon.apply'), [
            'coupon_code' => $coupon->code, 'course_ids' => [$this->course->id],
        ])->assertJsonPath('success', true);
    }

    public function test_received_payment_honours_expired_or_exhausted_quoted_coupon_and_is_idempotent(): void
    {
        $coupon = $this->coupon(['expires_at' => now()->subMinute(), 'max_uses' => 1, 'used_count' => 1]);
        [$order] = $this->order(['coupon_id' => $coupon->id, 'discount_amount' => 10000, 'total_amount' => 90000]);
        $service = app(PaymentGatewayService::class);
        $this->assertTrue($service->completePayOSPayment($order, 'received-once'));
        $this->assertTrue($service->completePayOSPayment($order->fresh(), 'received-once'));
        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame(2, (int) $coupon->fresh()->used_count);
    }

    public function test_issued_payment_blocks_price_and_gateway_changes_but_allows_local_cancellation(): void
    {
        [$order, $payment] = $this->order();
        $coupon = $this->coupon();
        $this->postJson(route('student.checkout.apply_coupon', $order->order_code), [
            'coupon_code' => $coupon->code,
        ])->assertConflict();
        $this->deleteJson(route('student.checkout.remove_coupon', $order->order_code))->assertConflict();
        config(['services.momo.partner_code' => 'test', 'services.momo.access_key' => 'test',
            'services.momo.secret_key' => 'test', 'services.momo.endpoint' => 'https://example.test']);
        $this->post(route('student.checkout.process_payment', $order->order_code), ['payment_method' => 'momo'])->assertSessionHas('error');
        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame('100000.00', $order->fresh()->total_amount);
        $this->assertSame('bank_transfer', $payment->fresh()->gateway);
        Http::assertNothingSent();
        $this->delete(route('student.orders.cancel', $order))->assertSessionHas('success', 'Đã hủy đơn hàng thành công.');
        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_verified_late_callback_can_reconcile_legacy_cancelled_order(): void
    {
        [$order] = $this->order(['status' => 'cancelled']);
        $this->assertTrue(app(PaymentGatewayService::class)->completePayOSPayment($order, 'late-payment'));
        $this->assertSame('paid', $order->fresh()->status);
    }

    public function test_momo_timeout_retry_keeps_the_same_reference(): void
    {
        [$order, $payment] = $this->order();
        $payment->update(['gateway' => 'momo', 'gateway_order_code' => 'MOMO-STABLE']);
        config(['services.momo.partner_code' => 'test', 'services.momo.access_key' => 'test',
            'services.momo.secret_key' => 'test', 'services.momo.endpoint' => 'https://example.test']);
        Http::fake(['*' => Http::response(['resultCode' => 0, 'payUrl' => 'https://example.test/pay'])]);
        app(MomoService::class)->createPaymentUrl($order);
        $this->assertSame('MOMO-STABLE', $payment->fresh()->gateway_order_code);
        Http::assertSent(fn ($request) => $request['orderId'] === 'MOMO-STABLE');
    }

    public function test_late_assignment_replay_preserves_a_graded_pass(): void
    {
        $lesson = Lesson::create(['course_id' => $this->course->id, 'title' => 'Assignment', 'type' => 'assignment']);
        $assignment = Assignment::create(['course_id' => $this->course->id, 'lesson_id' => $lesson->id, 'title' => 'Assignment', 'description' => 'Submit your work.']);
        $submission = Submission::create([
            'user_id' => $this->student->id, 'assignment_id' => $assignment->id,
            'started_at' => now()->subHours(7), 'submitted_at' => now()->subHours(6),
            'status' => 'graded', 'result' => 'pass', 'score' => 100,
        ]);
        $this->post(route('courses.lessons.assignment.submit', [$this->course, $lesson]))->assertRedirect();
        $this->assertSame('graded', $submission->fresh()->status);
        $this->assertSame('pass', $submission->fresh()->result);
        $this->assertSame(100, $submission->fresh()->score);
    }

    public function test_published_video_upload_creates_candidate_without_mutating_live_lesson(): void
    {
        $this->actingAs($this->course->instructor);
        $this->video->update(['original_video_key' => 'live.mp4', 'hls_manifest_key' => 'live/master.m3u8']);
        $key = "originals/courses/{$this->course->id}/replacement.mp4";
        $this->mock(AwsS3UploadService::class)->shouldReceive('completeMultipartUpload')->once()->andReturn([]);
        $this->postJson(route('instructor.courses.s3.multipart.complete', $this->course), [
            'key' => $key, 'uploadId' => 'upload', 'lesson_id' => $this->video->id,
            'parts' => [['PartNumber' => 1, 'ETag' => 'etag']],
        ])->assertOk();
        $this->assertSame('live.mp4', $this->video->fresh()->original_video_key);
        $this->assertSame('live/master.m3u8', $this->video->fresh()->hls_manifest_key);
        $candidate = ContentUpdate::where('entity_id', $this->video->id)->firstOrFail();
        $this->assertSame('draft', $candidate->status);
        $this->assertSame($key, $candidate->payload['original_video_key']);
        Queue::assertNotPushed(ConvertVideoToHLS::class);
    }

    public function test_payment_success_links_to_the_purchased_lesson_for_both_gateways(): void
    {
        [$order, $payment] = $this->order(['status' => 'paid']);
        $lessonUrl = route('courses.lessons.show', [$this->course, $this->video]);

        $this->get(route('student.checkout.success', $order->order_code))
            ->assertOk()->assertSee('href="'.$lessonUrl.'"', false)
            ->assertDontSee('href="'.route('student.dashboard').'"', false);
        $this->view('student.cart.momo_result', [
            'order' => $order, 'payment' => $payment, 'success' => true, 'message' => 'Success',
        ])->assertSee('href="'.$lessonUrl.'"', false)
            ->assertDontSee('href="'.route('student.dashboard').'"', false);

        $this->get($lessonUrl)->assertOk();
    }

    public function test_payment_success_offers_each_course_and_handles_course_without_lessons(): void
    {
        [$order] = $this->order(['status' => 'paid']);
        $other = $this->course->replicate();
        $other->slug = (string) Str::uuid();
        $other->title = 'Second purchased course';
        $other->save();
        $order->items()->create(['course_id' => $other->id, 'price' => 100000]);

        $this->get(route('student.checkout.success', $order->order_code))
            ->assertOk()
            ->assertSee('href="'.route('courses.lessons.show', [$this->course, $this->video]).'"', false)
            ->assertSee('href="'.route('courses.show', $other->slug).'"', false)
            ->assertSee($other->title);
    }

    public static function duplicatePurchaseRefundSequences(): array
    {
        return ['B then A, refund A' => ['B', 'A'], 'B then A, refund B' => ['B', 'B'],
            'A then B, refund A' => ['A', 'A'], 'A then B, refund B' => ['A', 'B']];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('duplicatePurchaseRefundSequences')]
    public function test_two_paid_orders_refund_matrix(string $firstPaid, string $refunded): void
    {
        $factory = new \ReflectionMethod(\Tests\Feature\DefenseTopTenRegressionTest::class, 'order');
        [$a] = $factory->invoke($this, ['status' => 'cancelled']);
        [$b] = $factory->invoke($this);
        $orders = ['A' => $a, 'B' => $b];
        $courseId = $a->items()->first()->course_id;
        \App\Models\Enrollment::where('user_id', $a->user_id)->where('course_id', $courseId)->delete();
        $service = app(\App\Services\PaymentGatewayService::class);
        foreach ([$firstPaid, $firstPaid === 'A' ? 'B' : 'A'] as $key) {
            $this->assertTrue($service->completePayOSPayment($orders[$key], 'AUDIT-'.$key));
        }
        $enrollment = \App\Models\Enrollment::where('user_id', $a->user_id)->where('course_id', $courseId)->sole();
        $this->assertSame($orders[$firstPaid]->id, $enrollment->order_id);
        $enrollment->update(['progress_percent' => 40, 'completed_lessons' => 1]);
        $countBefore = \App\Models\Course::findOrFail($courseId)->enrollment_count;
        $refund = \App\Models\Refund::create([
            'order_id' => $orders[$refunded]->id, 'user_id' => $a->user_id, 'amount' => 100000,
            'reason' => 'Refund duplicate payment', 'status' => 'pending', 'refund_method' => 'manual',
            'bank_code' => 'VCB', 'bank_account_number' => '0123456789', 'bank_account_name' => 'TEST STUDENT',
        ]);
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('admin.refunds.approve', $refund), ['transaction_reference' => 'AUDIT-REFUND'])
            ->assertSessionHas('success');
        $other = $orders[$refunded === 'A' ? 'B' : 'A'];
        $this->assertSame('paid', $other->fresh()->status);
        $this->assertSame('refunded', $orders[$refunded]->fresh()->status);
        // A remaining paid order preserves access and the existing learning progress.
        $this->assertSame('active', $enrollment->fresh()->status);
        $this->assertSame($other->id, $enrollment->fresh()->order_id);
        $this->assertEquals(40, $enrollment->fresh()->progress_percent);
        $this->assertSame(1, $enrollment->fresh()->completed_lessons);
        $this->assertTrue(\App\Models\Enrollment::whereKey($enrollment->id)->withLearningAccess()->exists());
        $this->assertSame($countBefore, \App\Models\Course::findOrFail($courseId)->enrollment_count);

        $lastRefund = $refund->replicate();
        $lastRefund->order_id = $other->id;
        $lastRefund->status = 'pending';
        $lastRefund->save();
        $service->processRefund($lastRefund, 'manual', null, 'AUDIT-LAST-REFUND');
        $this->assertSame('cancelled', $enrollment->fresh()->status);
        $this->assertSame($countBefore - 1, \App\Models\Course::findOrFail($courseId)->enrollment_count);
        $service->processRefund($lastRefund->fresh(), 'manual', null, 'AUDIT-LAST-REFUND');
        $this->assertSame($countBefore - 1, \App\Models\Course::findOrFail($courseId)->enrollment_count);
    }

    private function coupon(array $attributes = []): Coupon
    {
        return Coupon::create(array_merge([
            'code' => Str::upper(Str::random(12)), 'type' => 'percent', 'value' => 10,
            'creator_type' => 'admin', 'is_active' => true, 'is_private' => false,
        ], $attributes));
    }

    private function order(array $attributes = []): array
    {
        $order = Order::create(array_merge([
            'order_code' => Str::random(16), 'user_id' => $this->student->id,
            'subtotal' => 100000, 'total_amount' => 100000, 'status' => 'pending', 'payment_method' => 'bank_transfer',
        ], $attributes));
        $order->items()->create(['course_id' => $this->course->id, 'price' => 100000]);
        $payment = Payment::create([
            'order_id' => $order->id, 'gateway' => 'bank_transfer',
            'gateway_order_code' => (string) $order->id, 'amount' => $order->total_amount, 'status' => 'pending',
        ]);

        return [$order, $payment];
    }
}
