<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

// Registers shared application services and performs one-time boot tasks
// like HTTPS enforcement and locale setup.
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
        // when the request arrives via a different path prefix (e.g. the
        // /livewire/ nginx location block sets a SCRIPT_NAME that doesn't
        // match the actual sub-directory deployment path).
        $appUrl = config('app.url');
        if ($appUrl) {
            URL::forceRootUrl($appUrl);
        }

        // Set the system-level locale for date/time formatting functions like
        // Carbon's translatedFormat(). The application locale itself is handled
        // by config/app.php and the SetLocale middleware.
        setlocale(LC_TIME, 'id_ID.UTF-8');

        // Render a custom footer on every Filament panel page.
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            'panels::body.end',
            fn () => view('filament.footer'),
        );

        // Language switcher in the top bar of all panels.
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
            fn () => view('filament.language-switcher'),
        );

        // CSS fallback: if Alpine.js takes too long to initialize (due to slow
        // network, Cloudflare challenge, or script delay), make the main content
        // visible after 2 seconds instead of staying invisible with opacity-0.
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::STYLES_AFTER,
            fn () => new \Illuminate\Support\HtmlString('
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
