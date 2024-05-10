<?php

namespace App\Filament\User\Resources\PelatihanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;

class ModulRelationManager extends RelationManager
{
    protected static string $relationship = 'modul';

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
                        ->searchable(),
                ])->collapsed(false)
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('Lihat Modul')
                    ->icon('heroicon-s-eye')
                    ->action(fn($record) => $this->redirectRoute('filament.user.pelatihan.resources.pelatihans.modul', $record->slug)),
                Tables\Actions\ViewAction::make()->visible(!$peserta),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('urutan');
    }
}
