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
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('urutan');
    }
}
