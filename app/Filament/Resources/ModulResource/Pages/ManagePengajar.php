<?php

namespace App\Filament\Resources\ModulResource\Pages;

use App\Filament\Resources\ModulResource;
use App\Filament\Resources\PelatihanResource;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ManagePengajar extends ManageRelatedRecords
{
    use NestedPage;

    protected static string $resource = ModulResource::class;

    protected static string $relationship = 'pengajar';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function getNavigationBadge(): ?string
    {
        $cacheKey = 'navigation_badge_' . request()->route('record').'_pengajar';

        return cache()->remember($cacheKey, now()->addMinutes(5), function () use ($cacheKey) {
            return self::getResource()::getModel()
                ::where('slug', request()->route('record'))
                ->withCount('pengajar')
                ->first()
                ?->pengajar_count;
        });
    }

    public static function getNavigationLabel(): string
    {
        return 'Pengajar';
    }


    protected function canCreate(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Ditambahkan')
                    ->badge()
                    ->dateTime('d M Y H:i')
                    ->timezone('Asia/Jakarta'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('Pelatihan')
                    ->url(function () {
                        $cacheKey = 'url_pelatihan_' . $this->record->pelatihan_id;
                        return Cache::remember($cacheKey, now()->addHour(1), function () {
                            return PelatihanResource::getUrl('modul', ['record' => $this->record->pelatihan->slug]);
                        });
                    })
                    ->tooltip('Kembali ke halaman pelatihan')
                    ->icon('heroicon-o-arrow-left')
                    ->color('info'),
                Tables\Actions\AttachAction::make()
                    ->label('Tambah Pengajar')
                    ->color('primary')
                    ->recordSelect(fn(Forms\Components\Select $select) => $select->placeholder('Pilih Pengajar')
                    )
                    ->modalHeading('Tambah Pengajar')
                    ->preloadRecordSelect()
            ])->headerActionsPosition(Tables\Actions\HeaderActionsPosition::Bottom)
            ->actions([
                Tables\Actions\Action::make('lihatPengguna')
                    ->icon('heroicon-s-user')
                    ->label('Lihat Pengguna')
                    ->color('info')
                    ->url(fn($record) => route('filament.admin.resources.users.view', $record)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Hapus Pengajar'),
//                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
