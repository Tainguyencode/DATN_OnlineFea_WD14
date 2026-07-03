<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->two_factor_enabled && ! $request->session()->has('two_factor_passed_at')) {
            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}
