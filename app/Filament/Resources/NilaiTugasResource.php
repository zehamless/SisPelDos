<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NilaiTugasResource\Pages;
use App\Models\Tugas;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NilaiTugasResource extends Resource
{
    protected static ?string $model = Tugas::class;
    protected static ?string $slug = 'nilai-penugasan';
    protected static ?string $label = 'Penilaian Tugas & Kuis';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function canCreate(): bool
    {
        return false;
    }
//    public static function form(Form $form): Form
//    {
//        return $form
//            ->schema([
//                TextInput::make('modul_id')
//                    ->required()
//                    ->integer(),
//
//                TextInput::make('judul')
//                    ->required(),
//
//                TextInput::make('deskripsi'),
//
//                TextInput::make('jenis')
//                    ->required(),
//
//                Checkbox::make('published'),
//
//                DatePicker::make('tgl_mulai'),
//
//                DatePicker::make('tgl_tenggat'),
//
//                DatePicker::make('tgl_selesai'),
//
//                Checkbox::make('terjadwal'),
//
//                TextInput::make('urutan')
//                    ->integer(),
//
//                Placeholder::make('created_at')
//                    ->label('Created Date')
//                    ->content(fn(?MateriTugas $record): string => $record?->created_at?->diffForHumans() ?? '-'),
//
//                Placeholder::make('updated_at')
//                    ->label('Last Modified Date')
//                    ->content(fn(?MateriTugas $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
//
//                TextInput::make('max_attempt')
//                    ->integer(),
//            ]);
//    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('is_kuis')
                    ->label('Jenis')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn($state) => $state ? 'Kuis' : 'Tugas'),
                TextColumn::make('user.nama')
                    ->label('Peserta')
                    ->searchable(),
                TextColumn::make('modul.judul')
                    ->label('Tugas/Kuis')
                ->searchable(),
                TextColumn::make('penilaian')
                    ->label('nilai')
                    ->badge()
                    ->color(fn($state) => $state === 'belum dinilai' ? 'primary' : 'success'),
                TextColumn::make('files')
                    ->label('Benar/Total')
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        if (!$record->is_kuis) {
                            return '-';
                        }
                        $data = json_decode($record->files, true);
                        return $data['correct'] . '/' . $data['total'];
                    })
            ])
            ->filters([
//                TrashedFilter::make(),
                TernaryFilter::make('is_kuis')
                    ->placeholder('Semua'),
                Filter::make('penilaian')
                    ->label('Belum Dinilai')
                    ->query(fn(Builder $query): Builder => $query->where('penilaian', 'belum dinilai'))
            ])
            ->actions([
                EditAction::make()
                    ->label('Beri Penilaian')
                    ->fillForm(function ($record) {

                        return [
                            'user' => $record->user->nama,
                            'modul' => $record->modul->judul,
                            'penilaian' => $record->penilaian,
                            'pesan_peserta' => $record->pesan_peserta,
                            'pesan_admin' => $record->pesan_admin,
                            'status' => $record->status,
                            'files' => $record->files,
                            'file_name' => $record->file_name,
                        ];
                    })
                    ->form([
                        \Filament\Forms\Components\Section::make()
                            ->schema([
                                TextInput::make('user')
                                    ->label('Peserta')
                                    ->disabled(),
                                TextInput::make('modul')
                                    ->label('Tugas/Kuis')
                                    ->disabled(),
                            ])->columns(2),
                        \Filament\Forms\Components\Section::make()
                            ->schema([
                                Textarea::make('pesan_peserta')
                                    ->visible(fn($record) => $record->is_kuis == false)
                                    ->disabled(),
                                Textarea::make('pesan_admin')
                                    ->visible(fn($record) => $record->is_kuis == false),
                                TextInput::make('penilaian')
                                    ->autofocus(),
                                FileUpload::make('files')
                                    ->label('File Tugas')
                                    ->disk('public')
                                    ->directory('tugas')
                                    ->downloadable()
                                    ->disabled()
                                    ->storeFileNamesIn('file_name')
                                    ->visibility('public')
                                    ->visible(fn($record) => $record->is_kuis == false),
                                Actions::make([
                                    Actions\Action::make('Review Kuis')
                                        ->url(fn($record) => route('kuis.review', $record->id))
                                        ->openUrlInNewTab()
                                        ->color('info')

                                ])->visible(fn($record) => $record->is_kuis == true),
                            ])
                    ])
//                EditAction::make(),
//                DeleteAction::make(),
//                RestoreAction::make(),
//                ForceDeleteAction::make(),
            ])
            ->bulkActions([
//                BulkActionGroup::make([
//                    DeleteBulkAction::make(),
//                    RestoreBulkAction::make(),
//                    ForceDeleteBulkAction::make(),
//                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['user', 'modul']);
            })
            ->deferFilters()
            ->defaultSort('updated_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
//            KuisRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNilaiTugas::route('/'),
//            'create' => Pages\CreateNilaiTugas::route('/create'),
//            'edit' => Pages\EditNilaiTugas::route('/{record}/edit'),
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
        return [];
    }
}
