<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\AccountCategory;
use App\Models\Language;
use App\Models\Menu;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class MenuResource extends BaseResource
{
    protected static ?string $model = Menu::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bars-3-bottom-left';

    protected static ?string $modelLabel = 'filament.resources.menu';

    protected static ?string $pluralModelLabel = 'filament.resources.menus';

    protected static ?string $recordTitleAttribute = 'slug';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_parameters';

    protected static ?int $navigationSort = 17;

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

    public static function getRecordTitle(?Model $record): string
    {
        return $record instanceof Menu ? $record->admin_label : '';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'accountTypes' => fn (BelongsToMany $query): BelongsToMany => $query
                ->with(['translations.language.locale'])
                ->orderBy('sort_order')
                ->orderBy('id'),
        ]);
    }

    /**
     * Sync translation rows from the translations.{langId}.{name|tip} form state.
     *
     * @param  array<int, array{name?: string, tip?: string}>  $rows
     */
    public static function syncMenuTranslations(Menu $menu, array $rows): void
    {
        foreach ($rows as $languageId => $row) {
            $languageId = (int) $languageId;
            if ($languageId < 1) {
                continue;
            }
            $name = isset($row['name']) ? trim((string) $row['name']) : '';
            $tip = isset($row['tip']) ? trim((string) $row['tip']) : '';
            if ($name === '' && $tip === '') {
                $menu->translations()->where('language_id', $languageId)->delete();

                continue;
            }
            $menu->translations()->updateOrCreate(
                ['language_id' => $languageId],
                [
                    'name' => $name !== '' ? $name : null,
                    'tip' => $tip !== '' ? $tip : null,
                ]
            );
        }
    }

    /**
     * @return array<string, string>
     */
    public static function siblingParentFilterOptions(): array
    {
        $opts = [
            'all' => __('filament.resources.menu_filter.all_levels'),
            'root' => __('filament.resources.menu_filter.root_only'),
        ];
        $roots = Menu::query()
            ->whereNull('parent_id')
            ->with(['translations.language.locale'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
        foreach ($roots as $m) {
            $opts[(string) $m->id] = __('filament.resources.menu_filter.children_of', [
                'label' => $m->admin_label,
            ]);
        }

        return $opts;
    }

    /**
     * Reordering only applies within one sibling group; disable when the table shows all levels.
     */
    public static function siblingScopeAllowsReorder(?object $livewire): bool
    {
        if ($livewire === null || ! property_exists($livewire, 'tableFilters')) {
            return true;
        }

        $filters = $livewire->tableFilters;
        if (! is_array($filters)) {
            return true;
        }

        $state = $filters['sibling_parent'] ?? null;
        $v = is_array($state) ? ($state['value'] ?? null) : $state;
        if ($v === null || $v === '') {
            $v = 'root';
        }

        return (string) $v !== 'all';
    }

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        $languages = Language::query()->with('locale')->orderBy('id')->get();

        $translationSections = $languages->map(function (Language $lang) {
            return Section::make($lang->display_name)
                ->schema([
                    TextInput::make("translations.{$lang->id}.name")
                        ->label(__('filament.resources.menu_fields.translation_name'))
                        ->maxLength(255),
                    TextInput::make("translations.{$lang->id}.tip")
                        ->label(__('filament.resources.menu_fields.translation_tip'))
                        ->maxLength(255),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.menu_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('slug')
                                    ->label(__('filament.resources.menu_fields.slug'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText(__('filament.resources.menu_fields.slug_help')),
                                Select::make('parent_id')
                                    ->label(__('filament.resources.menu_fields.parent_id'))
                                    ->relationship(
                                        name: 'parent',
                                        titleAttribute: 'slug',
                                        modifyQueryUsing: fn (Builder $query) => $query
                                            ->orderByRaw('parent_id IS NULL DESC')
                                            ->orderBy('parent_id')
                                            ->orderBy('sort_order')
                                            ->orderBy('id'),
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn (Menu $record): string => $record->admin_label.' ('.$record->slug.')'
                                    )
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('icon')
                                    ->label(__('filament.resources.menu_fields.icon'))
                                    ->maxLength(255)
                                    ->placeholder('heroicon-o-home'),
                                TextInput::make('route')
                                    ->label(__('filament.resources.menu_fields.route'))
                                    ->maxLength(255)
                                    ->placeholder('provider.dashboard'),
                                Toggle::make('active')
                                    ->label(__('filament.common.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.menu_tabs.translations'))
                        ->schema($translationSections),
                    Tab::make(__('filament.resources.menu_tabs.account_types'))
                        ->schema([
                            Section::make('')->schema([
                                CheckboxList::make('accountTypes')
                                    ->label(__('filament.resources.menu_fields.account_types'))
                                    ->helperText(__('filament.resources.menu_fields.account_types_help'))
                                    ->relationship(
                                        'accountTypes',
                                        'code',
                                        modifyQueryUsing: fn (Builder $query) => $query
                                            ->where('active', true)
                                            ->byGroup('type')
                                            ->ordered()
                                            ->with(['translations.language.locale']),
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn (AccountCategory $record): string => $record->name ?: (string) $record->code
                                    )
                                    ->columns(2)
                                    ->gridDirection('row')
                                    ->columnSpanFull()
                                    ->bulkToggleable(),
                            ]),
                        ]),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.menu_columns.id'))
                    ->sortable(),
                TextColumn::make('tree_label')
                    ->label(__('filament.resources.menu_columns.label'))
                    ->getStateUsing(fn (Menu $record): string => str_repeat('· ', $record->depth).$record->admin_label)
                    ->description(fn (Menu $record): string => $record->slug),
                TextColumn::make('route')
                    ->label(__('filament.resources.menu_columns.route'))
                    ->toggleable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean(),
                TextColumn::make('account_types_labels')
                    ->label(__('filament.resources.menu_columns.account_types'))
                    ->getStateUsing(function (Menu $record): ?string {
                        $labels = $record->accountTypes
                            ->map(fn (AccountCategory $t): string => $t->name !== '' ? $t->name : (string) $t->code)
                            ->filter()
                            ->values();

                        return $labels->isEmpty() ? null : $labels->implode(', ');
                    })
                    ->placeholder(__('filament.resources.menu_columns.account_types_none'))
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('parent.admin_label')
                    ->label(__('filament.resources.menu_columns.parent'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('sibling_parent')
                    ->label(__('filament.resources.menu_filter.scope'))
                    ->options(fn (): array => static::siblingParentFilterOptions())
                    ->default('root')
                    ->query(function (Builder $query, array $data): void {
                        $v = $data['value'] ?? 'root';
                        if ($v === 'all' || $v === null || $v === '') {
                            return;
                        }
                        if ($v === 'root') {
                            $query->whereNull('parent_id');

                            return;
                        }
                        $query->where('parent_id', (int) $v);
                    }),
                SelectFilter::make('account_type_id')
                    ->label(__('filament.resources.menu_filter.account_type'))
                    ->options(fn (): array => AccountCategory::query()
                        ->where('active', true)
                        ->byGroup('type')
                        ->ordered()
                        ->with(['translations.language.locale'])
                        ->get()
                        ->mapWithKeys(fn (AccountCategory $c): array => [
                            (string) $c->getKey() => $c->name !== '' ? $c->name : (string) $c->code,
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->placeholder(__('filament.resources.menu_filter.account_type_placeholder'))
                    ->query(function (Builder $query, array $data): void {
                        $id = $data['value'] ?? null;
                        if ($id === null || $id === '') {
                            return;
                        }
                        $query->whereHas(
                            'accountTypes',
                            fn (Builder $q): Builder => $q->whereKey((int) $id)
                        );
                    }),
                SelectFilter::make('active')
                    ->label(__('filament.resources.menu_filter.active_status'))
                    ->options([
                        '1' => __('filament.resources.menu_filter.active_only'),
                        '0' => __('filament.resources.menu_filter.inactive_only'),
                    ])
                    ->placeholder(__('filament.resources.menu_filter.active_all'))
                    ->query(function (Builder $query, array $data): void {
                        $v = $data['value'] ?? null;
                        if ($v === null || $v === '') {
                            return;
                        }
                        $query->where('active', (bool) (int) $v);
                    }),
            ], layout: \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->reorderable(
                'sort_order',
                fn ($livewire): bool => static::siblingScopeAllowsReorder($livewire),
            )
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
