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
use Illuminate\Support\Facades\Cache;
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
                            ])->hidden(fn() => !config('filament.no_pddikti_register')),
                        Group::make([
                            $this->getDosenComponent(),
                            $this->getNameFormComponent(),
                            $this->nidnComponent(),
                            $this->getEmailFormComponent(),
                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),
                        ])->hidden(fn(Get $get) => !$get('is_dosen')),
                        $this->wizard()->hidden(fn(Get $get) => $get('is_dosen')),
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
                // Attempt to retrieve dosenMapp from cache
                $cacheKey = 'dosen_mapp_' . $search;
                $this->dosenMapp = Cache::get($cacheKey);

                if ($this->dosenMapp !== null) {
                    return $this->dosenMapp;
                }
                // If data is not in cache, fetch from API and cache the response
                $response = $this->fetchDosenData($search);
                $mappedData = $this->mapDosenData($response['dosen'] ?? []);
                Cache::put($cacheKey, $mappedData, now()->addHours(1)); // Cache dosenMapp for 1 hour
                return $this->dosenMapp = $mappedData;
            })
            ->afterStateUpdated(function (Set $set, $state) {
                $fullText = $this->dosenMapp[$state] ?? 'tidak ada';
                if ($fullText !== 'tidak ada') {
                    $namePart = $this->extractNameFromText($fullText);
                    $set('nama', $namePart);

                    $nidn = $this->extractNidnFromText($fullText);
                    $set('no_induk', $nidn);
                } else {
                    $set('nama', $fullText);
                    $set('no_induk', '');
                }
            });
    }

    /**
     * Fetch dosen data from the API.
     *
     * @param string $search
     * @return array
     */
    protected function fetchDosenData(string $search): array
    {
        try {
            $response = Http::get('https://api-frontend.kemdikbud.go.id/hit/' . $search)->json();
            return $response;
        } catch (\Exception $e) {
            // Handle the exception appropriately
            return [];
        }
    }

    /**
     * Map the dosen data to the desired format.
     *
     * @param array $dosenData
     * @return array
     */
    protected function mapDosenData(array $dosenData): array
    {
        return collect($dosenData)
            ->filter(fn($item) => $item['website-link'] !== '/data_dosen/')
            ->mapWithKeys(fn($item) => [$item['website-link'] => $item['text']])
            ->toArray();
    }

    /**
     * Extract the name from the full text.
     *
     * @param string $fullText
     * @return string
     */
    protected function extractNameFromText(string $fullText): string
    {
        return explode(',', $fullText)[0];
    }

    /**
     * Extract the NIDN from the full text.
     *
     * @param string $fullText
     * @return string
     */
    protected function extractNidnFromText(string $fullText): string
    {
        if (preg_match('/NIDN\s*:\s*(\d+)/', $fullText, $matches)) {
            return $matches[1];
        }
        return '';
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
