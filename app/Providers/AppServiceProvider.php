<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\WhatsAppService::class);
        $this->app->singleton(\App\Services\QRCodeService::class);
        $this->app->singleton(\App\Services\MidtransService::class);
        $this->app->singleton(\App\Services\XenditService::class);
        $this->app->singleton(\App\Services\RaporService::class);
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Set Indonesian locale
        config(['app.locale' => 'id']);
        setlocale(LC_TIME, 'id_ID.UTF-8');

        // Register Filament navigation
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            'panels::body.end',
            fn () => view('filament.footer'),
        );
    }
}
