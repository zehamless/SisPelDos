<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MateriTugasResource\Pages;
use App\Filament\User\Resources\MateriTugasResource\RelationManagers;
use App\Models\MateriTugas;
use App\Models\Modul;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Illuminate\Database\Eloquent\Model;

class MateriTugasResource extends Resource
{
    use NestedResource;

    protected static ?string $model = MateriTugas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $recordTitleAttribute = 'judul';

    public static function getAncestor(): ?Ancestor
    {
        return Ancestor::make('allTugas', 'modul');
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        $attemped = auth()->user()->kuis()->where('materi_tugas_id', $infolist->getRecord()->id)->count();
        $modul = Modul::find($infolist->getRecord()->modul_id, ['judul', 'deskripsi']);
        return $infolist
            ->schema([
                Section::make('Modul')
                    ->schema([
                        TextEntry::make('judul')
                            ->hiddenLabel()
                            ->size('lg')
                            ->formatStateUsing(function ($record) use ($modul) {
                                return $modul->judul;
                            }),
                        TextEntry::make('deskripsi')
                            ->hiddenLabel()
                            ->markdown()
                    ])
                    ->collapsed()
                    ->collapsible(),
                Section::make()
                    ->schema([
                        TextEntry::make('jenis')
                            ->hiddenLabel()
                            ->badge()
                            ->color(fn($record) => match ($record->jenis) {
                                'tugas' => 'primary',
                                'materi' => 'info',
                                'kuis' => 'danger',
                                default => 'secondary',
                            }),
                        TextEntry::make('deskripsi')
                            ->hiddenLabel()
                            ->markdown(),
                    ])->collapsible(),
                Section::make('File Terkait')
                    ->schema([
                        Actions::make([
                            Actions\Action::make('Files')
                                ->fillForm(function ($record) {
                                    return [
                                        'files' => $record->files,
                                        'file_name' => $record->file_name,
                                    ];
                                })
                                ->form([
                                    FileUpload::make('files')
                                        ->disabled()
                                        ->label('Download Files')
                                        ->disk('public')
                                        ->directory('materi')
                                        ->downloadable()
                                        ->multiple()
                                        ->storeFileNamesIn('file_name')
                                        ->visibility('public'),
                                ])
                        ])
                    ])->visible(fn($record) => $record->files !== null),
                Section::make('Tugas')
                    ->schema([
                        Fieldset::make('Tanggal')
                            ->schema([
                                TextEntry::make('tgl_tenggat')
                                    ->label('Tenggat')
                                    ->badge()
                                    ->color('warning'),
                                TextEntry::make('tgl_selesai')
                                    ->label('Selesai')
                                    ->badge()
                                    ->color('danger'),
                            ])->columns(2),
                        Actions::make([
                            Actions\Action::make('Submit Tugas')
                                ->form([
                                    FileUpload::make('files')
                                        ->label('File Tugas')
                                        ->disk('public')
                                        ->directory('tugas')
                                        ->downloadable()
                                        ->multiple()
                                        ->storeFileNamesIn('file_name')
                                        ->visibility('public'),
                                    Textarea::make('pesan_peserta')
                                        ->label('Pesan Peserta')
                                        ->placeholder('Tulis pesan untuk admin'),
                                ])
                                ->action(function (array $data, $record) {
                                    $status = 'belum';
                                    if ($record->tgl_selesai > now()) {
                                        $status = $record->tgl_tenggat > now() ? 'selesai' : 'telat';
                                    }

                                    auth()->user()->mengerjakan()->syncWithoutDetaching([
                                        $record->id => [
                                            'status' => $status,
                                            'files' => json_encode($data['files']),
                                            'file_name' => json_encode($data['file_name']),
                                            'pesan_peserta' => $data['pesan_peserta'],
                                        ]
                                    ]);
                                })
                                ->visible(fn($record) => auth()->user()->mengerjakan()->where('materi_tugas_id', $record->id)->doesntExist())
                                ->disabled(fn($record) => $record->tgl_selesai < now()),
                            Actions\Action::make('Cek Tugas')
                                ->fillForm(function ($record) {
                                    $mengerjakan = auth()->user()->mengerjakan()->where('materi_tugas_id', $record->id)->first()->pivot;
//                                    dd($mengerjakan);
                                    return [
                                        'tgl_submit' => $mengerjakan->updated_at,
                                        'penilaian' => $mengerjakan->penilaian,
                                        'files' => json_decode($mengerjakan->files, true),
                                        'file_name' => json_decode($mengerjakan->file_name, true),
                                        'pesan_peserta' => $mengerjakan->pesan_peserta,
                                        'status' => $mengerjakan->status,
                                        'pesan_admin' => $mengerjakan->pesan_admin,
                                    ];
                                })
                                ->form([
                                    \Filament\Forms\Components\Fieldset::make()
                                        ->schema([
                                            ToggleButtons::make('status')
                                                ->label('Status')
                                                ->options([
                                                    'selesai' => 'Selesai',
                                                    'telat' => 'Telat',
                                                    'belum' => 'Belum',
                                                ])
                                                ->colors([
                                                    'selesai' => 'success',
                                                    'telat' => 'warning',
                                                    'belum' => 'danger',
                                                ])
                                                ->grouped()
                                                ->disabled(),
                                            DateTimePicker::make('tgl_submit')
                                                ->label('Tanggal Submit')
                                                ->native(false)
                                                ->timezone('Asia/Jakarta')
                                                ->hint('Tanggal terakhir submit tugas akan berubah jika ada perubahan file')
                                                ->hintColor('danger')
                                                ->columnSpan(3)
                                                ->disabled(),
                                        ])->columns(4),

                                    TextInput::make('penilaian')
                                        ->label('Penilaian')
                                        ->disabled()
                                        ->placeholder('Belum dinilai'),
                                    Textarea::make('pesan_admin')
                                        ->label('Pesan Admin')
                                        ->disabled(),
                                    FileUpload::make('files')
                                        ->label('File Tugas')
                                        ->disk('public')
                                        ->directory('tugas')
                                        ->downloadable()
                                        ->multiple()
                                        ->storeFileNamesIn('file_name')
                                        ->visibility('public'),
                                    Textarea::make('pesan_peserta')
                                        ->label('Pesan Peserta')
                                        ->placeholder('Tulis pesan untuk admin'),
                                ])
                                ->action(function (array $data, $record) {
                                    $status = 'belum';
                                    if ($record->tgl_selesai > now()) {
                                        $status = $record->tgl_tenggat > now() ? 'selesai' : 'telat';
                                    }
                                    auth()->user()->mengerjakan()->updateExistingPivot($record->id, [
                                        'files' => json_encode($data['files']),
                                        'file_name' => json_encode($data['file_name']),
                                        'pesan_peserta' => $data['pesan_peserta'],
                                        'status' => $status,
                                    ]);
                                })
                        ])

                    ])
                    ->visible(fn($record) => $record->jenis === 'tugas'),
                Section::make('Kuis')
                    ->schema([
                        TextEntry::make('judul'),
                        Group::make()
                            ->schema([
                                TextEntry::make('max_attempt')
                                    ->label('Percobaan Maksimal')
                                    ->numeric()
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('max_attempt')
                                    ->label('Percobaan Dikerjakan')
                                    ->formatStateUsing(function ($record ) use ($attemped){
                                        return $attemped;
                                    })
                                    ->badge()
                                    ->color('warning'),
                            ])->columns(2),


                        Fieldset::make('Tanggal')
                            ->schema([
                                TextEntry::make('tgl_mulai')
                                    ->label('Mulai')
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('tgl_tenggat')
                                    ->label('Tenggat')
                                    ->badge()
                                    ->color('warning'),
                                TextEntry::make('tgl_selesai')
                                    ->label('Selesai')
                                    ->badge()
                                    ->color('danger'),
                            ])->columns(3),
                        Actions::make([
                            Actions\Action::make('Kerjakan Sekarang')
                                ->action(function ($record) {
                                    return redirect()->route('kuis.show', $record->id);
                                })
                                ->requiresConfirmation()
                        ])
                        ->visible(fn($record) => $record->tgl_mulai < now() && $record->tgl_selesai > now() && $record->tgl_tenggat > now() && $attemped < $record->max_attempt),
                    ])
                    ->columns(1)
                    ->visible(fn($record) => $record->jenis === 'kuis' ),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MengerjakanKuisRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
//            'index' => Pages\ListMateriTugas::route('/'),
//            'create' => Pages\CreateMateriTugas::route('/create'),
            'view' => Pages\ViewMateriTugas::route('/{record}'),
//            'edit' => Pages\EditMateriTugas::route('/{record}/edit'),
        ];
    }
}
