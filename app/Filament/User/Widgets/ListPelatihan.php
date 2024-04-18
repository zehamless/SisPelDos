<?php

namespace App\Filament\User\Widgets;

use App\Models\Pelatihan;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;

class ListPelatihan extends BaseWidget
{
    use HasToggleableTable;

    protected int|string|array $columnSpan = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pelatihan::where('published', true)
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
                            ->badge()
                            ->date('d M Y', 'Asia/Jakarta')
                            ->color('primary'),
                        Tables\Columns\TextColumn::make('tgl_selesai')
                            ->label('Tanggal Selesai')
                            ->badge()
                            ->date('d M Y', 'Asia/Jakarta')
                            ->columnStart(2)
                            ->alignEnd()
                            ->color('danger'),
                        Tables\Columns\TextColumn::make('judul')
                            ->label('Judul')
                            ->limit(50)
                            ->columnSpanFull()
                            ->searchable(),
                    ])

            ])
            ->actions([
//                Tables\Actions\Action::make('view')
//                ->action(fn($record) => $this->redirectRoute('filament.user.resources.pelatihans.view', $record)),
                Tables\Actions\Action::make('Detail')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalCloseButton(true)
                    ->infolist([
                        Section::make()
                            ->schema([
                                TextEntry::make('judul')
                                    ->label('Judul'),
                                TextEntry::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->html(),
                            ]),
                        Section::make('Tanggal')
                            ->schema([
                                TextEntry::make('tgl_mulai')
                                    ->label('Tanggal Mulai')
                                    ->dateTime('d M Y'),
                                TextEntry::make('tgl_selesai')
                                    ->label('Tanggal Selesai')
                                    ->dateTime('d M Y'),
                            ])->columns(2),
                    ]),
                Tables\Actions\Action::make('Daftar')
                    ->modalDescription('Baca Syarat dan Ketentuan di detail pelatihan sebelum mendaftar')
                    ->form([
                        FileUpload::make('file')
                            ->label('File')
                            ->disk('public')
                            ->directory('daftar')
                            ->required(F)
                            ->downloadable()
                            ->storeFileNamesIn('file_name')
                            ->visibility('public')
                    ])
            ])
            ->defaultSort('created_at', 'desc')
            ->contentGrid(['md' => 2, 'lg' => 3, 'xl' => 4]);
    }
}
