<?php

namespace App\Filament\User\Resources\PelatihanResource\Pages;

use App\Filament\Resources\ModulResource;
use App\Filament\User\Resources\PelatihanResource;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewModul extends ViewRecord
{
    protected static string $resource = PelatihanResource::class;
protected static string $view = 'filament-panels::resources.pages.view-record';
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('judul'),
            ]);
    }
}
