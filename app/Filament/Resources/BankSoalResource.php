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

class BankSoalResource extends Resource
{
    protected static ?string $model = kuis::class;
    protected static ?string $label = 'Bank Soal';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kategori_soal_id')
                    ->label('Kategori Soal')
                    ->relationship('kategori', 'kategori')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('kategori')
                            ->label('Kategori')
                            ->required()
                    ])
                    ->preload()
                    ->searchable()
                    ->required(),
                Forms\Components\RichEditor::make('pertanyaan')
                    ->label('Pertanyaan')
                    ->required()
                    ->columnSpan(2),
                Forms\Components\Builder::make('jawaban')
                    ->label('Tipe Soal')
                    ->blocks([
                        Forms\Components\Builder\Block::make('Radio')
                            ->schema([
                                Forms\Components\TagsInput::make('jawaban_option')
                                    ->label('Pilihan')
                                    ->hint("Gunakan petik satu (') untuk pilihan berupa angka atau numeric. Contoh: '1', '2', '3', '4', '5")
                                    ->hintColor('warning')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->reorderable()
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
//                        dd($state);
                                        $set('jawaban_benar', $state);
                                    }),
                                Forms\Components\Radio::make('jawaban_benar')
                                    ->label('Jawaban Benar')
                                    ->required()
                                    ->options(function (Forms\Get $get) {
                                        $state = $get('jawaban_option');
                                        $options = array_filter($state, function ($value) {
                                            return !is_numeric($value);
                                        });
                                        return $options;
                                    }),
                            ])->columns(2),
                        Forms\Components\Builder\Block::make('Checkbox')
                            ->schema([
                                Forms\Components\TagsInput::make('jawaban_option')
                                    ->label('Pilihan')
                                    ->hint("Gunakan petik satu (') untuk pilihan berupa angka atau numeric. Contoh: '1', '2', '3', '4', '5")
                                    ->hintColor('warning')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->reorderable()
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        $set('jawaban_benar', $state);
                                    }),
                                Forms\Components\CheckboxList::make('jawaban_benar')
                                    ->label('Jawaban Benar')
                                    ->required()
                                    ->options(function ($state, $record) {
                                        $options = array_filter($state, function ($value) {
                                            return !is_numeric($value);
                                        });
                                        dump($options);
                                        return $options;
                                    }),

                            ])->columns(2),
                    ])
                    ->reorderable(false)
                    ->maxItems(1)
                    ->required()
                    ->columnSpanFull()
            ]);
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
                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe Soal')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Radio' => 'info',
                        'Checkbox' => 'primary',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->searchable()
                    ->date('Y-m-d H:i:s', 'Asia/Jakarta'),
            ])->groups([
                'kategori.kategori'
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Kategori')
                    ->relationship('kategori', 'kategori'),
                Tables\Filters\SelectFilter::make('tipe')
                    ->label('Tipe Soal')
                    ->options([
                        'Radio' => 'Radio',
                        'Checkbox' => 'Checkbox',
                    ]),
            ])
            ->deferFilters()
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
