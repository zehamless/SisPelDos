<?php

namespace App\Filament\Widgets;

use App\Models\Pelatihan;
use App\Models\Pendaftaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatDashboard extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Pelatihan', Pelatihan::count()),
            Stat::make('Pelatihan Aktif', Pelatihan::where('published', 'true')->count()),
            Stat::make('Jumlah Peserta Aktif', Pendaftaran::whereNotIn('status', ['ditolak', 'pending'])->count()),
        ];
    }
}
