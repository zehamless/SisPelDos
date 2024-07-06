<?php

namespace App\Providers;

use BezhanSalleh\PanelSwitch\PanelSwitch;
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
    }
}
