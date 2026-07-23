<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Services\CaptchaService;
use App\Services\RoleSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Testing\TestResponse;
use Mockery;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private const OLD_PASSWORD = 'OldPassword1!';

    private const NEW_PASSWORD = 'NewPassword1!';

    private const NEUTRAL_RESET_MESSAGE = 'Nếu email tồn tại trong hệ thống, liên kết đặt lại mật khẩu sẽ được gửi.';

    private const THROTTLE_MESSAGE = 'Vui lòng đợi trước khi yêu cầu gửi lại liên kết đặt lại mật khẩu.';

    protected function setUp(): void
    {
        parent::setUp();

        app(RoleSyncService::class)->ensurePrimaryRolesExist();
    }

    public function test_forgot_password_page_is_accessible(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    public function test_student_can_request_password_reset_link(): void
    {
        Notification::fake();

        $user = $this->createResettableUser([
            'email' => 'student-reset@example.com',
            'role' => 'student',
        ]);

        $this->postForgotPassword($user->email)
            ->assertRedirect()
            ->assertSessionHas('success', self::NEUTRAL_RESET_MESSAGE);

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_instructor_can_request_password_reset_link(): void
    {
        Notification::fake();

        $user = $this->createResettableUser([
            'email' => 'instructor-reset@example.com',
            'role' => 'instructor',
        ]);

        $this->postForgotPassword($user->email)
            ->assertRedirect()
            ->assertSessionHas('success', self::NEUTRAL_RESET_MESSAGE);

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_unknown_email_receives_neutral_success_message(): void
    {
        Notification::fake();

        $this->postForgotPassword('unknown@example.com')
            ->assertRedirect()
            ->assertSessionHas('success', self::NEUTRAL_RESET_MESSAGE)
            ->assertSessionDoesntHaveErrors('email');

        Notification::assertNothingSent();
    }

    public function test_password_reset_is_throttled_for_recent_request(): void
    {
        Notification::fake();

        $user = $this->createResettableUser(['email' => 'throttle-reset@example.com']);

        $this->postForgotPassword($user->email)
            ->assertSessionHas('success', self::NEUTRAL_RESET_MESSAGE);

        $this->postForgotPassword($user->email)
            ->assertRedirect()
            ->assertSessionHasErrors(['email' => self::THROTTLE_MESSAGE])
            ->assertSessionHas('resend_after');

        Notification::assertSentToTimes($user, ResetPasswordNotification::class, 1);
    }

    public function test_smtp_exception_returns_friendly_error_without_server_error(): void
    {
        Log::spy();

        $user = $this->createResettableUser(['email' => 'smtp-fail@example.com']);

        $broker = Mockery::mock(\Illuminate\Contracts\Auth\PasswordBroker::class);
        $broker->shouldReceive('sendResetLink')
            ->once()
            ->andThrow(new TransportException('Connection timed out'));

        Password::shouldReceive('broker')
            ->once()
            ->with('users')
            ->andReturn($broker);

        $this->postForgotPassword($user->email)
            ->assertRedirect()
            ->assertStatus(302)
            ->assertSessionHasErrors('email');

        Log::shouldHaveReceived('error')
            ->once()
            ->withArgs(function (string $message, array $context = []) use ($user): bool {
                if ($message !== 'Password reset email could not be sent.') {
                    return false;
                }

                $serialized = (string) json_encode($context);

                return ($context['user_id'] ?? null) === $user->id
                    && ($context['email_masked'] ?? null) === 'sm*****il@example.com'
                    && ($context['exception'] ?? null) === TransportException::class
                    && ! str_contains($serialized, 'smtp-fail@example.com')
                    && ! str_contains($serialized, 'Connection timed out')
                    && ! str_contains($serialized, 'MAIL_PASSWORD')
                    && ! array_key_exists('token', $context)
                    && ! array_key_exists('password', $context);
            });
    }

    public function test_reset_password_notification_is_vietnamese(): void
    {
        Notification::fake();

        $user = $this->createResettableUser(['email' => 'vn-mail@example.com']);

        $this->postForgotPassword($user->email);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function (ResetPasswordNotification $notification) use ($user) {
            $mail = $notification->toMail($user);

            return $mail->subject === 'Đặt lại mật khẩu OnlineFEA'
                && collect($mail->introLines)->contains(
                    fn (string $line) => str_contains($line, 'đặt lại mật khẩu')
                );
        });
    }

    public function test_valid_token_resets_password_for_student_and_redirects_to_login(): void
    {
        $user = $this->createResettableUser([
            'email' => 'student-valid@example.com',
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
        $token = Password::broker('users')->createToken($user);

        $this->postPasswordReset($user->email, $token, self::NEW_PASSWORD)
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check(self::NEW_PASSWORD, $user->password));
        $this->assertNotNull($user->password_changed_at);

        $this->postLogin($user->email, self::NEW_PASSWORD)
            ->assertRedirect(route('student.dashboard'));
    }

    public function test_valid_token_resets_password_for_instructor_and_redirects_to_login(): void
    {
        $user = $this->createResettableUser([
            'email' => 'instructor-valid@example.com',
            'role' => 'instructor',
            'email_verified_at' => now(),
        ]);
        $token = Password::broker('users')->createToken($user);

        $this->postPasswordReset($user->email, $token, self::NEW_PASSWORD)
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $this->postLogin($user->email, self::NEW_PASSWORD)
            ->assertRedirect(route('instructor.dashboard'));
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
        $token = Password::broker('users')->createToken($user);

        $this->postPasswordReset($user->email, $token, self::NEW_PASSWORD)
            ->assertRedirect(route('login'));

        $this->postPasswordReset($user->email, $token, 'AnotherPass1!')
            ->assertRedirect()
            ->assertSessionHasErrors('email');
    }

    public function test_password_confirmation_mismatch_is_rejected_on_reset_form(): void
    {
        $user = $this->createResettableUser(['email' => 'mismatch@example.com']);
        $token = Password::broker('users')->createToken($user);

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
        $token = Password::broker('users')->createToken($user);

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
        $token = Password::broker('users')->createToken($user);

        $this->postPasswordReset($user->email, $token, self::NEW_PASSWORD);

        $this->assertNotSame('old-remember-token', $user->fresh()->remember_token);
        $this->assertNotEmpty($user->fresh()->remember_token);
    }

    public function test_reset_password_deletes_database_sessions_for_user(): void
    {
        Config::set('session.driver', 'database');
        Config::set('session.table', 'sessions');

        $user = $this->createResettableUser(['email' => 'session-purge@example.com']);
        $other = $this->createResettableUser(['email' => 'session-keep@example.com']);
        $token = Password::broker('users')->createToken($user);

        DB::table('sessions')->insert([
            [
                'id' => 'session-user-a',
                'user_id' => $user->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'PHPUnit',
                'payload' => 'payload-a',
                'last_activity' => time(),
            ],
            [
                'id' => 'session-user-b',
                'user_id' => $other->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'PHPUnit',
                'payload' => 'payload-b',
                'last_activity' => time(),
            ],
        ]);

        $this->postPasswordReset($user->email, $token, self::NEW_PASSWORD)
            ->assertRedirect(route('login'));

        $this->assertDatabaseMissing('sessions', ['id' => 'session-user-a']);
        $this->assertDatabaseHas('sessions', ['id' => 'session-user-b']);
    }

    public function test_old_password_no_longer_works_after_reset(): void
    {
        $user = $this->createResettableUser(['email' => 'old-pass@example.com']);
        $token = Password::broker('users')->createToken($user);

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
        $token = Password::broker('users')->createToken($user);

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
