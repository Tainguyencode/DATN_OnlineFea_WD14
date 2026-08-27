<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RefundFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::put('vietnam_banks_list', [
            ['code' => 'MB', 'shortName' => 'MBBank', 'name' => 'MBBank', 'bin' => '970422', 'logo' => ''],
            ['code' => 'VCB', 'shortName' => 'Vietcombank', 'name' => 'Vietcombank', 'bin' => '970436', 'logo' => ''],
            ['code' => 'VBA', 'shortName' => 'Agribank', 'name' => 'Agribank', 'bin' => '970405', 'logo' => ''],
        ], 60);
    }

    public function test_student_can_submit_refund_request_for_paid_order(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $category = Category::create(['name' => 'IT', 'slug' => 'it-cat']);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học Web Test',
            'slug' => 'khoa-hoc-web-test',
            'price' => 500000,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'enrollment_count' => 1,
        ]);

        $order = Order::create([
            'order_code' => 'ORD-TEST-100',
            'user_id' => $student->id,
            'subtotal' => 500000,
            'discount_amount' => 0,
            'total_amount' => 500000,
            'status' => 'paid',
            'payment_method' => 'payos',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'course_id' => $course->id,
            'price' => 500000,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'order_id' => $order->id,
            'status' => 'active',
            'progress_percent' => 0,
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('student.orders.refund', $order), [
                'reason' => 'Không còn nhu cầu học khóa này nữa.',
                'bank_code' => 'MB',
                'bank_account_number' => '0987654321',
                'bank_account_name' => 'NGUYEN VAN A',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('refunds', [
            'order_id' => $order->id,
            'user_id' => $student->id,
            'status' => 'pending',
            'bank_code' => 'MB',
            'bank_account_number' => '0987654321',
        ]);
    }

    public function test_student_cannot_request_refund_if_order_older_than_7_days(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $category = Category::create(['name' => 'IT', 'slug' => 'it-cat-old']);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học Cũ',
            'slug' => 'khoa-hoc-cu',
            'price' => 500000,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
        ]);

        $order = Order::create([
            'order_code' => 'ORD-OLD-999',
            'user_id' => $student->id,
            'subtotal' => 500000,
            'discount_amount' => 0,
            'total_amount' => 500000,
            'status' => 'paid',
            'payment_method' => 'payos',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'gateway' => 'bank_transfer',
            'amount' => 500000,
            'status' => 'success',
            'paid_at' => now()->subDays(10),
        ]);

        $response = $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('student.orders.refund', $order), [
                'reason' => 'Muốn hoàn tiền đơn hàng đã mua cách đây 10 ngày.',
                'bank_code' => 'MB',
                'bank_account_number' => '0987654321',
                'bank_account_name' => 'NGUYEN VAN A',
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('refunds', ['order_id' => $order->id]);
    }

    public function test_student_cannot_request_refund_for_zero_value_order(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $order = Order::create([
            'order_code' => 'ORD-FREE-REFUND',
            'user_id' => $student->id,
            'subtotal' => 50000,
            'discount_amount' => 50000,
            'total_amount' => 0,
            'status' => 'paid',
            'payment_method' => 'payos',
        ]);

        $this->actingAs($student)->post(route('student.orders.refund', $order), [
            'reason' => 'Yêu cầu hoàn tiền cho đơn miễn phí.',
            'bank_code' => 'VCB',
            'bank_account_number' => '0987654321',
            'bank_account_name' => 'NGUYEN VAN A',
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('refunds', ['order_id' => $order->id]);
    }

    public function test_refund_bank_details_use_supported_bank_and_strict_account_format(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $order = Order::create([
            'order_code' => 'ORD-BANK-VALIDATION',
            'user_id' => $student->id,
            'subtotal' => 50000,
            'discount_amount' => 0,
            'total_amount' => 50000,
            'status' => 'paid',
            'payment_method' => 'payos',
        ]);

        $this->actingAs($student)->post(route('student.orders.refund', $order), [
            'reason' => 'Yêu cầu hoàn tiền để kiểm tra ngân hàng.',
            'bank_code' => 'UNKNOWN',
            'bank_account_number' => 'ABC-123',
            'bank_account_name' => 'NGUYEN VAN A',
        ])->assertSessionHasErrors(['bank_code', 'bank_account_number']);

        $this->assertDatabaseMissing('refunds', ['order_id' => $order->id]);
    }

    public function test_student_cannot_request_refund_if_progress_over_50_percent(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $category = Category::create(['name' => 'IT', 'slug' => 'it-cat-prog']);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học Đã Học Nhiều',
            'slug' => 'khoa-hoc-da-hoc-nhieu',
            'price' => 500000,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
        ]);

        $order = Order::create([
            'order_code' => 'ORD-PROG-50',
            'user_id' => $student->id,
            'subtotal' => 500000,
            'total_amount' => 500000,
            'status' => 'paid',
            'payment_method' => 'payos',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'course_id' => $course->id,
            'price' => 500000,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'order_id' => $order->id,
            'status' => 'active',
            'progress_percent' => 60, // Tiến độ 60%
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('student.orders.refund', $order), [
                'reason' => 'Muốn hoàn tiền đơn hàng dù đã học 60%.',
                'bank_code' => 'VCB',
                'bank_account_number' => '0987654321',
                'bank_account_name' => 'NGUYEN VAN A',
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('refunds', ['order_id' => $order->id]);
    }

    public function test_admin_can_approve_refund_manually(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $category = Category::create(['name' => 'Design', 'slug' => 'design-cat']);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học UX UI',
            'slug' => 'khoa-hoc-ux-ui',
            'price' => 300000,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'enrollment_count' => 1,
        ]);

        $order = Order::create([
            'order_code' => 'ORD-TEST-200',
            'user_id' => $student->id,
            'subtotal' => 300000,
            'discount_amount' => 0,
            'total_amount' => 300000,
            'status' => 'paid',
            'payment_method' => 'payos',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'course_id' => $course->id,
            'price' => 300000,
        ]);

        $enrollment = Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'order_id' => $order->id,
            'status' => 'active',
            'progress_percent' => 0,
            'enrolled_at' => now(),
        ]);

        $refund = Refund::create([
            'order_id' => $order->id,
            'user_id' => $student->id,
            'amount' => 300000,
            'reason' => 'Yêu cầu hoàn tiền thử nghiệm',
            'bank_code' => 'VCB',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'LE VAN B',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('admin.refunds.approve', $refund), [
                'admin_note' => 'Đã chuyển tiền qua ứng dụng ngân hàng.',
                'transaction_reference' => 'FT-20260826-001',
            ]);

        $response->assertRedirect(route('admin.refunds.index'));

        $this->assertDatabaseHas('refunds', [
            'id' => $refund->id,
            'status' => 'approved',
            'refund_method' => 'manual',
            'transaction_reference' => 'FT-20260826-001',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'refunded',
        ]);

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => 'cancelled',
        ]);

        $this->actingAs($admin)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('admin.refunds.reject', $refund), [
                'admin_note' => 'Thử thay đổi kết quả sau khi đã duyệt.',
            ]);

        $this->assertSame('approved', $refund->fresh()->status);
        $this->assertSame('refunded', $order->fresh()->status);
    }

    public function test_manual_refund_requires_a_transaction_reference(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $order = Order::create([
            'order_code' => 'ORD-MANUAL-REF',
            'user_id' => $student->id,
            'subtotal' => 100000,
            'total_amount' => 100000,
            'status' => 'paid',
            'payment_method' => 'payos',
        ]);
        $refund = Refund::create([
            'order_id' => $order->id,
            'user_id' => $student->id,
            'amount' => 100000,
            'reason' => 'Khóa học không phù hợp với nhu cầu hiện tại.',
            'bank_code' => 'MB',
            'bank_account_number' => '0987654321',
            'bank_account_name' => 'NGUYEN VAN A',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('admin.refunds.approve', $refund), [
            ])
            ->assertSessionHasErrors('transaction_reference');

        $this->assertSame('pending', $refund->fresh()->status);
        $this->assertSame('paid', $order->fresh()->status);
    }
}
