<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminCommissionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_get_commission_rate_returns_default_when_commission_rate_is_null(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'commission_rate' => null,
        ]);

        $this->assertEquals(20.00, $instructor->getCommissionRate());

        // Update default setting
        SystemSetting::set('default_commission_rate', 15.50);

        $this->assertEquals(15.50, $instructor->getCommissionRate());
    }

    public function test_user_get_commission_rate_returns_custom_rate_when_set(): void
    {
        SystemSetting::set('default_commission_rate', 20.00);

        $instructor = User::factory()->create([
            'role' => 'instructor',
            'commission_rate' => 12.00,
        ]);

        $this->assertEquals(12.00, $instructor->getCommissionRate());
    }

    public function test_admin_can_view_commissions_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.commissions.index'));

        $response->assertStatus(200);
        $response->assertSee('Quản lý tỷ lệ chiết khấu');
    }

    public function test_non_admin_cannot_access_commissions_page(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get(route('admin.commissions.index'));

        $this->assertTrue(in_array($response->getStatusCode(), [302, 403], true));
    }

    public function test_admin_can_update_default_commission_rate(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.commissions.update-default'), [
            'default_commission_rate' => 25.50,
        ]);

        $response->assertRedirect();
        $this->assertEquals('25.5', SystemSetting::get('default_commission_rate'));
    }

    public function test_admin_can_update_instructor_commission_rate(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $instructor = User::factory()->create(['role' => 'instructor', 'commission_rate' => null]);

        $response = $this->actingAs($admin)->put(route('admin.commissions.update-instructor', $instructor), [
            'commission_rate' => 18.00,
        ]);

        $response->assertRedirect();
        $this->assertEquals(18.00, $instructor->fresh()->commission_rate);
    }

    public function test_admin_can_reset_instructor_commission_rate_to_null(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $instructor = User::factory()->create(['role' => 'instructor', 'commission_rate' => 15.00]);

        $response = $this->actingAs($admin)->put(route('admin.commissions.update-instructor', $instructor), [
            'commission_rate' => '',
        ]);

        $response->assertRedirect();
        $this->assertNull($instructor->fresh()->commission_rate);
    }

    public function test_revenue_reports_use_net_sale_amount_after_discounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor']);
        $category = Category::create([
            'name' => 'Revenue '.uniqid(),
            'slug' => 'revenue-'.uniqid(),
        ]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Revenue course '.uniqid(),
            'slug' => 'revenue-course-'.uniqid(),
            'short_description' => 'Revenue report test',
            'description' => 'Revenue report test',
            'objectives' => 'Test reporting',
            'target_audience' => 'Students',
            'requirements' => 'None',
            'price' => 50000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => 'published',
            'is_published' => true,
        ]);

        $freeOrder = Order::create([
            'order_code' => 'FREE-'.uniqid(),
            'user_id' => $student->id,
            'subtotal' => 50000,
            'discount_amount' => 50000,
            'total_amount' => 0,
            'status' => 'paid',
        ]);
        OrderItem::create([
            'order_id' => $freeOrder->id,
            'course_id' => $course->id,
            'price' => 50000,
            'commission_rate' => 20,
            'commission_amount' => 0,
            'instructor_earning' => 0,
        ]);

        $paidOrder = Order::create([
            'order_code' => 'PAID-'.uniqid(),
            'user_id' => $student->id,
            'subtotal' => 50000,
            'discount_amount' => 0,
            'total_amount' => 50000,
            'status' => 'paid',
        ]);
        OrderItem::create([
            'order_id' => $paidOrder->id,
            'course_id' => $course->id,
            'price' => 50000,
            'commission_rate' => 20,
            'commission_amount' => 10000,
            'instructor_earning' => 40000,
        ]);

        $adminResponse = $this->actingAs($admin)->get(route('admin.revenue'));
        $adminCourse = $adminResponse->viewData('courseRevenue')->firstWhere('course_id', $course->id);
        $this->assertSame(50000.0, (float) $adminCourse->gross_amount);

        $commissionResponse = $this->actingAs($admin)->get(route('admin.commissions.index'));
        $instructorSales = $commissionResponse->viewData('instructorSalesData')[$instructor->id];
        $this->assertSame(50000.0, $instructorSales['total_sales']);

        $instructorResponse = $this->actingAs($instructor)->get(route('instructor.revenue'));
        $instructorCourse = $instructorResponse->viewData('courseRevenue')->firstWhere('course_id', $course->id);
        $this->assertSame(50000.0, (float) $instructorCourse->gross);
        $this->assertSame(50000.0, (float) $instructorResponse->viewData('totalGross'));
    }

    public function test_admin_revenue_filters_financials_and_reconciles_legacy_orders(): void
    {
        SystemSetting::set('default_commission_rate', 25);
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor']);
        $category = Category::create(['name' => 'Audit '.uniqid(), 'slug' => 'audit-'.uniqid()]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Audit revenue',
            'slug' => 'audit-revenue-'.uniqid(),
            'price' => 200000,
            'status' => 'published',
            'is_published' => true,
        ]);

        $inside = Order::create([
            'order_code' => 'INSIDE-'.uniqid(), 'user_id' => $student->id,
            'subtotal' => 100000, 'discount_amount' => 0, 'total_amount' => 100000, 'status' => 'paid',
        ]);
        $inside->forceFill(['created_at' => '2026-08-12 10:00:00', 'updated_at' => '2026-08-12 10:00:00'])->saveQuietly();
        OrderItem::create([
            'order_id' => $inside->id, 'course_id' => $course->id, 'price' => 100000,
            'commission_rate' => 20, 'commission_amount' => 20000, 'instructor_earning' => 80000,
        ]);

        $outside = Order::create([
            'order_code' => 'OUTSIDE-'.uniqid(), 'user_id' => $student->id,
            'subtotal' => 200000, 'discount_amount' => 0, 'total_amount' => 200000, 'status' => 'paid',
        ]);
        $outside->forceFill(['created_at' => '2026-08-20 10:00:00', 'updated_at' => '2026-08-20 10:00:00'])->saveQuietly();
        OrderItem::create([
            'order_id' => $outside->id, 'course_id' => $course->id, 'price' => 200000,
            'commission_rate' => 20, 'commission_amount' => 40000, 'instructor_earning' => 160000,
        ]);

        $legacy = Order::create([
            'order_code' => 'LEGACY-'.uniqid(), 'user_id' => $student->id,
            'subtotal' => 40000, 'discount_amount' => 0, 'total_amount' => 40000, 'status' => 'paid',
        ]);
        $legacy->forceFill(['created_at' => '2026-08-13 10:00:00', 'updated_at' => '2026-08-13 10:00:00'])->saveQuietly();

        $response = $this->actingAs($admin)->get(route('admin.revenue', [
            'start_date' => '2026-08-10',
            'end_date' => '2026-08-15',
        ]));

        $response->assertOk();
        $this->assertSame(140000.0, (float) $response->viewData('totalGross'));
        $this->assertSame(30000.0, (float) $response->viewData('totalCommission'));
        $this->assertSame(110000.0, (float) $response->viewData('totalInstructorEarning'));

        $month = $response->viewData('monthly')->firstWhere('month', '2026-08');
        $this->assertSame(140000.0, (float) $month->total);
        $this->assertSame(30000.0, (float) $month->commission);
        $this->assertSame(110000.0, (float) $month->instructor_earning);
    }

    public function test_revenue_filters_reject_invalid_ranges(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.revenue', [
            'start_date' => '2026-08-20',
            'end_date' => '2026-08-10',
            'month' => 13,
        ]))->assertSessionHasErrors(['end_date', 'month']);
    }
}
