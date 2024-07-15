<?php

namespace App\Filament\Resources\KuisResource\RelationManagers;

use App\Filament\Resources\BankSoalResource;
use App\Models\kategoriSoal;
use App\Models\kuis;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class KuisRelationManager extends RelationManager
{
    protected static string $relationship = 'kuis';
    protected static ?string $title = 'Daftar Pertanyaan';

    private ?bool $isReadOnly = null;

    public function isReadOnly(): bool
    {
        if ($this->isReadOnly === null) {
            $this->isReadOnly = $this->getOwnerRecord()->mengerjakanKuis()->wherePivot('status', 'selesai')->exists();
        }
        return $this->isReadOnly;
    }

    public function form(Form $form): Form
    {
        $form = BankSoalResource::form($form);
        return $form;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('pertanyaan')
            ->columns([
                Tables\Columns\TextColumn::make('pertanyaan')
                    ->label('Pertanyaan')
                    ->description(fn($record) => $record->kategories->kategori)
                    ->html()
                    ->words(5),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->searchable()
                    ->date()
                ->timezone('Asia/Jakarta'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->tooltip('Tambahkan Pertanyaan Baru'),
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelect(fn(Select $select) =>
                    $select
//                        ->multiple()
                        ->placeholder('Pilih Pertanyaan dari BankSoal')
                        ->optionsLimit(20)
                        ->options(function () {
                            $options = [];
                            $kuis = kuis::with('kategories')->get();

                            foreach ($kuis as $item) {
                                $kategori = $item->kategories->kategori;
                                if (!isset($options[$kategori])) {
                                    $options[$kategori] = [];
                                }
                                $options[$kategori][$item->id] = $item->pertanyaan;
                            }

                            return $options;
                        })
                        ->extraAttributes(['class'=>'mt-20'])
                        ->native(false)
                        ->allowHtml(),
                    )
                    ->tooltip('Tambahkan Pertanyaan dari BankSoal')
//            Tables\Actions\Action::make('Lampirkan')
//                ->form([
//                    Select::make('pertanyaan')
//                    ->relationship('kategori', 'kategori')
//                ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                ]),
            ]);
    }
}
