<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Reads the user's preferred language from the session and applies it as the
// application locale. The session value is written by the /locale/{locale}
// route when the user clicks the language switcher.
class SetLocale
{
    private const SUPPORTED = ['id', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale'));

        if (in_array($locale, self::SUPPORTED)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
