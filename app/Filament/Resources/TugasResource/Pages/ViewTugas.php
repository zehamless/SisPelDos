<?php

namespace App\Filament\Resources\TugasResource\Pages;

use App\Filament\Resources\TugasResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewTugas extends ViewRecord
{
    Use NestedPage;
    protected static string $resource = TugasResource::class;
    protected function getHeaderActions(): array
    {
        $tugas = $this->getRecord()->mengerjakanTugas()->exists();
        return [
            Actions\DeleteAction::make()
                ->label(fn() => $tugas? 'Kuis sudah dikerjakan, tidak dapat dihapus' : 'Hapus')
                ->disabled(fn() => $tugas)
        ];
    }
}
