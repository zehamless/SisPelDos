<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\User\Resources\UserResource\RelationManagers\RiwayatPelatihanRelationManager;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\ImageEntry;
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

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $recordTitleAttribute = 'nama';

    public static function canCreate(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function canEdit(Model $record): bool
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
                TextInput::make('email')
                    ->label('Email')
                    ->type('email')
                    ->required(),
                TextInput::make('password')
                    ->label('Password')
                    ->type('password')
                    ->required(),
                Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'External' => 'Dosen External',
                        'Internal' => 'Dosen Internal',
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

                TextColumn::make('status_akun')
                    ->label('Status Akun')
                    ->badge()
                    ->color(fn($record) => match ($record->status_akun) {
                        'Aktif' => 'success',
                        'Non-aktif' => 'danger',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn($record) => match ($record->role) {
                        'admin' => 'info',
                        'Internal' => 'success',
                        'External' => 'warning',
                        default => 'danger',
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status_akun')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Non-aktif' => 'Non-aktif',
                    ])
                    ->label('Status Akun'),
                SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'External' => 'Dosen External',
                        'Internal' => 'Dosen Internal',
                    ])
                    ->label('Role'),
            ])
            ->actions([
                ViewAction::make(),
               DeleteAction::make(),
            ])
            ->bulkActions([
            ]);
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
                            ->color(fn($record) => match ($record->role) {
                                'admin' => 'info',
                                'Internal' => 'success',
                                'External' => 'warning',
                            })
                    ])->columns(2)->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ActivityRelationManager::make(),
            RiwayatPelatihanRelationManager::make()
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
