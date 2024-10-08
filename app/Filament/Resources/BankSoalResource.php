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

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?int $navigationSort = 3;
    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }
    public static function getNavigationBadge(): ?string
    {
        static $count = null;

        if ($count === null) {
            $count = static::getModel()::count();
        }

        return $count;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kategori_soal_id')
                    ->label('Kategori Soal')
                    ->relationship('kategories', 'kategori')
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
                            ->label('1 Jawaban')
                            ->schema([
                                Forms\Components\TagsInput::make('jawaban_option')
                                    ->label('Pilihan')
                                    ->placeholder('Buat pilihan')
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
                            ->label('Multiple Jawaban')
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
//                                        dump($options);
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
                Tables\Columns\TextColumn::make('kategories.kategori')
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->searchable()
                    ->date('Y-m-d H:i:s', 'Asia/Jakarta'),
            ])->groups([
                'kategori.kategori'
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Kategori')
                    ->relationship('kategories', 'kategori'),
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
                Tables\Actions\ActionGroup::make([
                Tables\Actions\DeleteAction::make()
                ->disabled(fn ($record) => $record->materiTugas()->exists()),
                 Tables\Actions\ReplicateAction::make(),
                ]),
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
