<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyEmailCodeRequest;
use App\Models\Cart;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\EmailVerificationCode;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use App\Models\Wishlist;
use App\Services\AuthService;
use App\Services\CaptchaService;
use App\Services\EmailVerificationService;
use App\Services\TwoFactorService;
use App\Support\MailErrorFormatter;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    public function showLogin(Request $request): View
    {
        $redirect = $request->query('redirect');

        if (is_string($redirect) && $this->isSafeRedirect($redirect)) {
            $request->session()->put('url.intended', $redirect);
        }

        return view('auth.login', [
            'captcha' => CaptchaService::generate('login'),
        ]);
    }

    public function login(LoginRequest $request, AuthService $authService, TwoFactorService $twoFactorService): RedirectResponse
    {
        $request->ensureIsNotRateLimited();
        $request->validateCaptcha();

        $user = $authService->login(
            $request->string('identifier')->toString(),
            $request->string('password')->toString(),
            $request->boolean('remember'),
            $request->throttleKey(),
            $request
        );

        if ($user->two_factor_enabled) {
            $twoFactorService->sendCode($user);
            $request->session()->forget('two_factor_passed_at');

            return redirect()->route('two-factor.challenge')
                ->with('success', 'Mã 2FA đã được gửi tới email của bạn.');
        }

        return redirect()->intended($user->dashboardUrl())->with('success', 'Đăng nhập thành công!');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function showRegisterRole(string $role): View
    {
        abort_unless(in_array($role, ['student', 'instructor'], true), Response::HTTP_NOT_FOUND);

        return view('auth.register-role', [
            'role' => $role,
            'captcha' => CaptchaService::generate('register'),
        ]);
    }

    public function register(
        string $role,
        RegisterRequest $request,
        AuthService $authService,
        EmailVerificationService $emailVerificationService
    ): RedirectResponse {
        abort_unless(in_array($role, ['student', 'instructor'], true), Response::HTTP_NOT_FOUND);

        $request->validateCaptcha();

        $data = $request->validated();
        $data['role'] = $role;

        $user = $authService->register($data, $request);

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        try {
            $emailVerificationService->sendCode($user);
        } catch (Throwable $exception) {
            Log::error('Verification code email could not be sent after registration.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('verification.notice')
                ->with('error', 'Đăng ký thành công nhưng chưa gửi được mã xác thực: '.MailErrorFormatter::verificationSendFailure($exception));
        }

        return redirect()->route('verification.notice')
            ->with('success', 'Đăng ký thành công. Vui lòng kiểm tra email để nhập mã xác thực.')
            ->with('resend_after', $emailVerificationService->resendCooldownSeconds($user) ?: EmailVerificationCode::RESEND_COOLDOWN_SECONDS);
    }

    public function logout(Request $request): RedirectResponse
    {
        \App\Services\ActivityLogService::log(Auth::id(), 'logout', User::class, Auth::id(), null, $request);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Đã đăng xuất.');
    }

    public function availability(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'field' => ['required', 'in:email,username'],
            'value' => ['required', 'string', 'max:255'],
        ]);

        $exists = User::where($validated['field'], $validated['value'])->exists();

        return response()->json([
            'available' => ! $exists,
            'message' => $exists ? 'Giá trị này đã được sử dụng.' : 'Có thể sử dụng.',
        ]);
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password', [
            'captcha' => CaptchaService::generate('forgot-password'),
        ]);
    }

    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        $request->validateCaptcha();

        Password::sendResetLink($request->only('email'));

        return back()
            ->with('success', 'Nếu email tồn tại trong hệ thống, liên kết đặt lại mật khẩu sẽ được gửi.')
            ->with('resend_after', 60);
    }

    public function showResetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                    'password_changed_at' => now(),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Mật khẩu đã được cập nhật. Bạn có thể đăng nhập lại.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function verificationNotice(Request $request, EmailVerificationService $emailVerificationService): View|RedirectResponse
    {
        $user = $request->user();

        if ($user?->hasVerifiedEmail()) {
            return redirect()->intended($user->dashboardUrl());
        }

        return view('auth.verify-email', [
            'currentUser' => $user,
            'maskedEmail' => $emailVerificationService->maskEmail($user->email),
            'resendAfter' => max(
                (int) session('resend_after', 0),
                $emailVerificationService->resendCooldownSeconds($user)
            ),
        ]);
    }

    public function studentDashboard(Request $request): View
    {
        return view('auth.verify-email', $this->studentHubData($request->user()));
    }

    public function verifyEmailCode(VerifyEmailCodeRequest $request, EmailVerificationService $emailVerificationService): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended($user->dashboardUrl());
        }

        $result = $emailVerificationService->verify($user, $request->string('code')->toString());

        if (! $result['success']) {
            return back()->withErrors(['code' => $result['message']]);
        }

        event(new Verified($user));
        $request->session()->regenerate();

        return redirect()->intended($user->dashboardUrl())
            ->with('success', $result['message']);
    }

    public function verifyEmail(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($request->user()->dashboardUrl());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended($request->user()->dashboardUrl())
            ->with('success', 'Email đã được xác thực thành công.');
    }

    public function resendVerification(Request $request, EmailVerificationService $emailVerificationService): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($request->user()->dashboardUrl());
        }

        try {
            $emailVerificationService->sendCode($request->user());
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->with('resend_after', $emailVerificationService->resendCooldownSeconds($request->user()));
        } catch (Throwable $exception) {
            Log::error('Verification code email could not be resent.', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
                'error' => $exception->getMessage(),
            ]);

            return back()->withErrors([
                'email' => MailErrorFormatter::verificationSendFailure($exception),
            ]);
        }

        return back()
            ->with('success', 'Mã xác thực mới đã được gửi.')
            ->with('resend_after', EmailVerificationCode::RESEND_COOLDOWN_SECONDS);
    }

    public function showTwoFactorChallenge(): View
    {
        return view('auth.two-factor-challenge');
    }

    public function verifyTwoFactor(Request $request, TwoFactorService $twoFactorService): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        if (! $twoFactorService->verify($request->user(), $validated['code'])) {
            return back()->withErrors(['code' => 'Mã 2FA không đúng hoặc đã hết hạn.']);
        }

        $request->session()->put('two_factor_passed_at', now()->timestamp);
        \App\Services\ActivityLogService::log($request->user()->id, 'verify_2fa', User::class, $request->user()->id, null, $request);

        return redirect()->intended($request->user()->dashboardUrl())->with('success', 'Xác thực 2FA thành công.');
    }

    public function resendTwoFactor(Request $request, TwoFactorService $twoFactorService): RedirectResponse
    {
        $twoFactorService->sendCode($request->user());

        return back()->with('success', 'Mã 2FA mới đã được gửi.')->with('resend_after', 60);
    }

    public function quickLogin(Request $request, string $role): RedirectResponse
    {
        abort_if(app()->environment('production'), Response::HTTP_NOT_FOUND);

        $email = match ($role) {
            'admin' => 'admin@example.com',
            'instructor' => 'instructor@example.com',
            default => 'student@example.com',
        };

        $user = User::where('email', $email)->firstOrFail();
        Auth::login($user);
        $request->session()->regenerate();

        return redirect($user->dashboardUrl())->with('success', 'Đăng nhập nhanh thành công.');
    }

    private function isSafeRedirect(string $redirect): bool
    {
        if ($redirect === '') {
            return false;
        }

        if (Str::startsWith($redirect, '/') && ! Str::startsWith($redirect, '//')) {
            return true;
        }

        return Str::startsWith($redirect, url('/'));
    }

    private function studentHubData(User $user): array
    {
        $activeEnrollments = Enrollment::where('user_id', $user->id)
            ->withLearningAccess();

        $enrollments = (clone $activeEnrollments)
            ->with(['course.instructor:id,name', 'course.category:id,name'])
            ->orderByDesc('updated_at')
            ->limit(4)
            ->get();

        $courseEnrollments = (clone $activeEnrollments)
            ->with(['course.instructor:id,name,avatar', 'course.category:id,name'])
            ->orderByDesc('enrolled_at')
            ->orderByDesc('created_at')
            ->limit(9)
            ->get();

        $cart = Cart::firstOrCreate(['user_id' => $user->id])
            ->load(['courses.instructor:id,name']);

        $cartTotal = $cart->courses->sum(
            fn ($course) => (float) ($course->discount_price ?? $course->sale_price ?? $course->price ?? 0)
        );

        $publishedCourse = fn ($query) => $query
            ->where('status', Course::STATUS_PUBLISHED)
            ->where('is_published', true);

        $wishlistQuery = Wishlist::where('user_id', $user->id)
            ->whereHas('course', $publishedCourse);

        $wishlistItems = (clone $wishlistQuery)
            ->with(['course' => fn ($query) => $query
                ->with(['instructor:id,name', 'category:id,name'])
                ->withCount('lessons')])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $certificates = Certificate::where('user_id', $user->id)
            ->with('course:id,title,slug,thumbnail')
            ->orderByDesc('issued_at')
            ->limit(6)
            ->get();

        $orders = Order::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $stats = [
            'enrolled' => (clone $activeEnrollments)->count(),
            'in_progress' => (clone $activeEnrollments)->where('progress_percent', '<', 100)->whereNull('completed_at')->count(),
            'completed' => (clone $activeEnrollments)->whereNotNull('completed_at')->count(),
            'certificates' => Certificate::where('user_id', $user->id)->count(),
            'cart_items' => $cart->courses->count(),
            'wishlist' => (clone $wishlistQuery)->count(),
            'orders' => Order::where('user_id', $user->id)->count(),
        ];

        return [
            'studentHub' => true,
            'emailVerified' => $user->hasVerifiedEmail(),
            'user' => $user,
            'enrollments' => $enrollments,
            'courseEnrollments' => $courseEnrollments,
            'stats' => $stats,
            'avgProgress' => (clone $activeEnrollments)->avg('progress_percent') ?? 0,
            'cart' => $cart,
            'cartTotal' => $cartTotal,
            'wishlistItems' => $wishlistItems,
            'certificates' => $certificates,
            'orders' => $orders,
        ];
    }
}
