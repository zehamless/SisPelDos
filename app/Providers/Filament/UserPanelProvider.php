<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Resources\PelatihanResource;
use App\Filament\Widgets\RunningPengumuman;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('user')
            ->profile(EditProfile::class)
            ->passwordReset()
            ->registration()
            ->login()
            ->colors([
                'primary' => '#3046b5',
//                'success' => '#55AD9B',
//                'danger' => '#EE4E4E',
            ])
            ->viteTheme('resources/css/filament/user/theme.css')
            ->renderHook(
                'panels::body.end',

                fn() => view('footer'),
            )
            ->renderHook(
                'panels::sidebar.nav.start',
                fn() => view(auth()->check() ? 'profilComponent' : 'masukDisiniComponent')
            )
            ->renderHook(
                'panels::auth.login.form.after',
                fn()=>view('kembalikeDasborComponent')
            )
            ->renderHook(
                'panels::auth.register.form.after',
                fn()=>view('kembalikeDasborComponent')
            )
            ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
            ->pages([Pages\Dashboard::class])
            ->resources([
                PelatihanResource::class,
            ])
            ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\\Filament\\User\\Widgets')
            ->widgets(array_merge(
                auth()->check() ? [Widgets\AccountWidget::class] : [],
                [
                    // Widgets\FilamentInfoWidget::class,
                    RunningPengumuman::class,
                    // Pelatihan::class
                ]
            ))
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
//                Authenticate::class,
            ])
            ->plugins([
                FilamentFullCalendarPlugin::make()
//                    ->schedulerLicenseKey()
                    ->selectable()
                    ->editable(false)
//                    ->timezone('Asia/Jakarta')
//                    ->locale('id')
//                    ->plugins()
                    ->config([
                        'displayEventTime' => false,
                        'eventDisplay' => 'block',
                    ])
            ])
            ->darkMode(false)
            ->brandName('Sistem Informasi Pelatihan Dosen UNILA')
            ->brandLogo(asset('assets/cropped-logo-unila-resmi-1-768x769.png'))
            ->brandLogoHeight('4rem');
    }
}
