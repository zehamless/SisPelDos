<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankSoalResource\Pages;
use App\Filament\Resources\BankSoalResource\RelationManagers;
use App\Models\BankSoal;
use App\Models\kuis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BankSoalResource extends Resource
{
    protected static ?string $model = kuis::class;
    protected static ?string $label = 'Bank Soal';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
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
                        $options = array_filter($state, function($value) {
                            return !is_numeric($value);
                        });
                        return $options;
                    }),


            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('pertanyaan')
            ->columns([
                Tables\Columns\TextColumn::make('pertanyaan')
                    ->label('Pertanyaan')
                    ->html()
                    ->searchable()
                    ->words(5),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBankSoals::route('/'),
            'create' => Pages\CreateBankSoal::route('/create'),
            'edit' => Pages\EditBankSoal::route('/{record}/edit'),
        ];
    }
}
