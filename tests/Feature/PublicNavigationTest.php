<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_public_header_without_student_navigation(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('data-public-header', false);
        $response->assertSee('Khám phá');
        $response->assertSee('Đăng nhập');
        $response->assertDontSee('id="public-nav-learning"', false);
        $response->assertDontSee('/student/orders', false);
    }

    public function test_student_pages_share_the_public_header_and_account_links(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('favorites.index'))
            ->assertOk()
            ->assertSee('data-public-header', false)
            ->assertSee('data-student-public-layout', false)
            ->assertSee('Khóa học yêu thích')
            ->assertSee('/student/orders', false)
            ->assertSee('/student/profile', false);

        $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('student.orders'))
            ->assertOk()
            ->assertSee('data-public-header', false)
            ->assertSee('Đơn hàng');

        $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('student.profile'))
            ->assertOk()
            ->assertSee('data-public-header', false)
            ->assertSee('Hồ sơ cá nhân');
    }

    public function test_authenticated_student_header_exposes_dropdown_accessibility_contract(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($student)->get(route('favorites.index'));

        $response->assertOk();
        $response->assertSee('aria-haspopup="true"', false);
        $response->assertSee('aria-expanded', false);
        $response->assertSee('x-transition:enter', false);
        $response->assertSee('opacity-0 -translate-y-2', false);
        $response->assertSee('Học tập');
        $response->assertSee('Voucher của tôi');
    }

    public function test_instructor_dashboard_keeps_role_specific_sidebar_shell(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('instructor.dashboard'));

        $response->assertOk();
        $response->assertDontSee('data-public-header', false);
        $response->assertSee('instructor-shell', false);
        $response->assertSee('Quản lý khóa học');
    }
}
