<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Guard middleware that aborts the request when ResolveTenant did not manage
// to set any tenant. Used on routes that absolutely require a school context
// (admin panel, teacher panel, student/parent portals).
class EnsureTenantIsSet
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! \App\Models\Tenant::current()) {
            abort(403, 'Tenant tidak ditemukan. Pastikan Anda mengakses dengan subdomain yang benar.');
        }

        return $next($request);
    }
}
