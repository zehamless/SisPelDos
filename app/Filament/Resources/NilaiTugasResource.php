<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NilaiTugasResource\Pages;
use App\Filament\Resources\NilaiTugasResource\RelationManagers\KuisRelationManager;
use App\Models\Tugas;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NilaiTugasResource extends Resource
{
    protected static ?string $model = Tugas::class;
    protected static ?string $slug = 'nilai-tugas';
    protected static ?string $label = 'Nilai Tugas';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                TextColumn::make('user.nama')
                    ->label('peserta'),
                TextColumn::make('modul.judul')
                    ->label('tugas'),
                TextColumn::make('penilaian')
                    ->label('nilai')
                    ->badge()
                ->color(fn($state) => $state === 'belum dinilai' ? 'primary' : 'success' )

            ])
            ->filters([
//                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make()
                    ->fillForm(function ($record){

                        return [
                            'user' => $record->user->nama,
                            'modul' => $record->modul->judul,
                            'penilaian' => $record->penilaian,
                            'pesan_peserta' => $record->pesan_peserta,
                            'pesan_admin' => $record->pesan_admin,
                            'status' => $record->status,
                        ];
                    })
                ->form([
                    \Filament\Forms\Components\Section::make()
                    ->schema([
                        TextInput::make('user')
                        ->label('Peserta')
                        ->disabled(),
                        TextInput::make('modul')
                        ->label('Tugas')
                        ->disabled(),
                    ])->columns(2),
                    \Filament\Forms\Components\Section::make()
                    ->schema([
                        Textarea::make('pesan_peserta')
                        ->disabled(),
                        Textarea::make('pesan_admin'),
                        TextInput::make('penilaian')
                        ->autofocus()
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
            ->defaultSort('updated_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            KuisRelationManager::class,
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
