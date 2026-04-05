<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * Known base domains that should NOT be treated as subdomains.
     * Add your production domain here to prevent false subdomain detection.
     */
    private array $baseDomains = [
        'localhost',
        '127.0.0.1',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        // Check if accessing super-admin panel (no tenant needed)
        if ($request->is('super-admin*')) {
            Tenant::forgetCurrent();
            return $next($request);
        }

        // Resolve tenant slug from subdomain
        $slug = null;

        // Only extract subdomain if the host is NOT a known base domain
        if (! in_array($host, $this->baseDomains)) {
            $parts = explode('.', $host);
            // Require at least 3 parts for subdomain detection (e.g., demo.example.com)
            if (count($parts) >= 3 && $parts[0] !== 'www') {
                $slug = $parts[0];
            }
        }

        // Support query parameter for testing: ?tenant=slug
        if (! $slug) {
            $slug = $request->query('tenant');
        }

        // Fallback to default tenant from config
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
