<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
