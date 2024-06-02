<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StatUserResource\Pages;
use App\Filament\Resources\StatUserResource\RelationManagers;
use App\Models\StatUser;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StatUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'nama';
    public static function getBreadcrumbs(): string
    {
        return '';
    }

    /**
     * @return mixed
     */
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStatUsers::route('/'),
            'create' => Pages\CreateStatUser::route('/create'),
            'view' => Pages\ViewStatUser::route('/{user}/{pelatihan}'),
            'edit' => Pages\EditStatUser::route('/{record}/edit'),
        ];
    }
}
