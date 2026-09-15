<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Restricts a route to active users of the given types, e.g.
// ->middleware('user.type:student') or 'user.type:school_admin,operator'.
class EnsureUserType
{
    public function handle(Request $request, Closure $next, string ...$types): Response
    {
        $user = $request->user();

        abort_unless(
            $user && $user->is_active && in_array($user->type?->value, $types, true),
            403,
            __('You do not have access to this page.'),
        );

        return $next($request);
    }
}
