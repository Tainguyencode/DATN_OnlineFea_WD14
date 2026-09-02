<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedInstructor
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'instructor') {
            return $next($request);
        }

        if (config('auth.email_verification_enabled', true) && ! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if ($user->isLocked()) {
            if ($request->routeIs('instructor.profile*') || $request->routeIs('instructor.pending*') || $request->routeIs('instructor.certificates.*')) {
                return $next($request);
            }

            return redirect()->route('instructor.profile')
                ->with('error', 'Tài khoản giảng viên của bạn đang bị tạm khóa. Vui lòng kiểm tra mục Hồ sơ & Chứng chỉ.');
        }

        if (! $user->isApprovedInstructor()) {
            return redirect()->route('instructor.profile')
                ->with('error', 'Tài khoản giảng viên của bạn chưa được phê duyệt. Vui lòng hoàn thiện hồ sơ và chờ Admin xét duyệt.');
        }

        return $next($request);
    }
}
