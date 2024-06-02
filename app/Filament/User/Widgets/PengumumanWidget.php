<?php

namespace App\Filament\User\Widgets;

use App\Models\Pengumuman;
use App\Models\Sertifikat;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PengumumanWidget extends BaseWidget
{
    protected int | string | array $columnSpan = "full";
    protected static ?int $sort = 1;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pengumuman::query()->orderByDesc('created_at')
            )
            ->columns([
                        Tables\Columns\TextColumn::make('pengumuman')
                            ->markdown()
                ->wrap(),
                        Tables\Columns\TextColumn::make('created_at')
                            ->label('Tanggal')
                        ->date()

            ])        ->contentGrid([
                'md' => 1,
                'xl' => 1,
            ])->defaultPaginationPageOption(5);
    }
}
