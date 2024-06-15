<?php

namespace App\Filament\Widgets;

use App\Filament\User\Resources\MateriTugasResource;
use App\Models\MateriTugas;
use App\Models\Mengerjakan;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
//    protected static string $view = 'filament.user.widgets.calendar-widget';
    protected static ?int $sort = 2;

    public function fetchEvents(array $info): array
    {
        $mengerjakan = Mengerjakan::where('users_id', auth()->user()->id)->pluck('materi_tugas_id')->toArray();
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

    public static function canView(): bool
    {
        return auth()->check();
    }

    protected function headerActions(): array
    {
        return [
//            CreateAction::make(),
        ];
    }
}
