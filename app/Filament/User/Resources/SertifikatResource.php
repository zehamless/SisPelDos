<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\SertifikatResource\Pages;
use App\Filament\User\Resources\SertifikatResource\RelationManagers;
use App\Models\Sertifikat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SertifikatResource extends Resource
{
    protected static ?string $model = Sertifikat::class;
    protected static string | array $routeMiddleware = 'auth';
    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('created_at')
                    ->label('Selesai Pada')
                    ->formatStateUsing(function ($record) {
//                    dd($record);
                        return $record->created_at->format('d-m-Y');
                    }),
                Forms\Components\TextInput::make('judul')
                    ->formatStateUsing(function ($record) {
                        return $record->pelatihan->judul;
                    }),
                Forms\Components\TextInput::make('no_sertifikat')
                    ->label('No. Sertifikat'),
                Forms\Components\FileUpload::make('files')
                    ->hint('Klik icon untuk mengunduh sertifikat.')
                    ->hintIcon('heroicon-s-arrow-down-tray')
                    ->label('Unduh Sertifikat')
                    ->disk('public')
                    ->directory('sertifikat')
                    ->downloadable()
                    ->storeFileNamesIn('file_name')
                    ->visibility('public'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pelatihan.judul')
                ->searchable(),
                Tables\Columns\TextColumn::make('tgl_sertifikat')
                ->label('Selesai Pada')
                ->date()
                ->timezone('Asia/Jakarta'),
                Tables\Columns\TextColumn::make('no_sertifikat')
                ->label('No. Sertifikat'),
            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('users_id', auth()->user()->id);
            })
            ->defaultSort('created_at', 'desc');
    }

    public static function canView(Model $record): bool
    {
        return auth()->check();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSertifikats::route('/'),
//            'create' => Pages\CreateSertifikat::route('/create'),
//            'edit' => Pages\EditSertifikat::route('/{record}/edit'),
        ];
    }
}
