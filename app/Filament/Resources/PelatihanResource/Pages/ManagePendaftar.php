<?php

namespace App\Filament\Resources\PelatihanResource\Pages;

use App\Filament\Resources\PelatihanResource;
use App\Models\Pelatihan;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Actions\Action;

class ManagePendaftar extends ManageRelatedRecords
{
    protected static string $resource = PelatihanResource::class;

    protected static string $relationship = 'pendaftar';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Pendaftar';
    }

    protected function canCreate(): bool
    {
        return false;
    }

    protected function canEdit(Model $record): bool
    {
        return auth()->user()->role === 'admin';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
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

    public function table(Table $table): Table
    {
        $pelatihan = $this->getRecord();
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('role')
                    ->label('Status Dosen')
                    ->badge()
                    ->color(fn($record) => match ($record->role) {
                        'admin' => 'primary',
                        'Internal' => 'success',
                        'External' => 'info',
                    }),
            ])
            ->filters([
//                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([

                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('lihatPengguna')
                    ->icon('heroicon-o-user')
                    ->label('Lihat Pengguna')
                    ->url(fn($record) => route('filament.admin.resources.users.view', $record)),
                Tables\Actions\EditAction::make()
                    ->label('Penentuan')
                    ->successNotification(function (array $data, $record) use ($pelatihan) {
//                        dump($pelat)
                        $message = match ($data['status']) {
                            'pending' => 'Pendaftaran anda masih dalam proses. Silahkan cek pesan dari admin',
                            'diterima' => 'Selamat! Pendaftaran anda telah diterima.',
                            'ditolak' => 'Maaf, pendaftaran anda pada pelatihan telah ditolak. Silahkan cek pesan dari admin',
                            default => 'Status tidak dikenal',
                        };

                        Notification::make()
                            ->title('Pelatihan '.$pelatihan->judul.' - Status Pendaftaran')
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
                                    ->url(route('filament.user.resources.pelatihans.view', $pelatihan->slug))
                            ])
                            ->sendToDatabase($record);
                    }),
                Tables\Actions\DetachAction::make()
                    ->label('Hapus Pendaftar'),
//                Tables\Actions\DeleteAction::make(),
//                Tables\Actions\ForceDeleteAction::make(),
//                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
//                    Tables\Actions\RestoreBulkAction::make(),
//                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
