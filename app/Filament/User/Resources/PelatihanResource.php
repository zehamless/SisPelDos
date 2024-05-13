<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\PelatihanResource\Pages;
use App\Filament\User\Resources\PelatihanResource\RelationManagers;
use App\Models\Pelatihan;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Illuminate\Database\Eloquent\Model;

class PelatihanResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Pelatihan::class;

    protected static ?string $label = 'PelatihanKu';
    protected static ?string $pluralLabel = 'PelatihanKu';

    protected static ?string $recordTitleAttribute = 'judul';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
//            ->query(
//                Pelatihan::whereIn('id', auth()->user()->peserta()->get()->pluck('id'))->newQuery()
//
//            )
            ->columns([
                Tables\Columns\Layout\Grid::make()
                    ->columns(2)
                    ->schema([
                        Tables\Columns\ImageColumn::make('sampul')
                            ->label('Sampul')
                            ->width('100%')
                            ->height('100%')
                            ->extraImgAttributes(['loading' => 'lazy'])
                            ->columnSpanFull()
                            ->alignCenter(),
                        Tables\Columns\TextColumn::make('tgl_mulai')
                            ->label('Tanggal Mulai')
                            ->badge()
                            ->date('d M Y', 'Asia/Jakarta')
                            ->color('primary'),
                        Tables\Columns\TextColumn::make('tgl_selesai')
                            ->label('Tanggal Selesai')
                            ->badge()
                            ->date('d M Y', 'Asia/Jakarta')
                            ->columnStart(2)
                            ->alignEnd()
                            ->color('danger'),
                        Tables\Columns\TextColumn::make('judul')
                            ->label('Judul')
                            ->limit(50)
                            ->columnSpanFull()
                            ->searchable(),
                    ])

            ])->contentGrid(['md' => 2, 'lg' => 3, 'xl' => 4])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        $userPelatihanIds = [];
        $userTerimaPelatihanIds = [];
        if (auth()->check()) {
            $userPelatihanIds = auth()->user()->mendaftar()->pluck('pelatihan_id')->toArray();
            $userTerimaPelatihanIds = auth()->user()->peserta()->pluck('pelatihan_id')->toArray();
//            dump($userPelatihanIds);
        }
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
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

                            ])
                    ]),
                \Filament\Infolists\Components\Section::make('Deskripsi')
                    ->schema([
                        TextEntry::make('deskripsi')
                            ->hiddenLabel()
                            ->html(),
                    ]),
                Section::make('Syarat')
                    ->schema([
                        RepeatableEntry::make('syarat')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('')
                            ]),
                    ])->visible(fn($record) => !auth()->check() || !in_array($record->id, $userPelatihanIds) && !in_array($record->id, $userTerimaPelatihanIds)),
                Actions::make([
                    Action::make('Daftar')
                        ->icon('heroicon-s-document-text')
                        ->modalDescription('Baca Syarat dan Ketentuan sebelum mendaftar')
                        ->form([
                            FileUpload::make('files')
                                ->label('File')
                                ->disk('public')
                                ->directory('daftar')
//                            ->required()
                                ->downloadable()
                                ->storeFileNamesIn('file_name')
                                ->visibility('public')
                        ])
                        ->action(function (array $data, Pelatihan $record) {
                            if (auth()->check()) {
                                $record->pendaftar()->attach(auth()->id(), [
                                    'files' => $data['files'],
                                    'file_name' => $data['file_name'],
                                ]);
                                Notification::make()
                                    ->title('Pendaftaran Berhasil')
                                    ->success()
                                    ->body('Pendaftaran berhasil, silahkan tunggu konfirmasi dari admin')
                                    ->send();

                            } else {
                                session()->flash('warning', 'Anda harus register terlebih dahulu untuk mendaftar pelatihan');
                                return redirect()->route('register');
                            }
                        })
                        ->visible(fn($record) => !auth()->check() || !in_array($record->id, $userPelatihanIds) && !in_array($record->id, $userTerimaPelatihanIds)),
                    Action::make('Status Pendaftaran')
//                        ->icon('heroicon-s-clipboard-check')
                        ->modalDescription('Lihat Status Pendaftaran')
                        ->fillForm(function (Pelatihan $record) {
//                            dump($record);
                            $pendaftar = $record->pendaftar()->where('users_id', auth()->id())->where('pelatihan_id', $record->id)->first()->pivot;
                            return [
                                'status' => $pendaftar->status,
                                'files' => $pendaftar->files,
                                'file_name' => $pendaftar->file_name,
                                'pesan' => $pendaftar->pesan ?? 'Belum ada pesan dari admin',
                            ];
                        })
                        ->form([
                            ToggleButtons::make('status')
                                ->label('Status')
                                ->options([
                                    'diterima' => 'Diterima',
                                    'pending' => 'Pending',
                                    'ditolak' => 'Ditolak',
                                ])
                                ->colors([
                                    'diterima' => 'success',
                                    'pending' => 'primary',
                                    'ditolak' => 'danger',
                                ])
                                ->grouped()
                                ->disabled(),
                            Textarea::make('pesan')
                                ->label('Pesan')
                                ->disabled(),
                            FileUpload::make('files')
                                ->label('File')
                                ->disk('public')
                                ->directory('daftar')
//                            ->required()
                                ->downloadable()
                                ->storeFileNamesIn('file_name')
                                ->visibility('public')
                        ])
                        ->action(function (array $data, Pelatihan $record) {
//                            dump(User::admin()->get() ) ;
                            $pendaftar = $record->pendaftar()->where('users_id', auth()->id())->first()->pivot;
//                            dump($pendaftar);
                            $pendaftar->update([
                                'files' => $data['files'],
                                'file_name' => $data['file_name'],
                            ]);
                            Notification::make()
                                ->title('File Pendaftaran Berhasil')
                                ->success()
                                ->send();
                            Notification::make()
                                ->title('Pendaftaran ' . $record->judul)
                                ->body(auth()->user()->nama . ' mengubah file pendaftaran')
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('Lihat')
                                        ->url(route('filament.admin.pelatihan.resources.pelatihans.view', $record->slug))
                                ])
                                ->info()
                                ->sendToDatabase(User::admin()->get());
                        })
                        ->visible(fn($record) => in_array($record->id, $userPelatihanIds)),
                ]),

            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ModulRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPelatihans::route('/'),
            'create' => Pages\CreatePelatihan::route('/create'),
            'edit' => Pages\EditPelatihan::route('/{record}/edit'),
            'view' => Pages\ViewPelatihan::route('/{record}'),

        ];
    }
}
