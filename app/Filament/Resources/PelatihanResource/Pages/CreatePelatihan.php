<?php

namespace App\Filament\Resources\PelatihanResource\Pages;

use App\Filament\Resources\PelatihanResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePelatihan extends CreateRecord
{
    protected static string $resource = PelatihanResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
