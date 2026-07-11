<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user('admin') ?? $request->user('web');

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isSuperAdmin() || Permission::roleHas($user->role, $permission)) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['success' => false, 'message' => 'Không có quyền truy cập'], 403);
        }

        return back()->with('error', 'Bạn không có quyền thực hiện thao tác này.');
    }
}
