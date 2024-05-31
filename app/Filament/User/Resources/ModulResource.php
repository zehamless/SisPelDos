<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\ModulResource\Pages;
use App\Filament\User\Resources\ModulResource\RelationManagers;
use App\Models\Modul;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Illuminate\Database\Eloquent\Model;

class ModulResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Modul::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $recordTitleAttribute = 'judul';

    public static function getAncestor(): ?Ancestor
    {
        return Ancestor::make('modul', 'pelatihan');
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canView(Model $record): bool
    {
        return true;
    }

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
//                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Actions::make([
                    Action::make('Kembali')
                        ->url(url()->previous())
                        ->icon('heroicon-o-arrow-left')
                        ->color('secondary'),
                ]),
                Section::make('Deskripsi')
                    ->schema([
                        TextEntry::make('deskripsi')
                            ->hiddenLabel()
                            ->markdown()
                    ])->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AllTugasRelationManager::make()
        ];
    }

    public static function getPages(): array
    {
        return [
//            'index' => Pages\ListModuls::route('/'),
//            'create' => Pages\CreateModul::route('/create'),
            'view' => Pages\ViewModul::route('/{record}'),
//            'edit' => Pages\EditModul::route('/{record}/edit'),
        ];
    }
}
