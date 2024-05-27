<?php

namespace App\Filament\Resources\StatUserResource\Pages;

use App\Filament\Resources\StatUserResource;
use App\Models\Pelatihan;
use App\Models\User;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewStatUser extends ViewRecord
{

    protected static string $resource = StatUserResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public $pelatihan;
    public $user;

    public function mount($user): void
    {
        $data = User::where('id', $user)->first();
        $this->record = $data;
    }

    public function infolist(Infolist|\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        $pelatihan = $this->pelatihan;

        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('nama'),
                        TextEntry::make('id')
                            ->label('Pelatihan')
                            ->formatStateUsing(function ($record) use ($pelatihan) {
                                $judulPelatihan = Pelatihan::where('id', $pelatihan)->first();

                                return $judulPelatihan->judul;
                            })
                    ])
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
//            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatUserResource\Widgets\StatsOverview::make([
                'pelatihan' => $this->pelatihan,
                'user' => $this->user
            ])
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            StatUserResource\RelationManagers\AllTugasRelationManager::make([
                'pelatihan' => $this->pelatihan,
                'user' => $this->user
            ])
        ];
    }
}
