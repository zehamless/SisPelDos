<?php

namespace App\Filament\Resources\ChatbotDatasResource\Pages;

use App\Filament\Resources\ChatbotDatasResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListChatbotDatas extends ListRecords
{
    protected static string $resource = ChatbotDatasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('Latih Bot')
                ->color('info')
                ->action(function () {
                    $result = \Artisan::call('chatbot');
                    if ($result === 1) {
                        Notification::make()
                            ->title('Bot Training Success')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Bot Training Failed')
                            ->body()
                            ->danger()
                            ->send();
                    }
                })
                ->tooltip('Melatih model chatbot')
        ];
    }
}
