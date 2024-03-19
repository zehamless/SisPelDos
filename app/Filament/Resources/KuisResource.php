<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KuisResource\Pages;
use App\Filament\Resources\KuisResource\RelationManagers;
use App\Models\MateriTugas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KuisResource extends Resource
{
    protected static ?string $model = MateriTugas::class;
    protected static ?string $label = 'Kuis';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('tipe')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\TextInput::make('judul')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('tgl_mulai')
                            ->label('Tanggal Mulai')
                            ->native(false)
                            ->timezone('Asia/Jakarta')
                            ->required(),
                        Forms\Components\DateTimePicker::make('tgl_selesai')
                            ->label('Tanggal Selesai')
                            ->native(false)
                            ->timezone('Asia/Jakarta')
                            ->required(),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->words(5),
                SelectColumn::make('tipe')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->label('Tanggal Mulai')
                    ->dateTime()
                    ->badge()
                    ->color('success')
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->label('Tanggal Selesai')
                    ->badge()
                    ->color('danger')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->where('jenis', 'kuis'))
            ->deferFilters()
            ->defaultSort('created_at', 'desc');

    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                ->schema([
                    TextEntry::make('tipe')
                    ->label('Status')
                    ->badge()
                    ->color('primary'),
                    TextEntry::make('judul')
                    ->label('Judul'),
                    TextEntry::make('tgl_mulai')
                    ->label('Tanggal Mulai')
                    ->badge()
                    ->color('success')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),
                    TextEntry::make('tgl_selesai')
                    ->label('Tanggal Selesai')
                    ->badge()
                    ->color('danger')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),
                ])->columns(2),
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
            'index' => Pages\ListKuis::route('/'),
            'create' => Pages\CreateKuis::route('/create'),
            'edit' => Pages\EditKuis::route('/{record}/edit'),
            'view' => Pages\ViewKuis::route('/{record}'),
        ];
    }
}
