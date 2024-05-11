<?php

namespace App\Filament\User\Resources\ModulResource\Pages;

use App\Filament\User\Resources\ModulResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateModul extends CreateRecord
{
    Use NestedPage;
    protected static string $resource = ModulResource::class;
}
