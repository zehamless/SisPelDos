<?php

namespace App\Filament\User\Resources\PelatihanResource\Widgets;

use App\Models\MateriTugas;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatPelatihan extends BaseWidget
{
    protected function getStats(): array
    {
        $tugasDikerjakan = auth()->user()->mengerjakan()->where('status', 'selesai')->count();
        $tugasTersisa =  MateriTugas::query()
            ->whereNot('jenis', 'materi')
            ->where('published', true)
            ->whereHas('modul.pelatihan.peserta', function ($query) {
                $query->where('users_id', auth()->user()->id);
            })
            ->count() - $tugasDikerjakan;
        return [
            Stat::make('Pelatihan Diikuti', auth()->user()->peserta()->count())->color('success'),
            Stat::make('Pelatihan Lulus', auth()->user()->sertifikat()->count()),
            Stat::make('Tugas Dikerjakan', $tugasDikerjakan),
            Stat::make('Tugas Tersisa', $tugasTersisa),
        ];
    }
}
