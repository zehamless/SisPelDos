<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeriodeResource\Pages;
use App\Models\Periode;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PeriodeResource extends Resource
{
    protected static ?string $model = Periode::class;

    protected static ?string $slug = 'periodes';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
//                DatePicker::make('tahun')
//                    ->format('Y')
//                    ->displayFormat('Y')
//                    ->label('Tahun')
//                    ->placeholder('Contoh: 2021')
//                    ->native(false)
//                    ->timezone('Asia/Jakarta')
//                    ->required(),
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn($record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn($record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                TextInput::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(2099)
                    ->columnSpanFull()
                    ->placeholder('Contoh: 2021')
                    ->required()
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                ->modal(),
//                ReplicateAction::make()
//                    ->beforeReplicaSaved(function (Periode $replica): void {
//                        $replica->tahun_ajar = 'New ' . $replica->tahun_ajar;
//                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeriodes::route('/'),
            'create' => Pages\CreatePeriode::route('/create'),
//            'edit' => Pages\EditPeriode::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
