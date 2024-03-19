<?php

namespace App\Filament\Resources\PelatihanResource\Pages;

use App\Filament\Resources\PelatihanResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageTugas extends ManageRelatedRecords
{
    protected static string $resource = PelatihanResource::class;

    protected static string $relationship = 'tugas';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Tugas';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->columnSpan(2)
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('tgl_mulai')
                    ->native(false)
                    ->timezone('Asia/Jakarta')
                    ->required(),
                Forms\Components\DateTimePicker::make('tgl_selesai')
                    ->native(false)
                    ->timezone('Asia/Jakarta')
                    ->after('tgl_mulai')
                    ->rule('after:tgl_mulai')
                    ->required(),
                Forms\Components\RichEditor::make('deskripsi')
                    ->columnSpan(2)
                    ->label('Deskripsi'),
                Forms\Components\FileUpload::make('files')
                    ->columnSpan(2)
                    ->label('File Materi')
                    ->disk('public')
                    ->directory('materi')
                    ->downloadable()
                    ->multiple()
                    ->storeFileNamesIn('file_name')
                    ->visibility('public'),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul')
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->words(5),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50),
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->label('Tanggal Mulai')
                    ->badge()
                    ->color('success')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->label('Tanggal Mulai')
                    ->badge()
                    ->color('danger')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        $data['jenis'] = 'tugas';
                        $data['tipe'] = 'tugas';
                        return $data;
                    }),
                Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view')
                        ->label('View')
                        ->action(fn($record) => $this->redirectRoute('filament.admin.resources.tugas.view', $record))
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DissociateAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
            ->defaultSort('urutan')
            ->reorderable('urutan');
    }
}
