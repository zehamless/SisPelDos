<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TugasResource\Pages;
use App\Filament\Resources\TugasResource\RelationManagers;
use App\Models\MateriTugas;
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
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TugasResource extends Resource
{
    use NestedResource;

    protected static ?string $model = MateriTugas::class;
    protected static ?string $label = 'Tugas';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'judul';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getAncestor(): ?Ancestor
    {
        return Ancestor::make('tugas', 'modul');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('jenis', 'tugas');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return !$record->mengerjakanTugas()->exists() || $record->mengerjakanTugas()->wherePivot('status', 'belum')->exists();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('Modul')
                        ->url(function ($record) {
                            $cacheKey = 'modul_url_' . $record->modul_id. '_tugas';
                            return Cache::remember($cacheKey, now()->addHour(), function () use ($record) {
                                return ModulResource::getUrl('tugas', ['record' => $record->modul->slug]);
                            });
                        })
                        ->tooltip('Kembali ke Modul terkait')
                        ->icon('heroicon-o-arrow-left')
                        ->color('info'),
                ])->hiddenOn('create'),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->columnSpan(2)
                            ->maxLength(255),
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
                            ->helperText('Apabila terjadwal, maka tugas akan diterbitkan pada tanggal mulai')
                            ->default(false),
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
                        ])->columns(3)->columnSpan(2),
                        Forms\Components\RichEditor::make('deskripsi')
                            ->columnSpan(2)
                            ->disableToolbarButtons(['attachFiles'])
                            ->label('Deskripsi'),
                        Forms\Components\FileUpload::make('files')
                            ->columnSpan(2)
                            ->deletable(true)
//                    ->maxFiles(1)
                            ->maxSize(102400)
                            ->label('File Materi')
                            ->directory('materi')
                            ->downloadable()
                            ->multiple()
                            ->storeFileNamesIn('file_name')
                            ->visibility('public'),
                    ])->columns(2)
            ]);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Actions::make([
                    Actions\Action::make('Modul')
                        ->url(function ($record) {
                            $cacheKey = 'modul_url_' . $record->modul_id. '_tugas';
                            return Cache::remember($cacheKey, now()->addHour(), function () use ($record) {
                                return ModulResource::getUrl('tugas', ['record' => $record->modul->slug]);
                            });
                        })
                        ->tooltip('Kembali ke Modul terkait')
                        ->icon('heroicon-o-arrow-left')
                        ->color('info'),
                    Actions\Action::make('publish')
                        ->label('Publish')
                        ->requiresConfirmation()
                        ->color('success')
                        ->tooltip('Ubah status menjadi published')
                        ->action(fn($record) => $record->update(['published' => true]))
                        ->hidden(fn($record) => $record->published),
                    Actions\Action::make('draft')
                        ->label('Draft')
                        ->color('danger')
                        ->tooltip('Ubah status menjadi draft')
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
                            ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                            ->color(fn($state) => $state ? 'success' : 'danger'),
                        TextEntry::make('created_at')
                            ->label('Dibuat pada')
                            ->badge()
                            ->dateTime('d M Y H:i')
                            ->timezone('Asia/Jakarta'),
                        TextEntry::make('updated_at')
                            ->label('Terakhir diubah pada')
                            ->badge()
                            ->dateTime('d M Y H:i')
                            ->timezone('Asia/Jakarta'),
                    ])->columns(2),
                Section::make('Tanggal')
                    ->schema([
                        TextEntry::make('tgl_mulai')
                            ->label('Tanggal Mulai')
                            ->badge()
                            ->color('success')
                            ->dateTime('d M Y H:i')
                            ->timezone('Asia/Jakarta'),
                        TextEntry::make('tgl_tenggat')
                            ->label('Tanggal Tenggat')
                            ->badge()
                            ->color('warning')
                            ->dateTime('d M Y H:i')
                            ->timezone('Asia/Jakarta'),
                        TextEntry::make('tgl_selesai')
                            ->label('Tanggal Selesai')
                            ->badge()
                            ->color('danger')
                            ->dateTime('d M Y H:i')
                            ->timezone('Asia/Jakarta'),
                    ])->columns(3),
                Section::make('File Materi')
                    ->schema([
                        TextEntry::make('file_name')
                            ->hiddenLabel()
                            ->listWithLineBreaks(),
                    ])->collapsible()->collapsed(),
                Section::make('Deskripsi')
                    ->schema([
                        TextEntry::make('deskripsi')
                            ->hiddenLabel()
                            ->markdown()

                    ])->collapsible()->collapsed(),
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
//            'index' => Pages\ListTugas::route('/'),
//            'create' => Pages\CreateTugas::route('/create'),
            'edit' => Pages\EditTugas::route('/{record}/edit'),
            'view' => Pages\ViewTugas::route('/{record}'),
            'penilaian' => Pages\ManagePengerjaanTugas::route('/{record}/penilaian'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewTugas::class,
            Pages\EditTugas::class,
            Pages\ManagePengerjaanTugas::class,
        ]);
    }
}
