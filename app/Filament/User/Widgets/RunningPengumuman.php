<?php

namespace App\Filament\User\Widgets;

use App\Models\Pengumuman;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class RunningPengumuman extends Widget
{
    protected static string $view = 'filament.user.widgets.running-pengumuman';
    protected int | string | array $columnSpan = 'full';
protected function getViewData(): array
{
    $pengumuman = Pengumuman::latest()
        ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
        ->pluck('pengumuman');

    return [
        'messages' => $pengumuman->count() <= 0 ? [
            'Selamat datang!',
            auth()->user()->nama,
        ] : $pengumuman,
    ];
}

}
