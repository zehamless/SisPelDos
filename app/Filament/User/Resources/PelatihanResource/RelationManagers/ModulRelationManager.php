<?php

namespace App\Filament\User\Resources\PelatihanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ModulRelationManager extends RelationManager
{
    Use NestedRelationManager;
    protected static string $relationship = 'modul';
public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
{
//    dd(auth()->user()->peserta()->where('pelatihan_id', $ownerRecord->id)->exists());
    return auth()->user()->peserta()->where('pelatihan_id', $ownerRecord->id)->exists();
}

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {

     $peserta = auth()->check() && auth()->user()->peserta()->where('pelatihan_id', $this->getOwnerRecord()->id)->exists();
//        dump($peserta);
        return $table
            ->recordTitleAttribute('judul')
            ->columns([
                Tables\Columns\Layout\Split::make([

                    Tables\Columns\TextColumn::make('judul')
                        ->icon('heroicon-s-newspaper')
                        ->iconColor('primary')
                        ->weight('bold')
                        ->searchable()

                ]),
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\TextColumn::make('deskripsi')
                        ->markdown()
                ])->collapsed(false)
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
//                Tables\Actions\Action::make('Lihat Modul')
//                    ->icon('heroicon-s-eye')
//                    ->action(fn($record) => $this->redirectRoute('filament.user.resources.moduls.view', $record->slug))->visible($peserta),
                Tables\Actions\ViewAction::make()->visible($peserta),
            ])
            ->bulkActions([
                //
            ])
            ->modifyQueryUsing(fn(Builder $query)=> $query->where('published', true))
            ->defaultSort('urutan');
    }
}
