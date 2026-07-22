<?php

namespace App\Services;

use App\Models\ActiveSession;
use App\Models\User;
use App\Services\SecurityAlertService;
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
        // NOTE: session ID has changed after regenerate(), so we MUST register
        // the active session HERE (after regenerate), not via the Login event.
        $this->registerActiveSession($user, $request);

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
            'is_active' => true,
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

    /**
     * Register the current session as an active session.
     * Must be called AFTER session()->regenerate() to use the correct new session ID.
     */
    private function registerActiveSession(User $user, Request $request): void
    {
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('active_sessions')) {
                return;
            }

            $sessionId = $request->session()->getId();
            $ip        = $request->ip();
            $ua        = $request->userAgent() ?? '';
            $deviceId  = md5($ip . $ua);

            $alertService = app(SecurityAlertService::class);

            // Detect multiple logins on other devices
            $otherActive = ActiveSession::where('user_id', $user->id)
                ->where('is_active', true)
                ->where('session_id', '!=', $sessionId)
                ->count();

            if ($otherActive > 0) {
                $alertService->logAlert('MULTIPLE_LOGIN', $user->id, [
                    'ip_address' => $ip,
                    'user_agent' => $ua,
                ]);

                // Kick all other sessions
                ActiveSession::where('user_id', $user->id)
                    ->where('session_id', '!=', $sessionId)
                    ->update(['is_active' => false]);
            }

            // Detect new device
            $knownDevice = ActiveSession::where('user_id', $user->id)
                ->where('device_id', $deviceId)
                ->exists();

            if (! $knownDevice && ActiveSession::where('user_id', $user->id)->exists()) {
                $alertService->logAlert('NEW_DEVICE', $user->id, [
                    'ip_address' => $ip,
                    'user_agent' => $ua,
                ]);
            }

            // Upsert: create or update with the new session ID
            ActiveSession::updateOrCreate(
                ['user_id' => $user->id, 'device_id' => $deviceId],
                [
                    'session_id'    => $sessionId,
                    'ip_address'    => $ip,
                    'user_agent'    => $ua,
                    'browser'       => $this->detectBrowser($ua),
                    'platform'      => $this->detectPlatform($ua),
                    'device_name'   => $this->detectDevice($ua),
                    'is_active'     => true,
                    'last_activity' => now(),
                ]
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('[AuthService] registerActiveSession error: ' . $e->getMessage());
        }
    }

    private function detectBrowser(string $ua): string
    {
        if (str_contains($ua, 'Edg'))     return 'Edge';
        if (str_contains($ua, 'Firefox')) return 'Firefox';
        if (str_contains($ua, 'Chrome'))  return 'Chrome';
        if (str_contains($ua, 'Safari'))  return 'Safari';
        return 'Unknown';
    }

    private function detectPlatform(string $ua): string
    {
        if (str_contains($ua, 'Windows')) return 'Windows';
        if (str_contains($ua, 'Android')) return 'Android';
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) return 'iOS';
        if (str_contains($ua, 'Mac'))     return 'macOS';
        if (str_contains($ua, 'Linux'))   return 'Linux';
        return 'Unknown';
    }

    private function detectDevice(string $ua): string
    {
        $lower = strtolower($ua);
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $lower)) return 'Tablet';
        if (preg_match('/(mobile|android|iphone)/i', $lower)) return 'Mobile';
        return 'Desktop';
    }
}
