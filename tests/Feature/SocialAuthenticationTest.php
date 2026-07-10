<?php

namespace Tests\Feature;

use App\Models\SocialAccount;
use App\Models\User;
use App\Services\SocialAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Mockery;
use Tests\TestCase;

class SocialAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.google.client_id' => 'google-test-id',
            'services.google.client_secret' => 'google-test-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
            'services.facebook.client_id' => 'facebook-test-id',
            'services.facebook.client_secret' => 'facebook-test-secret',
            'services.facebook.redirect' => 'http://localhost/auth/facebook/callback',
        ]);
    }

    public function test_google_redirect_succeeds(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\AbstractProvider');
        $provider->shouldReceive('redirect')->once()->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $response = $this->get(route('social.redirect', 'google'));

        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_facebook_redirect_requests_email_scope(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\AbstractProvider');
        $provider->shouldReceive('scopes')->once()->with(['email'])->andReturnSelf();
        $provider->shouldReceive('redirect')->once()->andReturn(redirect('https://www.facebook.com/v18.0/dialog/oauth'));

        Socialite::shouldReceive('driver')->once()->with('facebook')->andReturn($provider);

        $response = $this->get(route('social.redirect', 'facebook'));

        $response->assertRedirect('https://www.facebook.com/v18.0/dialog/oauth');
    }

    public function test_invalid_provider_is_rejected_with_friendly_message(): void
    {
        $response = $this->get(route('social.redirect', 'twitter'));

        $response->assertNotFound();
    }

    public function test_unconfigured_provider_shows_friendly_message(): void
    {
        config(['services.google.client_id' => null]);

        $response = $this->get(route('social.redirect', 'google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('social');
    }

    public function test_google_callback_creates_new_student_user(): void
    {
        $socialUser = $this->mockSocialiteUser('google-1', 'new.google@example.com', 'Google User');

        $this->mockSocialiteCallback('google', $socialUser);

        $response = $this->get(route('social.callback', 'google'));

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'new.google@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('student', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertNotNull($user->email_verified_at);

        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'google-1',
            'provider_email' => 'new.google@example.com',
        ]);
    }

    public function test_facebook_callback_creates_new_student_user(): void
    {
        $socialUser = $this->mockSocialiteUser('facebook-1', 'new.facebook@example.com', 'Facebook User');

        $this->mockSocialiteCallback('facebook', $socialUser, withEmailScope: true);

        $response = $this->get(route('social.callback', 'facebook'));

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'new.facebook@example.com',
            'role' => 'student',
        ]);
    }

    public function test_existing_email_links_social_account_without_creating_duplicate_user(): void
    {
        $existing = User::factory()->create([
            'email' => 'existing@example.com',
            'role' => 'student',
            'avatar' => null,
            'email_verified_at' => null,
        ]);

        $socialUser = $this->mockSocialiteUser('google-99', 'existing@example.com', 'Linked User', 'https://avatar.test/pic.png');

        $this->mockSocialiteCallback('google', $socialUser);

        $this->get(route('social.callback', 'google'))->assertRedirect(route('student.dashboard'));

        $this->assertSame(1, User::where('email', 'existing@example.com')->count());
        $existing->refresh();
        $this->assertSame('google-99', $existing->google_id);
        $this->assertNotNull($existing->email_verified_at);
        $this->assertSame('https://avatar.test/pic.png', $existing->avatar);
    }

    public function test_existing_social_account_logs_in_correct_user(): void
    {
        $user = User::factory()->create([
            'email' => 'social.user@example.com',
            'role' => 'instructor',
            'google_id' => 'google-existing',
        ]);

        SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'google-existing',
            'provider_email' => 'social.user@example.com',
        ]);

        $socialUser = $this->mockSocialiteUser('google-existing', 'social.user@example.com', 'Social User');

        $this->mockSocialiteCallback('google', $socialUser);

        $this->get(route('social.callback', 'google'))->assertRedirect(route('instructor.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_locked_user_cannot_login_via_social(): void
    {
        $user = User::factory()->create([
            'email' => 'locked@example.com',
            'is_active' => false,
            'google_id' => 'google-locked',
        ]);

        SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'google-locked',
            'provider_email' => 'locked@example.com',
        ]);

        $socialUser = $this->mockSocialiteUser('google-locked', 'locked@example.com', 'Locked User');

        $this->mockSocialiteCallback('google', $socialUser);

        $response = $this->get(route('social.callback', 'google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('social');
        $this->assertGuest();
    }

    public function test_facebook_without_email_shows_friendly_message(): void
    {
        $socialUser = $this->mockSocialiteUser('facebook-no-email', null, 'No Email User');

        $this->mockSocialiteCallback('facebook', $socialUser, withEmailScope: true);

        $response = $this->get(route('social.callback', 'facebook'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('social');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_cancelled_callback_shows_friendly_message(): void
    {
        $response = $this->get(route('social.callback', ['provider' => 'google', 'error' => 'access_denied']));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('social');
        $this->assertGuest();
    }

    public function test_invalid_state_shows_friendly_message(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\AbstractProvider');
        $provider->shouldReceive('user')->once()->andThrow(new InvalidStateException('Invalid state'));

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $response = $this->get(route('social.callback', 'google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('social');
    }

    public function test_admin_redirects_to_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'admin.social@example.com',
            'role' => 'admin',
            'google_id' => 'google-admin',
        ]);

        SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'google-admin',
            'provider_email' => 'admin.social@example.com',
        ]);

        $socialUser = $this->mockSocialiteUser('google-admin', 'admin.social@example.com', 'Admin User');

        $this->mockSocialiteCallback('google', $socialUser);

        $this->get(route('social.callback', 'google'))->assertRedirect(route('admin.dashboard'));
    }

    public function test_session_is_regenerated_after_social_login(): void
    {
        $socialUser = $this->mockSocialiteUser('google-session', 'session@example.com', 'Session User');
        $this->mockSocialiteCallback('google', $socialUser);

        $this->startSession();
        $oldSessionId = session()->getId();

        $this->get(route('social.callback', 'google'));

        $this->assertNotSame($oldSessionId, session()->getId());
    }

    public function test_service_does_not_create_duplicate_users_for_same_email(): void
    {
        $socialUser = $this->mockSocialiteUser('google-dup', 'dup@example.com', 'Dup User');
        $service = app(SocialAuthService::class);

        $first = $service->resolveUser('google', $socialUser);
        $second = $service->resolveUser('google', $socialUser);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, User::where('email', 'dup@example.com')->count());
        $this->assertSame(1, SocialAccount::where('provider', 'google')->where('provider_user_id', 'google-dup')->count());
    }

    public function test_social_account_linked_to_other_user_logs_in_linked_account(): void
    {
        $owner = User::factory()->create(['email' => 'owner@example.com']);
        User::factory()->create(['email' => 'other@example.com']);

        SocialAccount::create([
            'user_id' => $owner->id,
            'provider' => 'google',
            'provider_user_id' => 'google-conflict',
            'provider_email' => 'owner@example.com',
        ]);

        $socialUser = $this->mockSocialiteUser('google-conflict', 'other@example.com', 'Conflict User');

        $user = app(SocialAuthService::class)->resolveUser('google', $socialUser);

        $this->assertSame($owner->id, $user->id);
        $this->assertSame(1, SocialAccount::where('provider', 'google')->where('provider_user_id', 'google-conflict')->count());
    }

    public function test_social_provider_linked_on_another_user_is_rejected_when_linking(): void
    {
        User::factory()->create([
            'email' => 'owner@example.com',
            'google_id' => 'google-conflict',
        ]);
        User::factory()->create(['email' => 'other@example.com']);

        $socialUser = $this->mockSocialiteUser('google-conflict', 'other@example.com', 'Conflict User');

        $this->expectException(\App\Exceptions\SocialAuthException::class);

        app(SocialAuthService::class)->resolveUser('google', $socialUser);
    }

    private function mockSocialiteUser(
        string $id,
        ?string $email,
        string $name = 'Test User',
        ?string $avatar = null,
    ): SocialiteUser {
        $user = Mockery::mock(SocialiteUser::class);
        $user->shouldReceive('getId')->andReturn($id);
        $user->shouldReceive('getEmail')->andReturn($email);
        $user->shouldReceive('getName')->andReturn($name);
        $user->shouldReceive('getAvatar')->andReturn($avatar);
        $user->shouldReceive('getNickname')->andReturn(null);
        $user->shouldReceive('getRaw')->andReturn([]);

        return $user;
    }

    private function mockSocialiteCallback(string $provider, SocialiteUser $socialUser, bool $withEmailScope = false): void
    {
        $providerMock = Mockery::mock('Laravel\Socialite\Two\AbstractProvider');

        if ($withEmailScope) {
            $providerMock->shouldReceive('scopes')->once()->with(['email'])->andReturnSelf();
        }

        $providerMock->shouldReceive('user')->once()->andReturn($socialUser);

        Socialite::shouldReceive('driver')->once()->with($provider)->andReturn($providerMock);
    }

    public function test_login_page_works_when_social_oauth_is_not_configured(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
            'services.facebook.client_id' => null,
            'services.facebook.client_secret' => null,
        ]);

        $this->get(route('login'))->assertOk();
    }
}
