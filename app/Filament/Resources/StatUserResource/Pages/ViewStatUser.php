<?php

namespace App\Filament\Resources\StatUserResource\Pages;

use App\Filament\Resources\StatUserResource;
use App\Filament\Resources\UserResource;
use App\Models\Pelatihan;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\DB;

class ViewStatUser extends ViewRecord
{

    protected static string $resource = StatUserResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public $pelatihan;
    public $user;

    public function mount($user): void
    {
        $data = User::where('id', $user)->first();
        $this->record = $data;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $pelatihan = Pelatihan::find($this->pelatihan);
//        dd($pelatihan);
        $status = $this->getRecord()->kelulusan()->where('pelatihan_id', $this->pelatihan)->first()->pivot->status;
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('id')
                            ->label('Pelatihan')
                            ->formatStateUsing(function ($record) use ($pelatihan) {
//                                $judulPelatihan = Pelatihan::where('id', $pelatihan)->first();
                                return $pelatihan->judul;
                            }),
                        TextEntry::make('no_induk')
                            ->label('No Induk'),
                        TextEntry::make('nama')
                            ->label('Nama'),
                        TextEntry::make('nama_gelar')
                            ->label('Nama Gelar'),
                        TextEntry::make('id')
                            ->label('Status Kelulusan')
                            ->formatStateUsing(function () use ($status) {
                                switch ($status) {
                                    case 'selesai':
                                        return 'Lulus';
                                    case 'tidak_selesai':
                                        return 'Tidak Lulus';
                                    case 'diterima':
                                        return 'Belum Diperiksa';
                                }
                            })
                            ->badge()
                            ->color(function () use ($status) {
                                switch ($status) {
                                    case 'selesai':
                                        return 'success';
                                    case 'tidak_selesai':
                                        return 'danger';
                                    case 'diterima':
                                        return 'warning';
                                }
                            }),
                    ])->columns(2),
                Actions::make([
                    Action::make('Kelulusan')
                        ->requiresConfirmation()
                        ->modalWidth(MaxWidth::Large)
                        ->fillForm(function ($record) use ($status) {
                            $sertifkat = $record->peserta()->where('pelatihan_id', $this->pelatihan)->first();
//                            dd($sertifkat->pivot->files);
                            return [
                                'status' => $status,
                                'tgl_sertifikat' => $sertifkat->pivot->tgl_sertifikat ?? $sertifkat->tgl_selesai,
                                'files' => $sertifkat->pivot->files ?? null,
                                'file_name' => $sertifkat->pivot->file_name ?? null,
                                'no_sertifikat' => explode('/', $sertifkat->pivot->no_sertifikat)[0] ?? null
                            ];
                        })
                        ->form([
                            ToggleButtons::make('status')
                                ->label('Status')
                                ->required()
                                ->options([
                                    'selesai' => 'Lulus',
                                    'tidak_selesai' => 'Tidak'
                                ])
                                ->colors([
                                    'selesai' => 'success',
                                    'tidak_selesai' => 'danger'
                                ])->grouped(),
                            DatePicker::make('tgl_sertifikat')
                                ->label('Tanggal Sertifikat')
                                ->native(false)
                                ->hint('Tanggal default adalah tanggal selesai pelatihan')
                                ->timezone('Asia/Jakarta'),
                            TextInput::make('no_sertifikat')
                                ->label('No Sertifikat')
                                ->hint('Periksa No Sertifikat di Pelatihan terlebih dahulu')
                                ->numeric()
                                ->maxLength(5)
                                ->suffix(function ($record) use ($pelatihan) {
                                    return "/" . $pelatihan->no_sertifikat;
                                }),
                            FileUpload::make('files')
                                ->label('Upload Sertifikat')
                                ->deletable()
                                ->disk('public')
                                ->directory('sertifikat')
                                ->downloadable()
                                ->storeFileNamesIn('file_name')
                                ->visibility('public')
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ])
                        ->action(function ($record, $data) use ($pelatihan) {
//                            dd($record->peserta()->where('pelatihan_id', $this->pelatihan)->first()->pivot->status);
//                            dd($record);
                            DB::transaction(function () use ($record, $data, $pelatihan) {

                                $data['user_id'] = $record->id;
                                $data['pelatihan_id'] = $this->pelatihan;
                                $data['files'] = $data['files'];
                                $data['file_name'] = $data['file_name'];
                                $data['no_sertifikat'] = $data['no_sertifikat'];
                                $data['tgl_sertifikat'] = $data['tgl_sertifikat'];
//                                dd($data['status']);
//                                Sertifikat::create([
//                                    'users_id' => $data['user_id'],
//                                    'pelatihan_id' => $data['pelatihan_id'],
//                                    'files' => $data['files'],
//                                    'file_name' => $data['file_name'],
//                                    'no_sertifikat' => $data['no_sertifikat']."/".$pelatihan->no_sertifikat,
//                                    'tgl_sertifikat' => $data['tgl_sertifikat']
//                                ]);
                                $record->sertifikat()->syncWithPivotValues($this->pelatihan, [
                                    'files' => $data['files'],
                                    'file_name' => $data['file_name'],
                                    'no_sertifikat' => $data['no_sertifikat'] . "/" . $pelatihan->no_sertifikat,
                                    'tgl_sertifikat' => $data['tgl_sertifikat']
                                ]);
                                $peserta = $record->peserta()->where('pelatihan_id', $this->pelatihan)->first();
                                $peserta->pivot->status = $data['status'];
                                $peserta->pivot->tgl_sertifikat = $data['tgl_sertifikat'];
                                $peserta->pivot->no_sertifikat = $data['no_sertifikat'] . "/" . $pelatihan->no_sertifikat;
                                $peserta->pivot->files = $data['files'];
                                $peserta->pivot->file_name = $data['file_name'];
                                $peserta->pivot->save();
                            });
                        }),
                    Action::make('profil')
                        ->label('Lihat Profil User')
                        ->url(UserResource::getUrl('view', ['record' => $this->record->id]))
                        ->openUrlInNewTab()
                        ->color('info')
                ])
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
//            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatUserResource\Widgets\StatsOverview::make([
                'pelatihan' => $this->pelatihan,
                'user' => $this->user
            ])
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            StatUserResource\RelationManagers\AllTugasRelationManager::make([
                'pelatihan' => $this->pelatihan,
                'user' => $this->user
            ])
        ];
    }
}
