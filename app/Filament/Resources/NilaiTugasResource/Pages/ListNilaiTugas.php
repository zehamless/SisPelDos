<?php

namespace App\Filament\Resources\NilaiTugasResource\Pages;

use App\Filament\Resources\NilaiTugasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNilaiTugas extends ListRecords
{
    protected static string $resource = NilaiTugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
