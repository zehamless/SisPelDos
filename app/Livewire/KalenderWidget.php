<?php

namespace App\Livewire;

use App\Filament\User\Resources\MateriTugasResource;
use App\Models\MateriTugas;
use App\Models\Mengerjakan;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class KalenderWidget extends FullCalendarWidget
{
//    protected static string $view = 'livewire.kalender-widget';
    public function fetchEvents(array $info): array
    {
        $mengerjakan = Mengerjakan::where('users_id', auth()->user()->id)->where('status','selesai')->pluck('materi_tugas_id')->toArray();
        return MateriTugas::query()
            ->whereNot('jenis', 'materi')
            ->where('published', true)
            ->whereHas('modul.pelatihan.peserta', function ($query) {
                $query->where('users_id', auth()->user()->id);
            })
            ->get()
            ->map(
                fn(MateriTugas $materiTugas) => EventData::make()
                    ->id($materiTugas->id)
                    ->title($materiTugas->judul)
                    ->start($materiTugas->tgl_selesai)
                    ->end($materiTugas->tgl_selesai)
                    ->url(
                        MateriTugasResource::getUrl('view', ['record' => $materiTugas]),
                        shouldOpenUrlInNewTab: true,
                    )
                    ->backgroundColor(in_array($materiTugas->id, $mengerjakan) ? 'green' : 'red')
                    ->borderColor('black')
            )
            ->toArray();
    }

    protected function viewAction(): Action
    {
        return ViewAction::make();
    }
}
