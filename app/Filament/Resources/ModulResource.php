<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModulResource\Pages;
use App\Filament\Resources\ModulResource\RelationManagers;
use App\Models\Modul;
use App\Models\Pelatihan;
use Filament\Actions\Action;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ModulResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Modul::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
    protected static ?string $recordTitleAttribute = 'judul';

    public static function getAncestor(): ?Ancestor
    {
        return Ancestor::make('modul', 'pelatihan');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('Pelatihan')
                        ->url(function ($record) {
                            $cacheKey = 'url_pelatihan_' . $record->pelatihan_id;
                            return Cache::remember($cacheKey, now()->addHour(1), function () use ($record) {
                                return PelatihanResource::getUrl('modul', ['record' => $record->pelatihan->slug]);
                            });
                        })
                        ->tooltip('Kembali ke pelatihan terkait')
                        ->icon('heroicon-o-arrow-left')
                        ->color('info'),
                ])->hiddenOn('create'),
                Forms\Components\Section::make()
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
                            ->disableToolbarButtons(['attachFiles'])
                            ->label('Deskripsi'),
                    ])
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
                    Actions\Action::make('Pelatihan')
                        ->url(function ($record) {
                            $cacheKey = 'url_pelatihan_' . $record->pelatihan_id;
                            return Cache::remember($cacheKey, now()->addHour(1), function () use ($record) {
                                return PelatihanResource::getUrl('modul', ['record' => $record->pelatihan->slug]);
                            });
                        })
                        ->tooltip('Kembali ke pelatihan terkait')
                        ->icon('heroicon-o-arrow-left')
                        ->color('info'),
                    Actions\Action::make('Rekap Nilai')
                        ->tooltip('Lihat rekap nilai')
                        ->url(fn(Modul $record) => route('rekap.modul', $record))
                        ->openUrlInNewTab(),
                    Actions\Action::make('publish')
                        ->label('Publish')
                        ->requiresConfirmation()
                        ->tooltip('Ubah status menjadi published')
                        ->color('success')
                        ->action(fn(Modul $record) => $record->update(['published' => true]))
                        ->hidden(fn(Modul $record) => $record->published),
                    Actions\Action::make('draft')
                        ->label('Draft')
                        ->color('danger')
                        ->tooltip('Ubah status menjadi draft')
                        ->requiresConfirmation()
                        ->action(fn(Modul $record) => $record->update(['published' => false]))
                        ->hidden(fn(Modul $record) => !$record->published),

                ]),
                Section::make()
                    ->schema([
                        TextEntry::make('judul')
                            ->label('Judul'),
                        TextEntry::make('published')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn($state) => $state ? 'Published' : 'Draft')
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
                    ])
                    ->columns(2),
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
            Pages\ManageDiscuss::class,
            Pages\ManagePengajar::class,
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
            'diskusi' => Pages\ManageDiscuss::route('/{record}/diskusi'),
            'pengajar' => Pages\ManagePengajar::route('/{record}/pengajar'),
        ];
    }

}
