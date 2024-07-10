<?php

namespace App\Filament\Resources\ModulResource\Pages;

use App\Filament\Resources\ModulResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Illuminate\Database\Eloquent\Model;

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
        return ( self::getResource()::getModel()::where('slug',request()->route('record'))->first()?->pengajar->count());
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
                    ->color('primary')
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                ->label('Tambah Pengajar')
            ])
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
