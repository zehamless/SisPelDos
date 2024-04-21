<?php

namespace App\Filament\Resources\ModulResource\Pages;

use App\Filament\Resources\ModulResource;
use App\Filament\Resources\PelatihanResource;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageKuis extends ManageRelatedRecords
{
    protected static string $resource = ModulResource::class;

    protected static string $relationship = 'kuis';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Kuis';
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
                    ])->columns(2),
                        Forms\Components\TextInput::make('max_attempt')
                            ->label(__('Max Attempt'))
                            ->required()
                            ->default(1)
                            ->numeric(),
                        Forms\Components\TextInput::make('judul')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Group::make([
                            Forms\Components\DateTimePicker::make('tgl_mulai')
                                ->native(false)
                                ->timezone('Asia/Jakarta')
                                ->required(),
                            Forms\Components\DateTimePicker::make('tgl_tenggat')
                                ->native(false)
                                ->timezone('Asia/Jakarta')
                                ->after('tgl_mulai')
                                ->rule('after:tgl_mulai')
                                ->required(),
                            Forms\Components\DateTimePicker::make('tgl_selesai')
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
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                ->words(5),
                SelectColumn::make('tipe')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
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
                Tables\Actions\CreateAction::make()
                    ->label(__('Create Kuis'))
                    ->mutateFormDataUsing(function (array $data) {
                        $data['jenis'] = 'kuis';
                        return $data;
                    }),
                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view')
                        ->label('View')
                        ->action(fn($record) => $this->redirectRoute('filament.admin.resources.kuis.view', $record))
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DissociateAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
            ])
        ->
        bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DissociateBulkAction::make(),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
            ]),
        ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
