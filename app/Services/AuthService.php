<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(string $identifier, string $password, bool $remember, string $throttleKey, Request $request): User
    {
        $column = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [$column => $identifier, 'password' => $password];

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'identifier' => 'Thông tin đăng nhập không chính xác.',
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();

            throw ValidationException::withMessages([
                'identifier' => 'Tài khoản hiện đang bị khóa. Vui lòng liên hệ quản trị viên.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        ActivityLogService::log($user->id, 'login', User::class, $user->id, [
            'remember' => $remember,
        ], $request);

        return $user;
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function register(array $validated, Request $request): User
    {
        $avatarPath = null;

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'username' => self::generateUniqueUsername($validated['name']),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'avatar' => $avatarPath,
            'password_changed_at' => now(),
        ]);

        ActivityLogService::log($user->id, 'register', User::class, $user->id, [
            'role' => $user->role,
        ], $request);

        return $user;
    }

    public function deleteAvatar(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http')) {
            Storage::disk('public')->delete($path);
        }
    }

    public static function generateUniqueUsername(string $name): string
    {
        $base = Str::of($name)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9_]+/', '_')
            ->trim('_')
            ->limit(24, '')
            ->toString() ?: 'user';

        $username = $base;
        $suffix = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base.$suffix++;
        }

        return $username;
    }
}
