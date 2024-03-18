<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelatihanResource\Pages;
use App\Models\Pelatihan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PelatihanResource extends Resource
{
    protected static ?string $model = Pelatihan::class;

    protected static ?string $slug = '';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Riwayat Pelatihan')
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Created Date')
                            ->content(fn(?Pelatihan $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                        Placeholder::make('updated_at')
                            ->label('Last Modified Date')
                            ->content(fn(?Pelatihan $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ])->columns(2),
                Section::make('Detail Pelatihan')
                    ->schema([
                        TextInput::make('judul')
                            ->label('Judul')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->unique('pelatihans', 'slug', ignoreRecord: true)
                            ->readOnly(),

                        DatePicker::make('tgl_mulai')
                            ->label('Tanggal Mulai')
                            ->native(false)
                            ->timezone('Asia/Jakarta')
                            ->required(),

                        DatePicker::make('tgl_selesai')
                            ->label('Tanggal Selesai')
                            ->after('tgl_mulai')
                            ->timezone('Asia/Jakarta')
                            ->native(false)
                            ->rule('after:tgl_mulai')
                            ->required(),

                        Select::make('periode_id')
                            ->relationship('periode', 'tahun_ajar')
                            ->label('Periode')
                            ->required(),
                        Select::make('jenis_pelatihan')
                            ->label('Jenis Pelatihan')
                            ->options([
                                'dosen_lokal' => 'Dosen Lokal',
                                'dosen_luar' => 'Dosen Luar',
                                'semua' => 'Semua',
                            ])
                            ->required()
                            ->default('semua'),
                        FileUpload::make('sampul')
                            ->label('Sampul')
                            ->hint('Pastikan Ukuran gambar 16:9')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->previewable()
                            ->disk('public')
                            ->directory('pelatihan-sampul')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->columnSpan(2),
                        RichEditor::make('deskripsi')
                            ->label('Deskripsi')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('pelatihan-deskripsi')
                            ->fileAttachmentsVisibility('private')
                            ->required()
                            ->columnSpan(2),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul'),

                TextColumn::make('sampul'),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('deskripsi'),

                TextColumn::make('tgl_mulai')
                    ->date(),

                TextColumn::make('tgl_selesai')
                    ->date(),

                TextColumn::make('jmlh_user'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPelatihans::route('/'),
            'create' => Pages\CreatePelatihan::route('/create'),
            'edit' => Pages\EditPelatihan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['slug'];
    }
}
