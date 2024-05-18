<?php

namespace App\Filament\Resources\BankSoalResource\Pages;

use App\Filament\Resources\BankSoalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBankSoal extends CreateRecord
{
    protected static string $resource = BankSoalResource::class;
 protected function mutateFormDataBeforeCreate(array $data): array
{
    foreach ($data['jawaban'] ?? [] as $key => $jawaban) {
        $data['tipe'] = $jawaban['type'] ?? null;
    }
//    dd($data);
    return $data;
}

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
