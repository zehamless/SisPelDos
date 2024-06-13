<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Resources\Resource\Widgets\RunningPengumuman;
use App\Filament\Widgets\CalendarWidget;
use Filament\Pages\Page;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-calendar-days';
    protected static ?string $navigationLabel = 'Kalender';
    protected static string $view = 'filament.user.pages.calendar';
    public static function canAccess(): bool
    {
        return auth()->check();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CalendarWidget::make(),
        ];
    }
}
