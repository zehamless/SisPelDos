<?php

namespace App\Filament\Resources\ModulResource\Pages;

use App\Filament\Clusters\Pelatihan;
use App\Filament\Resources\MateriResource;
use App\Filament\Resources\ModulResource;
use App\Filament\Resources\PelatihanResource;
use App\Models\Modul;
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

class ManageMateri extends ManageRelatedRecords
{
    use NestedPage;

    protected static string $resource = ModulResource::class;

    protected static ?string $cluster = Pelatihan::class;
    protected static string $relationship = 'materi';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Materi';
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = 'navigation_badge_' . request()->route('record') . '_materi';

        return cache()->remember($cacheKey, now()->addMinutes(5), function () use ($cacheKey) {
            return self::getResource()::getModel()
                ::where('slug', request()->route('record'))
                ->withCount('materi')
                ->first()
                ?->materi_count;
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
        return MateriResource::form($form);
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
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->words(5),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50),
                Tables\Columns\TextColumn::make('file_name')
                    ->label('File Materi')
                    ->limit(20),
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
                    ->label('Tambah Materi')
                    ->mutateFormDataUsing(function (array $data) {
                        $data['jenis'] = 'materi';
                        return $data;
                    }),
//                Tables\Actions\AssociateAction::make(),
            ])->headerActionsPosition(Tables\Actions\HeaderActionsPosition::Bottom)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view')
                        ->label('View')
                        ->url(fn($record) => MateriResource::getUrl('view', ['record' => $record]))
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\ReplicateAction::make()
                        ->requiresConfirmation(),
//                    Tables\Actions\DissociateAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
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
