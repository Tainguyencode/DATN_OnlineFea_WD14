<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $instructor;
    protected Course $course;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo vai trò học viên
        $this->student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Tạo giảng viên
        $this->instructor = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
        ]);

        // Tạo danh mục
        $this->category = Category::create([
            'name' => 'Công nghệ thông tin',
            'slug' => 'cong-nghe-thong-tin',
        ]);

        // Tạo khóa học mẫu đã xuất bản
        $this->course = Course::create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'Lập trình PHP Laravel',
            'slug' => 'lap-trinh-php-laravel',
            'short_description' => 'Mô tả ngắn',
            'description' => 'Mô tả chi tiết',
            'price' => 100000,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
        ]);
    }

    /**
     * Test học viên có thể thêm khóa học vào giỏ hàng.
     */
    public function test_student_can_add_course_to_cart(): void
    {
        $response = $this->actingAs($this->student)
            ->post(route('student.cart.add', $this->course));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Đã thêm khóa học vào giỏ hàng thành công!');

        $cart = Cart::where('user_id', $this->student->id)->first();
        $this->assertNotNull($cart);
        $this->assertTrue($cart->courses->contains($this->course->id));
    }

    /**
     * Test học viên không thể thêm trùng khóa học vào giỏ hàng.
     */
    public function test_student_cannot_add_duplicate_course_to_cart(): void
    {
        // Thêm lần 1
        $this->actingAs($this->student)
            ->post(route('student.cart.add', $this->course));

        // Thêm lần 2
        $response = $this->actingAs($this->student)
            ->post(route('student.cart.add', $this->course));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Khóa học đã có sẵn trong giỏ hàng của bạn.');
    }

    /**
     * Test học viên không thể thêm khóa học đã mua/sở hữu vào giỏ hàng.
     */
    public function test_student_cannot_add_owned_course_to_cart(): void
    {
        // Giả lập ghi danh học viên trước
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($this->student)
            ->post(route('student.cart.add', $this->course));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Bạn đã sở hữu và đăng ký khóa học này rồi.');
    }

    /**
     * Test học viên có thể xóa khóa học khỏi giỏ hàng.
     */
    public function test_student_can_remove_course_from_cart(): void
    {
        // Thêm khóa học vào giỏ hàng trước
        $cart = Cart::firstOrCreate(['user_id' => $this->student->id]);
        $cart->courses()->attach($this->course->id);

        $response = $this->actingAs($this->student)
            ->delete(route('student.cart.remove', $this->course->id));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Đã xóa khóa học khỏi giỏ hàng.');

        $cart->refresh();
        $this->assertFalse($cart->courses->contains($this->course->id));
    }

    /**
     * Test học viên có thể checkout tạo đơn hàng ở trạng thái pending.
     */
    public function test_student_can_checkout_creating_pending_order(): void
    {
        // Thêm vào giỏ hàng
        $cart = Cart::firstOrCreate(['user_id' => $this->student->id]);
        $cart->courses()->attach($this->course->id);

        $response = $this->actingAs($this->student)
            ->post(route('student.cart.checkout'), [
                'payment_method' => 'bank_transfer',
            ]);

        // Sẽ chuyển hướng đến trang thanh toán
        $order = Order::where('user_id', $this->student->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals('bank_transfer', $order->payment_method);
        $this->assertEquals(100000, $order->total_amount);

        $response->assertRedirect(route('student.checkout.pay', $order->order_code));

        // Kiểm tra Payment cũng được tạo ở trạng thái pending
        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('pending', $payment->status);
    }

    /**
     * Test học viên có thể giả lập thanh toán thành công.
     */
    public function test_student_can_simulate_successful_payment(): void
    {
        // Thêm vào giỏ hàng và checkout tạo đơn hàng trước
        $cart = Cart::firstOrCreate(['user_id' => $this->student->id]);
        $cart->courses()->attach($this->course->id);

        $this->actingAs($this->student)
            ->post(route('student.cart.checkout'), [
                'payment_method' => 'bank_transfer',
            ]);

        $order = Order::where('user_id', $this->student->id)->first();

        // Giả lập gửi thông tin thanh toán thành công
        $response = $this->actingAs($this->student)
            ->post(route('student.checkout.simulate', $order->order_code), [
                'status' => 'success',
            ]);

        $response->assertRedirect(route('student.checkout.success', $order->order_code));

        $order->refresh();
        $this->assertEquals('paid', $order->status);
        $this->assertNotNull($order->transaction_id);

        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertEquals('success', $payment->status);
        $this->assertNotNull($payment->paid_at);

        // Đảm bảo học viên đã được ghi danh
        $enrollment = Enrollment::where('user_id', $this->student->id)
            ->where('course_id', $this->course->id)
            ->first();
        $this->assertNotNull($enrollment);
        $this->assertEquals('active', $enrollment->status);

        // Giỏ hàng phải được làm sạch các khóa học đã mua
        $cart->refresh();
        $this->assertFalse($cart->courses->contains($this->course->id));
    }

    /**
     * Test học viên giả lập thanh toán thất bại.
     */
    public function test_student_can_simulate_failed_payment(): void
    {
        $cart = Cart::firstOrCreate(['user_id' => $this->student->id]);
        $cart->courses()->attach($this->course->id);

        $this->actingAs($this->student)
            ->post(route('student.cart.checkout'), [
                'payment_method' => 'bank_transfer',
            ]);

        $order = Order::where('user_id', $this->student->id)->first();

        $response = $this->actingAs($this->student)
            ->post(route('student.checkout.simulate', $order->order_code), [
                'status' => 'failed',
            ]);

        $response->assertRedirect(route('student.dashboard'));
        
        $order->refresh();
        $this->assertEquals('failed', $order->status);

        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertEquals('failed', $payment->status);

        // Học viên không được ghi danh
        $enrollment = Enrollment::where('user_id', $this->student->id)
            ->where('course_id', $this->course->id)
            ->first();
        $this->assertNull($enrollment);
    }
}
