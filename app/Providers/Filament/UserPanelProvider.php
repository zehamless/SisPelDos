<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Filament\User\Pages\Auth\Register;
use App\Filament\Widgets\RunningPengumuman;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $logo = config('filament.brand_logo');
        $panel->brandLogo($logo ? Storage::url($logo) : asset('assets/Logo-Be-Strong-Unila-2023.png'));
        return $panel
            ->id('user')
            ->path('user')
            ->profile(EditProfile::class)
            ->passwordReset(RequestPasswordReset::class)
            ->registration(Register::class)
            ->login()
            ->colors([
                'primary' => Color::hex(config('filament.colors.primary')),
                'success' => Color::hex(config('filament.colors.success')),
                'warning' => Color::hex(config('filament.colors.warning')),
                'danger' => Color::hex(config('filament.colors.danger')),
                'info' => Color::hex(config('filament.colors.info')),
                'gray' => Color::hex(config('filament.colors.gray')),

            ])
            ->viteTheme('resources/css/filament/user/theme.css')
            ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
            ->pages([Pages\Dashboard::class])
            ->resources([
//                PelatihanResource::class,
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
                    ->selectable(false)
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
            ->brandName(config('app.name'))
            ->brandLogoHeight(config('filament.brand_logo_height'));
    }
}
