<?php

namespace App\Filament\Resources\StatUserResource\Pages;

use App\Filament\Resources\StatUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStatUser extends EditRecord
{
    protected static string $resource = StatUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
