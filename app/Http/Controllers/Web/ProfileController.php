<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\AuthService;
use App\Services\EmailVerificationService;
use App\Services\TwoFactorService;
use App\Support\MailErrorFormatter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Throwable;

class ProfileController extends Controller
{
    public function studentShow(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('student.profile', compact('user'));
    }

    public function show(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get();

        $activityLogs = ActivityLog::where('user_id', $user->id)
            ->latest()
            ->limit(12)
            ->get();

        return view('profile.show', compact('user', 'sessions', 'activityLogs'));
    }

    public function update(Request $request, AuthService $authService): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'alpha_dash:ascii', 'min:3', 'max:32', 'unique:users,username,'.$user->id],
            'phone' => ['nullable', 'string', 'regex:/^[0-9+\-\s().]{8,20}$/'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->hasFile('avatar')) {
            $authService->deleteAvatar($user->avatar);
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);
        ActivityLogService::log($user->id, 'update_profile', User::class, $user->id, null, $request);

        return back()->with('success', 'Cập nhật hồ sơ thành công.');
    }

    public function updateEmail(Request $request, EmailVerificationService $emailVerificationService): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email,'.$user->id],
            'current_password' => ['required', 'current_password'],
        ]);

        $emailVerificationService->invalidateActiveCodes($user);

        $user->forceFill([
            'email' => $validated['email'],
            'email_verified_at' => null,
        ])->save();

        try {
            $emailVerificationService->sendCode($user);
        } catch (Throwable $exception) {
            return redirect()->route('verification.notice')
                ->with('error', 'Email đã được cập nhật nhưng chưa gửi được mã xác thực: '.MailErrorFormatter::verificationSendFailure($exception));
        }

        ActivityLogService::log($user->id, 'update_email', User::class, $user->id, ['email' => $validated['email']], $request);

        return redirect()->route('verification.notice')->with('success', 'Email đã được cập nhật. Vui lòng nhập mã xác thực gửi tới email mới.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $user->forceFill([
            'password' => $validated['password'],
            'password_changed_at' => now(),
            'remember_token' => \Illuminate\Support\Str::random(60),
        ])->save();

        Auth::logoutOtherDevices($validated['current_password']);
        ActivityLogService::log($user->id, 'update_password', User::class, $user->id, null, $request);

        return back()->with('success', 'Mật khẩu đã được cập nhật.');
    }

    public function sendTwoFactorCode(Request $request, TwoFactorService $twoFactorService): RedirectResponse
    {
        $twoFactorService->sendCode($request->user());

        return back()->with('success', 'Mã 2FA đã được gửi tới email của bạn.')->with('two_factor_pending', true);
    }

    public function enableTwoFactor(Request $request, TwoFactorService $twoFactorService): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        if (! $twoFactorService->verify($request->user(), $validated['code'])) {
            return back()->withErrors(['code' => 'Mã 2FA không đúng hoặc đã hết hạn.'])->with('two_factor_pending', true);
        }

        $request->user()->forceFill([
            'two_factor_enabled' => true,
            'two_factor_secret' => Hash::make($validated['code']),
        ])->save();

        $request->session()->put('two_factor_passed_at', now()->timestamp);
        ActivityLogService::log($request->user()->id, 'enable_2fa', User::class, $request->user()->id, null, $request);

        return back()->with('success', '2FA đã được bật cho tài khoản.');
    }

    public function disableTwoFactor(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ]);

        $request->user()->forceFill([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
        ])->save();

        ActivityLogService::log($request->user()->id, 'disable_2fa', User::class, $request->user()->id, null, $request);

        return back()->with('success', '2FA đã được tắt.');
    }

    public function destroyOtherSessions(Request $request): RedirectResponse
    {
        DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        ActivityLogService::log($request->user()->id, 'logout_other_devices', User::class, $request->user()->id, null, $request);

        return back()->with('success', 'Đã đăng xuất các thiết bị khác.');
    }
}
