<?php

namespace App\Filament\Resources\ModulResource\Pages;

use App\Filament\Resources\KuisResource;
use App\Filament\Resources\ModulResource;
use App\Filament\Resources\PelatihanResource;
use App\Jobs\cloneKuisJob;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Cache;

class ManageKuis extends ManageRelatedRecords
{
    use NestedPage;

    protected static string $resource = ModulResource::class;

    protected static string $relationship = 'kuis';

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    public static function getNavigationLabel(): string
    {
        return 'Kuis';
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = 'navigation_badge_' . request()->route('record') . '_kuis';

        return cache()->remember($cacheKey, now()->addMinutes(5), function () use ($cacheKey) {
            return self::getResource()::getModel()
                ::where('slug', request()->route('record'))
                ->withCount('kuis')
                ->first()
                ?->kuis_count;
        });
    }

    public function form(Form $form): Form
    {
        return KuisResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul')
            ->columns([
                ToggleColumn::make('published')
                    ->label('Published')
                    ->onIcon('heroicon-c-check')
                    ->offIcon('heroicon-c-x-mark')
                    ->onColor('success')
                    ->sortable(),
                ToggleColumn::make('terjadwal')
                    ->label('Terjadwal')
                    ->onIcon('heroicon-c-check')
                    ->offIcon('heroicon-c-x-mark')
                    ->onColor('success')
                    ->tooltip('Apabila terjadwal, maka kuis akan diterbitkan pada tanggal mulai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->words(5),
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->label('Tanggal Mulai')
                    ->dateTime('d M Y H:i')
                    ->badge()
                    ->color('success')
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->label('Tanggal Selesai')
                    ->badge()
                    ->color('danger')
                    ->dateTime('d M Y H:i')
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('max_attempt')
                    ->label('Max Attempt')
                    ->numeric(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime('d M Y H:i')
                    ->timezone('Asia/Jakarta')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\Action::make('Pelatihan')
                    ->url(function () {
                        $cacheKey = 'url_pelatihan_' . $this->record->pelatihan_id;
                        return Cache::remember($cacheKey, now()->addHour(1), function () {
                            return PelatihanResource::getUrl('modul', ['record' => $this->record->pelatihan->slug]);
                        });
                    })
                    ->icon('heroicon-o-arrow-left')
                    ->color('info'),
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Kuis')
                    ->mutateFormDataUsing(function (array $data) {
                        $data['jenis'] = 'kuis';
                        return $data;
                    }),
//                Tables\Actions\AssociateAction::make(),
            ])->headerActionsPosition(Tables\Actions\HeaderActionsPosition::Bottom)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view')
                        ->label('View')
                        ->url(fn($record) => KuisResource::getUrl('view', ['record' => $record]))
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\Action::make('replicate')
                        ->label('Duplikat Data')
                        ->icon('heroicon-m-square-2-stack')
                        ->action(function ($record) {
                            dispatch(new cloneKuisJob($record));
                        })
                        ->requiresConfirmation()
                        ->successNotificationTitle('Duplikat data berhasil'),
//                    Tables\Actions\DissociateAction::make()
//                    ->label('Hapus'),
//                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
            ])
            ->
            bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DissociateBulkAction::make(),
//                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('publish')
                        ->label('Publish')
                        ->icon('heroicon-c-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->update(['published' => true])),
                    BulkAction::make('draft')
                        ->label('Draft')
                        ->icon('heroicon-c-x-circle')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->update(['published' => false])),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
            ->defaultSort('updated_at', 'desc');
    }
}
