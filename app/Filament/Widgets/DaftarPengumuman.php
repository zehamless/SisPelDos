<?php

namespace App\Filament\Widgets;

use App\Models\Pengumuman;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class DaftarPengumuman extends BaseWidget
{
    protected int|string|array $columnSpan = "full";
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
                    ->label('Tanggal Pengumuman')
                    ->date()

            ])->contentGrid([
                'md' => 1,
                'xl' => 1,
            ])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Belum Ada Pengumuman');
    }
}
