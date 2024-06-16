<?php

namespace App\Filament\User\Pages;

use App\Filament\Widgets\CalendarWidget;
use App\Filament\Widgets\KalenderWidget;
use Filament\Pages\Page;

class Kalender extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-calendar-days';

    protected static string $view = 'filament.user.pages.kalender';
    public static function canAccess(): bool
    {
        return auth()->check();
    }

    protected function getHeaderWidgets(): array
    {
        return [
          \App\Livewire\KalenderWidget::make()
        ];
    }
}
