<?php

namespace Tests\Feature;

use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Notifications\VerifyEmailCodeNotification;
use App\Services\CaptchaService;
use App\Services\EmailVerificationService;
use App\Services\RoleSyncService;
use App\Services\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(RoleSyncService::class)->ensurePrimaryRolesExist();
    }

    public function test_registration_creates_email_verification_code(): void
    {
        Notification::fake();

        $this->postRegister('student', ['email' => 'otp-create@example.com']);

        $user = User::query()->where('email', 'otp-create@example.com')->firstOrFail();

        $this->assertDatabaseHas('email_verification_codes', [
            'user_id' => $user->id,
        ]);
        $this->assertNull($user->email_verified_at);
    }

    public function test_registration_skips_email_verification_when_disabled(): void
    {
        config(['auth.email_verification_enabled' => false]);
        Notification::fake();

        $this->postRegister('student', ['email' => 'otp-disabled@example.com'])
            ->assertRedirect(route('student.dashboard'));

        $user = User::query()->where('email', 'otp-disabled@example.com')->firstOrFail();

        $this->assertDatabaseMissing('email_verification_codes', [
            'user_id' => $user->id,
        ]);
        $this->assertNull($user->email_verified_at);
        Notification::assertNothingSent();
    }

    public function test_otp_is_six_digits_in_notification(): void
    {
        Notification::fake();

        $this->postRegister('student', ['email' => 'otp-six@example.com']);

        $user = User::query()->where('email', 'otp-six@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmailCodeNotification::class, function (VerifyEmailCodeNotification $notification): bool {
            return preg_match('/^\d{6}$/', $notification->code) === 1;
        });
    }

    public function test_otp_is_hashed_in_database(): void
    {
        Notification::fake();

        $this->postRegister('student', ['email' => 'otp-hash@example.com']);

        $user = User::query()->where('email', 'otp-hash@example.com')->firstOrFail();
        $record = EmailVerificationCode::query()->where('user_id', $user->id)->firstOrFail();

        $this->assertNotSame('000000', $record->code_hash);
        $this->assertGreaterThan(20, strlen($record->code_hash));
        $this->assertDoesNotMatchRegularExpression('/^\d{6}$/', $record->code_hash);
    }

    public function test_verification_notification_is_sent_on_register(): void
    {
        Notification::fake();

        $this->postRegister('student', ['email' => 'otp-notify@example.com']);

        $user = User::query()->where('email', 'otp-notify@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmailCodeNotification::class);
        Notification::assertCount(1);
    }

    public function test_verification_notice_page_is_accessible(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'student']);

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertViewIs('auth.verify-email')
            ->assertSee('Xác thực email')
            ->assertSee('6 chữ số');
    }

    public function test_correct_code_verifies_email_successfully(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'student']);
        $code = '482910';
        $this->createActiveCode($user, $code);

        $this->actingAsStudent($user)
            ->post(route('verification.code.verify'), ['code' => $code])
            ->assertRedirect(route('student.dashboard'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_verified_at_is_updated_after_successful_verification(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'student']);
        $code = '123456';
        $this->createActiveCode($user, $code);

        $this->actingAsStudent($user)
            ->post(route('verification.code.verify'), ['code' => $code]);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_wrong_code_does_not_verify_email(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'student']);
        $this->createActiveCode($user, '111111');

        $this->actingAsStudent($user)
            ->post(route('verification.code.verify'), ['code' => '999999'])
            ->assertSessionHasErrors('code');

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_attempt_count_increases_on_wrong_code(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'student']);
        $record = $this->createActiveCode($user, '222222');

        $this->actingAsStudent($user)
            ->post(route('verification.code.verify'), ['code' => '000001']);

        $this->assertSame(1, $record->fresh()->attempt_count);
    }

    public function test_too_many_wrong_attempts_invalidates_code(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'student']);
        $record = $this->createActiveCode($user, '333333');

        for ($i = 0; $i < 5; $i++) {
            $this->actingAsStudent($user)
                ->post(route('verification.code.verify'), ['code' => '000000']);
        }

        $this->assertNotNull($record->fresh()->used_at);
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_expired_code_cannot_be_used(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'student']);
        $record = $this->createActiveCode($user, '444444', now()->subMinute());

        $this->actingAsStudent($user)
            ->post(route('verification.code.verify'), ['code' => '444444'])
            ->assertSessionHasErrors('code');

        $this->assertNotNull($record->fresh()->used_at);
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_used_code_cannot_be_reused(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'student']);
        $code = '555555';
        $this->createActiveCode($user, $code);

        $this->actingAsStudent($user)
            ->post(route('verification.code.verify'), ['code' => $code])
            ->assertRedirect(route('student.dashboard'));

        $user->forceFill(['email_verified_at' => null])->save();

        $this->actingAsStudent($user)
            ->post(route('verification.code.verify'), ['code' => $code])
            ->assertSessionHasErrors('code');
    }

    public function test_resending_code_invalidates_previous_code(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create(['role' => 'student']);
        $oldRecord = $this->createActiveCode($user, '666666', now()->addMinutes(10), now()->subMinutes(2));

        $this->travel(61)->seconds();

        $this->actingAsStudent($user)
            ->post(route('verification.send'))
            ->assertRedirect();

        $this->assertNotNull($oldRecord->fresh()->used_at);
        $this->assertSame(1, EmailVerificationCode::query()->where('user_id', $user->id)->whereNull('used_at')->count());
    }

    public function test_resend_is_blocked_before_sixty_seconds(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create(['role' => 'student']);
        $this->createActiveCode($user, '777777', now()->addMinutes(10), now());

        $this->actingAsStudent($user)
            ->post(route('verification.send'))
            ->assertSessionHasErrors('email');

        Notification::assertNothingSent();
    }

    public function test_verified_user_cannot_request_new_code(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $this->actingAsStudent($user)
            ->post(route('verification.send'))
            ->assertRedirect(route('student.dashboard'));

        Notification::assertNothingSent();
    }

    public function test_guest_cannot_verify_code(): void
    {
        $this->post(route('verification.code.verify'), ['code' => '123456'])
            ->assertRedirect(route('login'));
    }

    public function test_user_cannot_use_another_users_code(): void
    {
        $userA = User::factory()->unverified()->create(['role' => 'student', 'email' => 'user-a@example.com']);
        $userB = User::factory()->unverified()->create(['role' => 'student', 'email' => 'user-b@example.com']);
        $this->createActiveCode($userA, '888888');

        $this->actingAsStudent($userB)
            ->post(route('verification.code.verify'), ['code' => '888888'])
            ->assertSessionHasErrors('code');

        $this->assertFalse($userA->fresh()->hasVerifiedEmail());
        $this->assertFalse($userB->fresh()->hasVerifiedEmail());
    }

    public function test_code_starting_with_zero_works(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'student']);
        $code = '042851';
        $this->createActiveCode($user, $code);

        $this->actingAsStudent($user)
            ->post(route('verification.code.verify'), ['code' => $code])
            ->assertRedirect(route('student.dashboard'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_unverified_student_is_blocked_by_verified_middleware(): void
    {
        $student = User::factory()->unverified()->create(['role' => 'student']);

        $this->actingAsStudent($student)
            ->get(route('student.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_student_can_access_dashboard_when_email_verification_is_disabled(): void
    {
        config(['auth.email_verification_enabled' => false]);

        $student = User::factory()->unverified()->create(['role' => 'student']);

        $this->actingAsStudent($student)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertViewHas('emailVerified', true);
    }

    public function test_unverified_instructor_does_not_see_verification_banner_when_email_verification_is_disabled(): void
    {
        config(['auth.email_verification_enabled' => false]);

        $instructor = User::factory()->unverified()->create(['role' => 'instructor']);

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('instructor.dashboard'))
            ->assertOk()
            ->assertDontSee('email/verification-notification');
    }

    public function test_verified_student_can_access_dashboard(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $this->actingAsStudent($student)
            ->get(route('student.dashboard'))
            ->assertOk();
    }

    public function test_verified_instructor_can_access_dashboard(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('instructor.dashboard'))
            ->assertOk();
    }

    public function test_two_factor_flow_still_works(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'email_verified_at' => now(),
            'two_factor_enabled' => true,
        ]);

        $service = app(TwoFactorService::class);
        $code = $service->sendCode($user);

        $this->actingAs($user)
            ->post(route('two-factor.verify'), ['code' => $code])
            ->assertRedirect(route('student.dashboard'));
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function postRegister(string $role, array $overrides = []): \Illuminate\Testing\TestResponse
    {
        $captcha = $this->registerCaptcha();

        return $this->post(route('register.role', $role), array_merge([
            'name' => 'Người dùng OTP',
            'email' => 'otp-user@example.com',
            'phone' => '0912345678',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'terms' => '1',
            'captcha_token' => $captcha['token'],
            'captcha_answer' => $captcha['answer'],
        ], $overrides));
    }

    private function createActiveCode(User $user, string $plainCode, ?\Illuminate\Support\Carbon $expiresAt = null, ?\Illuminate\Support\Carbon $lastSentAt = null): EmailVerificationCode
    {
        return EmailVerificationCode::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make($plainCode),
            'expires_at' => $expiresAt ?? now()->addMinutes(10),
            'last_sent_at' => $lastSentAt,
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

    private function actingAsStudent(User $user): static
    {
        return $this->actingAs($user)->withSession([
            'two_factor_passed_at' => now()->timestamp,
        ]);
    }
}
