<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanItemResource\Pages;
use App\Models\Language;
use App\Models\PlanItem;
use BackedEnum;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class PlanItemResource extends LmpResource
{
    protected static ?string $model = PlanItem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $modelLabel = 'filament.resources.plan_item';

    protected static ?string $pluralModelLabel = 'filament.resources.plan_items_standalone';

    protected static ?string $recordTitleAttribute = 'id';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_plans';

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

        return $group instanceof \UnitEnum ? $group->value : ($group !== null ? (string) __($group) : null);
    }

    /**
     * Root items of the plan that may be selected as parent (same rules as the plan form repeater).
     *
     * @return array<int|string, string>
     */
    public static function parentRootOptionsForPlan(?int $planId, ?int $ignoreItemId = null): array
    {
        if (! $planId) {
            return [];
        }

        $query = PlanItem::query()
            ->where('plan_id', $planId)
            ->whereNull('parent_id')
            ->with(['translations.language.locale'])
            ->orderBy('sort_order');

        if ($ignoreItemId) {
            $query->whereKeyNot($ignoreItemId);
        }

        return $query->get()->mapWithKeys(fn (PlanItem $item): array => [
            $item->id => $item->display_text ?: "#{$item->id}",
        ])->all();
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
                    Textarea::make("translations.{$lang->id}.text")
                        ->label(__('filament.resources.plan_item_fields.text'))
                        ->rows(2),
                ])
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.plan_item_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                Select::make('plan_id')
                                    ->label(__('filament.resources.plan_item_fields.plan_id'))
                                    ->relationship(
                                        name: 'plan',
                                        titleAttribute: 'code',
                                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('sort_order')
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                                    ->dehydrated(true),
                                Select::make('parent_id')
                                    ->label(__('filament.resources.plan_item_fields.parent_id'))
                                    ->placeholder(__('filament.resources.plan_item_fields.parent_root'))
                                    ->options(fn (Get $get): array => static::parentRootOptionsForPlan(
                                        planId: (int) $get('plan_id') ?: null,
                                        ignoreItemId: $get('id') ? (int) $get('id') : null,
                                    ))
                                    ->searchable()
                                    ->nullable(),
                                TextInput::make('sort_order')
                                    ->label(__('filament.resources.plan_item_fields.sort_order'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->required(),
                                Toggle::make('active')
                                    ->label(__('filament.resources.plan_item_fields.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.plan_item_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function tableActions(array $actions = ['view', 'edit', 'delete']): array
    {
        return parent::tableActions(['edit', 'delete']);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.plan_item_standalone_columns.id'))
                    ->sortable(),
                TextColumn::make('plan.code')
                    ->label(__('filament.resources.plan_item_standalone_columns.plan'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent.display_text')
                    ->label(__('filament.resources.plan_item_standalone_columns.parent'))
                    ->placeholder('—')
                    ->sortable(false),
                TextColumn::make('display_text')
                    ->label(__('filament.resources.plan_item_standalone_columns.text'))
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereHas(
                        'translations',
                        fn (Builder $q): Builder => $q->where('text', 'like', '%'.$search.'%')
                    )),
                TextColumn::make('sort_order')
                    ->label(__('filament.resources.plan_item_columns.sort_order'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.resources.plan_item_columns.active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('plan_id')
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'plan',
                'parent.translations.language.locale',
                'translations.language.locale',
            ]))
            ->filters([
                SelectFilter::make('plan_id')
                    ->label(__('filament.resources.plan_item_fields.plan_id'))
                    ->relationship(
                        name: 'plan',
                        titleAttribute: 'code',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('sort_order')
                    )
                    ->searchable()
                    ->preload(),
                SelectFilter::make('children_of_plan_item_id')
                    ->label(__('filament.resources.plan_item_standalone_filter_parent_with_children'))
                    ->options(function (): array {
                        return PlanItem::query()
                            ->whereNull('parent_id')
                            ->has('children')
                            ->with(['plan', 'translations.language.locale'])
                            ->orderBy('plan_id')
                            ->orderBy('sort_order')
                            ->get()
                            ->mapWithKeys(function (PlanItem $item): array {
                                $planCode = $item->plan?->code ?? '#'.$item->plan_id;
                                $label = $item->display_text ?: '#'.$item->id;

                                return [(string) $item->id => $planCode.' — '.$label];
                            })
                            ->all();
                    })
                    ->query(function (Builder $query, array $data): void {
                        $value = $data['value'] ?? null;
                        if (blank($value)) {
                            return;
                        }

                        $query->where('parent_id', (int) $value);
                    })
                    ->searchable(),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlanItems::route('/'),
            'create' => Pages\CreatePlanItem::route('/create'),
            'edit' => Pages\EditPlanItem::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('edit', ['record' => $record]);
    }
}
