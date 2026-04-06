<?php

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
        // SetLocale runs on every web request to apply the user's language
        // preference stored in the session by the /locale/{locale} route.
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Short aliases used in route group definitions.
        // 'tenant'          = resolve the current school from subdomain/query/default
        // 'tenant.required' = abort 403 when no school could be resolved
        $middleware->alias([
            'tenant' => \App\Http\Middleware\ResolveTenant::class,
            'tenant.required' => \App\Http\Middleware\EnsureTenantIsSet::class,
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
