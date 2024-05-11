<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MateriTugasResource\Pages;
use App\Filament\User\Resources\MateriTugasResource\RelationManagers;
use App\Models\MateriTugas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MateriTugasResource extends Resource
{
    protected static ?string $model = MateriTugas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            ]);
    }
public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Section::make([
                TextEntry::make('judul'),
                TextEntry::make('jenis'),
                TextEntry::make('tgl_mulai'),
                TextEntry::make('tgl_selesai'),
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
            'index' => Pages\ListMateriTugas::route('/'),
            'create' => Pages\CreateMateriTugas::route('/create'),
            'view' => Pages\ViewMateriTugas::route('/{record}'),
            'edit' => Pages\EditMateriTugas::route('/{record}/edit'),
        ];
    }
}
