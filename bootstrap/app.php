<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\EnsureApprovedInstructor;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\EnsureTwoFactorIsVerified;
use App\Http\Middleware\SingleSessionMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Required when the local app is exposed through Cloudflare/ngrok so
        // URL generation honours X-Forwarded-Host and X-Forwarded-Proto.
        $middleware->trustProxies(at: env('TRUSTED_PROXIES'));

        $middleware->validateCsrfTokens(except: [
            'payments/payos/ipn',
            'payments/momo/ipn',
            'instructor/courses/*/s3/multipart/*',
        ]);

        $middleware->alias([
            'active' => EnsureAccountIsActive::class,
            'role' => CheckRole::class,
            'verified' => EnsureEmailIsVerified::class,
            '2fa' => EnsureTwoFactorIsVerified::class,
            'approved.instructor' => EnsureApprovedInstructor::class,
            'single.session' => SingleSessionMiddleware::class,
        ]);

        $middleware->appendToGroup('web', SingleSessionMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
