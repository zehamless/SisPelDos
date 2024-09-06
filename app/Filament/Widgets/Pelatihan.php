<?php

namespace App\Filament\Widgets;

use App\Filament\User\Resources\PelatihanResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class Pelatihan extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort= 2;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                query: \App\Models\Pelatihan::where('published', true)
            )
            ->columns([
                Tables\Columns\Layout\Grid::make()
                    ->columns(2)
                    ->schema([
                        Tables\Columns\ImageColumn::make('sampul')
                            ->label('Sampul')
                            ->width('100%')
                            ->height('100%')
                            ->extraImgAttributes(['loading' => 'lazy'])
                            ->columnSpanFull()
                            ->alignCenter(),
                        Tables\Columns\TextColumn::make('tgl_mulai')
                            ->label('Tanggal Mulai')
                            ->tooltip('Tanggal Pelatihan Dimulai')
                            ->badge()
                            ->date('d M Y', 'Asia/Jakarta')
                            ->color('success'),
                        Tables\Columns\TextColumn::make('tgl_selesai')
                            ->label('Tanggal Selesai')
                            ->tooltip('Tanggal Pelatihan Berakhir')
                            ->badge()
                            ->date('d M Y', 'Asia/Jakarta')
                            ->columnStart(2)
                            ->alignEnd()
                            ->color('danger'),
                        Tables\Columns\TextColumn::make('judul')
                            ->label('Judul')
//                            ->limit(75)
                                ->wrap()
                            ->columnSpanFull()
                            ->searchable(),
                    ])

            ])->contentGrid(['md' => 2, 'lg' => 3, 'xl' => 4])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->url(fn($record) => !auth()->check() ? route('filament.user.auth.login') :PelatihanResource::getUrl('view', ['record' => $record])),
            ])->emptyStateHeading('Belum Ada Pelatihan');
    }
}
