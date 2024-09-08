<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Actions;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Table;
use GeoSot\EnvEditor\Dto\BackupObj;
use GeoSot\EnvEditor\Dto\EntryObj;
use GeoSot\EnvEditor\Facades\EnvEditor;
use GeoSot\FilamentEnvEditor\Pages\Actions\Backups\DeleteBackupAction;
use GeoSot\FilamentEnvEditor\Pages\Actions\Backups\DownloadEnvFileAction;
use GeoSot\FilamentEnvEditor\Pages\Actions\Backups\MakeBackupAction;
use GeoSot\FilamentEnvEditor\Pages\Actions\Backups\RestoreBackupAction;
use GeoSot\FilamentEnvEditor\Pages\Actions\Backups\ShowBackupContentAction;
use GeoSot\FilamentEnvEditor\Pages\Actions\Backups\UploadBackupAction;
use GeoSot\FilamentEnvEditor\Pages\Actions\CreateAction;
use GeoSot\FilamentEnvEditor\Pages\Actions\DeleteAction;
use GeoSot\FilamentEnvEditor\Pages\Actions\EditAction;
use GeoSot\FilamentEnvEditor\Pages\ViewEnv;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class Settings extends ViewEnv
{
//    protected static ?string $navigationIcon = 'heroicon-o-document-text';
//
//    protected static string $view = 'filament.pages.settings';
    public function form(Form $form): Form
    {
        $tabs = Tabs::make('Tabs')
            ->tabs([
                Tab::make(__('filament-env-editor::filament-env-editor.tabs.current-env.title'))
                    ->schema($this->getFirstTab()),
                Tab::make(__('filament-env-editor::filament-env-editor.tabs.backups.title'))
                    ->schema($this->getSecondTab()),
            ]);

        return $form
            ->schema([$tabs]);
    }
    private function getFirstTab(): array
    {
        $envData = EnvEditor::getEnvFileContent()
            ->filter(fn(EntryObj $obj) => !$obj->isSeparator())
            ->takeUntil(fn(EntryObj $obj) => $obj->key === 'NOTEDITABLE')
            ->groupBy('group')
            ->map(function (Collection $group) {
                $fields = $group->map(function (EntryObj $obj) {
                    return Group::make([
                        Actions::make([
                            EditAction::make("edit_{$obj->key}")->setEntry($obj)
                                ->form([
                                    TextInput::make('key')
                                        ->default(fn() => $obj->key)
                                        ->readOnly()
                                        ->required(),
                                    TextInput::make('value')
                                        ->required()
                                        ->hidden(fn() => str_starts_with($obj->key, 'COLOR')||str_starts_with($obj->key, 'NO_PDDIKTI')||str_starts_with($obj->key, 'BRAND_LOGO'))
                                        ->default(fn() => $obj->getValue()),
                                    ColorPicker::make('value')
                                        ->label('Value')
                                        ->default($obj->getValue())
                                        ->required()
                                        ->formatStateUsing(fn($state) =>  trim($state, '"'))
                                        ->hidden(fn() => !str_starts_with($obj->key, 'COLOR'))
                                        ->columnSpan(3),
                                    Select::make('value')
                                        ->label('Value')
                                        ->options([
                                            'true' => 'True',
                                            'false' => 'False',
                                        ])
                                        ->required()
                                        ->hidden(fn() => !str_starts_with($obj->key, 'NO_PDDIKTI'))
                                        ->default(fn() => $obj->getValue())
                                        ->columnSpan(3),
                                    FileUpload::make('value')
                                        ->label('Value')
                                        ->required()
                                        ->hidden(fn() => !str_starts_with($obj->key, 'BRAND_LOGO'))
                                        ->image()
                                        ->imageEditor()
                                        ->disk('public')
                                        ->directory('brand_logo')
                                        ->columnSpan(3),
                                ])->mutateFormDataUsing(function (array $data) use ($obj) {
                                    if (str_starts_with($obj->key, 'COLOR')) {
                                        $data['value'] = "\"{$data['value']}\"";
                                    }
                                    return $data;
                                }),
//                            DeleteAction::make("delete_{$obj->key}")->setEntry($obj),
                        ])->alignEnd(),
                        Placeholder::make($obj->key)
                            ->label('')
                            ->content(new HtmlString("<code>{$obj->getAsEnvLine()}</code>"))
                            ->columnSpan(4),
                    ])->columns(5);
                });
                return Section::make()->schema($fields->all())->columns(1);
            })->all();
//        $header = Group::make([
//            Actions::make([
//                CreateAction::make('Add'),
//            ])->alignEnd(),
//        ]);

        return [...$envData];
    }

    private function getSecondTab(): array
    {
        $data = EnvEditor::getAllBackUps()
            ->map(function (BackupObj $obj) {
                return Group::make([
                    Actions::make([
                        DeleteBackupAction::make("delete_{$obj->name}")->setEntry($obj),
                        DownloadEnvFileAction::make("download_{$obj->name}")->setEntry($obj->name)->hiddenLabel()->size(ActionSize::Small),
                        RestoreBackupAction::make("restore_{$obj->name}")->setEntry($obj->name),
                        ShowBackupContentAction::make("show_raw_content_{$obj->name}")->setEntry($obj),
                    ])->alignEnd(),
                    Placeholder::make('name')
                        ->label('')
                        ->content(new HtmlString("<strong>{$obj->name}</strong>"))
                        ->columnSpan(2),
                    Placeholder::make('created_at')
                        ->label('')
                        ->content($obj->createdAt->format(Table::$defaultDateTimeDisplayFormat))
                        ->columnSpan(2),
                ])->columns(5);
            })->all();

        $header = Group::make([
            Actions::make([
                DownloadEnvFileAction::make('download_current}')->tooltip('')->outlined(false),
                UploadBackupAction::make('upload'),
                MakeBackupAction::make('backup'),
            ])->alignEnd(),
        ]);

        return [$header, ...$data];
    }
}
