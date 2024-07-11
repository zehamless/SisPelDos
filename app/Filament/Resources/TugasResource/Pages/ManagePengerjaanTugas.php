<?php

namespace App\Filament\Resources\TugasResource\Pages;

use App\Filament\Resources\TugasResource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ManagePengerjaanTugas extends ManageRelatedRecords
{
    use NestedPage;

    protected static string $resource = TugasResource::class;
    protected static string $relationship = 'mengerjakanTugas';

    public static function getNavigationLabel(): string
    {
        return 'Pengerjaan Tugas';
    }
    public static function getNavigationBadge(): ?string
    {
        $record = self::getResource()::getModel()::find(request()->route()->parameter('record'));
        return $record ? $record->mengerjakanTugas()->wherePivotNotIn('status', ['belum'])->count() : null;
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->disabled(),
                TextInput::make('penilaian')
                    ->autofocus(),
                Textarea::make('pesan_peserta')
                    ->disabled(),
                Textarea::make('pesan_admin'),
                FileUpload::make('files')
                    ->hint('Klik icon untuk mengunduh tugas.')
                    ->hintIcon('heroicon-s-arrow-down-tray')
                    ->label('File Tugas')
                    ->disk('public')
                    ->directory('tugas')
                    ->downloadable()
                    ->disabled()
                    ->storeFileNamesIn('file_name')
                    ->visibility('public'),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('status')
                    ->label('Status Pengerjaan')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'selesai' => 'success',
                        'belum' => 'danger',
                        'telat' => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('nama')
                    ->searchable(),
                TextColumn::make('penilaian')
//                    ->formatStateUsing(fn($state) => $state ?? '-')
                    ->label('Nilai')
                    ->badge()
                    ->default('-')
                    ->color('success'),
                TextColumn::make('tgl_submit')
                    ->label('Waktu Mengerjakan')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'selesai' => 'Selesai',
                        'belum' => 'Belum',
                        'telat' => 'Telat',
                    ])
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                EditAction::make()
                    ->label('Beri Penilaian')
                    ->hidden(fn($record) => $record->status === 'belum'),
            ])
            ->bulkActions([
                //
            ])
            ->deferFilters()
            ->defaultGroup('status')
            ->defaultSort('pivot_updated_at', 'desc');
    }

}
