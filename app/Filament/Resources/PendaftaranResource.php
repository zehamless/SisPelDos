<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendaftaranResource\Pages;
use App\Filament\Resources\PendaftaranResource\RelationManagers;
use App\Models\Pendaftaran;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PendaftaranResource extends Resource
{
    protected static ?string $model = Pendaftaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?int $navigationSort = 2;
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->formatStateUsing(function ($record) {
                        return $record->user->nama;
                    })
                    ->disabled()
                    ->maxLength(255),
                Forms\Components\Textarea::make('pesan')
                    ->label('Pesan')
                    ->placeholder('Masukkan pesan'),
                FileUpload::make('files')
                    ->label('File')
                    ->deletable(false)
                    ->disk('public')
                    ->directory('daftar')
                    ->downloadable()
                    ->storeFileNamesIn('file_name')
                    ->visibility('public'),
                ToggleButtons::make('status')
                    ->label('Status')
                    ->options([
                        'diterima' => 'Terima',
                        'pending' => 'Pending',
                        'ditolak' => 'Tolak',
                    ])
                    ->colors([
                        'diterima' => 'success',
                        'pending' => 'primary',
                        'ditolak' => 'danger',
                    ])
                    ->hint('Jika status diterima, maka peserta akan masuk ke dalam daftar peserta pelatihan.')
                    ->hintColor('warning')
                    ->grouped(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')
                ->label('Status')
                    ->sortable()
                ->badge(),
                Tables\Columns\TextColumn::make('user.nama')
                ->label('Nama')
                ->words(3),
                Tables\Columns\TextColumn::make('pelatihan.judul')
                    ->searchable()
                ->label('Pelatihan')
                ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                ->label('Daftar Pada')
                ->date()
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'pending' => 'Pending',
                    'diterima' => 'Diterima',
                    'ditolak' => 'Ditolak',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Penentuan')
                    ->mutateFormDataUsing( function ($data, $record) {
                        $data['users_id'] = $record->user->id;
                        return $data;
                    })
                    ->successNotification(function (array $data, $record) {
//                        dd($record->pelatihan->slug);
                        $message = match ($data['status']) {
                            'pending' => 'Pendaftaran anda masih dalam proses. Silahkan cek pesan dari admin',
                            'diterima' => 'Selamat! Pendaftaran anda telah diterima.',
                            'ditolak' => 'Maaf, pendaftaran anda pada pelatihan telah ditolak. Silahkan cek pesan dari admin',
                            default => 'Status tidak dikenal',
                        };

                        Notification::make()
                            ->title('Pelatihan '.$record->pelatihan->judul.' - Status Pendaftaran')
                            ->status(
                                match ($data['status']) {
                                    'pending' => 'warning',
                                    'diterima' => 'success',
                                    'ditolak' => 'danger',
                                    default => 'info',
                                }
                            )
                            ->body($message)
                            ->actions([
                                Action::make('Lihat')
                                    ->url(route('filament.user.resources.pelatihans.view', $record->pelatihan->slug))
                            ])
                            ->sendToDatabase($record->user);
                    }),
                Tables\Actions\Action::make('lihatPengguna')
                    ->icon('heroicon-o-user')
                    ->label('Lihat Pengguna')
                    ->url(fn($record) => route('filament.admin.resources.users.view', $record->user)),
                Tables\Actions\Action::make('lihatPelatihan')
                    ->icon('heroicon-o-academic-cap')
                    ->label('Lihat Pelatihan')
                    ->url(fn($record) => PelatihanResource::getUrl('view',['record'=>$record->pelatihan])),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['user', 'pelatihan'])->mendaftar();
            })
            ->defaultSort('created_at', 'desc')
            ->deferFilters();
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
            'index' => Pages\ListPendaftarans::route('/'),
//            'create' => Pages\CreatePendaftaran::route('/create'),
//            'edit' => Pages\EditPendaftaran::route('/{record}/edit'),
        ];
    }
}
