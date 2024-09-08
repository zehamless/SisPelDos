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
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

class MateriResource extends Resource
{
    use NestedResource;

    protected static ?string $model = MateriTugas::class;
    protected static ?string $navigationLabel = 'Materi';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $pluralLabel = 'Materi';
    protected static ?string $recordTitleAttribute = 'judul';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getAncestor(): ?Ancestor
    {
        return Ancestor::make('materi', 'modul');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('jenis', 'materi');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('Modul')
                        ->url(function ($record) {
                            $cacheKey = 'modul_url_' . $record->modul_id. '_materi';
                            return Cache::remember($cacheKey, now()->addHour(), function () use ($record) {
                                return ModulResource::getUrl('materi', ['record' => $record->modul->slug]);
                            });
                        })
                        ->icon('heroicon-o-arrow-left')
                        ->color('info'),
                ])->hiddenOn('create'),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Fieldset::make()
                            ->schema([
                                Toggle::make('published')
                                    ->label('Published')
                                    ->onIcon('heroicon-c-check')
                                    ->offIcon('heroicon-c-x-mark')
                                    ->onColor('success')
                                    ->default(false),
                                Toggle::make('terjadwal')
                                    ->label('Terjadwal')
                                    ->columnSpan(2)
                                    ->onIcon('heroicon-c-check')
                                    ->offIcon('heroicon-c-x-mark')
                                    ->onColor('success')
                                    ->helperText('Apabila terjadwal, maka materi akan diterbitkan pada tanggal mulai')
                                    ->default(false),
                            ])
                            ->columns(3),
                        Forms\Components\RichEditor::make('deskripsi')
                            ->required()
                            ->disableToolbarButtons(['attachFiles'])
                            ->label('Deskripsi'),
                        Forms\Components\FileUpload::make('files')
                            ->label('File Materi')
                            ->maxSize(102400)
                            ->directory('materi')
                            ->downloadable()
                            ->multiple()
                            ->storeFileNamesIn('file_name')
                            ->visibility('public'),
                    ])

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
                Tables\Filters\SelectFilter::make('modul_id')
                    ->label('Modul')
                    ->relationship('modul', 'judul'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data['jenis'] = 'materi';
                        return $data;
                    }),
//                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
//                    Action::make('view materi')
//                        ->label('View Materi')
//                        ->icon('heroicon-c-document-magnifying-glass')
//                        ->url(fn ($record): string => route('filament.admin.pelatihan.resources.pelatihans.materi', $record->pelatihan_id)),
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
                Actions::make([
                    Actions\Action::make('Modul')
                        ->url(function ($record) {
                            $cacheKey = 'modul_url_' . $record->modul_id. '_materi';
                            return Cache::remember($cacheKey, now()->addHour(), function () use ($record) {
                                return ModulResource::getUrl('materi', ['record' => $record->modul->slug]);
                            });
                        })
                        ->icon('heroicon-o-arrow-left')
                        ->color('info'),
                    Actions\Action::make('publish')
                        ->label('Publish')
                        ->requiresConfirmation()
                        ->color('success')
                        ->action(fn($record) => $record->update(['published' => true]))
                        ->hidden(fn($record) => $record->published),
                    Actions\Action::make('draft')
                        ->label('Draft')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn($record) => $record->update(['published' => false]))
                        ->hidden(fn($record) => !$record->published),

                ]),
                Section::make('Status')
                    ->schema([
                        TextEntry::make('published')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn($state) => $state ? 'Published' : 'Draft')
                            ->color(fn($state) => $state ? 'success' : 'danger'),
                        TextEntry::make('terjadwal')
                            ->label('Terjadwal')
                            ->badge()
                            ->formatStateUsing(fn($state) => $state ? 'Ya' : 'Tidak')
                            ->color(fn($state) => $state ? 'success' : 'danger'),
                        TextEntry::make('created_at')
                            ->label('Dibuat pada')
                            ->badge()
                            ->dateTime('d F Y H:i')
                            ->timezone('Asia/Jakarta'),
                        TextEntry::make('updated_at')
                            ->label('Terakhir diubah pada')
                            ->badge()
                            ->dateTime('d F Y H:i')
                            ->timezone('Asia/Jakarta'),
                    ])->columns(2),
                Section::make()
                    ->schema([
                        TextEntry::make('judul')
                            ->label('Judul'),
                        TextEntry::make('file_name')
                            ->label('File Materi')
                            ->listWithLineBreaks()
                    ])->columns(2),
                Section::make()
                    ->schema([
                        TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->markdown()

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
//            'index' => Pages\ListMateris::route('/'),
//            'create' => Pages\CreateMateri::route('/create'),
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
