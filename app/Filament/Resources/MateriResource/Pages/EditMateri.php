<?php

namespace App\Filament\Resources\MateriResource\Pages;

use App\Filament\Resources\MateriResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditMateri extends EditRecord
{
    Use NestedPage;
    protected static string $resource = MateriResource::class;
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
