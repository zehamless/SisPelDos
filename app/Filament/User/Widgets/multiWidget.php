<?php

namespace App\Filament\User\Widgets;

use App\Filament\Widgets\Pelatihan;
use App\Filament\Widgets\PengumumanWidget;

class multiWidget extends \Kenepa\MultiWidget\MultiWidget
{
//    protected static string $view = 'filament.user.widgets.multi-widget';
protected static ?int $sort=1;
    public array $widgets = [
        Pelatihan::class,
        PengumumanWidget::class
    ];
}
