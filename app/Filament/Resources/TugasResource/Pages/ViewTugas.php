<?php

namespace App\Filament\Resources\TugasResource\Pages;

use App\Filament\Resources\TugasResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewTugas extends ViewRecord
{
    Use NestedPage;
    protected static string $resource = TugasResource::class;
}
