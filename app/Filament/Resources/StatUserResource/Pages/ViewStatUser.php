<?php

namespace App\Filament\Resources\StatUserResource\Pages;

use App\Filament\Resources\StatUserResource;
use App\Models\Pelatihan;
use App\Models\Sertifikat;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
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

    public function infolist(Infolist|\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        $pelatihan = $this->pelatihan;

        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('nama'),
                        TextEntry::make('id')
                            ->label('Pelatihan')
                            ->formatStateUsing(function ($record) use ($pelatihan) {
                                $judulPelatihan = Pelatihan::where('id', $pelatihan)->first();
                                return $judulPelatihan->judul;
                            })
                    ]),
                Actions::make([
                    Action::make('Kelulusan')
                        ->requiresConfirmation()
                        ->fillForm(function ($record) {
                            $sertifkat = $record->sertifikat()->where('pelatihan_id', $this->pelatihan)->first();
                            return [
                                'status' => $peserta = $record->peserta()->where('pelatihan_id', $this->pelatihan)->first()->pivot->status,
                                'files' => $sertifkat->files,
                                'file_name' => $sertifkat->file_name
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
                            FileUpload::make('files')
                                ->label('File')
                                ->deletable()
                                ->disk('public')
                                ->directory('sertifikat')
                                ->downloadable()
                                ->storeFileNamesIn('file_name')
                                ->visibility('public')
                                ->acceptedFileTypes(['application/pdf'])
                        ])
                        ->action(function ($record, $data) {
//                            dd($record->peserta()->where('pelatihan_id', $this->pelatihan)->first()->pivot->status);
                            DB::transaction(function () use ($record, $data) {

                                $data['user_id'] = $record->id;
                                $data['pelatihan_id'] = $this->pelatihan;
                                $data['files'] = $data['files'];
                                $data['file_name'] = $data['file_name'];
//                                dd($data['status']);
                                Sertifikat::create([
                                    'users_id' => $data['user_id'],
                                    'pelatihan_id' => $data['pelatihan_id'],
                                    'files' => $data['files'],
                                    'file_name' => $data['file_name']
                                ]);
                                $peserta = $record->peserta()->where('pelatihan_id', $this->pelatihan)->first();
                                $peserta->pivot->status = $data['status'];
                                $peserta->pivot->save();
                            });
                        })
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
