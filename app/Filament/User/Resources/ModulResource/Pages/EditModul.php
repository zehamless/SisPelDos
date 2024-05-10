<?php

namespace App\Filament\User\Resources\ModulResource\Pages;

use App\Filament\User\Resources\ModulResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditModul extends EditRecord
{
    protected static string $resource = ModulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
