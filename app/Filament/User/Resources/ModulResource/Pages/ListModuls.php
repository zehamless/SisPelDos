<?php

namespace App\Filament\User\Resources\ModulResource\Pages;

use App\Filament\User\Resources\ModulResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ListModuls extends ListRecords
{
    Use NestedPage;
    protected static string $resource = ModulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
