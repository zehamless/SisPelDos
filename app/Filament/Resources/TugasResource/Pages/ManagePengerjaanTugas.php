<?php

namespace App\Filament\Resources\TugasResource\Pages;

use App\Filament\Resources\TugasResource;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRecords;
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
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'selesai' => 'success',
                        'belum' => 'danger',
                        'telat' => 'warning',
                    }),
                TextColumn::make('nama')
                ->searchable(),
                TextColumn::make('penilaian')
//                    ->formatStateUsing(fn ($record) => dd($record))
                    ->label('Nilai')
                    ->badge()
                    ->color('success'),
                TextColumn::make('pivot.created_at')
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
                ->label('Review Tugas'),
            ])
            ->bulkActions([
                //
            ])
            ->deferFilters()
            ->defaultGroup('status')
            ->defaultSort('pivot_updated_at', 'desc');
    }

}
