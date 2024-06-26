<?php

namespace App\Filament\Resources\KuisResource\Pages;

use App\Filament\Resources\KuisResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewKuis extends ViewRecord
{
    use NestedPage;

    protected static string $resource = KuisResource::class;

    protected function getHeaderActions(): array
    {
        $kuis = $this->getRecord()->mengerjakanKuis()->exists();
        return [
            Actions\DeleteAction::make()
                ->label(fn() => $kuis? 'Kuis sudah dikerjakan, tidak dapat dihapus' : 'Hapus')
                ->disabled(fn() => $kuis)
        ];
    }
}
