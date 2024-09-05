<?php

namespace App\Filament\Resources\ChatbotDatasResource\Pages;

use App\Filament\Resources\ChatbotDatasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChatbotDatas extends ListRecords
{
    protected static string $resource = ChatbotDatasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
