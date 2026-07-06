<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = $this->authService->handleGoogleUser($googleUser);

            return redirect()->intended($user->dashboardUrl())
                ->with('success', 'Đăng nhập Google thành công!');
        } catch (\Throwable) {
            return redirect()->route('login')
                ->withErrors(['identifier' => 'Không thể đăng nhập bằng Google. Vui lòng thử lại.']);
        }
    }
}
