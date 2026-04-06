<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Resolves the current tenant from the incoming request. Tenant detection
// follows this order: subdomain extraction, ?tenant= query parameter, and
// finally the configured default slug. The resolved tenant is stored in
// Tenant::$currentTenant so it can be read anywhere via Tenant::current().
class ResolveTenant
{
    // Domains that should never be treated as having a subdomain prefix.
    // Loaded from config/app.php 'base_domains' on each request so that
    // production domains can be added without touching this file.
    private array $baseDomains = [
        'localhost',
        '127.0.0.1',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Merge any extra base domains defined in configuration.
        $configDomains = config('app.base_domains', []);
        $this->baseDomains = array_merge($this->baseDomains, $configDomains);

        $host = $request->getHost();

        // Super admin panel operates without any tenant context.
        if ($request->is('super-admin*')) {
            Tenant::forgetCurrent();
            return $next($request);
        }

        // Resolve tenant slug from the subdomain of the host.
        $slug = null;

        if (! in_array($host, $this->baseDomains)) {
            $parts = explode('.', $host);
            // A valid subdomain requires at least 3 parts (e.g. demo.example.com).
            // Two-part hosts like example.com are treated as the base domain.
            if (count($parts) >= 3 && $parts[0] !== 'www') {
                $slug = $parts[0];
            }
        }

        // Allow overriding via query parameter for local testing.
        if (! $slug) {
            $slug = $request->query('tenant');
        }

        // When no slug could be determined, fall back to the configured default.
        if (! $slug) {
            $slug = config('app.default_tenant_slug', 'demo');
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
