<?php

namespace App\Filament\Resources\StatUserResource\Pages;

use App\Filament\Resources\StatUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStatUsers extends ListRecords
{
    protected static string $resource = StatUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
