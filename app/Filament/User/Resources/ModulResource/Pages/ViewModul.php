<?php

namespace App\Filament\User\Resources\ModulResource\Pages;

use App\Filament\User\Resources\ModulResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewModul extends ViewRecord
{
    Use NestedPage;
    protected static string $resource = ModulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
