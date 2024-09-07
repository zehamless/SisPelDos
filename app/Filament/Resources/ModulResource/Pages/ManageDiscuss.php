<?php

namespace App\Filament\Resources\ModulResource\Pages;

use App\Filament\Resources\DiskusiResource;
use App\Filament\Resources\ModulResource;
use App\Filament\Resources\PelatihanResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageDiscuss extends ManageRelatedRecords
{
    use NestedPage;

    protected static string $resource = ModulResource::class;

    protected static string $relationship = 'diskusi';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function getNavigationLabel(): string
    {
        return 'Diskusi';
    }

    public static function getNavigationBadge(): ?string
    {
        return self::getResource()::getModel()::where('slug', request()->route('record'))
            ->withCount('diskusi')
            ->first()?->diskusi_count;
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
        return DiskusiResource::form($form);
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
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('Kembali')
                    ->url(fn() => PelatihanResource::getUrl('modul', ['record' => $this->record->pelatihan->slug]))
                    ->icon('heroicon-o-arrow-left')
                    ->color('info'),
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Diskusi')
                    ->mutateFormDataUsing(function (array $data) {
                        $data['jenis'] = 'diskusi';
                        return $data;
                    }),
            ])->headerActionsPosition(Tables\Actions\HeaderActionsPosition::Bottom)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view')
                        ->label('Lihat')
                        ->url(fn($record) => DiskusiResource::getUrl('view', ['record' => $record->id]))
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\ReplicateAction::make()
                        ->requiresConfirmation(),
//                    Tables\Actions\DissociateAction::make(),
//                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
