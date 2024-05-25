<?php

namespace App\Filament\Resources\NilaiTugasResource\Pages;

use App\Filament\Resources\NilaiTugasResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditNilaiTugas extends EditRecord
{
    protected static string $resource = NilaiTugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
