<?php

namespace App\Filament\Resources\StatUserResource\Widgets;

use App\Models\MateriTugas;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    public $pelatihan;
    public $user;
    protected function getStats(): array
    {
        $pelatihanId = $this->pelatihan;
        $userId = $this->user;
        $completedTugasCount = MateriTugas::whereHas('modul.pelatihan', function ($query) use ($pelatihanId) {
            $query->where('pelatihan_id', $pelatihanId);
        })->whereHas('peserta', function ($query) use ($userId) {
            $query->where('users_id', $userId);
        })->whereNot('jenis', 'materi')->count();
        $allTugas = MateriTugas::whereHas('modul', function ($query) use ($pelatihanId) {
            $query->where('pelatihan_id', $pelatihanId)->where('published', true);
        })->where('published', true)->whereNot('jenis', 'materi')->count();
        $belumDikerjakan = $allTugas - $completedTugasCount;
        return [
            Stat::make('Total Dikerjakan', $completedTugasCount),
            Stat::make('Total Belum Dikerjakan', $belumDikerjakan)

        ];
    }
}
