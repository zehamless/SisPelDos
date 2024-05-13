<?php

namespace App\Filament\User\Resources\PelatihanResource\Pages;

use App\Filament\User\Resources\PelatihanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewPelatihan extends ViewRecord
{
    Use NestedPage;
    protected static string $resource = PelatihanResource::class;
}
