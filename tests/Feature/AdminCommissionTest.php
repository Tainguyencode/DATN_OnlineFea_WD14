<?php

namespace Tests\Feature;

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
}
