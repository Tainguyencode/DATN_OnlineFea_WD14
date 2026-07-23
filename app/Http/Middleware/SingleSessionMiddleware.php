<?php

namespace App\Http\Middleware;

use App\Models\ActiveSession;
use App\Services\SecurityAlertService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SingleSessionMiddleware
{
    public function __construct(private SecurityAlertService $alertService) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        try {
            if (! Schema::hasTable('active_sessions')) {
                return $next($request);
            }

            $sessionId = session()->getId();

            $activeSession = ActiveSession::where('user_id', Auth::id())
                ->where('session_id', $sessionId)
                ->first();

            if (! $activeSession || ! $activeSession->is_active) {
                if ($activeSession && ! $activeSession->is_active) {
                    $this->alertService->logAlert('SESSION_KICKED', Auth::id(), [
                        'session_id' => $sessionId,
                    ]);
                }

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->is('api/session/check')) {
                    return response()->json([
                        'active' => false,
                        'message' => 'Tài khoản đã được đăng nhập trên thiết bị khác.'
                    ], 200);
                }

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json(['error' => 'Tài khoản đã được đăng nhập trên thiết bị khác.'], 401);
                }

                return redirect()->route('login')->with('error', 'Tài khoản đã được đăng nhập trên thiết bị khác.');
            }

            // Update last_activity only once per minute to reduce DB writes
            if (! $activeSession->last_activity || Carbon::parse($activeSession->last_activity)->diffInMinutes(now()) >= 1) {
                $activeSession->update(['last_activity' => now()]);
            }
        } catch (\Throwable $e) {
            Log::error('[SingleSession] Middleware error: ' . $e->getMessage());
            // Don't block the user if the session check itself fails
        }

        return $next($request);
    }
}
