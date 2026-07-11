<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Services\CaptchaService;
use App\Services\RoleSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RoleAndMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(RoleSyncService::class)->ensurePrimaryRolesExist();
    }

    public function test_student_has_primary_role_student(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $this->assertSame('student', $user->role);
    }

    public function test_instructor_has_primary_role_instructor(): void
    {
        $user = User::factory()->create(['role' => 'instructor']);

        $this->assertSame('instructor', $user->role);
    }

    public function test_admin_has_primary_role_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertSame('admin', $user->role);
    }

    public function test_student_registration_syncs_role_user_pivot(): void
    {
        $captcha = $this->registerCaptcha();

        $this->post(route('register.role', 'student'), [
            'name' => 'Học viên Mới',
            'email' => 'new-student@example.com',
            'phone' => '0912345678',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'terms' => '1',
            'captcha_token' => $captcha['token'],
            'captcha_answer' => $captcha['answer'],
        ])->assertRedirect();

        $user = User::query()->where('email', 'new-student@example.com')->firstOrFail();

        $this->assertSame('student', $user->role);
        $this->assertTrue($this->userHasPrimaryRolePivot($user, 'student'));
    }

    public function test_instructor_registration_syncs_role_user_pivot(): void
    {
        $captcha = $this->registerCaptcha();

        $this->post(route('register.role', 'instructor'), [
            'name' => 'Giảng viên Mới',
            'email' => 'new-instructor@example.com',
            'phone' => '0912345679',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'terms' => '1',
            'captcha_token' => $captcha['token'],
            'captcha_answer' => $captcha['answer'],
        ])->assertRedirect();

        $user = User::query()->where('email', 'new-instructor@example.com')->firstOrFail();

        $this->assertSame('instructor', $user->role);
        $this->assertTrue($this->userHasPrimaryRolePivot($user, 'instructor'));
    }

    public function test_admin_update_user_role_syncs_role_user_pivot(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAsVerified($admin)
            ->put(route('admin.users.update', $student), ['role' => 'instructor'])
            ->assertRedirect();

        $student->refresh();

        $this->assertSame('instructor', $student->role);
        $this->assertTrue($this->userHasPrimaryRolePivot($student, 'instructor'));
        $this->assertFalse($this->userHasPrimaryRolePivot($student, 'student'));
    }

    public function test_sync_primary_role_does_not_create_duplicate_pivot_rows(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $roleId = Role::query()->where('slug', 'student')->value('id');

        $user->syncPrimaryRole();
        $user->syncPrimaryRole();

        $this->assertSame(
            1,
            DB::table('role_user')
                ->where('user_id', $user->id)
                ->where('role_id', $roleId)
                ->count()
        );
    }

    public function test_student_cannot_access_admin_area(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAsVerified($student)
            ->get(route('admin.dashboard'))
            ->assertRedirect($student->dashboardUrl())
            ->assertSessionHas('error');
    }

    public function test_instructor_cannot_access_admin_area(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);

        $this->actingAsVerified($instructor)
            ->get(route('admin.dashboard'))
            ->assertRedirect($instructor->dashboardUrl())
            ->assertSessionHas('error');
    }

    public function test_student_cannot_access_instructor_area(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAsVerified($student)
            ->get(route('instructor.dashboard'))
            ->assertRedirect($student->dashboardUrl())
            ->assertSessionHas('error');
    }

    public function test_admin_can_access_admin_area(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAsVerified($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_inactive_user_is_logged_out_and_cannot_access_protected_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'is_active' => false,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('student.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('identifier');

        $this->assertGuest();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $this->actingAsVerified($admin)
            ->put(route('admin.users.update', $admin), ['toggle_active' => '1'])
            ->assertSessionHasErrors('error');

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_cannot_demote_own_account_from_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAsVerified($admin)
            ->put(route('admin.users.update', $admin), ['role' => 'student'])
            ->assertSessionHasErrors('role');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    /**
     * @return array{token: string, answer: string}
     */
    private function registerCaptcha(): array
    {
        $this->startSession();

        $generated = CaptchaService::generate('register');
        $captchas = session('auth_captchas', []);
        $answer = $captchas[$generated['token']]['answer'] ?? '0';

        return [
            'token' => $generated['token'],
            'answer' => $answer,
        ];
    }

    private function actingAsVerified(User $user): static
    {
        $user->forceFill(['email_verified_at' => now(), 'is_active' => true])->save();

        return $this->actingAs($user)->withSession([
            'two_factor_passed_at' => now()->timestamp,
        ]);
    }

    private function userHasPrimaryRolePivot(User $user, string $slug): bool
    {
        $roleId = Role::query()->where('slug', $slug)->value('id');

        if (! $roleId) {
            return false;
        }

        return DB::table('role_user')
            ->where('user_id', $user->id)
            ->where('role_id', $roleId)
            ->exists();
    }
}
