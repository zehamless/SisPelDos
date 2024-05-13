<?php

namespace App\Filament\User\Resources\PelatihanResource\Pages;

use App\Filament\User\Resources\PelatihanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditPelatihan extends EditRecord
{
    Use NestedPage;
    protected static string $resource = PelatihanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
