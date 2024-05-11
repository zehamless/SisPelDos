<?php

namespace App\Filament\User\Resources\MateriTugasResource\Pages;

use App\Filament\User\Resources\MateriTugasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMateriTugas extends ListRecords
{
    protected static string $resource = MateriTugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
