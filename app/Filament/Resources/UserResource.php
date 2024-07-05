<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\User\Resources\UserResource\RelationManagers\RiwayatPelatihanRelationManager;
use App\Models\User;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 99;
    protected static ?string $recordTitleAttribute = 'nama';

public static function canAccess(): bool
{
    return auth()->user()->role === 'admin';
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->label('Nama')
                    ->required(),
                TextInput::make('nama_gelar')
                    ->label('Nama Gelar')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->type('email')
                    ->required(),
                TextInput::make('password')
                    ->label('Password')
                    ->type('password')
                    ->hiddenOn('edit')
                    ->disabledOn('edit')
                    ->required(),
                Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'external' => 'Dosen External',
                        'internal' => 'Dosen UNILA',
                        'pengajar' => 'Pengajar',
                    ])
                    ->required(),
                Select::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_induk')
                    ->label('No Induk')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('universitas')
                    ->label('Universitas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'external' => 'Dosen External',
                        'internal' => 'Dosen UNILA',
                        'pengajar' => 'Pengajar',
                    ])
                    ->label('Role'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Actions::make([
                    Actions\Action::make('Kembali')
                        ->url(url()->previous())
                        ->icon('heroicon-o-arrow-left')
                        ->color('secondary'),
                ]),
                Section::make('Informasi Akun')
                    ->schema([
                        TextEntry::make('nama')
                            ->label('Nama'),
                        TextEntry::make('nama_gelar')
                            ->label('Nama Gelar'),
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('no_induk')
                            ->label('No Induk'),
                        TextEntry::make('no_hp')
                            ->label('No HP'),
                        TextEntry::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->formatStateUsing(fn($state) => match ($state) {
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            }),
                    ])->columns(2),
                Section::make('Informasi Dosen')
                    ->schema([
                        TextEntry::make('universitas')
                            ->label('Universitas'),
                        TextEntry::make('prodi')
                            ->label('Prodi'),
                        TextEntry::make('jabatan_fungsional')
                            ->label('Jabatan Fungsional'),
                        TextEntry::make('pendidikan_tertinggi')
                            ->label('Pendidikan Tertinggi'),
                        TextEntry::make('status_kerja')
                            ->label('Status Kerja')
                            ->badge()
                            ->color(fn($record) => match ($record->status_kerja) {
                                'Aktif' => 'success',
                                'Non-aktif' => 'danger',
                            }),
                        TextEntry::make('status_dosen')
                            ->label('Status Dosen')
                            ->badge()
                            ->color(fn($record) => match ($record->status_dosen) {
                                'Aktif' => 'success',
                                'Non-aktif' => 'danger',
                            }),
                        TextEntry::make('role')
                            ->label('Role')
                            ->badge()
                            ->color('info')
                    ])->columns(2)->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ActivityRelationManager::make(),
            RiwayatPelatihanRelationManager::make(),
//            RelationManagers\ModulsRelationManager::make()
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
