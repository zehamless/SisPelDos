<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatbotDatasResource\Pages;
use App\Models\ChatbotDatas;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ChatbotDatasResource extends Resource
{
    protected static ?string $model = ChatbotDatas::class;
    protected static ?string $navigationLabel = 'Chatbot';
    protected static ?string $navigationGroup = 'System';

    protected static ?string $slug = 'chatbot-datas';

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?ChatbotDatas $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?ChatbotDatas $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                Section::make([
                    Toggle::make('admin')
                        ->helperText('Jika diaktifkan, jawaban ini hanya bisa diakses oleh admin'),
                    Textarea::make('question')
                        ->unique(ignoreRecord: true)
                        ->required(),
                    MarkdownEditor::make('answer')
                        ->toolbarButtons([
                            'attachFiles',
                            'blockquote',
                            'bold',
                            'bulletList',
                            'codeBlock',
                            'heading',
                            'italic',
                            'link',
                            'orderedList',
                            'redo',
                            'strike',
                            'table',
                            'undo',
                        ])
                        ->fileAttachmentsDirectory('bot-answers')
                        ->fileAttachmentsVisibility('private')
                        ->required(),
                ])->columns(1)->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('admin')
                    ->label('Untuk Admin')
                    ->sortable(),
                TextColumn::make('question')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('answer')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
            ])
            ->filters([
                Filter::make('Untuk Admin')
                    ->query(fn(Builder $query): Builder => $query->where('admin', true))
                    ->toggle()
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChatbotDatas::route('/'),
            'create' => Pages\CreateChatbotDatas::route('/create'),
            'edit' => Pages\EditChatbotDatas::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
