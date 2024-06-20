<?php

namespace App\Filament\Resources\KuisResource\Pages;

use App\Filament\Resources\KuisResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewKuis extends ViewRecord
{
    Use NestedPage;
    protected static string $resource = KuisResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->disabled(fn($record) => $record->mengerjakanKuis()->exists())
        ];
    }
}
