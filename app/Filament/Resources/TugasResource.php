<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TugasResource\Pages;
use App\Filament\Resources\TugasResource\RelationManagers;
use App\Models\MateriTugas;
use App\Models\Tugas;
use Filament\Actions\Action;
use Filament\Forms;
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

class TugasResource extends Resource
{
    protected static ?string $model = MateriTugas::class;

    protected static ?string $label = 'Tugas';
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
                    ->label('Deskripsi'),
                Forms\Components\FileUpload::make('files')
                    ->columnSpan(2)
                    ->label('File Materi')
                    ->disk('public')
                    ->directory('materi')
                    ->downloadable()
                    ->multiple()
                    ->storeFileNamesIn('file_name')
                    ->visibility('public'),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('published')
                    ->label('Published')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('terjadwal')
                    ->label('Terjadwal')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->words(5),
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->label('Tanggal Mulai')
                    ->badge()
                    ->color('success')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('tgl_tenggat')
                    ->label('Tanggal Mulai')
                    ->badge()
                    ->color('warning')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->label('Tanggal Mulai')
                    ->badge()
                    ->color('danger')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
//                    Tables\Actions\DissociateAction::make(),
                    Action::make('view materi')
                        ->label('View Materi')
                        ->icon('heroicon-c-document-magnifying-glass')
                        ->url(fn ($record): string => route('filament.admin.pelatihan.resources.pelatihans.tugas', $record->pelatihan_id)),
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
            ])->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
            ->modifyQueryUsing(fn (Builder $query) => $query->where('jenis', 'tugas'))
            ->deferFilters()
            ->defaultSort('urutan')
            ->reorderable('urutan');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Status')
                    ->schema([
                        TextEntry::make('published')
                            ->label('Published')
                            ->badge()
                            ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                            ->color(fn($state) => $state ? 'success' : 'danger'),
                        TextEntry::make('terjadwal')
                            ->label('Terjadwal')
                            ->badge()
                            ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                            ->color(fn($state) => $state ? 'success' : 'danger'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->date('Y-m-d H:i:s', 'Asia/Jakarta'),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->date('Y-m-d H:i:s', 'Asia/Jakarta'),
                    ])->columns(2),
                Section::make('Tanggal')
                    ->schema([
                        TextEntry::make('tgl_mulai')
                            ->label('Tanggal Mulai')
                            ->badge()
                            ->color('success')
                            ->dateTime()
                            ->timezone('Asia/Jakarta'),
                        TextEntry::make('tgl_tenggat')
                            ->label('Tanggal Tenggat')
                            ->badge()
                            ->color('warning')
                            ->dateTime()
                            ->timezone('Asia/Jakarta'),
                        TextEntry::make('tgl_selesai')
                            ->label('Tanggal Selesai')
                            ->badge()
                            ->color('danger')
                            ->dateTime()
                            ->timezone('Asia/Jakarta'),
                    ])->columns(3),
                Section::make()
                    ->schema([
                        TextEntry::make('judul')
                            ->label('Judul'),
                        TextEntry::make('file_name')
                            ->label('File Materi')
                            ->listWithLineBreaks(),
                    ])->columns(2),
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
            'index' => Pages\ListTugas::route('/'),
            'create' => Pages\CreateTugas::route('/create'),
            'edit' => Pages\EditTugas::route('/{record}/edit'),
            'view' => Pages\ViewTugas::route('/{record}'),
        ];
    }
    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewTugas::class,
            Pages\EditTugas::class,
        ]);
    }
}
