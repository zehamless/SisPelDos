<?php

namespace App\Filament\Resources\PelatihanResource\Pages;

use App\Filament\Resources\PelatihanResource;
use App\Filament\Resources\StatUserResource;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ManagePeserta extends ManageRelatedRecords
{
    protected static string $resource = PelatihanResource::class;

    protected static string $relationship = 'peserta';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Peserta';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->disabled()
                    ->maxLength(255),
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
                        'selesai' => 'Selesai',
                    ])
                    ->colors([
                        'diterima' => 'success',
                        'pending' => 'primary',
                        'ditolak' => 'danger',
                        'selesai' => 'info',
                    ])
                    ->hint('Jika status pending, maka peserta akan masuk kembali ke daftar pendaftar pelatihan.')
                    ->hintColor('danger')
                    ->grouped(),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
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
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($record) => match ($record->status) {
                        'diterima' => 'info',
                        'pending' => 'primary',
                        'ditolak' => 'danger',
                        'selesai' => 'success',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
//                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('lihatPengguna')
                    ->icon('heroicon-s-user')
                    ->label('Lihat Pengguna')
                    ->color('info')
                    ->url(fn($record) => route('filament.admin.resources.users.view', $record)),
                Tables\Actions\Action::make('stat')
                    ->label('Penentuan Kelulusan')
                    ->icon('heroicon-s-check-badge')
                    ->url(fn($record) => StatUserResource::getUrl('view', ['user' => $record->id, 'pelatihan' => $record->pelatihan_id]))
                    ->openUrlInNewTab(),
                Tables\Actions\ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make()
                    ->label('Hapus Peserta'),
                ]),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
