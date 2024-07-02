<?php

namespace App\Filament\Resources\ModulResource\Pages;

use App\Filament\Resources\ModulResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewModul extends ViewRecord
{
    Use NestedPage;
    protected static string $resource = ModulResource::class;
    public static function canAccess(array $parameters = []): bool
    {
        $id = $parameters['record']['id'];
        if (auth()->user()->role === 'admin') {
            return true;
        }
        if (auth()->user()->role === 'pengajar' && auth()->user()->moduls()->where('modul_id', $id)->exists()) {
            return true;
        }
        return false;
    }
}
