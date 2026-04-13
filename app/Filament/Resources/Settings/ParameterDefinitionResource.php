<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Settings\ParameterDefinitionResource\Pages;
use App\Models\Account;
use App\Models\Language;
use App\Models\ParameterDefinition;
use BackedEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ParameterDefinitionResource extends BaseResource
{
    protected static ?string $model = ParameterDefinition::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $modelLabel = 'filament.resources.parameter_definition';

    protected static ?string $pluralModelLabel = 'filament.resources.parameter_definitions';

    protected static ?string $recordTitleAttribute = 'code';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_parameters';

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

        return $group instanceof \UnitEnum ? $group->value : ($group !== null ? (string) __($group) : null);
    }

    public static function form(Schema $schema): Schema
    {
        if (property_exists(static::getModel(), 'dont_use_audit')) {
            return $schema->schema(
                array_map(
                    fn ($c) => $c->columnSpanFull(),
                    static::getMainFormSchema($schema),
                ),
            );
        }

        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $optionsState
     * @return array<string, string>
     */
    public static function optionChoicesFromFormState(?string $uiComponent, ?array $optionsState): array
    {
        if (! ParameterDefinition::uiComponentRequiresOptions($uiComponent)) {
            return [];
        }

        $rows = array_values(array_filter(
            $optionsState ?? [],
            static fn (array $row): bool => ($row['value'] ?? '') !== ''
        ));

        if (count($rows) < 2) {
            return [];
        }

        $out = [];
        foreach ($rows as $row) {
            $stored = (string) $row['value'];
            $label = $stored;
            foreach ($row['translations'] ?? [] as $t) {
                if (! empty($t['name'])) {
                    $label = (string) $t['name'];
                    break;
                }
            }
            $out[$stored] = $label;
        }

        return $out;
    }

    public static function definitionUsesStoredOptions(ParameterDefinition $definition): bool
    {
        if (! ParameterDefinition::uiComponentRequiresOptions($definition->ui_component)) {
            return false;
        }

        $definition->loadCount('parameterOptions');

        return $definition->parameter_options_count >= 2;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function resolvedValueFromRepeaterRow(array $row, ?string $uiComponent, ?array $optionsState): ?string
    {
        $choices = static::optionChoicesFromFormState($uiComponent, $optionsState);

        if ($choices !== []) {
            $v = $row['value_select'] ?? null;

            return $v !== null && $v !== '' ? (string) $v : null;
        }

        $free = $row['value_free'] ?? null;

        return $free !== null && $free !== '' ? (string) $free : null;
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        $languages = Language::query()->with('locale')->orderBy('id')->get();

        $definitionTranslationSections = $languages->map(function (Language $lang) {
            return Section::make($lang->display_name)
                ->schema([
                    TextInput::make("translations.{$lang->id}.name")
                        ->label(__('filament.resources.parameter_definition_fields.translation_name'))
                        ->maxLength(255),
                    TextInput::make("translations.{$lang->id}.description")
                        ->label(__('filament.resources.parameter_definition_fields.translation_description'))
                        ->maxLength(255),
                    Textarea::make("translations.{$lang->id}.help")
                        ->label(__('filament.resources.parameter_definition_fields.translation_help'))
                        ->rows(3)
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        $optionRepeaterTranslationBlocks = $languages->map(function (Language $lang) {
            return TextInput::make("translations.{$lang->id}.name")
                ->label(__('filament.resources.parameter_option_fields.label').' ('.$lang->display_name.')')
                ->maxLength(255);
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.parameter_definition_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('category')
                                    ->label(__('filament.resources.parameter_definition_fields.category'))
                                    ->maxLength(50)
                                    ->required(),
                                TextInput::make('subcategory')
                                    ->label(__('filament.resources.parameter_definition_fields.subcategory'))
                                    ->maxLength(50)
                                    ->nullable(),
                                TextInput::make('code')
                                    ->label(__('filament.resources.parameter_definition_fields.code'))
                                    ->maxLength(200)
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Select::make('type')
                                    ->label(__('filament.resources.parameter_definition_fields.type'))
                                    ->options(array_combine(ParameterDefinition::TYPES, ParameterDefinition::TYPES))
                                    ->required(),
                                Select::make('scope')
                                    ->label(__('filament.resources.parameter_definition_fields.scope'))
                                    ->options(array_combine(ParameterDefinition::SCOPES, ParameterDefinition::SCOPES))
                                    ->required()
                                    ->live(),
                                Toggle::make('has_default')
                                    ->label(__('filament.resources.parameter_definition_fields.has_default'))
                                    ->default(false),
                                Select::make('ui_component')
                                    ->label(__('filament.resources.parameter_definition_fields.ui_component'))
                                    ->options(static function (): array {
                                        $out = [];
                                        foreach (ParameterDefinition::UI_COMPONENTS as $value) {
                                            $out[$value] = (string) __(
                                                'filament.resources.parameter_definition_ui_components.'.$value
                                            );
                                        }

                                        return $out;
                                    })
                                    ->required()
                                    ->searchable()
                                    ->live(),
                                TextInput::make('sort_order')
                                    ->label(__('filament.resources.parameter_definition_fields.sort_order'))
                                    ->numeric()
                                    ->default(0),
                                Textarea::make('default_value')
                                    ->label(__('filament.resources.parameter_definition_fields.default_value'))
                                    ->rows(2)
                                    ->nullable()
                                    ->columnSpanFull(),
                                KeyValue::make('validation_rules')
                                    ->label(__('filament.resources.parameter_definition_fields.validation_rules'))
                                    ->nullable()
                                    ->columnSpanFull(),
                                KeyValue::make('ui_options')
                                    ->label(__('filament.resources.parameter_definition_fields.ui_options'))
                                    ->nullable()
                                    ->columnSpanFull(),
                                Textarea::make('comments')
                                    ->label(__('filament.resources.parameter_definition_fields.comments'))
                                    ->rows(3)
                                    ->nullable()
                                    ->columnSpanFull(),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.parameter_definition_tabs.translations'))
                        ->schema($definitionTranslationSections),
                    Tab::make(__('filament.resources.parameter_definition_tabs.options'))
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Repeater::make('options')
                                        ->label(__('filament.resources.parameter_definition_tabs.options'))
                                        ->schema([
                                            TextInput::make('value')
                                                ->label(__('filament.resources.parameter_option_fields.value'))
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('sort_order')
                                                ->label(__('filament.resources.parameter_option_fields.sort_order'))
                                                ->numeric()
                                                ->default(0),
                                            Section::make(__('filament.resources.parameter_option_fields.labels'))
                                                ->schema($optionRepeaterTranslationBlocks)
                                                ->columns(2)
                                                ->collapsible(),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->addActionLabel(__('filament.resources.parameter_option_fields.add'))
                                        ->reorderable()
                                        ->orderColumn('sort_order')
                                        ->columnSpanFull()
                                        ->helperText(__('filament.resources.parameter_definition_options_help')),
                                ]),
                        ]),
                    Tab::make(__('filament.resources.parameter_definition_tabs.values'))
                        ->schema([
                            Section::make('')->schema([
                                Repeater::make('parameter_values')
                                    ->label(__('filament.resources.parameter_values'))
                                    ->schema([
                                        Select::make('account_id')
                                            ->label(__('filament.resources.parameter_value_fields.account_id'))
                                            ->options(
                                                fn (): array => Account::query()
                                                    ->orderBy('name')
                                                    ->pluck('name', 'id')
                                                    ->all()
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->nullable()
                                            ->placeholder(__('filament.resources.parameter_value_fields.account_placeholder'))
                                            ->visible(fn (Get $get): bool => ($get('../../scope') ?? $get('../../../scope')) === 'tenant')
                                            ->helperText(__('filament.resources.parameter_value_fields.account_help')),
                                        Select::make('value_select')
                                            ->label(__('filament.resources.parameter_value_fields.value'))
                                            ->options(fn (Get $get): array => static::optionChoicesFromFormState(
                                                $get('../../ui_component') ?? $get('../../../ui_component'),
                                                $get('../../options') ?? $get('../../../options')
                                            ))
                                            ->searchable()
                                            ->visible(fn (Get $get): bool => static::optionChoicesFromFormState(
                                                $get('../../ui_component') ?? $get('../../../ui_component'),
                                                $get('../../options') ?? $get('../../../options')
                                            ) !== [])
                                            ->required(fn (Get $get): bool => static::optionChoicesFromFormState(
                                                $get('../../ui_component') ?? $get('../../../ui_component'),
                                                $get('../../options') ?? $get('../../../options')
                                            ) !== []),
                                        Textarea::make('value_free')
                                            ->label(__('filament.resources.parameter_value_fields.value'))
                                            ->rows(4)
                                            ->nullable()
                                            ->columnSpanFull()
                                            ->visible(fn (Get $get): bool => static::optionChoicesFromFormState(
                                                $get('../../ui_component') ?? $get('../../../ui_component'),
                                                $get('../../options') ?? $get('../../../options')
                                            ) === []),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel(__('filament.resources.parameter_value_fields.add_row'))
                                    ->columnSpanFull()
                                    ->helperText(__('filament.resources.parameter_definition_values_tab_help')),
                            ]),
                        ])
                        ->visibleOn(['create', 'edit']),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.parameter_definition_columns.id'))
                    ->sortable(),
                TextColumn::make('category')
                    ->label(__('filament.resources.parameter_definition_columns.category'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subcategory')
                    ->label(__('filament.resources.parameter_definition_columns.subcategory'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('code')
                    ->label(__('filament.resources.parameter_definition_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.parameter_definition_columns.name'))
                    ->searchable(query: function (Builder $query, string $search): void {
                        $query->whereHas('translations', function (Builder $q) use ($search): void {
                            $q->where('name', 'like', '%'.$search.'%');
                        });
                    }),
                TextColumn::make('type')
                    ->label(__('filament.resources.parameter_definition_columns.type'))
                    ->sortable(),
                TextColumn::make('scope')
                    ->label(__('filament.resources.parameter_definition_columns.scope'))
                    ->sortable(),
                IconColumn::make('has_default')
                    ->label(__('filament.resources.parameter_definition_columns.has_default'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('ui_component')
                    ->label(__('filament.resources.parameter_definition_columns.ui_component'))
                    ->formatStateUsing(fn (?string $state): string => $state !== null && $state !== ''
                        ? (string) __('filament.resources.parameter_definition_ui_components.'.$state)
                        : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('category')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['translations.language.locale']));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParameterDefinitions::route('/'),
            'create' => Pages\CreateParameterDefinition::route('/create'),
            'view' => Pages\ViewParameterDefinition::route('/{record}'),
            'edit' => Pages\EditParameterDefinition::route('/{record}/edit'),
        ];
    }
}
