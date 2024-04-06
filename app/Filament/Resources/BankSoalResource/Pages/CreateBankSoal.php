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
        $data['jawaban'] = [
            'jawabanInput' => $data['jawabanInput'],
            'jawaban_benar' => $data['jawaban_benar'],
        ];
        $data = collect($data)->except(['jawabanInput', 'jawaban_benar'])->toArray();
        return $data;
    }
}
