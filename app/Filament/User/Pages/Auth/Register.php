<?php

namespace App\Filament\User\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;

class Register extends \Filament\Pages\Auth\Register
{
//    protected static ?string $navigationIcon = 'heroicon-o-document-text';
//
//    protected static string $view = 'filament.user.pages.auth.register';
    public $dosenMapp = [];

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Section::make()
                            ->schema([
                                Toggle::make('is_dosen')
                                    ->label('Terdaftar PDDIKTI?')
                                    ->live()
                                    ->default(true),
                            ])->hidden(fn()=> !config('filament.no_pddikti_register')),
                        Group::make([
                            $this->getDosenComponent(),
                            $this->getNameFormComponent(),
                            $this->nidnComponent(),
                            $this->getEmailFormComponent(),
                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),
                        ])->hidden(fn (Get $get) => !$get('is_dosen')),
                        $this->wizard()->hidden(fn (Get $get) => $get('is_dosen')),
                    ])
                    ->statePath('data'),
            ),
        ];
    }
    protected function wizard()
    {
        return Wizard::make([
            Wizard\Step::make('datadiri')
                ->label('Data Diri')
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Lengkap')
                        ->required(),
                    TextInput::make('nama_gelar')
                        ->label('Nama Lengkap Beserta Gelar')
                        ->placeholder('Contoh: Pak Budi, S.Pd., M.Pd.')
                        ->required(),
                    Select::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->options([
                            'L' => 'Laki-laki',
                            'P' => 'Perempuan',
                        ])
                        ->required(),
                    TextInput::make('no_hp')
                        ->label('Nomor HP')
                        ->tel()
                        ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                        ->required(),
                ]),
            Wizard\Step::make('dataakademik')
                ->label('Data Akademik')
                ->schema([
                    TextInput::make('no_induk')
                        ->numeric()
                        ->label('NIDN/NIDK'),
                    TextInput::make('universitas')
                        ->label('Universitas')
                        ->required(),
                    TextInput::make('prodi')
                        ->label('Program Studi')
                        ->required(),
                    TextInput::make('jabatan_fungsional')
                        ->label('Jabatan Fungsional'),
                    TextInput::make('pendidikan_tertinggi')
                        ->label('Pendidikan Tertinggi'),
                    TextInput::make('status_kerja')
                        ->label('Status Kerja')
                        ->placeholder('Contoh: Dosen Tetap'),
                    Select::make('status_dosen')
                        ->label('Status Dosen')
                        ->options([
                            'Aktif' => 'Aktif',
                            'Tidak Aktif' => 'Tidak Aktif',
                        ])
                        ->default('Aktif')
                        ->required(),
                ]),
            Wizard\Step::make('dataakun')
                ->label('Data Akun')
                ->schema([
                    $this->getEmailFormComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                ]),
        ]);
    }
    protected function getDosenComponent()
    {
        return Select::make('link')
            ->label('Dosen')
            ->placeholder('Nama atau Nomor Induk')
            ->searchable()
            ->required()
            ->autofocus()
            ->live()
            ->preload()
            ->getSearchResultsUsing(function (string $search) {
                $response = Http::get('https://api-frontend.kemdikbud.go.id/hit/' . $search)->json();
//                dd($response['dosen']);
                if (isset($response['dosen']) && is_array($response['dosen'])) {
                    // Memodifikasi array sehingga key adalah 'website-link' dan value adalah 'text'
                    $this->dosenMapp = collect($response['dosen'])->mapWithKeys(function ($item) {
                        return [$item['website-link'] => $item['text']];
                    })->toArray();
//                    dd($this->dosenMapp);
                    return $this->dosenMapp;
                }

                return [];
            })
            ->afterStateUpdated(function (Set $set, $state) {
                $fullText = $this->dosenMapp[$state] ?? 'tidak ada';
//                dump($this->dosenMapp);
//                dump($state);
                if ($fullText !== 'tidak ada') {
                    // Extract the name before the comma
                    $namePart = explode(',', $fullText)[0];
                    $set('nama', $namePart);

                    // Extract the NIDN
                    if (preg_match('/NIDN\s*:\s*(\d+)/', $fullText, $matches)) {
                        $nidn = $matches[1];
                        $set('no_induk', $nidn);
                    }
                } else {
                    $set('nama', $fullText);
                    $set('no_induk', '');
                }

            });
//            ->getSelectedRecord(fn($value, $state) => dd($value, $state));

    }

    protected function nidnComponent()
    {
        return Hidden::make('no_induk');
    }

    protected function getNameFormComponent(): Component
    {
        return Hidden::make('nama')
            ->label(__('filament-panels::pages/auth/register.form.name.label'));
    }
}
