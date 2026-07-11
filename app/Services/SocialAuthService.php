<?php

namespace App\Services;

use App\Enums\SocialProvider;
use App\Exceptions\SocialAuthException;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class SocialAuthService
{
    public function resolveUser(string $provider, SocialiteUser $socialUser): User
    {
        if (! SocialProvider::tryFromName($provider)) {
            throw new SocialAuthException('Nhà cung cấp đăng nhập không được hỗ trợ.');
        }

        $providerId = (string) $socialUser->getId();
        $email = $this->resolveEmail($provider, $socialUser);

        if (! $email) {
            throw $this->missingEmailException($provider);
        }

        return DB::transaction(function () use ($provider, $providerId, $email, $socialUser) {
            $socialAccount = SocialAccount::query()
                ->where('provider', $provider)
                ->where('provider_user_id', $providerId)
                ->lockForUpdate()
                ->first();

            if ($socialAccount) {
                $this->syncSocialAccount($socialAccount, $socialUser, $email);

                return $this->ensureActive($socialAccount->user()->lockForUpdate()->firstOrFail());
            }

            $this->assertProviderNotLinkedToAnotherUser($provider, $providerId);

            $user = User::query()
                ->where('email', $email)
                ->lockForUpdate()
                ->first();

            if ($user) {
                $this->linkProvider($user, $provider, $socialUser, $email);

                return $this->ensureActive($user->fresh());
            }

            try {
                $user = User::create([
                    'name' => $socialUser->getName() ?: Str::headline(Str::before($email, '@')),
                    'username' => AuthService::generateUniqueUsername($socialUser->getName() ?: Str::before($email, '@')),
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => Str::password(24),
                    'role' => 'student',
                    'avatar' => $socialUser->getAvatar(),
                    'is_active' => true,
                ]);
            } catch (UniqueConstraintViolationException) {
                $user = User::query()
                    ->where('email', $email)
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $this->linkProvider($user, $provider, $socialUser, $email);

            return $this->ensureActive($user->fresh());
        });
    }

    private function resolveEmail(string $provider, SocialiteUser $socialUser): ?string
    {
        $email = $socialUser->getEmail();

        if ($email) {
            return Str::lower(trim($email));
        }

        if ($provider === SocialProvider::Facebook->value) {
            $raw = $socialUser->getRaw();

            if (! empty($raw['email'])) {
                return Str::lower(trim((string) $raw['email']));
            }
        }

        return null;
    }

    private function missingEmailException(string $provider): SocialAuthException
    {
        if ($provider === SocialProvider::Facebook->value) {
            return new SocialAuthException(
                'Tài khoản Facebook chưa cung cấp email. Vui lòng cấp quyền truy cập email trong Facebook hoặc đăng ký bằng Google/email thông thường.'
            );
        }

        return new SocialAuthException(
            'Tài khoản '.SocialProvider::tryFromName($provider)?->label().' chưa cung cấp email xác thực. Vui lòng thử phương thức đăng nhập khác.'
        );
    }

    private function linkProvider(User $user, string $provider, SocialiteUser $socialUser, string $email): void
    {
        $providerId = (string) $socialUser->getId();

        $this->assertProviderNotLinkedToAnotherUser($provider, $providerId, $user->id);

        $column = "{$provider}_id";
        if (in_array($column, ['google_id', 'facebook_id', 'github_id', 'microsoft_id'], true)) {
            $user->forceFill([
                $column => $socialUser->getId(),
                'email_verified_at' => $user->email_verified_at ?: now(),
                'avatar' => $user->avatar ?: $socialUser->getAvatar(),
            ])->save();
        }

        SocialAccount::updateOrCreate(
            ['provider' => $provider, 'provider_user_id' => $providerId],
            [
                'user_id' => $user->id,
                'provider_email' => $email,
                'avatar' => $socialUser->getAvatar(),
            ]
        );
    }

    private function assertProviderNotLinkedToAnotherUser(string $provider, string $providerId, ?int $exceptUserId = null): void
    {
        $query = SocialAccount::query()
            ->where('provider', $provider)
            ->where('provider_user_id', $providerId);

        if ($exceptUserId !== null) {
            $query->where('user_id', '!=', $exceptUserId);
        }

        if ($query->exists()) {
            throw new SocialAuthException('Tài khoản mạng xã hội này đã được liên kết với người dùng khác.');
        }

        $column = "{$provider}_id";
        if (! in_array($column, ['google_id', 'facebook_id', 'github_id', 'microsoft_id'], true)) {
            return;
        }

        $userQuery = User::query()->where($column, $providerId);

        if ($exceptUserId !== null) {
            $userQuery->where('id', '!=', $exceptUserId);
        }

        if ($userQuery->exists()) {
            throw new SocialAuthException('Tài khoản mạng xã hội này đã được liên kết với người dùng khác.');
        }
    }

    private function syncSocialAccount(SocialAccount $account, SocialiteUser $socialUser, string $email): void
    {
        $account->update([
            'provider_email' => $email,
            'avatar' => $socialUser->getAvatar() ?: $account->avatar,
        ]);
    }

    private function ensureActive(User $user): User
    {
        if (! $user->is_active) {
            throw new SocialAuthException('Tài khoản hiện đang bị khóa. Vui lòng liên hệ quản trị viên.');
        }

        return $user;
    }
}
