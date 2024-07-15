<?php

namespace App\Filament\User\Resources\MateriTugasResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MengerjakanKuisRelationManager extends RelationManager
{
    protected static string $relationship = 'mengerjakanKuis';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->jenis == 'kuis';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('created_at')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
//            ->recordTitleAttribute('created_at')
                ->modifyQueryUsing(function ($query) {
                    $query->where('status', 'selesai');
                })
            ->columns([
                Tables\Columns\TextColumn::make('pivot.created_at')
                    ->label('Dikerjakan Pada')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('penilaian')
                    ->numeric(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('Preview')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => route('kuis.review', $record->pivot->id))
                    ->openUrlInNewTab()
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }
}
