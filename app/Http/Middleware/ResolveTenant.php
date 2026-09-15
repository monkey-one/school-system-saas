<?php

namespace App\Http\Middleware;

use App\Enums\TenantStatus;
use App\Models\Tenant;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Resolves the current tenant for the request and stores it in
// Tenant::$currentTenant so it can be read anywhere via Tenant::current().
//
// Authenticated users are ALWAYS pinned to their own school, whatever the
// URL says. This is what keeps one school from reading another school's
// data by changing the subdomain or the ?tenant= parameter. Guests fall back
// to subdomain → ?tenant= → the configured default slug (public pages).
//
// Pass the "panel" parameter on Filament panels: guests there only see the
// login and password-reset screens, which must be able to look up users of
// every school, so no tenant is applied until someone signs in.
class ResolveTenant
{
    // Hosts that should never be treated as having a subdomain prefix.
    // Extended with config('app.base_domains') on each request.
    private array $baseDomains = [
        'localhost',
        '127.0.0.1',
    ];

    public function handle(Request $request, Closure $next, ?string $mode = null): Response
    {
        $this->baseDomains = array_merge($this->baseDomains, config('app.base_domains', []));

        // Super admin panel operates without any tenant context.
        if ($request->is('super-admin*')) {
            Tenant::forgetCurrent();

            return $next($request);
        }

        $user = $request->user();

        if (! $user && $mode === 'panel') {
            Tenant::forgetCurrent();

            return $next($request);
        }

        $tenant = $user
            ? $this->tenantForUser($request, $user)
            : $this->tenantFromRequest($request);

        if (! $tenant) {
            abort(404, __('School not found.'));
        }

        if ($tenant->status === TenantStatus::SUSPENDED) {
            abort(403, __('This school account is suspended. Please contact the administrator.'));
        }

        Tenant::setCurrent($tenant);

        return $next($request);
    }

    // A school user can only ever work inside their own school. A super admin
    // (no school) enters a school only through the impersonation session key.
    private function tenantForUser(Request $request, User $user): ?Tenant
    {
        if ($user->tenant_id) {
            return Tenant::find($user->tenant_id);
        }

        if ($user->isSuperAdmin() && $request->hasSession()) {
            $impersonated = $request->session()->get('impersonate_tenant_id');

            if ($impersonated) {
                return Tenant::find($impersonated);
            }
        }

        return $this->tenantFromRequest($request);
    }

    // Public visitors: subdomain first, then ?tenant=, then the default slug.
    private function tenantFromRequest(Request $request): ?Tenant
    {
        $host = $request->getHost();
        $slug = null;

        if (! in_array($host, $this->baseDomains, true)) {
            $parts = explode('.', $host);
            // A valid subdomain requires at least 3 parts (e.g. demo.example.com).
            if (count($parts) >= 3 && $parts[0] !== 'www') {
                $slug = $parts[0];
            }
        }

        $slug ??= $request->query('tenant');
        $slug = is_string($slug) && $slug !== '' ? $slug : config('app.default_tenant_slug', 'demo');

        return Tenant::where('slug', $slug)->first();
    }
}
