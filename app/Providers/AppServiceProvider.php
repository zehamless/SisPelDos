<?php

namespace App\Providers;

use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Model::unguard();
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch->modalWidth('sm')
                ->simple()
            ->visible(fn() =>  auth()->check()&&auth()->user()->role === 'admin' );
        });
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn() => view('footer')
        );
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_NAV_START,
            fn() => view(auth()->check() ? 'profilComponent' : 'masukDisiniComponent')
        );
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_AFTER,
            fn() => view(auth()->check() ? 'LogoutButtonComponent' : null)
        );
        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn() => view('kembalikeDasborComponent')
        );
        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_REGISTER_FORM_AFTER,
            fn() => view('kembalikeDasborComponent')
        );
    }
}
