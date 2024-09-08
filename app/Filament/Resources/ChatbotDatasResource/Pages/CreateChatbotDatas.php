<?php

namespace App\Filament\Resources\ChatbotDatasResource\Pages;

use App\Filament\Resources\ChatbotDatasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChatbotDatas extends CreateRecord
{
    protected static string $resource = ChatbotDatasResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
