<?php

namespace App\Filament\Resources\KuisResource\Pages;

use App\Filament\Resources\KuisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditKuis extends EditRecord
{
    Use NestedPage;
    protected static string $resource = KuisResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
