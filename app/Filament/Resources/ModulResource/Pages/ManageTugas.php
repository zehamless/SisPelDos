<?php

namespace App\Filament\Resources\ModulResource\Pages;

use App\Filament\Resources\ModulResource;
use App\Filament\Resources\PelatihanResource;
use App\Filament\Resources\TugasResource;
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

class ManageTugas extends ManageRelatedRecords
{
    use NestedPage;

    protected static string $resource = ModulResource::class;
    protected static string $relationship = 'tugas';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getNavigationLabel(): string
    {
        return 'Tugas';
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = 'navigation_badge_' . request()->route('record') . '_tugas';

        return cache()->remember($cacheKey, now()->addMinutes(5), function () use ($cacheKey) {
            return self::getResource()::getModel()
                ::where('slug', request()->route('record'))
                ->withCount('tugas')
                ->first()
                ?->tugas_count;
        });
    }

    public static function canAccess(array $parameters = []): bool
    {
        $id = $parameters['record']['id'];
        if (auth()->user()->role === 'admin') {
            return true;
        }
        if (auth()->user()->role === 'pengajar' && auth()->user()->moduls()->where('modul_id', $id)->exists()) {
            return true;
        }
        return false;
    }

    public function form(Form $form): Form
    {
        return TugasResource::form($form);
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
                    ->tooltip('Apabila terjadwal, maka tugas akan diterbitkan pada tanggal mulai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->words(5),
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->label('Tanggal Mulai')
                    ->badge()
                    ->color('success')
                    ->dateTime('d M Y H:i')
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('tgl_tenggat')
                    ->label('Tanggal Tenggat')
                    ->badge()
                    ->color('warning')
                    ->dateTime('d M Y H:i')
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->label('Tanggal Selesai')
                    ->badge()
                    ->color('danger')
                    ->dateTime('d M Y H:i')
                    ->timezone('Asia/Jakarta'),
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
                Tables\Actions\Action::make('Kembali')
                    ->url(fn() => PelatihanResource::getUrl('modul', ['record' => $this->record->pelatihan->slug]))
                    ->icon('heroicon-o-arrow-left')
                    ->color('info'),
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Tugas')
                    ->mutateFormDataUsing(function (array $data) {
                        $data['jenis'] = 'tugas';
                        return $data;
                    }),
//                Tables\Actions\AssociateAction::make(),
            ])->headerActionsPosition(Tables\Actions\HeaderActionsPosition::Bottom)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view')
                        ->label('Lihat')
                        ->url(fn($record) => TugasResource::getUrl('view', ['record' => $record]))
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\ReplicateAction::make()
                        ->requiresConfirmation(),
//                    Tables\Actions\DissociateAction::make(),
//                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DissociateBulkAction::make(),
//                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
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
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
            ->defaultSort('updated_at', 'desc');
    }
}
