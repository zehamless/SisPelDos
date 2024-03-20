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
                    ->maxLength(255),
                Forms\Components\TagsInput::make('jawabanInput')
                    ->label('Jawaban')
                    ->required()
                    ->live(onBlur: true)
                    ->reorderable()
                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                        $set('jawaban_benar', $state);
                    }),
                Forms\Components\CheckboxList::make('jawaban_benar')
                    ->label('Jawaban Benar')
                    ->required()
                    ->options(function ($state) {
                        return is_array($state) ? array_combine($state, $state) : [];
                    }),
            ]);
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
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data) {
                    $data['jawaban'] = [
                        'jawabanInput' => $data['jawabanInput'],
                        'jawaban_benar' => $data['jawaban_benar'],
                    ];
                    $data = collect($data)->except(['jawabanInput', 'jawaban_benar'])->toArray();
                    return $data;
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->mutateRecordDataUsing(function (array $data) {
//                    $jawaban = json_decode($data['jawaban'], true);
//                    $data['jawabanInput'] = $jawaban['jawabanInput'];
//                    $data['jawaban_benar'] = $jawaban['jawaban_benar'];
                    $data['jawabanInput'] = $data['jawaban']['jawabanInput'];
                    $data['jawaban_benar'] = $data['jawaban']['jawaban_benar'];
//                    dump($data);
                    return $data;
                })
                ->mutateFormDataUsing(function (array $data) {
                    $data['jawaban'] = [
                        'jawabanInput' => $data['jawabanInput'],
                        'jawaban_benar' => $data['jawaban_benar'],
                    ];
                    $data = collect($data)->except(['jawabanInput', 'jawaban_benar'])->toArray();
                    return $data;
                }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('urutan')
            ->defaultSort('urutan', 'asc');
    }
}
