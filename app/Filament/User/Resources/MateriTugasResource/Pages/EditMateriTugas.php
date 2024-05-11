<?php

namespace App\Filament\User\Resources\MateriTugasResource\Pages;

use App\Filament\User\Resources\MateriTugasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMateriTugas extends EditRecord
{
    protected static string $resource = MateriTugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
