<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return $request->expectsJson() || $request->is('api/*')
                ? response()->json(['success' => false, 'message' => 'Unauthenticated'], 401)
                : redirect()->route('login');
        }

        if (! in_array($request->user()->role, $roles)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Không có quyền truy cập'], 403);
            }

            return redirect($request->user()->dashboardUrl())
                ->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}
