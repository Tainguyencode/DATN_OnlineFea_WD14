<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Tài khoản đã bị khóa.'], 403)
                : redirect()->route('login')->withErrors(['identifier' => 'Tài khoản đã bị khóa.']);
        }

        if ($user && (! $user->last_login_at || ! $user->last_login_at->isToday())) {
            $user->forceFill(['last_login_at' => now()])->saveQuietly();
        }

        return $next($request);
    }
}
