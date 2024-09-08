<?php

namespace App\Filament\Resources\ChatbotDatasResource\Pages;

use App\Filament\Resources\ChatbotDatasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditChatbotDatas extends EditRecord
{
    protected static string $resource = ChatbotDatasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
