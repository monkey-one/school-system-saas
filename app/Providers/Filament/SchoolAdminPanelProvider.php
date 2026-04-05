<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureTenantIsSet;
use App\Http\Middleware\ResolveTenant;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SchoolAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('school-admin')
            ->path('school')
            ->login()
            ->colors([
                'primary' => [
                    50 => '#E8F0FB',
                    100 => '#D1E1F7',
                    200 => '#A3C3EF',
                    300 => '#75A5E7',
                    400 => '#4A7AAF',
                    500 => '#3B6494',
                    600 => '#2D4E7A',
                    700 => '#1E3A5F',
                    800 => '#0F2A47',
                    900 => '#091D33',
                    950 => '#051120',
                ],
                'danger' => Color::Red,
                'warning' => Color::Amber,
                'success' => Color::Emerald,
                'info' => Color::Blue,
            ])
            ->font('Plus Jakarta Sans')
            ->brandName('EduSaaS Sekolah')
            ->favicon(asset('favicon.ico'))
            ->discoverResources(in: app_path('Filament/SchoolAdmin/Resources'), for: 'App\\Filament\\SchoolAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/SchoolAdmin/Pages'), for: 'App\\Filament\\SchoolAdmin\\Pages')
            ->discoverWidgets(in: app_path('Filament/SchoolAdmin/Widgets'), for: 'App\\Filament\\SchoolAdmin\\Widgets')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Akademik')
                    ->icon('heroicon-o-academic-cap'),
                NavigationGroup::make()
                    ->label('Kesiswaan')
                    ->icon('heroicon-o-users'),
                NavigationGroup::make()
                    ->label('Kepegawaian')
                    ->icon('heroicon-o-briefcase'),
                NavigationGroup::make()
                    ->label('Absensi')
                    ->icon('heroicon-o-clipboard-document-check'),
                NavigationGroup::make()
                    ->label('Penilaian')
                    ->icon('heroicon-o-document-text'),
                NavigationGroup::make()
                    ->label('Keuangan')
                    ->icon('heroicon-o-currency-dollar'),
                NavigationGroup::make()
                    ->label('Komunikasi')
                    ->icon('heroicon-o-megaphone'),
                NavigationGroup::make()
                    ->label('Perpustakaan')
                    ->icon('heroicon-o-book-open'),
                NavigationGroup::make()
                    ->label('Inventaris')
                    ->icon('heroicon-o-archive-box'),
                NavigationGroup::make()
                    ->label('Pengaturan')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                ResolveTenant::class,
                EnsureTenantIsSet::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full');
    }
}
