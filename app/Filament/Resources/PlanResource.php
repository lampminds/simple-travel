<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Models\Language;
use App\Models\Plan;
use App\Models\PlanItem;
use BackedEnum;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class PlanResource extends LmpResource
{
    protected static ?string $model = Plan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $modelLabel = 'filament.resources.plan';

    protected static ?string $pluralModelLabel = 'filament.resources.plans';

    protected static ?string $recordTitleAttribute = 'code';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_plans';

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

        return $group instanceof \UnitEnum ? $group->value : ($group !== null ? (string) __($group) : null);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    /**
     * @return array<int, mixed>
     */
    public static function planItemsRepeaterSchema(): array
    {
        $languagesList = Language::query()->with('locale')->orderBy('id')->get();

        $itemTranslationSections = $languagesList->map(function (Language $lang) {
            return Section::make($lang->display_name)
                ->schema([
                    Textarea::make("translations.{$lang->id}.text")
                        ->label(__('filament.resources.plan_item_fields.text'))
                        ->rows(2),
                ])
                ->collapsible();
        })->all();

        return [
            Hidden::make('id')
                ->dehydrated(),
            Hidden::make('client_key')
                ->default(fn () => (string) Str::uuid())
                ->dehydrated(),
            Select::make('parent_ref')
                ->label(__('filament.resources.plan_item_fields.parent_id'))
                ->options(fn (Get $get): array => static::planItemParentRefOptions($get))
                ->nullable()
                ->searchable(),
            Toggle::make('active')
                ->label(__('filament.resources.plan_item_fields.active'))
                ->default(true),
            ...$itemTranslationSections,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $allItemsState
     * @return array<string, string>
     */
    public static function planItemParentRefOptions(Get $get): array
    {
        $items = $get('../plan_items')
            ?? $get('../../plan_items')
            ?? $get('../../../plan_items')
            ?? $get('../../../../plan_items')
            ?? [];

        if (! is_array($items)) {
            $items = [];
        }

        $selfKey = (string) ($get('client_key') ?? '');

        $options = ['' => __('filament.resources.plan_item_fields.parent_root')];

        foreach ($items as $row) {
            if (! is_array($row)) {
                continue;
            }
            $key = (string) ($row['client_key'] ?? '');
            if ($key === '' || $key === $selfKey) {
                continue;
            }
            $parentRef = $row['parent_ref'] ?? null;
            if ($parentRef !== null && $parentRef !== '') {
                continue;
            }
            $options[$key] = static::planItemRowLabel($row);
        }

        return $options;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function planItemRowLabel(array $row): string
    {
        $translations = $row['translations'] ?? [];
        if (is_array($translations)) {
            foreach ($translations as $t) {
                if (is_array($t) && ! empty($t['text'])) {
                    return Str::limit((string) $t['text'], 60);
                }
            }
        }

        return (string) __('filament.resources.plan_item_fields.untitled_row');
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public static function syncPlanItems(Plan $plan, array $rows): void
    {
        $rows = array_values(array_filter($rows, static fn ($r): bool => is_array($r)));

        $keyToId = [];
        foreach ($rows as $r) {
            $ck = (string) ($r['client_key'] ?? '');
            if ($ck !== '' && ! empty($r['id'])) {
                $keyToId[$ck] = (int) $r['id'];
            }
        }

        $roots = [];
        $children = [];
        foreach ($rows as $r) {
            $pr = $r['parent_ref'] ?? null;
            if ($pr === null || $pr === '') {
                $roots[] = $r;
            } else {
                $children[] = $r;
            }
        }
        $ordered = array_merge($roots, $children);

        $seenIds = [];
        $sortOrder = 0;

        foreach ($ordered as $r) {
            $parentRef = $r['parent_ref'] ?? null;
            $parentId = null;
            if ($parentRef !== null && $parentRef !== '') {
                $parentId = $keyToId[(string) $parentRef] ?? (ctype_digit((string) $parentRef) ? (int) $parentRef : null);
            }

            $payload = [
                'plan_id' => $plan->id,
                'parent_id' => $parentId,
                'sort_order' => $sortOrder++,
                'active' => (bool) ($r['active'] ?? true),
            ];

            if (! empty($r['id'])) {
                $item = PlanItem::query()->where('plan_id', $plan->id)->whereKey($r['id'])->first();
                if ($item) {
                    $item->forceFill($payload)->save();
                    $seenIds[] = $item->id;
                    $ck = (string) ($r['client_key'] ?? '');
                    if ($ck !== '') {
                        $keyToId[$ck] = $item->id;
                    }
                    static::syncSinglePlanItemTranslations($item, $r['translations'] ?? []);
                }
            } else {
                $item = PlanItem::query()->create($payload);
                $seenIds[] = $item->id;
                $ck = (string) ($r['client_key'] ?? '');
                if ($ck !== '') {
                    $keyToId[$ck] = $item->id;
                }
                static::syncSinglePlanItemTranslations($item, $r['translations'] ?? []);
            }
        }

        static::deletePlanItemsNotKept($plan, $seenIds);
    }

    /**
     * @param  array<int>  $keepIds
     */
    protected static function deletePlanItemsNotKept(Plan $plan, array $keepIds): void
    {
        $keepIds = array_values(array_unique(array_filter($keepIds)));
        $maxIterations = 500;
        while ($maxIterations-- > 0) {
            $query = PlanItem::query()
                ->where('plan_id', $plan->id)
                ->whereNotIn('id', $keepIds)
                ->whereDoesntHave('children');

            if (! $query->exists()) {
                break;
            }
            $query->delete();
        }
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $translations
     */
    public static function syncSinglePlanItemTranslations(PlanItem $record, array $translations): void
    {
        $record->translations()->delete();
        foreach ($translations as $languageId => $row) {
            if (! is_array($row)) {
                continue;
            }
            $text = $row['text'] ?? '';
            if ($text !== '' && $text !== null) {
                $record->translations()->create([
                    'language_id' => (int) $languageId,
                    'text' => $text,
                ]);
            }
        }
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        $languages = Language::query()->with('locale')->orderBy('id')->get();

        $translationSections = $languages->map(function (Language $lang) {
            return Section::make($lang->display_name)
                ->schema([
                    TextInput::make("translations.{$lang->id}.price")
                        ->label(__('filament.resources.plan_fields.price'))
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01),
                    TextInput::make("translations.{$lang->id}.name")
                        ->label(__('filament.resources.plan_fields.name'))
                        ->maxLength(255),
                    Textarea::make("translations.{$lang->id}.description")
                        ->label(__('filament.resources.plan_fields.description'))
                        ->rows(2),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.plan_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('code')
                                    ->label(__('filament.resources.plan_fields.code'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Toggle::make('active')
                                    ->label(__('filament.resources.plan_fields.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.plan_tabs.translations'))
                        ->schema($translationSections),
                    Tab::make(__('filament.resources.plan_tabs.items'))
                        ->schema([
                            Section::make('')->schema([
                                Repeater::make('plan_items')
                                    ->label(__('filament.resources.plan_items'))
                                    ->schema(static::planItemsRepeaterSchema())
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel(__('filament.resources.plan_item_fields.add_row'))
                                    ->reorderable()
                                    ->itemLabel(fn (array $state): ?string => static::planItemRowLabel($state))
                                    ->columnSpanFull()
                                    ->helperText(__('filament.resources.plan_items_repeater_help')),
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
                    ->label(__('filament.resources.plan_columns.id'))
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.plan_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.plan_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label(__('filament.resources.plan_columns.price'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.resources.plan_columns.active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn ($query) => $query->with(['translations.language.locale']));
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'view' => Pages\ViewPlan::route('/{record}'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
