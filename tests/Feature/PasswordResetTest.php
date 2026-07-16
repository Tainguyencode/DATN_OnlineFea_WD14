<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CaptchaService;
use App\Services\RoleSyncService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private const OLD_PASSWORD = 'OldPassword1!';

    private const NEW_PASSWORD = 'NewPassword1!';

    private const NEUTRAL_RESET_MESSAGE = 'Nếu email tồn tại trong hệ thống, liên kết đặt lại mật khẩu sẽ được gửi.';

    protected function setUp(): void
    {
        parent::setUp();

        app(RoleSyncService::class)->ensurePrimaryRolesExist();
    }

    public function test_forgot_password_page_is_accessible(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    public function test_password_reset_request_succeeds_for_existing_user(): void
    {
        Notification::fake();

        $user = $this->createResettableUser();

        $this->postForgotPassword($user->email)
            ->assertRedirect()
            ->assertSessionHas('success', self::NEUTRAL_RESET_MESSAGE);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_reset_notification_is_sent_for_existing_email(): void
    {
        Notification::fake();

        $user = $this->createResettableUser(['email' => 'notify-reset@example.com']);

        $this->postForgotPassword('notify-reset@example.com');

        Notification::assertSentTo($user, ResetPassword::class);
        Notification::assertCount(1);
    }

    public function test_unknown_email_still_receives_neutral_success_message(): void
    {
        Notification::fake();

        $this->postForgotPassword('unknown@example.com')
            ->assertRedirect()
            ->assertSessionHas('success', self::NEUTRAL_RESET_MESSAGE)
            ->assertSessionDoesntHaveErrors('email');

        Notification::assertNothingSent();
    }

    public function test_valid_token_resets_password_successfully(): void
    {
        $user = $this->createResettableUser(['email' => 'valid-token@example.com']);
        $token = Password::createToken($user);

        $this->postPasswordReset($user->email, $token, self::NEW_PASSWORD)
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check(self::NEW_PASSWORD, $user->password));
        $this->assertNotNull($user->password_changed_at);
    }

    public function test_invalid_token_cannot_reset_password(): void
    {
        $user = $this->createResettableUser(['email' => 'invalid-token@example.com']);

        $this->postPasswordReset($user->email, 'invalid-token-value', self::NEW_PASSWORD)
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check(self::OLD_PASSWORD, $user->fresh()->password));
    }

    public function test_used_token_cannot_be_reused(): void
    {
        $user = $this->createResettableUser(['email' => 'reuse-token@example.com']);
        $token = Password::createToken($user);

        $this->postPasswordReset($user->email, $token, self::NEW_PASSWORD)
            ->assertRedirect(route('login'));

        $this->postPasswordReset($user->email, $token, 'AnotherPass1!')
            ->assertRedirect()
            ->assertSessionHasErrors('email');
    }

    public function test_password_confirmation_mismatch_is_rejected_on_reset_form(): void
    {
        $user = $this->createResettableUser(['email' => 'mismatch@example.com']);
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => 'Different1!',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check(self::OLD_PASSWORD, $user->fresh()->password));
    }

    public function test_reset_password_is_hashed_in_database(): void
    {
        $user = $this->createResettableUser(['email' => 'hash-reset@example.com']);
        $token = Password::createToken($user);

        $this->postPasswordReset($user->email, $token, self::NEW_PASSWORD);

        $user->refresh();

        $this->assertNotSame(self::NEW_PASSWORD, $user->password);
        $this->assertTrue(Hash::check(self::NEW_PASSWORD, $user->password));
    }

    public function test_reset_password_rotates_remember_token(): void
    {
        $user = $this->createResettableUser([
            'email' => 'remember-rotate@example.com',
            'remember_token' => 'old-remember-token',
        ]);
        $token = Password::createToken($user);

        $this->postPasswordReset($user->email, $token, self::NEW_PASSWORD);

        $this->assertNotSame('old-remember-token', $user->fresh()->remember_token);
        $this->assertNotEmpty($user->fresh()->remember_token);
    }

    public function test_user_can_login_with_new_password_after_reset(): void
    {
        $user = $this->createResettableUser([
            'email' => 'login-new@example.com',
            'email_verified_at' => now(),
        ]);
        $token = Password::createToken($user);

        $this->postPasswordReset($user->email, $token, self::NEW_PASSWORD);

        $this->postLogin('login-new@example.com', self::NEW_PASSWORD)
            ->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_old_password_no_longer_works_after_reset(): void
    {
        $user = $this->createResettableUser(['email' => 'old-pass@example.com']);
        $token = Password::createToken($user);

        $this->postPasswordReset($user->email, $token, self::NEW_PASSWORD);

        $this->postLogin('old-pass@example.com', self::OLD_PASSWORD)
            ->assertSessionHasErrors('identifier');

        $this->assertGuest();
    }

    public function test_locked_user_can_reset_password_but_cannot_login(): void
    {
        $user = $this->createResettableUser([
            'email' => 'locked-reset@example.com',
            'is_active' => false,
        ]);
        $token = Password::createToken($user);

        $this->postPasswordReset($user->email, $token, self::NEW_PASSWORD)
            ->assertRedirect(route('login'));

        $this->assertTrue(Hash::check(self::NEW_PASSWORD, $user->fresh()->password));

        $this->postLogin('locked-reset@example.com', self::NEW_PASSWORD)
            ->assertSessionHasErrors('identifier');

        $this->assertGuest();
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createResettableUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'password' => Hash::make(self::OLD_PASSWORD),
            'role' => 'student',
        ], $overrides));
    }

    private function postForgotPassword(string $email): TestResponse
    {
        $captcha = $this->forgotPasswordCaptcha();

        return $this->post(route('password.email'), [
            'email' => $email,
            'captcha_token' => $captcha['token'],
            'captcha_answer' => $captcha['answer'],
        ]);
    }

    private function postPasswordReset(string $email, string $token, string $password): TestResponse
    {
        return $this->post(route('password.update'), [
            'token' => $token,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);
    }

    private function postLogin(string $identifier, string $password): TestResponse
    {
        $captcha = $this->loginCaptcha();

        return $this->post(route('login'), [
            'identifier' => $identifier,
            'password' => $password,
            'captcha_token' => $captcha['token'],
            'captcha_answer' => $captcha['answer'],
        ]);
    }

    /**
     * @return array{token: string, answer: string}
     */
    private function forgotPasswordCaptcha(): array
    {
        $this->startSession();

        $generated = CaptchaService::generate('forgot-password');
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
}
