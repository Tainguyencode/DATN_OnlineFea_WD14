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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
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

        if ($user->isInstructorDeadlineExpired()) {
            $user->demoteToStudentDueToExpiry();

            return redirect()->route('student.dashboard')
                ->with('error', 'Đã quá thời hạn 7 ngày hoàn thiện hồ sơ kể từ khi xác thực email. Tài khoản của bạn đã được chuyển về Học viên.');
        }

        if ($user->instructor_status !== 'approved') {
            return redirect()->route('instructor.pending');
        }

        return $next($request);
    }
}
