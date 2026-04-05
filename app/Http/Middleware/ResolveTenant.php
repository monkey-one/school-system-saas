<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        // Check if accessing super-admin panel (no tenant needed)
        if ($request->is('super-admin*')) {
            Tenant::forgetCurrent();
            return $next($request);
        }

        // For demo/local development, check for tenant slug in subdomain
        $slug = null;
        if (count($parts) >= 3) {
            $slug = $parts[0];
        } elseif (count($parts) === 2 && ! in_array($parts[0], ['localhost', 'www'])) {
            $slug = $parts[0];
        }

        // Also support query parameter for testing
        if (! $slug) {
            $slug = $request->query('tenant');
        }

        if ($slug && $slug !== 'super-admin') {
            $tenant = Tenant::where('slug', $slug)->first();

            if (! $tenant) {
                abort(404, 'Sekolah tidak ditemukan.');
            }

            if ($tenant->status->value === 'suspended') {
                abort(403, 'Akun sekolah ditangguhkan. Hubungi administrator.');
            }

            Tenant::setCurrent($tenant);
        }

        return $next($request);
    }
}
