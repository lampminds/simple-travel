<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TodoTaskResource\Pages;
use App\Models\Language;
use App\Models\TodoCategory;
use App\Models\TodoTask;
use App\Support\TodoTaskRoutePicker;
use BackedEnum;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
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
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class TodoTaskResource extends LmpResource
{
    protected static ?string $model = TodoTask::class;

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $modelLabel = 'filament.resources.todo_task';

    protected static ?string $pluralModelLabel = 'filament.resources.todo_tasks';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_onboarding';

    protected static ?int $navigationSort = 2;

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
                        ->label(__('filament.resources.todo_task_fields.name'))
                        ->maxLength(255),
                    Textarea::make("translations.{$lang->id}.description")
                        ->label(__('filament.resources.todo_task_fields.description'))
                        ->rows(3),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.todo_task_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                Select::make('account_id')
                                    ->label(__('filament.resources.todo_task_fields.account_id'))
                                    ->relationship('account', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live(),
                                TextInput::make('code')
                                    ->label(__('filament.resources.todo_task_fields.code'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(
                                        table: 'todo_tasks',
                                        column: 'code',
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn ($rule, callable $get) => $rule->where('account_id', (int) $get('account_id')),
                                    ),
                                Select::make('todo_category_id')
                                    ->label(__('filament.resources.todo_task_fields.todo_category_id'))
                                    ->options(
                                        fn () => TodoCategory::query()
                                            ->where('active', true)
                                            ->with(['translations.language.locale'])
                                            ->orderBy('sort_order')
                                            ->get()
                                            ->mapWithKeys(fn (TodoCategory $c) => [$c->id => $c->displayLabel()])
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make('original_task_id')
                                    ->label(__('filament.resources.todo_task_fields.original_task_id'))
                                    ->options(function (Get $get, $livewire): array {
                                        $accountId = $get('account_id');
                                        if (! is_numeric($accountId)) {
                                            return [];
                                        }
                                        $query = TodoTask::query()
                                            ->where('account_id', (int) $accountId)
                                            ->orderBy('code');
                                        $record = $livewire->record ?? null;
                                        if ($record instanceof TodoTask) {
                                            $query->whereKeyNot($record->getKey());
                                        }

                                        return $query->pluck('code', 'id')->all();
                                    })
                                    ->searchable()
                                    ->nullable(),
                                Select::make('action_type')
                                    ->label(__('filament.resources.todo_task_fields.action_type'))
                                    ->options([
                                        TodoTask::ACTION_NONE => __('filament.resources.todo_task_action_types.none'),
                                        TodoTask::ACTION_ROUTE => __('filament.resources.todo_task_action_types.route'),
                                        TodoTask::ACTION_URL => __('filament.resources.todo_task_action_types.url'),
                                        TodoTask::ACTION_EXTERNAL => __('filament.resources.todo_task_action_types.external'),
                                    ])
                                    ->default(TodoTask::ACTION_NONE)
                                    ->required()
                                    ->live(),
                                Select::make('action_url')
                                    ->label(__('filament.resources.todo_task_fields.route_name'))
                                    ->helperText(__('filament.resources.todo_task_fields.route_name_help'))
                                    ->options(fn (): array => TodoTaskRoutePicker::search('', 75))
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $search): array => TodoTaskRoutePicker::search($search, 75))
                                    ->getOptionLabelUsing(fn (?string $value): ?string => $value ? (TodoTaskRoutePicker::labelForName($value) ?? $value) : null)
                                    ->nullable()
                                    ->visible(fn (Get $get): bool => (string) $get('action_type') === TodoTask::ACTION_ROUTE)
                                    ->dehydrated(fn (Get $get): bool => (string) $get('action_type') === TodoTask::ACTION_ROUTE),
                                TextInput::make('action_url')
                                    ->label(__('filament.resources.todo_task_fields.action_url'))
                                    ->helperText(__('filament.resources.todo_task_fields.action_url_help'))
                                    ->maxLength(2048)
                                    ->nullable()
                                    ->visible(fn (Get $get): bool => in_array((string) $get('action_type'), [
                                        TodoTask::ACTION_URL,
                                        TodoTask::ACTION_EXTERNAL,
                                    ], true))
                                    ->dehydrated(fn (Get $get): bool => in_array((string) $get('action_type'), [
                                        TodoTask::ACTION_URL,
                                        TodoTask::ACTION_EXTERNAL,
                                    ], true)),
                                Select::make('verification_type')
                                    ->label(__('filament.resources.todo_task_fields.verification_type'))
                                    ->options([
                                        TodoTask::VERIFICATION_NONE => __('filament.resources.todo_task_verification_types.none'),
                                        TodoTask::VERIFICATION_API_CHECK => __('filament.resources.todo_task_verification_types.api-check'),
                                    ])
                                    ->default(TodoTask::VERIFICATION_NONE)
                                    ->required()
                                    ->live(),
                                TextInput::make('verification_url')
                                    ->label(__('filament.resources.todo_task_fields.verification_url'))
                                    ->url()
                                    ->maxLength(2048)
                                    ->nullable()
                                    ->visible(fn (Get $get): bool => (string) $get('verification_type') === TodoTask::VERIFICATION_API_CHECK),
                                TextInput::make('sort_order')
                                    ->label(__('filament.resources.todo_task_fields.sort_order'))
                                    ->numeric()
                                    ->default(9999)
                                    ->required(),
                                Toggle::make('active')
                                    ->label(__('filament.common.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.todo_task_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.todo_task_columns.id'))
                    ->sortable(),
                TextColumn::make('account.name')
                    ->label(__('filament.resources.todo_task_columns.account'))
                    ->sortable()
                    ->searchable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('filament.resources.todo_task_columns.sort_order'))
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.todo_task_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category_label')
                    ->label(__('filament.resources.todo_task_columns.category'))
                    ->getStateUsing(fn (TodoTask $record): string => $record->category?->displayLabel() ?? '—')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy((new TodoTask)->getTable().'.todo_category_id', $direction);
                    }),
                TextColumn::make('name')
                    ->label(__('filament.resources.todo_task_columns.name'))
                    ->searchable(query: function (Builder $query, string $search): void {
                        $query->whereHas('translations', function (Builder $q) use ($search): void {
                            $q->where('name', 'like', '%'.$search.'%');
                        });
                    }),
                TextColumn::make('action_type')
                    ->label(__('filament.resources.todo_task_columns.action_type'))
                    ->formatStateUsing(fn (?string $state): string => match ($state ?? '') {
                        TodoTask::ACTION_NONE => __('filament.resources.todo_task_action_types.none'),
                        TodoTask::ACTION_ROUTE => __('filament.resources.todo_task_action_types.route'),
                        TodoTask::ACTION_URL => __('filament.resources.todo_task_action_types.url'),
                        TodoTask::ACTION_EXTERNAL => __('filament.resources.todo_task_action_types.external'),
                        default => (string) $state,
                    })
                    ->badge(),
                TextColumn::make('verification_type')
                    ->label(__('filament.resources.todo_task_columns.verification_type'))
                    ->formatStateUsing(fn (?string $state): string => match ($state ?? '') {
                        TodoTask::VERIFICATION_NONE => __('filament.resources.todo_task_verification_types.none'),
                        TodoTask::VERIFICATION_API_CHECK => __('filament.resources.todo_task_verification_types.api-check'),
                        default => (string) $state,
                    })
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('account_id')
                    ->label(__('filament.resources.todo_task_filters.account_id'))
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'account',
                'category.translations.language.locale',
                'translations.language.locale',
            ]))
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTodoTasks::route('/'),
            'create' => Pages\CreateTodoTask::route('/create'),
            'edit' => Pages\EditTodoTask::route('/{record}/edit'),
        ];
    }
}
