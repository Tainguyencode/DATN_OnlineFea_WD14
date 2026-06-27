<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng'])->onlyInput('email');
        }

        if (! $user->is_active) {
            return back()->withErrors(['email' => 'Tài khoản đã bị khóa'])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));

        ActivityLogService::log($user->id, 'login', User::class, $user->id, null, $request);

        return redirect()->intended($user->dashboardUrl())->with('success', 'Đăng nhập thành công!');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:student,instructor',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        event(new Registered($user));

        Auth::login($user);

        ActivityLogService::log($user->id, 'register', User::class, $user->id, null, $request);

        return redirect($user->dashboardUrl())->with('success', 'Đăng ký thành công! Chào mừng bạn đến với EduPlatform.');
    }

    public function logout(Request $request): RedirectResponse
    {
        ActivityLogService::log(Auth::id(), 'logout', User::class, Auth::id(), null, $request);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Đã đăng xuất.');
    }
}
