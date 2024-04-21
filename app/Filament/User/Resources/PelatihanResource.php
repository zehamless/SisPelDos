<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\PelatihanResource\Pages;
use App\Filament\User\Resources\PelatihanResource\RelationManagers;
use App\Models\Pelatihan;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PelatihanResource extends Resource
{
    protected static ?string $model = Pelatihan::class;

    /**
     * @param string|null $slug
     */
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Group::make([
                                    TextEntry::make('judul')
                                        ->label('Judul'),
                                ]),

                                TextEntry::make('tgl_mulai')
                                    ->label('Tanggal Mulai')
                                    ->dateTime('d M Y')
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('tgl_selesai')
                                    ->label('Tanggal Selesai')
                                    ->dateTime('d M Y')
                                    ->badge()
                                    ->color('danger'),

                            ])
                    ]),
                \Filament\Infolists\Components\Section::make('Deskripsi')
                    ->schema([
                        TextEntry::make('deskripsi')
                            ->hiddenLabel()
                            ->html(),
                    ]),
                Section::make('Syarat')
                    ->schema([
                        RepeatableEntry::make('syarat')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('')
                            ]),
                    ]),
                Actions::make([
                    Action::make('Daftar')
                        ->icon('heroicon-s-document-text')
                        ->modalDescription('Baca Syarat dan Ketentuan sebelum mendaftar')
                        ->form([
                            FileUpload::make('files')
                                ->label('File')
                                ->disk('public')
                                ->directory('daftar')
//                            ->required()
                                ->downloadable()
                                ->storeFileNamesIn('file_name')
                                ->visibility('public')
                        ])
                        ->action(function (array $data, Pelatihan $record) {
                            if (auth()->check()) {
                                $record->pendaftar()->attach(auth()->id(), [
                                    'status' => false,
                                    'files' => $data['files'],
                                    'file_name' => $data['file_name'],
                                    'pesan' => 'Pendaftaran berhasil',
                                ]);
                                Notification::make()
                                    ->title('Pendaftaran Berhasil')
                                    ->success()
                                    ->body('Pendaftaran berhasil, silahkan tunggu konfirmasi dari admin')
                                    ->send();

                            } else {
                                session()->flash('warning', 'Anda harus register terlebih dahulu untuk mendaftar pelatihan');
                                return redirect()->route('register');
                            }
                        })
                ])
                    ->visible(fn($record) => !auth()->check() || (auth()->check() && auth()->user()->mendaftar()->where('pelatihan_id', $record->id)->doesntExist()))
            ]);
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
            'index' => Pages\ListPelatihans::route('/'),
            'create' => Pages\CreatePelatihan::route('/create'),
            'edit' => Pages\EditPelatihan::route('/{record}/edit'),
            'view' => Pages\ViewPelatihan::route('/{record}'),
        ];
    }
}
