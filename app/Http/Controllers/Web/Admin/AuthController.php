<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function showLogin(): View
    {
        return view('auth.admin.login');
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        $user = $this->authService->attemptLogin(
            $request->input('identifier'),
            $request->input('password'),
            $request->boolean('remember'),
            'admin'
        );

        if (! $user) {
            return back()
                ->withErrors(['identifier' => 'Thông tin đăng nhập không chính xác hoặc bạn không có quyền quản trị.'])
                ->onlyInput('identifier');
        }

        ActivityLogService::log($user->id, 'admin_login', User::class, $user->id, null, $request);

        return redirect()->intended(route('admin.dashboard'))->with('success', 'Đăng nhập quản trị thành công!');
    }

    public function logout(Request $request): RedirectResponse
    {
        if ($id = Auth::guard('admin')->id()) {
            ActivityLogService::log($id, 'admin_logout', User::class, $id, null, $request);
        }

        $this->authService->logout('admin');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Đã đăng xuất khỏi hệ thống quản trị.');
    }
}
