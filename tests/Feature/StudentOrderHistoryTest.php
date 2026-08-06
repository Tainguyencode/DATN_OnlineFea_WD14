<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentOrderHistoryTest extends TestCase
{
    use RefreshDatabase;

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
            'payment_method' => 'momo',
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
        $response->assertSee('MoMo Wallet');
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
}
