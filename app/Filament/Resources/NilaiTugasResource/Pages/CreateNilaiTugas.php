<?php

namespace App\Filament\Resources\NilaiTugasResource\Pages;

use App\Filament\Resources\NilaiTugasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNilaiTugas extends CreateRecord
{
    protected static string $resource = NilaiTugasResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
