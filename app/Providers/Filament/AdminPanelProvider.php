<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\ChartDashboard;
use App\Filament\Widgets\StatDashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => '#3046b5',
                'secondary' => Color::Gray,

            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->renderHook(
                'panels::body.end',

                fn() => view('footer'),
            )
            ->renderHook(
                'panels::sidebar.nav.start',
                fn() => view(auth()->check() ? 'profilComponent' : 'masukDisiniComponent')
            )
            ->renderHook(
                'panels::user-menu.after',
                fn()=> view('LogoutButtonComponent')
            )
            ->renderHook(
                'panels::auth.login.form.after',
                fn()=>view('kembalikeDasborComponent')
            )
            ->renderHook(
                'panels::auth.register.form.after',
                fn()=>view('kembalikeDasborComponent')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\Admin\\Widgets')
            ->widgets([
//                Widgets\AccountWidget::class,
//                Widgets\FilamentInfoWidget::class,
                StatDashboard::class,
                ChartDashboard::class
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
            ])
            ->databaseNotifications()
            ->authMiddleware([
                Authenticate::class,
            ])
            ->darkMode(false)
            ->brandName('Sistem Pelatihan Dosen Unila - Admin')
            ->brandLogo(asset('assets/Logo-Be-Strong-Unila-2023.png'))
            ->brandLogoHeight('50px');
    }
}
