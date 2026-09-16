<?php

namespace App\Providers;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Demo;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Registers shared application services and performs one-time boot tasks
// like HTTPS enforcement, rate limiting, demo-mode guards and panel hooks.
class AppServiceProvider extends ServiceProvider
{
    // Bind long-lived services as singletons so they are constructed once and
    // reused across the entire request. This avoids repeated initialization of
    // API clients and token reading.
    public function register(): void
    {
        $this->app->singleton(\App\Services\WhatsAppService::class);
        $this->app->singleton(\App\Services\QRCodeService::class);
        $this->app->singleton(\App\Services\MidtransService::class);
        $this->app->singleton(\App\Services\XenditService::class);
        $this->app->singleton(\App\Services\RaporService::class);

        // Override Filament's default LogoutResponse so ALL panels redirect
        // to the unified login page at /edusaas-admin/login after logout.
        $this->app->bind(
            \Filament\Http\Responses\Auth\Contracts\LogoutResponse::class,
            \App\Http\Responses\LogoutResponse::class,
        );
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Force URL root to APP_URL so that URL generation is correct even
        // when the app is deployed under a sub-directory (e.g. /edusaas).
        $appUrl = config('app.url');
        if ($appUrl) {
            URL::forceRootUrl($appUrl);
            $this->configureSubdirectoryAssets($appUrl);
        }

        // Set the system-level locale for date/time formatting functions like
        // Carbon's translatedFormat(). The application locale itself is handled
        // by config/app.php and the SetLocale middleware.
        setlocale(LC_TIME, 'id_ID.UTF-8');

        $this->applySystemSettings();
        \App\Support\QueueTenancy::register();
        $this->configureRateLimiting();
        $this->registerDemoGuards();
        $this->registerPanelHooks();
    }

    // When the app is served from a sub-directory (e.g. https://site.com/edusaas),
    // Livewire still renders its script and update URLs without that prefix,
    // because Laravel strips the request base path from relative route URLs.
    // The browser would then post to /livewire/update on the main domain and
    // get a 404, which breaks every Filament panel. Registering a prefixed
    // update route and pinning the script URL keeps both inside the app.
    private function configureSubdirectoryAssets(string $appUrl): void
    {
        $prefix = rtrim((string) parse_url($appUrl, PHP_URL_PATH), '/');

        if ($prefix === '') {
            return;
        }

        config(['livewire.asset_url' => rtrim($appUrl, '/') . '/livewire/livewire.min.js']);

        // Laravel removes the request base path from relative route URLs, so
        // the route itself only needs the prefix when that base path already
        // carries it (behind a proxy sending X-Forwarded-Prefix, or a real
        // sub-directory). Otherwise the prefix would end up twice.
        $base = rtrim((string) $this->app['request']->getBaseUrl(), '/');
        $routeUri = ($base === $prefix ? $prefix : '') . '/livewire/update';

        // Livewire already registered its own /livewire/update route, which
        // still matches the incoming request after the web server strips the
        // prefix. This route only exists so the rendered URL keeps it.
        Livewire::setUpdateRoute(fn ($handle) => Route::post($routeUri, $handle)
            ->middleware('web')
            ->name('subdirectory.livewire.update'));
    }

    // Applies the name, language and timezone chosen in Super Admin →
    // System Settings. Skipped on the console, where the cache table may not
    // exist yet (fresh installs, migrations).
    private function applySystemSettings(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            $settings = Cache::many(['system.app_name', 'system.default_locale', 'system.timezone']);
        } catch (\Throwable) {
            return;
        }

        if (filled($settings['system.app_name'])) {
            config(['app.name' => $settings['system.app_name']]);
        }

        if (in_array($settings['system.default_locale'], ['id', 'en'], true)) {
            config(['app.locale' => $settings['system.default_locale']]);
            $this->app->setLocale($settings['system.default_locale']);
        }

        if (is_string($settings['system.timezone']) && in_array($settings['system.timezone'], timezone_identifiers_list(), true)) {
            config(['app.timezone' => $settings['system.timezone']]);
            date_default_timezone_set($settings['system.timezone']);
        }
    }

    // Named rate limiters used by the routes. Keys combine the client IP with
    // the user (or submitted email) so one abuser cannot lock out a school.
    private function configureRateLimiting(): void
    {
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('api-login', fn (Request $request) => [
            Limit::perMinute(5)->by(strtolower((string) $request->input('email')) . '|' . $request->ip()),
            Limit::perMinute(20)->by($request->ip()),
        ]);

        RateLimiter::for('public-forms', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));

        RateLimiter::for('registration', fn (Request $request) => Limit::perHour(5)->by($request->ip()));

        RateLimiter::for('attendance', fn (Request $request) => Limit::perMinute(20)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('webhooks', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));
    }

    // On the public demo, visitors share the same accounts. These guards stop
    // anyone from changing login details or deleting the core records that
    // other visitors need. Returning false from a model event cancels it.
    private function registerDemoGuards(): void
    {
        if (! Demo::enabled() || $this->app->runningInConsole()) {
            return;
        }

        $credentials = ['password', 'email', 'type', 'is_active', 'tenant_id'];

        User::updating(fn (User $user) => $user->isDirty($credentials)
            ? Demo::deny(__('Login details of demo accounts cannot be changed on the public demo.'))
            : null);
        User::deleting(fn () => Demo::deny(__('Accounts cannot be deleted on the public demo.')));

        Tenant::updating(fn (Tenant $tenant) => $tenant->isDirty(['slug', 'status']) ? Demo::deny() : null);
        Tenant::deleting(fn () => Demo::deny());
        Plan::deleting(fn () => Demo::deny());

        Role::updating(fn () => Demo::deny());
        Role::deleting(fn () => Demo::deny());
        Permission::updating(fn () => Demo::deny());
        Permission::deleting(fn () => Demo::deny());
    }

    private function registerPanelHooks(): void
    {
        // Render a custom footer on every Filament panel page.
        FilamentView::registerRenderHook(
            'panels::body.end',
            fn () => view('filament.footer'),
        );

        // Public demo only: call-to-action linking to the developer.
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn () => view('partials.demo-cta'),
        );

        // Language switcher in the top bar of all panels.
        FilamentView::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
            fn () => view('filament.language-switcher'),
        );

        // Banner with an exit link while a super admin is inside a school panel.
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_START,
            fn () => Filament::getCurrentPanel()?->getId() === 'school-admin'
                && session()->has('impersonate_tenant_id')
                && auth()->user()?->isSuperAdmin()
                    ? view('filament.impersonation-banner', ['tenant' => Tenant::current()])
                    : '',
        );

        // CSS fallback: if Alpine.js takes too long to initialize (due to slow
        // network, Cloudflare challenge, or script delay), make the main content
        // visible after 2 seconds instead of staying invisible with opacity-0.
        FilamentView::registerRenderHook(
            PanelsRenderHook::STYLES_AFTER,
            fn () => new HtmlString('
                <style>
                    .fi-main-ctn.opacity-0 {
                        animation: fi-opacity-fallback 0s 2s forwards;
                    }
                    @keyframes fi-opacity-fallback {
                        to { opacity: 1 !important; display: flex; }
                    }
                </style>
            '),
        );
    }
}
