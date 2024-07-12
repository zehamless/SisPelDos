<?php

namespace App\Filament\User\Resources\ModulResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AllTugasRelationManager extends RelationManager
{
    use NestedRelationManager;

    protected static string $relationship = 'allTugas';
    protected static ?string $label = 'Semua Tugas';

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
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(
                        fn($record) => match ($record->jenis) {
                            'tugas' => 'primary',
                            'materi' => 'info',
                            'kuis' => 'warning',
                            default => 'secondary',
                        }
                    )
                    ->searchable(),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->words(5),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->markdown()
                    ->limit(50),
                Tables\Columns\TextColumn::make('tgl_selesai')
                    ->label('Tanggal Selesai')
                    ->badge()
                    ->dateTime()
                    ->timezone('Asia/Jakarta')
                    ->color('danger')
                    ->searchable(),
                Tables\Columns\TextColumn::make('modul_id')
                    ->label('Penilaian')
                    ->formatStateUsing(function ($record) {
                        switch ($record->jenis) {
                            case 'tugas':
                                return $record->mengerjakanTugas()->where('users_id', auth()->user()->id)->first()->pivot->penilaian ?? '-';
                            case 'diskusi':
                                return $record->mengerjakanTugas()->where('users_id', auth()->user()->id)->first()->pivot->penilaian ?? '-';
                            case 'kuis':
                                return $record->mengerjakanKuis()->where('users_id', auth()->user()->id)->first()->pivot->penilaian ?? '-';
                            default:
                                return '-';
                        }
                    })
                ->badge()

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
//
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
//                    Tables\Actions\Action::make('Detail')
//                        ->label('Detail')
//                        ->action(
//                            function ($record) {
//                                switch ($record->jenis) {
//                                    case 'tugas':
//                                        return $this->redirectRoute('filament.admin.resources.tugas.view', $record);
//                                    case 'materi':
//                                        return $this->redirectRoute('filament.admin.resources.materis.view', $record);
//                                }
//                            }
//                        )
//                        ->icon('heroicon-o-document-magnifying-glass')
//                        ->color('info'),
                ]),

            ])
            ->bulkActions([
                //
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->where('published', true))
            ->defaultSort('urutan');
    }
}
