<?php

namespace App\Filament\Resources\KuisResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class KuisRelationManager extends RelationManager
{
    protected static string $relationship = 'kuis';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('pertanyaan')
                    ->label('Pertanyaan')
                    ->required()
                    ->columnSpan(2)
                    ->maxLength(255),
                Forms\Components\TagsInput::make('jawaban_option')
                    ->label('Pilihan')
                    ->hint("Gunakan petik satu (') untuk pilihan berupa angka atau numeric. Contoh: '1', '2', '3', '4', '5")
                    ->hintColor('warning')
                    ->required()
                    ->live(onBlur: true)
                    ->reorderable()
                    ->afterStateUpdated(function (Forms\Set $set, $state) {
//                        dump($state);
                        $set('jawaban_benar', $state);
                    }),
                Forms\Components\CheckboxList::make('jawaban_benar')
                    ->label('Jawaban Benar')
                    ->required()
                    ->options(function ($state, $record) {
                        $options = array_filter($state, function ($value) {
                            return !is_numeric($value);
                        });
                        return $options;
                    }),


            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('pertanyaan')
            ->columns([
                Tables\Columns\TextColumn::make('pertanyaan')
                    ->label('Pertanyaan')
                    ->html()
                    ->words(5),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->searchable()
                    ->date('Y-m-d H:i:s', 'Asia/Jakarta'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
