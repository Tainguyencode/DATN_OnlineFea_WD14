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
        $response->assertSee('data-header-left', false);
        $response->assertSee('data-header-right', false);
        $response->assertSee('data-primary-navigation', false);
        $response->assertSee('Khóa học');
        $response->assertSee('Xếp hạng');
        $response->assertSee('Lộ trình');
        $response->assertSee('Giảng viên');
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
        $response->assertSee('data-student-wishlist', false);
        $response->assertSee('data-student-cart', false);
        $response->assertSee('Tổng quan');
        $response->assertSee('Bảo mật tài khoản');
    }

    public function test_student_home_and_dashboard_use_the_same_primary_navigation(): void
    {
        $student = User::factory()->create([
            'role' => 'student', 'is_active' => true, 'email_verified_at' => now(),
        ]);
        $this->actingAs($student)->withSession(['two_factor_passed_at' => now()->timestamp]);
        $home = $this->get(route('home'))->assertOk();
        $dashboard = $this->get(route('student.dashboard'))->assertOk();
        preg_match('/<nav data-primary-navigation.*?<\/nav>/s', $home->getContent(), $homeNav);
        preg_match('/<nav data-primary-navigation.*?<\/nav>/s', $dashboard->getContent(), $dashboardNav);
        $this->assertNotEmpty($homeNav);
        $this->assertSame($dashboardNav[0], $homeNav[0]);
        $this->assertStringContainsString('Khám phá', $homeNav[0]);
        $this->assertStringContainsString('Học tập', $homeNav[0]);
        $this->assertStringContainsString('Hỗ trợ', $homeNav[0]);
        $home->assertDontSee('studentSidebarDesktopOpen', false);
        $dashboard->assertSee('toggle-student-sidebar', false);
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
        $response->assertSee('data-public-header', false);
        $response->assertSee('instructor-shell', false);
        $response->assertSee('Quản lý khóa học');
    }

    public function test_public_instructors_page_loads_and_searches_without_query_exception(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
            'email_verified_at' => now(),
            'name' => 'Nguyễn Văn Giảng Viên',
        ]);

        \App\Models\InstructorProfile::create([
            'user_id' => $instructor->id,
            'position' => 'Senior Backend Developer',
            'specialty' => 'Laravel & MySQL',
            'teaching_field' => 'Công nghệ thông tin',
            'bio' => 'Hơn 8 năm kinh nghiệm giảng dạy và phát triển web.',
        ]);

        $response = $this->get(route('instructors.index'));
        $response->assertOk();
        $response->assertSee('Nguyễn Văn Giảng Viên');
        $response->assertSee('Senior Backend Developer');

        $searchResponse = $this->get(route('instructors.index', ['search' => 'Backend']));
        $searchResponse->assertOk();
        $searchResponse->assertSee('Nguyễn Văn Giảng Viên');
    }
}
