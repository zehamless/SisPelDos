<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModulResource\Pages;
use App\Filament\Resources\ModulResource\RelationManagers;
use App\Filament\Resources\TugasResource\Pages\CreateTugas;
use App\Models\Modul;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;

class ModulResource extends Resource
{
    Use NestedResource;
    protected static ?string $model = Modul::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    protected static ?string $recordTitleAttribute = 'judul';
    public static function getAncestor(): ?Ancestor
    {
        return Ancestor::make('modul', 'pelatihan');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255),
                Toggle::make('published')
                    ->label('Published')
                    ->onIcon('heroicon-c-check')
                    ->offIcon('heroicon-c-x-mark')
                    ->onColor('success')
                    ->default(false),
                Forms\Components\RichEditor::make('deskripsi')
                    ->label('Deskripsi'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('published')
                    ->label('Published')
                    ->onIcon('heroicon-c-check')
                    ->offIcon('heroicon-c-x-mark')
                    ->onColor('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('judul'),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Actions::make([
                    Actions\Action::make('Kembali')
                        ->url(url()->previous())
                        ->icon('heroicon-o-arrow-left')
                        ->color('secondary'),
                ]),
                Section::make('Judul')
                ->schema([
                    TextEntry::make('judul')
                    ->hiddenLabel(),
                ]),
                Section::make('Deskripsi')
                    ->schema([
                        TextEntry::make('deskripsi')
                            ->hiddenLabel()
                            ->html(),
                    ])->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AllTugasRelationManager::class
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewModul::class,
            Pages\EditModul::class,
            Pages\ManageMateri::class,
            Pages\ManageTugas::class,
            Pages\ManageKuis::class,
        ]);
    }
    public static function getPages(): array
    {
        return [
//            'index' => Pages\ListModuls::route('/'),
            'create' => Pages\CreateModul::route('/create'),
            'edit' => Pages\EditModul::route('/{record}/edit'),
            'view' => Pages\ViewModul::route('/{record}'),
            'materi' => Pages\ManageMateri::route('/{record}/materi'),
            'tugas' => Pages\ManageTugas::route('/{record}/tugas'),
            'kuis' => Pages\ManageKuis::route('/{record}/kuis'),
        ];
    }

}
