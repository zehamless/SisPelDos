<?php

namespace App\Filament\User\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
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
                        $this->getDosenComponent(),
                        $this->getNameFormComponent(),
                        $this->nidnComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
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
                dump($this->dosenMapp);
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
