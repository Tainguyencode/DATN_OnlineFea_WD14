<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorCouponTest extends TestCase
{
    use RefreshDatabase;

    public function test_instructor_can_create_and_manage_own_coupons(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'email_verified_at' => now(),
        ]);
        $course = $this->createCourse($instructor);

        $response = $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.coupons.store'), [
                'code' => 'TEACHER10',
                'type' => 'percent',
                'value' => 10,
                'min_order_amount' => 50000,
                'max_uses' => 100,
                'course_id' => $course->id,
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('instructor.coupons.index'));

        $this->assertDatabaseHas('coupons', [
            'code' => 'TEACHER10',
            'creator_type' => 'instructor',
            'instructor_id' => $instructor->id,
            'course_id' => $course->id,
            'value' => 10.00,
        ]);
    }

    public function test_instructor_cannot_create_coupon_for_other_instructors_course(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $otherInstructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $otherCourse = $this->createCourse($otherInstructor);

        $response = $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.coupons.store'), [
                'code' => 'INVALIDCOUPON',
                'type' => 'fixed',
                'value' => 50000,
                'course_id' => $otherCourse->id,
            ]);

        $response->assertSessionHasErrors('course_id');
        $this->assertDatabaseMissing('coupons', ['code' => 'INVALIDCOUPON']);
    }

    public function test_checkout_with_instructor_coupon_deducts_from_instructor_earning_not_admin_commission(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'commission_rate' => 20.00, // 20% to Admin, 80% to Instructor
        ]);
        $course = $this->createCourse($instructor, 1000000); // 1,000,000 đ

        // Instructor Coupon: 100,000 đ discount
        $coupon = Coupon::create([
            'code' => 'INST100K',
            'creator_type' => 'instructor',
            'instructor_id' => $instructor->id,
            'type' => 'fixed',
            'value' => 100000,
            'min_order_amount' => 0,
            'is_active' => true,
        ]);

        $cart = Cart::create(['user_id' => $student->id]);
        $cart->courses()->attach($course->id);

        $response = $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('student.cart.checkout'), [
                'payment_method' => 'bank_transfer',
                'coupon_code' => 'INST100K',
                'course_ids' => [$course->id],
            ]);

        $response->assertRedirect();

        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals(900000, $order->total_amount); // 1,000,000 - 100,000 = 900,000

        $orderItem = OrderItem::where('order_id', $order->id)->first();
        $this->assertNotNull($orderItem);

        // Under Instructor Coupon:
        // Admin commission = 1,000,000 * 20% = 200,000 đ
        // Instructor earning = 900,000 - 200,000 = 700,000 đ
        $this->assertEquals(200000, $orderItem->commission_amount);
        $this->assertEquals(700000, $orderItem->instructor_earning);
    }

    public function test_checkout_with_admin_coupon_deducts_from_admin_commission_not_instructor_earning(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'commission_rate' => 20.00, // 20% to Admin, 80% to Instructor
        ]);
        $course = $this->createCourse($instructor, 1000000); // 1,000,000 đ

        // Admin Coupon: 100,000 đ discount
        $coupon = Coupon::create([
            'code' => 'ADMIN100K',
            'creator_type' => 'admin',
            'type' => 'fixed',
            'value' => 100000,
            'min_order_amount' => 0,
            'is_active' => true,
        ]);

        $cart = Cart::create(['user_id' => $student->id]);
        $cart->courses()->attach($course->id);

        $response = $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('student.cart.checkout'), [
                'payment_method' => 'bank_transfer',
                'coupon_code' => 'ADMIN100K',
                'course_ids' => [$course->id],
            ]);

        $response->assertRedirect();

        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals(900000, $order->total_amount); // 1,000,000 - 100,000 = 900,000

        $orderItem = OrderItem::where('order_id', $order->id)->first();
        $this->assertNotNull($orderItem);

        // Under Admin Coupon:
        // Instructor earning = 1,000,000 * 80% = 800,000 đ
        // Admin commission = 900,000 - 800,000 = 100,000 đ
        $this->assertEquals(800000, $orderItem->instructor_earning);
        $this->assertEquals(100000, $orderItem->commission_amount);
    }

    private function createCourse(User $instructor, float $price = 500000): Course
    {
        $category = Category::create([
            'name' => 'Cat ' . uniqid(),
            'slug' => 'cat-' . uniqid(),
        ]);

        return Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Course ' . uniqid(),
            'slug' => 'course-' . uniqid(),
            'short_description' => 'Short desc',
            'description' => 'Full description',
            'objectives' => 'Objectives',
            'target_audience' => 'Audience',
            'requirements' => 'Requirements',
            'price' => $price,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => 'published',
            'is_published' => true,
        ]);
    }
}
