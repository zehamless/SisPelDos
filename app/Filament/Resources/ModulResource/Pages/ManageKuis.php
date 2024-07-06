<?php

namespace App\Filament\Resources\ModulResource\Pages;

use App\Filament\Resources\ModulResource;
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

class ManageKuis extends ManageRelatedRecords
{
    use NestedPage;

    protected static string $resource = ModulResource::class;

    protected static string $relationship = 'kuis';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Kuis';
    }
    public static function getNavigationBadge(): ?string
    {
        return ( self::getResource()::getModel()::where('slug',request()->route('record'))->first()?->kuis->count());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Group::make([
                            Toggle::make('published')
                                ->label('Published')
                                ->onIcon('heroicon-c-check')
                                ->offIcon('heroicon-c-x-mark')
                                ->onColor('success')
                                ->default(false),
                            Toggle::make('terjadwal')
                                ->label('Terjadwal')
                                ->onIcon('heroicon-c-check')
                                ->offIcon('heroicon-c-x-mark')
                                ->onColor('success')
                                ->helperText('Apabila terjadwal, maka kuis akan diterbitkan pada tanggal mulai')
                                ->default(false),
                        ])->columns(2)->grow(false),
                        Forms\Components\Group::make([
                            Forms\Components\TextInput::make('max_attempt')
                                ->label(__('Max Attempt'))
                                ->required()
                                ->default(1)
                                ->numeric(),
                            Forms\Components\TextInput::make('durasi')
                                ->label('Durasi Pengerjaan')
                                ->suffix(' menit')
                                ->required()
                                ->step(10)
                                ->default(0)
                                ->numeric(),
                        ])->columns(2),
                        Forms\Components\TextInput::make('judul')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi Singkat'),
                        Forms\Components\Group::make([
                            Forms\Components\DateTimePicker::make('tgl_mulai')
                                ->label('Tanggal Mulai')
                                ->native(false)
                                ->timezone('Asia/Jakarta')
                                ->required(),
                            Forms\Components\DateTimePicker::make('tgl_tenggat')
                                ->label('Tanggal Tenggat')
                                ->native(false)
                                ->timezone('Asia/Jakarta')
                                ->after('tgl_mulai')
                                ->rule('after:tgl_mulai')
                                ->required(),
                            Forms\Components\DateTimePicker::make('tgl_selesai')
                                ->label('Tanggal Selesai')
                                ->native(false)
                                ->timezone('Asia/Jakarta')
                                ->after('tgl_tenggat')
                                ->rule('after:tgl_tenggat')
                                ->required(),
                        ])->columns(3),
                    ]),
            ]);
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
                    ->dateTime()
                    ->badge()
                    ->color('success')
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->label('Tanggal Selesai')
                    ->badge()
                    ->color('danger')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('max_attempt')
                    ->label('Max Attempt')
                    ->numeric(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\Action::make('Kembali')
                    ->url(url()->previous())
                    ->icon('heroicon-o-arrow-left')
                    ->color('secondary'),
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
                        ->action(fn($record) => $this->redirectRoute('filament.admin.resources.kuis.view', $record))
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\Action::make('replicate')
                        ->label('Duplikat Data')
                        ->icon('heroicon-m-square-2-stack')
                        ->action(function ($record) {
                            $record->duplicate();
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
            ]));
    }
}
