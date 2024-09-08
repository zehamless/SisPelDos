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
                    $message = ['Model chatbot berhasil dilatih','Model chatbot gagal dilatih', 'Tidak ada data admin untuk dilatih', 'Tidak ada data dosen untuk dilatih'];
                    Notification::make()
                        ->title($message[$result])
                        ->info()
                        ->send();
                })
                ->tooltip('Melatih model chatbot')
        ];
    }
}
