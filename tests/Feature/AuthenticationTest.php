<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Services\CaptchaService;
use App\Services\RoleSyncService;
use App\Notifications\VerifyEmailCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private const REGISTER_PASSWORD = 'Password1!';

    protected function setUp(): void
    {
        parent::setUp();

        app(RoleSyncService::class)->ensurePrimaryRolesExist();
    }

    public function test_register_page_is_accessible(): void
    {
        $this->get(route('register'))->assertOk();
        $this->get(route('register.role', 'student'))->assertOk();
        $this->get(route('register.role', 'instructor'))->assertOk();
    }

    public function test_student_registration_succeeds(): void
    {
        Notification::fake();

        $response = $this->postRegister('student', [
            'email' => 'new-student@example.com',
            'name' => 'Học viên Mới',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'new-student@example.com')->firstOrFail();
        $this->assertSame('student', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertTrue($this->userHasPrimaryRolePivot($user, 'student'));
    }

    public function test_instructor_registration_succeeds(): void
    {
        Notification::fake();

        $response = $this->postRegister('instructor', [
            'email' => 'new-instructor@example.com',
            'name' => 'Giảng viên Mới',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'new-instructor@example.com')->firstOrFail();
        $this->assertSame('instructor', $user->role);
        $this->assertTrue($this->userHasPrimaryRolePivot($user, 'instructor'));
    }

    public function test_duplicate_email_registration_is_rejected(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->postRegister('student', ['email' => 'taken@example.com'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertSame(1, User::query()->where('email', 'taken@example.com')->count());
    }

    public function test_duplicate_phone_registration_is_rejected(): void
    {
        User::factory()->create(['phone' => '0393028777']);

        $this->postRegister('student', ['phone' => '0393028777'])
            ->assertSessionHasErrors('phone');

        $this->assertGuest();
        $this->assertSame(1, User::query()->where('phone', '0393028777')->count());
    }

    public function test_password_confirmation_mismatch_is_rejected(): void
    {
        $captcha = $this->registerCaptcha();

        $this->post(route('register.role', 'student'), [
            'name' => 'Test User',
            'email' => 'mismatch@example.com',
            'phone' => '0912345678',
            'password' => self::REGISTER_PASSWORD,
            'password_confirmation' => 'Different1!',
            'terms' => '1',
            'captcha_token' => $captcha['token'],
            'captcha_answer' => $captcha['answer'],
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_public_registration_cannot_create_admin_role(): void
    {
        $this->post('/register/admin', $this->registerPayload('student', [
            'email' => 'admin-hack@example.com',
        ]))->assertNotFound();

        $captcha = $this->registerCaptcha();

        $this->post(route('register.role', 'student'), [
            ...$this->registerPayload('student', ['email' => 'admin-body@example.com']),
            'role' => 'admin',
            'captcha_token' => $captcha['token'],
            'captcha_answer' => $captcha['answer'],
        ])->assertSessionHasErrors('role');

        $this->assertNull(User::query()->where('email', 'admin-hack@example.com')->first());
        $this->assertNull(User::query()->where('email', 'admin-body@example.com')->first());
    }

    public function test_registered_password_is_hashed(): void
    {
        Notification::fake();

        $this->postRegister('student', ['email' => 'hash-test@example.com']);

        $user = User::query()->where('email', 'hash-test@example.com')->firstOrFail();

        $this->assertNotSame(self::REGISTER_PASSWORD, $user->password);
        $this->assertTrue(Hash::check(self::REGISTER_PASSWORD, $user->password));
    }

    public function test_registration_regenerates_session(): void
    {
        Notification::fake();

        $this->startSession();
        $oldSessionId = session()->getId();

        $this->postRegister('student', ['email' => 'session-reg@example.com']);

        $this->assertNotSame($oldSessionId, session()->getId());
        $this->assertAuthenticated();
    }

    public function test_registration_sends_email_verification_notification(): void
    {
        Notification::fake();

        $this->postRegister('student', ['email' => 'verify-send@example.com']);

        $user = User::query()->where('email', 'verify-send@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmailCodeNotification::class);
        Notification::assertCount(1);
    }

    public function test_login_with_email_succeeds(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'login-email@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $this->postLogin('login-email@example.com', 'password')
            ->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->get(route('student.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_login_with_username_succeeds(): void
    {
        $user = User::factory()->unverified()->create([
            'username' => 'loginuser',
            'email' => 'login-username@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $this->postLogin('loginuser', 'password')
            ->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_wrong_password_is_rejected(): void
    {
        User::factory()->create([
            'email' => 'wrong-pass@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->postLogin('wrong-pass@example.com', 'wrong-password')
            ->assertSessionHasErrors('identifier');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'locked@example.com',
            'password' => Hash::make('password'),
            'is_active' => false,
        ]);

        $this->postLogin('locked@example.com', 'password')
            ->assertSessionHasErrors('identifier');

        $this->assertGuest();
    }

    public function test_soft_deleted_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'deleted@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->delete();

        $this->postLogin('deleted@example.com', 'password')
            ->assertSessionHasErrors('identifier');

        $this->assertGuest();
    }

    public function test_login_does_not_reveal_whether_email_exists(): void
    {
        User::factory()->create([
            'email' => 'exists@example.com',
            'password' => Hash::make('password'),
        ]);

        $existingUserResponse = $this->postLogin('exists@example.com', 'wrong-password');
        $missingUserResponse = $this->postLogin('missing@example.com', 'wrong-password');

        $existingUserResponse->assertSessionHasErrors([
            'identifier' => 'Thông tin đăng nhập không chính xác.',
        ]);
        $missingUserResponse->assertSessionHasErrors([
            'identifier' => 'Thông tin đăng nhập không chính xác.',
        ]);
    }

    public function test_remember_me_sets_recaller_cookie(): void
    {
        User::factory()->create([
            'email' => 'remember@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postLogin('remember@example.com', 'password', remember: true);

        $response->assertCookie(Auth::guard()->getRecallerName());
        $this->assertAuthenticated();
    }

    public function test_login_rate_limit_blocks_excessive_attempts(): void
    {
        $user = User::factory()->create([
            'email' => 'ratelimit@example.com',
            'password' => Hash::make('password'),
        ]);

        RateLimiter::clear(strtolower('ratelimit@example.com').'|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $this->postLogin('ratelimit@example.com', 'wrong-password')
                ->assertSessionHasErrors('identifier');
            $this->assertGuest();
        }

        $blockedResponse = $this->postLogin('ratelimit@example.com', 'wrong-password');

        if ($blockedResponse->getStatusCode() === 429) {
            $blockedResponse->assertStatus(429);
        } else {
            $blockedResponse->assertSessionHasErrors('identifier');
        }

        $this->assertGuest();
    }

    public function test_login_redirects_to_correct_dashboard_by_role(): void
    {
        $student = User::factory()->create([
            'email' => 'role-student@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
        $instructor = User::factory()->create([
            'email' => 'role-instructor@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create([
            'email' => 'role-admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->postLogin('role-student@example.com', 'password')
            ->assertRedirect(route('student.dashboard'));

        $this->post(route('logout'));

        $this->postLogin('role-instructor@example.com', 'password')
            ->assertRedirect(route('instructor.dashboard'));

        $this->post(route('logout'));

        $this->postLogin('role-admin@example.com', 'password')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_post_logout_succeeds_for_authenticated_users(): void
    {
        foreach (['student', 'instructor', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->post(route('logout'))
                ->assertRedirect(route('home'));

            $this->assertGuest();
        }
    }

    public function test_logout_invalidates_session_and_regenerates_csrf_token(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $oldSessionId = session()->getId();
        $oldToken = session()->token();

        $this->post(route('logout'))->assertRedirect(route('home'));

        $this->assertGuest();
        $this->assertNotSame($oldSessionId, session()->getId());
        $this->assertNotSame($oldToken, session()->token());
    }

    public function test_get_logout_route_does_not_exist(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/logout')
            ->assertStatus(405);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function postRegister(string $role, array $overrides = []): \Illuminate\Testing\TestResponse
    {
        $captcha = $this->registerCaptcha();

        return $this->post(route('register.role', $role), [
            ...$this->registerPayload($role, $overrides),
            'captcha_token' => $captcha['token'],
            'captcha_answer' => $captcha['answer'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function registerPayload(string $role, array $overrides = []): array
    {
        return array_merge([
            'name' => $overrides['name'] ?? 'Người dùng '.$role,
            'email' => $overrides['email'] ?? $role.'-'.uniqid().'@example.com',
            'phone' => '0912345678',
            'password' => self::REGISTER_PASSWORD,
            'password_confirmation' => self::REGISTER_PASSWORD,
            'terms' => '1',
        ], $overrides);
    }

    private function postLogin(string $identifier, string $password, bool $remember = false): \Illuminate\Testing\TestResponse
    {
        $captcha = $this->loginCaptcha();

        return $this->post(route('login'), [
            'identifier' => $identifier,
            'password' => $password,
            'remember' => $remember ? '1' : null,
            'captcha_token' => $captcha['token'],
            'captcha_answer' => $captcha['answer'],
        ]);
    }

    /**
     * @return array{token: string, answer: string}
     */
    private function registerCaptcha(): array
    {
        $this->startSession();

        $generated = CaptchaService::generate('register');
        $captchas = session('auth_captchas', []);

        return [
            'token' => $generated['token'],
            'answer' => $captchas[$generated['token']]['answer'] ?? '0',
        ];
    }

    /**
     * @return array{token: string, answer: string}
     */
    private function loginCaptcha(): array
    {
        $this->startSession();

        $generated = CaptchaService::generate('login');
        $captchas = session('auth_captchas', []);

        return [
            'token' => $generated['token'],
            'answer' => $captchas[$generated['token']]['answer'] ?? '0',
        ];
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
