<?php

namespace App\Filament\User\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RiwayatPelatihanRelationManager extends RelationManager
{
    protected static string $relationship = 'peserta';
    protected static ?string $title = 'Riwayat pelatihan';

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
                Tables\Columns\TextColumn::make('judul'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Mendaftar pada')
                ->date()
                ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->formatStateUsing(function ($state) {
                    return match ($state) {
                        'selesai' => 'Lulus',
                        'tidak_selesai' => 'Tidak Lulus',
                        'diterima' => 'Belum Lulus',
                    };
                })
                ->color(function ($state) {
                    return match ($state) {
                        'selesai' => 'success',
                        'tidak_selesai' => 'danger',
                        'diterima' => 'warning',
                    };
                })
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
            ]);
    }
}
