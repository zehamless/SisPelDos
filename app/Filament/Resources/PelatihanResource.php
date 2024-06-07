<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelatihanResource\Pages;
use App\Filament\Resources\PelatihanResource\Pages\EditPelatihan;
use App\Filament\Resources\PelatihanResource\RelationManagers\MateriRelationManager;
use App\Models\Pelatihan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PelatihanResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Pelatihan::class;

    protected static ?string $slug = '';
    protected static ?string $cluster = \App\Filament\Clusters\Pelatihan::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'judul';

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Riwayat Pelatihan')
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Created Date')
                            ->content(fn(?Pelatihan $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                        Placeholder::make('updated_at')
                            ->label('Last Modified Date')
                            ->content(fn(?Pelatihan $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ])->columns(2),
                Tabs::make('Tab')
                    ->tabs([
                        Tab::make('Detail Pelatihan')
                            ->schema([
                                TextInput::make('judul')
                                    ->label('Judul')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', Str::slug($state));
                                    }),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->unique('pelatihans', 'slug', ignoreRecord: true),
                                Fieldset::make()
                                    ->schema([
                                        DatePicker::make('tgl_mulai')
                                            ->label('Tanggal Mulai')
                                            ->native(false)
                                            ->timezone('Asia/Jakarta')
                                            ->required(),
                                        DatePicker::make('tgl_selesai')
                                            ->label('Tanggal Selesai')
                                            ->after('tgl_mulai')
                                            ->timezone('Asia/Jakarta')
                                            ->native(false)
                                            ->rule('after:tgl_mulai')
                                            ->required(),
                                    ]),
                                Fieldset::make()
                                    ->schema([
//                                        Select::make('periode_id')
//                                            ->relationship('periode', 'tahun_ajar')
//                                            ->label('Periode')
//                                            ->required(),
                                        Select::make('periode_id')
                                            ->label('Periode')
                                            ->relationship('periode', 'tahun_ajar')
                                            ->createOptionForm([
                                                TextInput::make('tahun_ajar')
                                                    ->label('Tahun Ajar')
                                                    ->placeholder('Contoh: 2021/2022')
                                                    ->required(),
                                                DatePicker::make('tahun')
                                                    ->format('Y')
                                                    ->label('Tahun')
                                                    ->placeholder('Contoh: 2021')
                                                    ->native(false)
                                                    ->timezone('Asia/Jakarta')
                                                    ->required(),
                                            ])
                                            ->preload()
                                            ->searchable()
                                            ->required(),
                                        ToggleButtons::make('published')
                                            ->label('Status?')
                                            ->boolean('Published', 'Draft')
                                            ->default(false)
                                            ->grouped()
                                    ]),

                                FileUpload::make('sampul')
                                    ->label('Sampul')
                                    ->hint('Pastikan Ukuran gambar 16:9')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->previewable()
                                    ->disk('public')
                                    ->directory('pelatihan-sampul')
                                    ->visibility('public')
                                    ->maxSize(2048),
                                RichEditor::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('pelatihan-deskripsi')
                                    ->fileAttachmentsVisibility('private')
                                    ->required(),


                            ]),
                        Tab::make('Syarat Dan Ketentuan')
                            ->schema([
                                Repeater::make('syarat')
                                    ->schema([
                                        TextInput::make('syaratKetentuan')
                                            ->label('Syarat dan Ketentuan'),
                                    ])
                            ])
                    ])->columnSpanFull()

            ]);
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
                TextColumn::make('judul')
                    ->words(5)
                    ->description(fn($record) => $record->periode->tahun_ajar)
                    ->searchable(),

                TextColumn::make('deskripsi')
                    ->markdown()
                    ->limit(50),
                TextColumn::make('tgl_mulai')
                    ->sortable()
                    ->date(),

                TextColumn::make('tgl_selesai')
                    ->sortable()
                    ->date(),

            ])
            ->filters([
                SelectFilter::make('periode')
                    ->relationship('periode', 'tahun_ajar'),
                TrashedFilter::make(),
            ])->deferFilters()
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    ReplicateAction::make()
                        ->beforeReplicaSaved(function (Pelatihan $replica): void {
                            $replica->slug = 'New-' . $replica->slug;
                            $replica->judul = 'New-' . $replica->judul;
                        })
                        ->requiresConfirmation(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    BulkAction::make('publish')
                        ->label('Publish')
                        ->icon('heroicon-c-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->update(['published' => true])),
                    BulkAction::make('draft')
                        ->label('Draft')
                        ->icon('heroicon-c-x-circle')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->update(['published' => false])),
                ]),

            ])
            ->modifyQueryUsing(function (Builder $builder) {
                $builder->with('periode');
            });
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
                                        ->label('Sampul')
                                        ->disk('public'),
                                ])
                            ])
                    ]),
                \Filament\Infolists\Components\Section::make('Deskripsi')
                    ->schema([
                        TextEntry::make('deskripsi')
                            ->hiddenLabel()
                            ->html(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPelatihans::route('/'),
            'create' => Pages\CreatePelatihan::route('/create'),
            'edit' => EditPelatihan::route('/{record}/edit'),
            'view' => Pages\ViewPelatihan::route('/{record}'),
            'modul' => Pages\ManageModul::route('/{record}/modul'),
            'pendaftar' => Pages\ManagePendaftar::route('/{record}/pendaftar'),
            'peserta' => Pages\ManagePeserta::route('/{record}/peserta'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['judul'];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewPelatihan::class,
            EditPelatihan::class,
            Pages\ManageModul::class,
            Pages\ManagePendaftar::class,
            Pages\ManagePeserta::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
//            AllTugasRelationManager::class,
        ];
    }
}
