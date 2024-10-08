<?php

namespace App\Filament\Resources\TugasResource\Pages;

use App\Filament\Resources\ModulResource;
use App\Filament\Resources\TugasResource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Illuminate\Support\Facades\Cache;

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
        $cacheKey = 'navigation_badge_' . request()->route()->parameter('record').'_mengerjakan_tugas';

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
                Action::make('Modul')
                    ->url(function () {
                        $cacheKey = 'modul_url_' .$this->record->modul_id . '_tugas';
                        return Cache::remember($cacheKey, now()->addHour(), function () {
                            return ModulResource::getUrl('tugas', ['record' => $this->record->modul->slug]);
                        });
                    })
                    ->tooltip('Kembali ke Modul terkait')
                    ->icon('heroicon-o-arrow-left')
                    ->color('info'),
            ])->headerActionsPosition(HeaderActionsPosition::Bottom)
            ->actions([
                EditAction::make()
                    ->label('Beri Penilaian')
                    ->mutateRecordDataUsing(function (array $data): array {
                        $data['files'] = json_decode($data['files'], true);
                        $data['file_name'] = json_decode($data['file_name'], true);

                        return $data;
                    })
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
