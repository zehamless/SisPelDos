<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelatihanResource\Pages;
use App\Filament\Resources\PelatihanResource\Pages\EditPelatihan;
use App\Filament\Resources\PelatihanResource\RelationManagers\MateriRelationManager;
use App\Jobs\clonePelatihanJob;
use App\Models\Pelatihan;
use Faker\Provider\Text;
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
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class PelatihanResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Pelatihan::class;

    protected static ?string $slug = '';
//    protected static ?string $cluster = \App\Filament\Clusters\Pelatihan::class;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'judul';
    protected static ?int $navigationSort = 1;

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }
//    public static function getBreadcrumbRecordLabel(Model $record)
//    {
//        return $record->judul . ' - ' . $record->periode->tahun;
//    }

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function getNavigationBadge(): ?string
    {
        return cache()->remember('navigation_badge_pelatihan', now()->addMinutes(5), function () {
            return static::getModel()::count();
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Riwayat Pelatihan')
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Tanggal Dibuat')
                            ->content(fn(?Pelatihan $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                        Placeholder::make('updated_at')
                            ->label('Terakhir Diubah')
                            ->content(fn(?Pelatihan $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ])->columns(2),
                Tabs::make('Tab')
                    ->tabs([
                        Tab::make('Detail Pelatihan')
                            ->schema([
                                Fieldset::make()
                                    ->schema([
                                        Select::make('periode_id')
                                            ->label('Periode')
                                            ->relationship('periode', 'tahun')
                                            ->createOptionForm([
                                                TextInput::make('tahun')
                                                    ->label('Tahun')
                                                    ->numeric()
                                                    ->minValue(1900)
                                                    ->maxValue(2099)
                                                    ->placeholder('Contoh: 2021')
                                                    ->required()
                                            ])
                                            ->preload()
                                            ->searchable()
                                            ->required(),
                                        Select::make('kategori_pelatihan_id')
                                            ->label('Kategori Pelatihan')
                                            ->relationship('kategori', 'nama')
                                            ->createOptionForm([
                                                TextInput::make('nama')
                                                    ->label('Nama')
                                                    ->required()
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
                                TextInput::make('judul')
                                    ->label('Judul')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', Str::slug($state));
                                    }),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique('pelatihans', 'slug', ignoreRecord: true),
                                \Filament\Forms\Components\Group::make()
                                    ->schema([
                                        TextInput::make('no_sertifikat')
                                            ->label('No Sertifikat')
                                            ->prefix('/')
                                            ->placeholder('Contoh: PPAI-IP-05/LP3M-UNILA/2023'),
                                        TextInput::make('jam_pelatihan')
                                            ->label('Jam Pelatihan')
                                            ->numeric()
                                            ->suffix('Jam')
                                            ->placeholder('Contoh: 40')
                                            ->minValue(0)
                                            ->required(),
                                    ])->columns(2),
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
                                FileUpload::make('sampul')
                                    ->label('Sampul')
                                    ->required()
                                    ->hint('Pastikan Ukuran gambar 16:9')
                                    ->hintColor('warning')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorMode(1)
//                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9')
                                    ->previewable()
                                    ->directory('pelatihan/sampul')
                                    ->maxSize(2048),
                                RichEditor::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->disableToolbarButtons(['attachFiles'])
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
                    ->limit(80)
                    ->description(fn($record) => "Periode: {$record->periode->tahun}, Kategori: {$record->kategori->nama}", position: 'above')
                    ->searchable(),

//                TextColumn::make('deskripsi')
//                    ->markdown()
//                    ->limit(50),
                TextColumn::make('tgl_mulai')
                    ->label('Tanggal Mulai')
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->date('d M Y'),
                TextColumn::make('tgl_selesai')
                    ->label('Tanggal Selesai')
                    ->sortable()
                    ->badge()
                    ->color('danger')
                    ->date('d M Y'),
            ])
            ->filters([
                SelectFilter::make('periode')
                    ->relationship('periode', 'tahun'),
                SelectFilter::make('kategori')
                    ->relationship('kategori', 'nama'),
                TrashedFilter::make(),
            ])->deferFilters()
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
//                    ReplicateAction::make()
//                        ->beforeReplicaSaved(function (Pelatihan $replica): void {
//                            $replica->slug = 'New-' . $replica->slug;
//                            $replica->judul = 'New-' . $replica->judul;
//                        })
//                        ->requiresConfirmation(),
                    Action::make('duplikat')
                        ->label('Duplikat Data')
//                        ->color('secondary')
                        ->icon('heroicon-m-square-2-stack')
                        ->action(fn(Pelatihan $pelatihan) => clonePelatihanJob::dispatch($pelatihan))
                        ->requiresConfirmation()
                        ->modalHeading(fn(Pelatihan $pelatihan) => 'Duplikat  ' . $pelatihan->judul)
                        ->modalDescription('Apakah anda yakin ingin menduplikat data ini? Data yang diduplikat adalah data pelatihan, modul, materi, tugas, dan kuis yang terkait dengan pelatihan ini. Proses ini membutuhkan waktu yang cukup lama, harap bersabar.'),
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
            })
            ->deferFilters()
            ->defaultSort('updated_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Actions::make([
                    Actions\Action::make('publish')
                        ->label('Publish')
                        ->requiresConfirmation()
                        ->tooltip('Ubah status menjadi published')
                        ->color('success')
                        ->action(fn(Pelatihan $record) => $record->update(['published' => true]))
                        ->hidden(fn(Pelatihan $record) => $record->published),
                    Actions\Action::make('draft')
                        ->label('Draft')
                        ->color('danger')
                        ->tooltip('Ubah status menjadi draft')
                        ->requiresConfirmation()
                        ->action(fn(Pelatihan $record) => $record->update(['published' => false]))
                        ->hidden(fn(Pelatihan $record) => !$record->published),
                ]),
                \Filament\Infolists\Components\Section::make()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Group::make([
                                    ImageEntry::make('sampul')
                                        ->label('Sampul')
                                        ->disk('public'),
                                    TextEntry::make('judul')
                                        ->label('Judul'),
                                ]),
                                Group::make([
                                    TextEntry::make('periode.tahun')
                                        ->label("Periode"),
                                    TextEntry::make('created_at')
                                        ->label('Dibuat pada')
                                        ->badge()
                                        ->dateTime('d M Y H:i')
                                        ->timezone('Asia/Jakarta'),
                                    TextEntry::make('tgl_mulai')
                                        ->label('Tanggal Mulai')
                                        ->date('d M Y')
                                        ->badge()
                                        ->color('success'),
                                ]),
                                Group::make([

                                    TextEntry::make('kategori.nama')
                                        ->label('Kategori'),
                                    TextEntry::make('updated_at')
                                        ->label('Terakhir diubah pada')
                                        ->badge()
                                        ->dateTime('d M Y H:i')
                                        ->timezone('Asia/Jakarta'),
                                    TextEntry::make('tgl_selesai')
                                        ->label('Tanggal Selesai')
                                        ->date('d M Y')
                                        ->badge()
                                        ->color('danger'),


                                ]),
                                Group::make([
                                    TextEntry::make('published')
                                        ->label('Status')
                                        ->badge()
                                        ->formatStateUsing(fn($state) => $state ? 'Published' : 'Draft')
                                        ->color(fn($state) => $state ? 'success' : 'danger'),
                                    TextEntry::make('no_sertifikat')
                                        ->label('No. Sertifikat')
                                        ->badge(),
                                    TextEntry::make('jam_pelatihan')
                                        ->label('Jam pelatihan')
                                        ->badge()
                                        ->suffix(' Jam')
                                ])
                            ])
                    ]),
                \Filament\Infolists\Components\Section::make('Deskripsi')
                    ->schema([
                        TextEntry::make('deskripsi')
                            ->hiddenLabel()
                            ->markdown(),
                    ]),
                \Filament\Infolists\Components\Section::make('Syarat')
                    ->schema([
                        RepeatableEntry::make('syarat')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('')
                            ]),
                    ])
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
            ])
            ->with(['periode', 'kategori']);
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
