<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MateriResource\Pages;
use App\Filament\Resources\MateriResource\RelationManagers;
use App\Models\Materi;
use App\Models\MateriTugas;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MateriResource extends Resource
{
    protected static ?string $model = MateriTugas::class;
    protected static ?string $navigationLabel = 'Materi';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('judul')
                    ->label('Judul')
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
                Forms\Components\FileUpload::make('files')
                    ->label('File Materi')
                    ->disk('public')
                    ->directory('materi')
                    ->downloadable()
                    ->multiple()
                    ->storeFileNamesIn('file_name')
                    ->visibility('public'),

            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul')
            ->columns([
                Tables\Columns\TextColumn::make('published')
                    ->label('Published')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                    ->color(fn($state) => $state ? 'success' : 'danger')
                ->sortable(),

                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->words(5)
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50),
                Tables\Columns\TextColumn::make('file_name')
                    ->label('File Materi')
                    ->limit(20),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('pelatihan_id')
                    ->label('Pelatihan')
                    ->relationship('pelatihan', 'judul'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data['jenis'] = 'materi';
                        $data['tipe'] = 'materi';
                        return $data;
                    }),
//                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
//                    Tables\Actions\DissociateAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
            ->modifyQueryUsing(fn(Builder $query) => $query->where('jenis', 'materi'))
            ->deferFilters()
            ->defaultSort('urutan')
            ->reorderable('urutan');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('judul')
                            ->label('Judul'),
                        TextEntry::make('file_name')
                            ->label('File Materi')
                            ->listWithLineBreaks()
                    ])->columns(1),
                Section::make()
                    ->schema([
                        TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->html()

                    ])->columns(1),
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
            'index' => Pages\ListMateris::route('/'),
            'create' => Pages\CreateMateri::route('/create'),
            'edit' => Pages\EditMateri::route('/{record}/edit'),
            'view' => Pages\ViewMateri::route('/{record}'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewMateri::class,
            Pages\EditMateri::class,

        ]);
    }
}
