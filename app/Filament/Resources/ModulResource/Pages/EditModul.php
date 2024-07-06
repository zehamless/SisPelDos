<?php

namespace App\Filament\Resources\ModulResource\Pages;

use App\Filament\Resources\ModulResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditModul extends EditRecord
{
    Use NestedPage;
    protected static string $resource = ModulResource::class;
public function getRelationManagers(): array
{
    return [];
}
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
