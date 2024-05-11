<?php

namespace App\Filament\User\Resources\MateriTugasResource\Pages;

use App\Filament\User\Resources\MateriTugasResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMateriTugas extends ViewRecord
{
    protected static string $resource = MateriTugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
