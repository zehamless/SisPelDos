<?php

namespace App\Filament\Resources\BankSoalResource\Pages;

use App\Filament\Resources\BankSoalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBankSoal extends EditRecord
{
    protected static string $resource = BankSoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['jawabanInput'] = $data['jawaban']['jawabanInput'];
        $data['jawaban_benar'] = $data['jawaban']['jawaban_benar'];
//                    dump($data);
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['jawaban'] = [
            'jawabanInput' => $data['jawabanInput'],
            'jawaban_benar' => $data['jawaban_benar'],
        ];
        $data = collect($data)->except(['jawabanInput', 'jawaban_benar'])->toArray();
        return $data;
    }
}
