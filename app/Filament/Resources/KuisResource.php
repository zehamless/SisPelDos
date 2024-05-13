<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KuisResource\Pages;
use App\Filament\Resources\KuisResource\RelationManagers;
use App\Models\MateriTugas;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Illuminate\Database\Eloquent\Builder;

class KuisResource extends Resource
{
    Use NestedResource;
    protected static ?string $model = MateriTugas::class;
    protected static ?string $label = 'Kuis';
    protected static ?string $recordTitleAttribute = 'judul';

    public static function getAncestor(): ?Ancestor
    {
       return Ancestor::make('kuis', 'modul');
    }

    protected static SubNavigationPosition $subNavigationPosition= SubNavigationPosition::Top;
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
                        Forms\Components\Group::make([
                            Toggle::make('published')
                                ->label('Published')
                                ->onIcon('heroicon-c-check')
                                ->offIcon('heroicon-c-x-mark')
                                ->onColor('success')
                                ->default(false),
                            Toggle::make('terjadwal')
                                ->label('Terjadwal')
                                ->onIcon('heroicon-c-check')
                                ->offIcon('heroicon-c-x-mark')
                                ->onColor('success')
                                ->helperText('Apabila terjadwal, maka kuis akan diterbitkan pada tanggal mulai')
                                ->default(false),
                        ])->columns(2),
                        Forms\Components\TextInput::make('max_attempt')
                            ->label(__('Max Attempt'))
                            ->required()
                            ->default(1)
                            ->numeric(),
                        Forms\Components\TextInput::make('judul')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Group::make([
                            Forms\Components\DateTimePicker::make('tgl_mulai')
                                ->native(false)
                                ->timezone('Asia/Jakarta')
                                ->required(),
                            Forms\Components\DateTimePicker::make('tgl_tenggat')
                                ->native(false)
                                ->timezone('Asia/Jakarta')
                                ->after('tgl_mulai')
                                ->rule('after:tgl_mulai')
                                ->required(),
                            Forms\Components\DateTimePicker::make('tgl_selesai')
                                ->native(false)
                                ->timezone('Asia/Jakarta')
                                ->after('tgl_tenggat')
                                ->rule('after:tgl_tenggat')
                                ->required(),
                        ])->columns(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('published')
                    ->label('Published')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('terjadwal')
                    ->label('Terjadwal')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->words(5),
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->label('Tanggal Mulai')
                    ->dateTime()
                    ->badge()
                    ->color('success')
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('tgl_tenggat')
                    ->label('Tanggal Mulai')
                    ->badge()
                    ->color('warning')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->label('Tanggal Selesai')
                    ->badge()
                    ->color('danger')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('max_attempt')
                    ->label('Max Attempt')
                ->numeric(),
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
                    TextEntry::make('max_attempt')
                    ->label('Max Attempt')
                    ->badge()
                    ->color('info'),
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
            RelationManagers\KuisRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
//            'index' => Pages\ListKuis::route('/'),
            'create' => Pages\CreateKuis::route('/create'),
            'edit' => Pages\EditKuis::route('/{record}/edit'),
            'view' => Pages\ViewKuis::route('/{record}'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewKuis::class,
            Pages\EditKuis::class,
        ]);
    }
}
