<?php

namespace App\Http\Controllers\Web;

use App\Enums\SocialProvider;
use App\Exceptions\SocialAuthException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\SocialAuthService;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        $socialProvider = SocialProvider::tryFromName($provider);

        if (! $socialProvider) {
            abort(Response::HTTP_NOT_FOUND);
        }

        if (! $socialProvider->isConfigured()) {
            return $this->redirectWithError('Đăng nhập mạng xã hội chưa được cấu hình. Vui lòng dùng email và mật khẩu.');
        }

        $driver = Socialite::driver($provider);

        if ($socialProvider === SocialProvider::Facebook) {
            $driver = $driver->scopes(['email']);
        }

        return $driver->redirect();
    }

    public function callback(
        string $provider,
        Request $request,
        SocialAuthService $socialAuthService,
        TwoFactorService $twoFactorService
    ): RedirectResponse {
        if ($request->has('error')) {
            return $this->redirectWithError('Bạn đã hủy đăng nhập mạng xã hội.');
        }

        $socialProvider = SocialProvider::tryFromName($provider);

        if (! $socialProvider || ! $socialProvider->isConfigured()) {
            return $this->redirectWithError('Đăng nhập mạng xã hội chưa được cấu hình. Vui lòng dùng email và mật khẩu.');
        }

        try {
            $driver = Socialite::driver($provider);

            if ($socialProvider === SocialProvider::Facebook) {
                $driver = $driver->scopes(['email']);
            }

            $socialUser = $driver->user();
            $user = $socialAuthService->resolveUser($provider, $socialUser);
        } catch (InvalidStateException) {
            return $this->redirectWithError('Phiên đăng nhập mạng xã hội không hợp lệ. Vui lòng thử lại.');
        } catch (SocialAuthException $exception) {
            return $this->redirectWithError($exception->getMessage());
        } catch (Throwable) {
            return $this->redirectWithError('Không thể đăng nhập bằng '.$socialProvider->label().'. Vui lòng thử lại.');
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        ActivityLogService::log($user->id, "login_{$provider}", User::class, $user->id, null, $request);

        if ($user->two_factor_enabled) {
            $twoFactorService->sendCode($user);
            $request->session()->forget('two_factor_passed_at');

            return redirect()->route('two-factor.challenge')
                ->with('success', 'Mã 2FA đã được gửi tới email của bạn.');
        }

        return redirect()->intended($user->dashboardUrl())
            ->with('success', 'Đăng nhập '.$socialProvider->label().' thành công!');
    }

    private function redirectWithError(string $message): RedirectResponse
    {
        return redirect()->route('login')->withErrors(['social' => $message]);
    }
}
