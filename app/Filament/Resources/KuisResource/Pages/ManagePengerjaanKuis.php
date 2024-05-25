<?php

namespace App\Filament\Resources\KuisResource\Pages;

use App\Filament\Resources\KuisResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManagePengerjaanKuis extends ManageRelatedRecords
{
    use NestedPage;
    protected static string $resource = KuisResource::class;

    protected static string $relationship = 'mengerjakanKuis';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Mengerjakan Kuis';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->readOnly(),
                Forms\Components\TextInput::make('penilaian')
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'selesai' => 'success',
                        'belum' => 'danger',
                        'telat' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('penilaian')
                    ->label('Nilai')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('pivot.created_at')
                    ->label('Waktu Mengerjakan')
                    ->dateTime()
                    ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('files')
                    ->label('Benar/Total')
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        $jsonString = $record->files; // Replace this with the actual way you're getting the JSON string
                        $data = json_decode($jsonString, true);
                        return $data['correct'].'/'.$data['total'];
                    })
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'selesai' => 'Selesai',
                        'belum' => 'Belum',
                        'telat' => 'Telat',
                    ])
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                EditAction::make()
                    ->label('Beri Penilaian'),
                Tables\Actions\Action::make('Review Kuis')
                    ->url(fn($record) => route('kuis.review', $record->pivot->id))
                    ->openUrlInNewTab()
                    ->color('info'),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ])
            ->deferFilters()
            ->defaultGroup('status')
            ->defaultSort('pivot_updated_at', 'desc');
    }
}
