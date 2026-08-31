<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentOrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_cancel_own_pending_order(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $order = Order::create([
            'user_id' => $student->id,
            'order_code' => 'ORD-CANCEL-1',
            'subtotal' => 200000,
            'total_amount' => 200000,
            'status' => 'pending',
            'payment_method' => 'payos',
        ]);
        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway' => 'bank_transfer',
            'amount' => 200000,
            'status' => 'pending',
        ]);

        $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->delete(route('student.orders.cancel', $order))
            ->assertRedirect(route('student.orders.show', $order))
            ->assertSessionHas('success');

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame('failed', $payment->fresh()->status);
    }

    public function test_issued_payment_can_be_cancelled_and_shows_confirmation(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $order = Order::create(['user_id' => $student->id, 'order_code' => 'LOCKED-CANCEL', 'subtotal' => 50000, 'total_amount' => 50000, 'status' => 'pending']);
        $this->assertTrue($order->canCancel());
        Payment::create(['order_id' => $order->id, 'gateway' => 'bank_transfer', 'amount' => 50000, 'status' => 'pending', 'gateway_order_code' => '123456789']);
        $this->assertTrue($order->fresh()->canCancel());
        $this->actingAs($student)->get(route('student.orders.show', $order))->assertOk()
            ->assertSee('Hủy đơn hàng');
        $this->actingAs($student)->delete(route('student.orders.cancel', $order))
            ->assertRedirect(route('student.orders.show', $order))
            ->assertSessionHas('success', 'Đã hủy đơn hàng thành công.');
        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame('123456789', $order->payment->gateway_order_code);
        $this->get(route('student.orders.show', $order))->assertOk()->assertSee('Đã hủy đơn hàng thành công.')->assertSee('Đơn hàng đã được hủy.')
            ->assertSee('href="'.route('student.orders').'" data-explicit-back', false);
        $this->get(route('student.orders'))->assertOk()->assertDontSee('Đã hủy đơn hàng thành công.')
            ->assertViewHas('orders', fn ($orders) => $orders->firstWhere('id', $order->id)?->status === 'cancelled');
    }

    public function test_student_cannot_cancel_paid_or_another_students_order(): void
    {
        $owner = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $other = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $paidOrder = Order::create([
            'user_id' => $owner->id,
            'order_code' => 'ORD-CANCEL-PAID',
            'subtotal' => 200000,
            'total_amount' => 200000,
            'status' => 'paid',
            'payment_method' => 'payos',
        ]);

        $this->actingAs($owner)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->delete(route('student.orders.cancel', $paidOrder))
            ->assertSessionHas('error');
        $this->assertSame('paid', $paidOrder->fresh()->status);

        $this->actingAs($other)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->delete(route('student.orders.cancel', $paidOrder))
            ->assertForbidden();
    }

    public function test_pending_order_does_not_show_stale_refund_message(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $order = Order::create([
            'user_id' => $student->id,
            'order_code' => 'ORD-PENDING-STALE-REFUND',
            'subtotal' => 200000,
            'total_amount' => 200000,
            'status' => 'pending',
            'payment_method' => 'payos',
        ]);
        Refund::create([
            'order_id' => $order->id,
            'user_id' => $student->id,
            'amount' => 0,
            'reason' => 'Dữ liệu cũ không hợp lệ',
            'status' => 'approved',
            'refund_method' => 'manual',
            'bank_code' => 'VCB',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'TEST USER',
        ]);

        $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('student.orders.show', $order))
            ->assertOk()
            ->assertSee('Đơn hàng chưa hoàn tất thanh toán')
            ->assertDontSee('Đã hoàn tiền thành công');
    }

    public function test_student_can_view_order_history_list(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);

        $category = Category::create(['name' => 'IT', 'slug' => 'it']);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học Web',
            'slug' => 'khoa-hoc-web',
            'price' => 500000,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
        ]);

        $order = Order::create([
            'user_id' => $student->id,
            'order_code' => 'ORD-1001',
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

        $response = $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('student.orders'));

        $response->assertOk();
        $response->assertSee('ORD-1001');
        $response->assertSee('Khóa học Web');
    }

    public function test_student_can_view_order_details(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);

        $category = Category::create(['name' => 'Design', 'slug' => 'design']);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học Figma',
            'slug' => 'khoa-hoc-figma',
            'price' => 300000,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
        ]);

        $order = Order::create([
            'user_id' => $student->id,
            'order_code' => 'ORD-2002',
            'subtotal' => 300000,
            'total_amount' => 300000,
            'status' => 'paid',
            'payment_method' => 'payos',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'course_id' => $course->id,
            'price' => 300000,
        ]);

        $response = $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('student.orders.show', $order));

        $response->assertOk();
        $response->assertSee('ORD-2002');
        $response->assertSee('Khóa học Figma');
        $response->assertSee('PayOS VietQR');
    }

    public function test_student_cannot_view_other_students_order_details(): void
    {
        $owner = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $otherStudent = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);

        $order = Order::create([
            'user_id' => $owner->id,
            'order_code' => 'ORD-3003',
            'subtotal' => 200000,
            'total_amount' => 200000,
            'status' => 'paid',
            'payment_method' => 'bank_transfer',
        ]);

        $response = $this->actingAs($otherStudent)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('student.orders.show', $order));

        $response->assertForbidden();
    }

    public function test_student_can_filter_refunded_orders(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $refunded = Order::create([
            'order_code' => 'ORD-REFUNDED-'.uniqid(),
            'user_id' => $student->id,
            'subtotal' => 200000,
            'discount_amount' => 0,
            'total_amount' => 200000,
            'status' => 'refunded',
            'payment_method' => 'payos',
        ]);
        Order::create([
            'order_code' => 'ORD-PAID-'.uniqid(),
            'user_id' => $student->id,
            'subtotal' => 200000,
            'discount_amount' => 0,
            'total_amount' => 200000,
            'status' => 'paid',
            'payment_method' => 'payos',
        ]);

        $response = $this->actingAs($student)->get(route('student.orders', ['status' => 'refunded']));

        $response->assertOk()->assertSee($refunded->order_code)->assertSee('Đã hoàn tiền');
        $this->assertCount(1, $response->viewData('orders'));
    }
}
