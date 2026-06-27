<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsInstructor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, ['instructor', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Instructor access required.'], 403);
        }

        return $next($request);
    }
}
