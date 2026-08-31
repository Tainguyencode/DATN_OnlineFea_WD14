<?php

namespace App\Services;

use App\Models\ActiveSession;
use App\Models\Category;
use App\Models\InstructorApplication;
use App\Models\InstructorCertificate;
use App\Models\InstructorProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class AuthService
{
    public function login(string $identifier, string $password, bool $remember, string $throttleKey, Request $request): User
    {
        $identifier = trim($identifier);
        $column = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [$column => $identifier, 'password' => $password];

        try {
            $authenticated = Auth::attempt($credentials, $remember);
        } catch (RuntimeException $exception) {
            // Invalid stored hashes must fail closed, not turn a login into HTTP 500.
            // Do not hide unrelated infrastructure errors or accept plaintext passwords.
            if (! in_array($exception->getMessage(), [
                'This password does not use the Bcrypt algorithm.',
                'This password does not use the Argon2i algorithm.',
                'This password does not use the Argon2id algorithm.',
            ], true)) {
                throw $exception;
            }

            Log::warning('Login rejected because the stored password hash does not match the configured algorithm.');
            $authenticated = false;
        }

        if (! $authenticated) {
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
        $user = User::create([
            'name' => $validated['name'],
            'username' => self::generateUniqueUsername($validated['name']),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'avatar' => null,
            'bio' => $validated['bio'] ?? null,
            'instructor_status' => $validated['role'] === 'instructor' ? 'pending' : null,
            'needs_admin_review' => $validated['role'] === 'instructor',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);

        if ($validated['role'] === 'instructor') {
            $cvPath = null;
            if ($request->hasFile('cv')) {
                $cvPath = $request->file('cv')->store('instructor_cvs', 'public');
            }

            $certificatePath = null;
            if ($request->hasFile('certificate')) {
                $file = $request->file('certificate');
                $extension = $file->getClientOriginalExtension() ?: 'pdf';
                $storedPath = $file->storeAs(
                    "instructor-certificates/{$user->id}",
                    Str::uuid().'.'.$extension,
                    'local'
                );
                $certificatePath = $storedPath;

                InstructorCertificate::create([
                    'user_id' => $user->id,
                    'file_path' => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'status' => 'pending',
                    'uploaded_at' => now(),
                ]);
            }

            $categoryId = ! empty($validated['category_id']) ? (int) $validated['category_id'] : null;
            $teachingField = $validated['teaching_field'] ?? null;
            if ($categoryId && ! $teachingField) {
                $teachingField = Category::find($categoryId)?->name;
            }

            InstructorProfile::create([
                'user_id' => $user->id,
                'category_id' => $categoryId,
                'teaching_field' => $teachingField,
                'phone' => $validated['phone'],
                'specialty' => $validated['specialty'],
                'experience' => $validated['experience'],
                'bio' => $validated['bio'],
                'linkedin_url' => $validated['linkedin_url'] ?? null,
                'github_url' => $validated['github_url'] ?? null,
                'website_url' => $validated['website_url'] ?? null,
                'cv' => $cvPath,
                'agree_information' => true,
                'agree_terms' => true,
            ]);

            InstructorApplication::create([
                'user_id' => $user->id,
                'expertise' => $validated['specialty'],
                'experience' => $validated['experience'],
                'introduction' => $validated['bio'],
                'cv_path' => $cvPath,
                'certificate_path' => $certificatePath,
                'status' => 'pending',
            ]);

            try {
                app(NotificationService::class)->notifyAdmins(
                    'Đăng ký Giảng viên mới',
                    "Giảng viên {$user->name} ({$user->email}) vừa đăng ký tài khoản và đang chờ xét duyệt.",
                    'instructor_registered',
                    route('admin.instructors.applications.show', $user)
                );
            } catch (\Throwable $e) {
                Log::error('Gửi thông báo đăng ký giảng viên cho admin thất bại: '.$e->getMessage());
            }
        }

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
    public function registerActiveSession(User $user, Request $request): void
    {
        try {
            if (! Schema::hasTable('active_sessions')) {
                return;
            }

            $sessionId = $request->session()->getId();
            $ip = $request->ip();
            $ua = $request->userAgent() ?? '';
            $deviceId = md5($ip.$ua);

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
                    'session_id' => $sessionId,
                    'ip_address' => $ip,
                    'user_agent' => $ua,
                    'browser' => $this->detectBrowser($ua),
                    'platform' => $this->detectPlatform($ua),
                    'device_name' => $this->detectDevice($ua),
                    'is_active' => true,
                    'last_activity' => now(),
                ]
            );
        } catch (\Throwable $e) {
            Log::error('[AuthService] registerActiveSession error: '.$e->getMessage());
        }
    }

    private function detectBrowser(string $ua): string
    {
        if (str_contains($ua, 'Edg')) {
            return 'Edge';
        }
        if (str_contains($ua, 'Firefox')) {
            return 'Firefox';
        }
        if (str_contains($ua, 'Chrome')) {
            return 'Chrome';
        }
        if (str_contains($ua, 'Safari')) {
            return 'Safari';
        }

        return 'Unknown';
    }

    private function detectPlatform(string $ua): string
    {
        if (str_contains($ua, 'Windows')) {
            return 'Windows';
        }
        if (str_contains($ua, 'Android')) {
            return 'Android';
        }
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) {
            return 'iOS';
        }
        if (str_contains($ua, 'Mac')) {
            return 'macOS';
        }
        if (str_contains($ua, 'Linux')) {
            return 'Linux';
        }

        return 'Unknown';
    }

    private function detectDevice(string $ua): string
    {
        $lower = strtolower($ua);
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $lower)) {
            return 'Tablet';
        }
        if (preg_match('/(mobile|android|iphone)/i', $lower)) {
            return 'Mobile';
        }

        return 'Desktop';
    }
}
