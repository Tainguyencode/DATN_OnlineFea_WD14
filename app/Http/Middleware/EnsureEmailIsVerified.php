<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified as BaseEnsureEmailIsVerified;

class EnsureEmailIsVerified extends BaseEnsureEmailIsVerified
{
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (! config('auth.email_verification_enabled', true)) {
            return $next($request);
        }

        return parent::handle($request, $next, $redirectToRoute);
    }
}
