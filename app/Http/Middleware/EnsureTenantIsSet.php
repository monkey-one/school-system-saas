<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Guard middleware that aborts the request when ResolveTenant did not manage
// to set any tenant. Used on routes that absolutely require a school context
// (admin panel, teacher panel, student/parent portals). With the "panel"
// parameter, guests pass through so Filament can show its login page.
class EnsureTenantIsSet
{
    public function handle(Request $request, Closure $next, ?string $mode = null): Response
    {
        if ($mode === 'panel' && ! $request->user()) {
            return $next($request);
        }

        if (! Tenant::current()) {
            abort(403, __('School not found for this account.'));
        }

        return $next($request);
    }
}
