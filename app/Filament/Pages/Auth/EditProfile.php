<?php

namespace App\Filament\Pages\Auth;

use Exception;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

class EditProfile extends BaseEditProfile
{
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['role'] = $data['universitas'] === 'Universitas Lampung' ? 'internal' : 'external';
        return $data;
    }

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()
                    ->tabs([
                        Tabs\Tab::make('profile')
                            ->label('Foto Profil')
                            ->schema([
                                Hidden::make('no_induk')
                                    ->hidden(),
                                Actions::make([
                                    Action::make('Fetch Data')
                                        ->label('Sinkronisasi Data Dosen')
                                        ->action(function (Get $get, Set $set) {
                                            $id = $get('no_induk');
                                            $data = $this->hitDosenApiController($id);
//                                            dd($data);
                                            if (is_array($data)) {
                                                $set('universitas', $data['nama_pt']);
                                                $set('prodi', $data['nama_prodi']);
                                                $set('jenis_kelamin', $data['jenis_kelamin']);
                                                $set('jabatan_fungsional', $data['jabatan_akademik']);
                                                $set('pendidikan_tertinggi', $data['pendidikan_tertinggi']);
                                                $set('status_kerja', $data['status_ikatan_kerja']);
                                                $set('status_dosen', $data['status_aktivitas']);
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
                                Fieldset::make('foto profile')
                                    ->schema([
                                        Placeholder::make('picture')
                                            ->hiddenLabel()
                                            ->content(function ($record): HtmlString {
                                                $url = asset($record->picture ? 'storage/' . $record->picture : 'assets/defaultProfile.jpg');
                                                return new HtmlString("<img src= '" . $url . "' style='max-width: 250px; display: block; margin-left: auto; margin-right: auto;'>");
                                            }),
                                        FileUpload::make('picture')
                                            ->label('Upload Foto Profil')
                                            ->required()
                                            ->hint('Pastikan Ukuran gambar 1:1')
                                            ->hintColor('warning')
                                            ->image()
                                            ->maxFiles(1)
                                            ->imageEditor()
                                            ->imageEditorMode(1)
//                                    ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('1:1')
                                            ->previewable()
                                            ->optimize('webp')
                                            ->resize(50)
                                            ->disk('public')
                                            ->directory('profil')
                                            ->visibility('public')
                                            ->maxSize(2048),

                                    ])
                                    ->columns(1),
                            ]),
                        Tabs\Tab::make('data')
                            ->label('Data Lengkap')
                            ->schema([
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
                                                'Laki-laki' => 'Laki-laki',
                                                'Perempuan' => 'Perempuan',
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
                            ]),
                        Tabs\Tab::make('login')
                            ->label('Data Login')
                            ->schema([
                                Fieldset::make('Data Login')
                                    ->schema([
                                        TextInput::make('nama')
                                            ->label('Nama')
                                            ->maxLength(255),
                                        $this->getEmailFormComponent(),
                                        $this->getPasswordFormComponent(),
                                        $this->getPasswordConfirmationFormComponent(),
                                    ])->columns(1),
                            ]),
                    ])
            ]);
    }

    private function hitDosenApiController(mixed $param)
    {
//        https://pddikti.kemdikbud.go.id/api/pencarian/dosen/param
        try {
            $getId = Http::get('https://pddikti.kemdikbud.go.id/api/pencarian/dosen/' . $param)->json();
//            https://pddikti.kemdikbud.go.id/api/dosen/profile/param
            $response = Http::get('https://pddikti.kemdikbud.go.id/api/dosen/profile/' . $getId[0]['id'])->json();
            return  $response;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
