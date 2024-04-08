<?php

namespace App\Filament\Pages\Auth;

use Exception;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\Facades\Http;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('link')
                    ->label('Link')
                    ->hidden(),
                Actions::make([
                    Action::make('Fetch Data')
                        ->action(function (Get $get, Set $set) {
//                           dump($get('link'));
                            $data = $this->hitDosenApiController($get('link'));
//                            dump($data);
                            if (is_array($data)) {
                                $set('jenis_kelamin', $data['jk']);
                                $set('jabatan_fungsional', $data['fungsional']);
                                $set('pendidikan_tertinggi', $data['pend_tinggi']);
                                $set('status_kerja', $data['ikatankerja']);
                                $set('status_dosen', $data['statuskeaktifan']);
                                Notification::make()
                                    ->title('Success')
                                    ->success()
                                    ->body('Data Dosen Berhasil Diambil.')
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Error')
                                    ->warning()
                                    ->body('PDDikti Service Error, coba lagi nanti.')
                                    ->send();
                            }
                        })
                ]),
                Fieldset::make('Data Dosen')
                    ->schema([
                        TextInput::make('no_induk')
                            ->label('NIDN/NIDK')
                            ->readOnly()
                            ->maxLength(255),
                        TextInput::make('no_hp')
                            ->label('No. HP')
                            ->numeric()
                            ->maxLength(255),
                        Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ]),
                        TextInput::make('universitas')
                            ->label('Universitas')
                            ->maxLength(255),
                        TextInput::make('prodi')
                            ->label('Program Studi')
                            ->maxLength(255),
                        TextInput::make('jabatan_fungsional')
                            ->label('Jabatan Fungsional')
                            ->maxLength(255),
                        TextInput::make('pendidikan_tertinggi')
                            ->label('Pendidikan Tertinggi')
                            ->maxLength(255),
                        TextInput::make('status_kerja')
                            ->label('Status Kerja')
                            ->maxLength(255),
                        TextInput::make('status_dosen')
                            ->label('Status Dosen')
                            ->maxLength(255),

                    ])->columns(1),
                Fieldset::make('Data Login')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama')
                            ->maxLength(255),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])->columns(1),
            ]);
    }

    private function hitDosenApiController(mixed $param)
    {
        try {
            $response = Http::get('https://api-frontend.kemdikbud.go.id/detail_dosen/' . $param)->json();
            return $response['dataumum'];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
