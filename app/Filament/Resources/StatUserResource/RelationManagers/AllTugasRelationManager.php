<?php

namespace App\Filament\Resources\StatUserResource\RelationManagers;

use App\Filament\Resources\KuisResource;
use App\Filament\Resources\TugasResource;
use App\Models\MateriTugas;
use App\Models\Mengerjakan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AllTugasRelationManager extends RelationManager
{
    protected static string $relationship = 'mengerjakan';
    public $pelatihan;
    public $user;


//    public function mount(): void
//    {
//        $pelatihanId = $this->pelatihan;
//        $userId = $this->user;
//        $completedTugasCount = MateriTugas::whereHas('modul.pelatihan', function ($query) use ($pelatihanId) {
//            $query->where('pelatihan_id', $pelatihanId);
//        })->get();
//        $this->table = $completedTugasCount;
//    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        $pelatihanId = $this->pelatihan;
        $userId = $this->user;
        $completedTugas = MateriTugas::whereHas('modul.pelatihan', function ($query) use ($pelatihanId) {
            $query->where('pelatihan_id', $pelatihanId);
        })->whereHas('peserta', function ($query) use ($userId) {
            $query->where('users_id', $userId);
        })->whereNot('jenis', 'materi')->pluck('id')->toArray();
        return $table
            ->query(MateriTugas::whereHas('modul', function ($query) use ($pelatihanId) {
                $query->where('pelatihan_id', $pelatihanId)->where('published', true);
            })->where('published', true)->whereNot('jenis', 'materi'))
            ->recordTitleAttribute('id')
            ->columns([
//                Tables\Columns\TextColumn::make('id')
//                    ->formatStateUsing(function ($record) use ($completedTugas) {
//                        dd($record);
//                        if (in_array($record->id, $completedTugas)) {
//                            return 'Sudah Dikerjakan';
//                        } else {
//                            return 'Belum Dikerjakan';
//                        }
//                    }),
                Tables\Columns\TextColumn::make('judul')
                    ->limit(20),
                Tables\Columns\TextColumn::make('jenis')
                    ->badge(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Nilai')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(function ($record) {
                        $data = $record->peserta()->where('users_id', $this->user)->orderBy('mengerjakan.created_at', 'desc')->pluck('penilaian')->first();
                        if ($data) {
                            return $data;
                        } else {
                            return 'Belum Dikerjakan';
                        }
                    }),
                Tables\Columns\IconColumn::make('id')
                    ->label('Status')
                    ->icon(function ($record) use ($completedTugas) {
                        if (in_array($record->id, $completedTugas)) {
                            return 'heroicon-s-check-circle';
                        } else {
                            return 'heroicon-s-x-circle';
                        }
                    })
                    ->color(function ($record) use ($completedTugas) {
                        if (in_array($record->id, $completedTugas)) {
                            return 'success';
                        } else {
                            return 'danger';
                        }
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make('lihat')
                    ->label('Lihat')
                    ->url(function ($record) {
                        if ($record->jenis == 'tugas') {
                            return TugasResource::getUrl('view', ['record' => $record->id]);
                        } else {
                            return KuisResource::getUrl('view', ['record' => $record->id]);
                        }
                    }),
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
