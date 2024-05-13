<?php

namespace App\Filament\User\Resources\ModulResource\Pages;

use App\Filament\User\Resources\ModulResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditModul extends EditRecord
{
    Use NestedPage;
    protected static string $resource = ModulResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
