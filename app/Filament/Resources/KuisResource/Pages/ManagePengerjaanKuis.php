<?php

namespace App\Filament\Resources\KuisResource\Pages;

use App\Filament\Resources\KuisResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ManagePengerjaanKuis extends ManageRelatedRecords
{
    use NestedPage;

    protected static string $resource = KuisResource::class;

    protected static string $relationship = 'mengerjakanKuis';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        $record = self::getResource()::getModel()::find(request()->route()->parameter('record'));
        return $record ? $record->mengerjakanKuis()->wherePivotNotIn('status', ['belum'])->count() : null;
    }

    public static function getNavigationLabel(): string
    {
        return 'Pengerjakan Kuis';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->readOnly(),
                Forms\Components\TextInput::make('penilaian')
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Pengerjaan')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'selesai' => 'success',
                        'belum' => 'danger',
                        'telat' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('penilaian')
                    ->label('Nilai')
                    ->badge()
                    ->default('-')
                    ->color('success'),
                TextColumn::make('tgl_submit')
                    ->label('Tanggal mengerjakan')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('files')
                    ->label('Benar/Total')
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        if (!is_null($record->files) && is_string($record->files)) {
                            $data = json_decode($record->files, true);
                            if (is_array($data) && isset($data['correct']) && isset($data['total'])) {
                                return $data['correct'] . '/' . $data['total'];
                            }
                        }
                        return '-';
                    })
                    ->default('-')
                    ->color('info')
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
                Tables\Actions\Action::make('Review Kuis')
                    ->icon('heroicon-c-document-magnifying-glass')
                    ->url(fn($record) => route('kuis.review', $record->pivot->id))
                    ->openUrlInNewTab()
                    ->color('info')
                    ->hidden(fn($record) => $record->status === 'belum'),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ])
            ->deferFilters()
            ->defaultGroup('status')
            ->defaultSort('pivot_updated_at', 'desc');
    }
}
