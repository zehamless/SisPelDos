<?php

namespace App\Filament\Resources\DiskusiResource\Pages;

use App\Filament\Resources\DiskusiResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenilaianDiskusi extends ManageRelatedRecords
{
    use NestedPage;

    protected static string $resource = DiskusiResource::class;

    protected static string $relationship = 'mengerjakanTugas';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Penilaian Diskusi';
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = 'navigation_badge_' . request()->route()->parameter('record').'_penilaian_diskusi';

        return cache()->remember($cacheKey, now()->addMinutes(5), function () use ($cacheKey) {
            return self::getResource()::getModel()
                ::where('id', request()->route()->parameter('record'))
                ->withCount(['mengerjakanTugas' => function ($query) {
                    $query->where('status', '!=', 'belum');
                }])
                ->first()
                ?->mengerjakan_tugas_count;
        });
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->disabled(),
                TextInput::make('penilaian')
                    ->autofocus(),
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
                    ->dateTime('d M Y H:i')
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
                    ->mutateFormDataUsing(function ($data) {
                        $data['status'] = 'selesai';
                        return $data;
                    })
                    ->hidden(fn($record) => $record->status === 'selesai')
                ,
            ])
            ->bulkActions([
                //
            ])
            ->deferFilters()
            ->defaultGroup('status')
            ->defaultSort('pivot_updated_at', 'desc');
    }
}
