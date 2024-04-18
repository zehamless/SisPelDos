<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\PelatihanResource\Pages;
use App\Filament\User\Resources\PelatihanResource\RelationManagers;
use App\Models\Pelatihan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PelatihanResource extends Resource
{
    protected static ?string $model = Pelatihan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                ->label('Judul')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Group::make([
                                    TextEntry::make('judul')
                                        ->label('Judul'),
                                    TextEntry::make('published')
                                        ->label('Published')
                                        ->badge()
                                        ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                                        ->color(fn($state) => $state ? 'success' : 'danger'),
                                ]),
                                Group::make([
                                    TextEntry::make('tgl_mulai')
                                        ->label('Tanggal Mulai')
                                        ->dateTime('d M Y')
                                        ->badge()
                                        ->color('success'),
                                    TextEntry::make('tgl_selesai')
                                        ->label('Tanggal Selesai')
                                        ->dateTime('d M Y')
                                        ->badge()
                                        ->color('danger'),
                                ]),
                                Group::make([
                                    ImageEntry::make('sampul')
                                        ->label('Sampul'),
                                ])
                            ])
                    ]),
                \Filament\Infolists\Components\Section::make('Deskripsi')
                    ->schema([
                        TextEntry::make('deskripsi')
                            ->hiddenLabel()
                            ->html(),
                    ]),
                Action::make('Edit')
                    ->route(fn($record) => route('filament.resources.pelatihans.edit', $record))
                    ->icon('heroicon-o-pencil'),
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
            'index' => Pages\ListPelatihans::route('/'),
            'create' => Pages\CreatePelatihan::route('/create'),
            'edit' => Pages\EditPelatihan::route('/{record}/edit'),
            'view'=> Pages\ViewPelatihan::route('/{record}'),
        ];
    }
}
