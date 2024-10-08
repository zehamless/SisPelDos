<?php

namespace App\Filament\Resources\KuisResource\Pages;

use App\Filament\Resources\KuisResource;
use App\Filament\Resources\ModulResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Illuminate\Support\Facades\Cache;

class ManagePengerjaanKuis extends ManageRelatedRecords
{
    use NestedPage;

    protected static string $resource = KuisResource::class;

    protected static string $relationship = 'mengerjakanKuis';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = 'navigation_badge_' . request()->route()->parameter('record').'_mengerjakan_kuis';

        return cache()->remember($cacheKey, now()->addMinutes(5), function () use ($cacheKey) {
            return self::getResource()::getModel()
                ::where('id', request()->route()->parameter('record'))
                ->withCount(['mengerjakanKuis' => function ($query) {
                    $query->where('status', '!=', 'belum');
                }])
                ->first()
                ?->mengerjakan_kuis_count;
        });
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
                Action::make('Modul')
                    ->url(function () {
                        $cacheKey = 'modul_url_' .$this->record->modul_id . '_kuis';
                        return Cache::remember($cacheKey, now()->addHour(), function () {
                            return ModulResource::getUrl('kuis', ['record' => $this->record->modul->slug]);
                        });
                    })
                    ->tooltip('Kembali ke Modul terkait')
                    ->icon('heroicon-o-arrow-left')
                    ->color('info'),
            ])->headerActionsPosition(HeaderActionsPosition::Bottom)
            ->actions([
                EditAction::make()
                    ->label('Beri Penilaian')
                    ->hidden(fn($record) => $record->status === 'belum'),
                Tables\Actions\Action::make('Review Kuis')
                    ->icon('heroicon-c-document-magnifying-glass')
                    ->url(fn($record) => route('kuis.adminReview', $record->pivot->id))
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
