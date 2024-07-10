<?php

namespace App\Filament\Resources\PelatihanResource\Pages;

use App\Filament\Exports\PelatihanExporter;
use App\Filament\Resources\PelatihanResource;
use App\Filament\Resources\StatUserResource;
use App\Models\Pendaftaran;
use App\Models\Sertifikat;
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

    public static function getNavigationBadge(): ?string
    {
        return (self::getResource()::getModel()::where('slug', request()->route('record'))->first()?->peserta->count());
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
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Status Dosen')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($record) => match ($record->status) {
                        'diterima' => 'info',
                        'pending' => 'primary',
                        'ditolak' => 'danger',
                        'selesai' => 'success',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('exported')
                    ->label('Exported')
                    ->icon(fn($state) => $state ? 'heroicon-s-check-circle' : 'heroicon-s-x-circle')
                    ->color(fn($state) => $state ? 'success' : 'danger')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'diterima' => 'Diterima',
                        'pending' => 'Pending',
                        'ditolak' => 'Ditolak',
                        'selesai' => 'Selesai',
                    ])
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'internal' => 'Internal',
                        'external' => 'External',
                        'pengajar' => 'Pengajar',
                    ])
                    ->label('Role'),
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
//                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('lihatPengguna')
                        ->icon('heroicon-s-user')
                        ->label('Lihat Pengguna')
                        ->color('info')
                        ->url(fn($record) => route('filament.admin.resources.users.view', $record))
                        ->openUrlInNewTab(),
                    Tables\Actions\Action::make('stat')
                        ->label('Penentuan Kelulusan')
                        ->icon('heroicon-s-check-badge')
                        ->url(fn($record) => StatUserResource::getUrl('view', ['user' => $record->id, 'pelatihan' => $record->pelatihan_id]))
                        ->openUrlInNewTab(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ExportAction::make()
                        ->label('Export Sertifikat')
                        ->modifyQueryUsing(fn($query, $record) => Sertifikat::where('users_id', $record->id)
                            ->where('pelatihan_id', $record->pelatihan_id)
                            ->with('pelatihan.periode', 'user'))
                        ->exporter(PelatihanExporter::class)
                        ->after(fn($record) => Pendaftaran::where('users_id', $record->id)
                            ->where('pelatihan_id', $record->pelatihan_id)
                            ->update(['exported' => true]))
                        ->columnMapping(false)
                        ->visible(fn($record) => $record->status === 'selesai'),
                    Tables\Actions\DetachAction::make()
                        ->label('Hapus Peserta'),
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make()
                        ->label('Export Sertifikat')
                        ->modifyQueryUsing(fn($query) => $query->with('pelatihan.periode'))
                        ->after(function ($records) {
                            foreach ($records as $record) {
                                Pendaftaran::where('users_id', $record)
                                    ->where('pelatihan_id', $this->getRecord()->id)
                                    ->update(['exported' => true]);
                            }
                        })
                        ->exporter(PelatihanExporter::class)
                        ->columnMapping(false),
                ]),
            ])->checkIfRecordIsSelectableUsing(fn($record) => $record->status === 'selesai')
            ->deferFilters()
            ->defaultGroup('status');
    }
}
