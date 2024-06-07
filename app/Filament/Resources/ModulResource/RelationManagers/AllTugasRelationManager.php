<?php

namespace App\Filament\Resources\ModulResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AllTugasRelationManager extends RelationManager
{
    protected static string $relationship = 'allTugas';

    protected static ?string $label = 'Semua Tugas';
    protected static ?string $pluralLabel = 'Semua Tugas';

    protected function canEdit(Model $record): bool
    {
        return false;
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
                Tables\Columns\TextColumn::make('tgl_mulai')
                    ->label('Tanggal Mulai')
                    ->badge()
                    ->dateTime()
                    ->timezone('Asia/Jakarta')
                    ->color('success')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->label('Tanggal Selesai')
                    ->badge()
                    ->dateTime()
                    ->timezone('Asia/Jakarta')
                    ->color('danger')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(
                        fn($record) => match ($record->jenis) {
                            'tugas' => 'primary',
                            'materi' => 'info',
                            'kuis' => 'danger',
                            default => 'secondary',
                        }
                    )
                    ->sortable()
                    ->searchable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->label('Jenis')
                    ->options([
                        'tugas' => 'Tugas',
                        'materi' => 'Materi',
                        'kuis' => 'Kuis',
                    ]),

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('Detail')
                        ->label('Detail')
                        ->action(
                            function ($record) {
                                switch ($record->jenis) {
                                    case 'tugas':
                                        return $this->redirectRoute('filament.admin.resources.tugas.view', $record);
                                    case 'materi':
                                        return $this->redirectRoute('filament.admin.resources.materis.view', $record);
                                    case 'kuis':
                                        return $this->redirectRoute('filament.admin.resources.kuis.view', $record);
                                }
                            }
                        )
                        ->icon('heroicon-o-document-magnifying-glass')
                    ->color('info'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('urutan')
            ->reorderable('urutan');
    }
}
