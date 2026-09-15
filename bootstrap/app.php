<?php

use App\Http\Middleware\EnsureTenantIsSet;
use App\Http\Middleware\EnsureUserType;
use App\Http\Middleware\ResolveTenant;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust Cloudflare proxy headers so Laravel correctly detects HTTPS,
        // real client IP, and forwarded host/port behind the CDN.
        $middleware->trustProxies(
            at: '*',
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
        );

        // SetLocale applies the language chosen via /locale/{locale}.
        $middleware->web(append: [
            SetLocale::class,
        ]);

        // Global so Filament panels (which use their own middleware stack),
        // API routes and the health check all get the hardening headers.
        $middleware->append(SecurityHeaders::class);
        $middleware->throttleApi();

        // Short aliases used in route group definitions.
        // 'tenant'          = resolve the current school (see ResolveTenant)
        // 'tenant.required' = abort 403 when no school could be resolved
        // 'user.type'       = restrict a route to specific user types
        $middleware->alias([
            'tenant' => ResolveTenant::class,
            'tenant.required' => EnsureTenantIsSet::class,
            'user.type' => EnsureUserType::class,
        ]);

        // The tenant must be resolved AFTER authentication (it depends on the
        // signed-in user) and BEFORE route model binding, otherwise {student}
        // or {bill} parameters would be loaded without the tenant scope and
        // could point at another school's records.
        $middleware->priority([
            \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
            \Illuminate\Contracts\Session\Middleware\AuthenticatesSessions::class,
            ResolveTenant::class,
            EnsureTenantIsSet::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);

        // Payment gateway webhooks are external POST requests without a
        // CSRF token. Exempt the entire webhooks/ path.
        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
