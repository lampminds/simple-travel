<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TodoCategoryResource\Pages;
use App\Models\Account;
use App\Models\Language;
use App\Models\TodoCategory;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Throwable;
use App\Services\TodoCategoryCopyTasksToAccountService;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class TodoCategoryResource extends LmpResource
{
    protected static ?string $model = TodoCategory::class;

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'filament.resources.todo_category';

    protected static ?string $pluralModelLabel = 'filament.resources.todo_categories';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_onboarding';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return (string) __(static::$modelLabel);
    }

    public static function getPluralModelLabel(): string
    {
        return (string) __(static::$pluralModelLabel);
    }

    public static function getNavigationGroup(): ?string
    {
        $group = static::$navigationGroup;

        return $group instanceof \UnitEnum
            ? $group->value
            : ($group !== null ? (string) __($group) : null);
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        $languages = Language::query()->with('locale')->orderBy('id')->get();

        $translationSections = $languages->map(function (Language $lang) {
            return Section::make($lang->display_name)
                ->schema([
                    TextInput::make("translations.{$lang->id}.name")
                        ->label(__('filament.resources.todo_category_fields.name'))
                        ->maxLength(255),
                    Textarea::make("translations.{$lang->id}.description")
                        ->label(__('filament.resources.todo_category_fields.description'))
                        ->rows(2),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.todo_category_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('code')
                                    ->label(__('filament.resources.todo_category_fields.code'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('sort_order')
                                    ->label(__('filament.resources.todo_category_fields.sort_order'))
                                    ->numeric()
                                    ->default(9999)
                                    ->required(),
                                Toggle::make('active')
                                    ->label(__('filament.common.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.todo_category_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.todo_category_columns.id'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('filament.resources.todo_category_columns.sort_order'))
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.todo_category_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.todo_category_columns.name'))
                    ->searchable(query: function (Builder $query, string $search): void {
                        $query->whereHas('translations', function (Builder $q) use ($search): void {
                            $q->where('name', 'like', '%'.$search.'%');
                        });
                    }),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('tasks_for_account')
                    ->label(__('filament.resources.todo_category_filters.account_id'))
                    ->options(fn (): array => Account::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data): void {
                        $id = $data['value'] ?? null;
                        if ($id === null || $id === '') {
                            return;
                        }
                        $query->whereHas(
                            'tasks',
                            fn (Builder $q): Builder => $q->where('account_id', (int) $id)
                        );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['translations.language.locale']))
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('copyTasksToAccount')
                        ->label(__('filament.resources.todo_category_actions.copy_to_account'))
                        ->icon('heroicon-o-document-duplicate')
                        ->modalHeading(__('filament.resources.todo_category_actions.copy_to_account_heading'))
                        ->modalDescription(__('filament.resources.todo_category_actions.copy_to_account_description'))
                        ->form([
                            Select::make('destination_account_id')
                                ->label(__('filament.resources.todo_category_actions.copy_destination_account'))
                                ->options(fn (): array => Account::query()->orderBy('name')->pluck('name', 'id')->all())
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function (array $data, TodoCategory $record): void {
                            $dest = (int) ($data['destination_account_id'] ?? 0);

                            if ($dest < 1) {
                                Notification::make()
                                    ->title(__('filament.resources.todo_category_actions.copy_failed_title'))
                                    ->body(__('filament.resources.todo_category_actions.copy_invalid_account'))
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $service = new TodoCategoryCopyTasksToAccountService;

                            try {
                                $count = $service->copy($record, $dest);
                            } catch (Throwable $e) {
                                Notification::make()
                                    ->title(__('filament.resources.todo_category_actions.copy_failed_title'))
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();

                                return;
                            }

                            if ($count === 0) {
                                Notification::make()
                                    ->title(__('filament.resources.todo_category_actions.copy_none_title'))
                                    ->body(__('filament.resources.todo_category_actions.copy_none_body'))
                                    ->warning()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title(__('filament.resources.todo_category_actions.copy_success_title'))
                                ->body(__('filament.resources.todo_category_actions.copy_success_body', ['count' => $count]))
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTodoCategories::route('/'),
            'create' => Pages\CreateTodoCategory::route('/create'),
            'edit' => Pages\EditTodoCategory::route('/{record}/edit'),
        ];
    }
}
