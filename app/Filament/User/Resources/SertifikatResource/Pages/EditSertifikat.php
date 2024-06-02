<?php

namespace App\Filament\User\Resources\SertifikatResource\Pages;

use App\Filament\User\Resources\SertifikatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSertifikat extends EditRecord
{
    protected static string $resource = SertifikatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
