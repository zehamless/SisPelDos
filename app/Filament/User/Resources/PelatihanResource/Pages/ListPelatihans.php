<?php

namespace App\Filament\User\Resources\PelatihanResource\Pages;

use App\Filament\User\Resources\PelatihanResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Illuminate\Database\Eloquent\Builder;

class ListPelatihans extends ListRecords
{
    use NestedPage;

    protected static string $resource = PelatihanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];
        if (auth()->check()) {
            $tabs = [
                'Semua' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('published', true)),
                'Pelatihanku' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('id', auth()->user()->peserta()->get()->pluck('id'))),
                'Daftar Tunggu' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('id', auth()->user()->mendaftar()->get()->pluck('id'))),
            ];
        }
        return $tabs;
    }
}
