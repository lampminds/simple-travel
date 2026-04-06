<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceFeatureResource\Pages;
use App\Models\ServiceFeature;
use App\Models\ServiceFeatureCategory;
use App\Models\Language;
use App\Models\ServiceType;
use Closure;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceFeatureResource extends LmpResource
{
    protected static ?string $model = ServiceFeature::class;

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $modelLabel = 'filament.resources.service_feature';

    protected static ?string $pluralModelLabel = 'filament.resources.service_features';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_services';

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
                        ->label(__('filament.resources.service_feature_fields.name'))
                        ->maxLength(255),
                    Textarea::make("translations.{$lang->id}.description")
                        ->label(__('filament.resources.service_feature_fields.description'))
                        ->rows(3),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()->tabs([
                Tab::make(__('filament.resources.service_feature_tabs.general'))
                    ->schema([
                        Section::make('')->schema([
                            Select::make('service_feature_category_id')
                                ->label(__('filament.resources.service_feature_fields.category'))
                                ->options(
                                    fn () => ServiceFeatureCategory::query()
                                        ->where('active', true)
                                        ->with(['translations.language.locale'])
                                        ->ordered()
                                        ->get()
                                        ->pluck('name', 'id')
                                )
                                ->required()
                                ->searchable(),
                            TextInput::make('code')
                                ->label(__('filament.resources.service_feature_fields.code'))
                                ->placeholder(__('filament.resources.service_feature_fields.code'))
                                ->required()
                                ->maxLength(255),
                            Toggle::make('is_selectable')
                                ->label(__('filament.resources.service_feature_fields.is_selectable'))
                                ->default(true)
                                ->live(),
                            Toggle::make('active')
                                ->label(__('filament.common.active'))
                                ->default(true),
                            Select::make('parent_id')
                                ->label(__('filament.resources.service_feature_fields.parent_id'))
                                ->nullable()
                                ->searchable()
                                ->preload()
                                ->optionsLimit(500)
                                ->options(
                                    fn () => ServiceFeature::query()
                                        ->where('active', true)
                                        ->where(function (Builder $query): void {
                                            $query->whereNull('parent_id')->orWhere('parent_id', 0);
                                        })
                                        ->ordered()
                                        ->get()
                                        ->mapWithKeys(fn (ServiceFeature $feature) => [
                                            (string) $feature->id => $feature->name !== '' ? $feature->name : $feature->code,
                                        ])
                                        ->all()
                                )
                                ->rules(static function (Select $component): array {
                                    return [
                                        static function (string $attribute, mixed $value, Closure $fail) use ($component): void {
                                            if (blank($value)) {
                                                return;
                                            }

                                            $record = $component->getRecord();

                                            if (! ($record instanceof ServiceFeature)) {
                                                return;
                                            }

                                            $requestedParentId = (int) $value;
                                            $currentId = (int) $record->id;

                                            if ($requestedParentId === $currentId) {
                                                $fail(__('filament.resources.service_feature_set_parent_failure_body_self'));
                                                return;
                                            }

                                            // Iteratively collect descendants to avoid recursion.
                                            $visited = [$currentId => true];
                                            $queue = [$currentId];
                                            $descendants = [];

                                            while ($queue) {
                                                $children = ServiceFeature::query()
                                                    ->whereIn('parent_id', $queue)
                                                    ->pluck('id')
                                                    ->map(fn ($id) => (int) $id)
                                                    ->all();

                                                $nextQueue = [];

                                                foreach ($children as $childId) {
                                                    if (isset($visited[$childId])) {
                                                        continue;
                                                    }

                                                    $visited[$childId] = true;
                                                    $descendants[] = $childId;
                                                    $nextQueue[] = $childId;
                                                }

                                                $queue = $nextQueue;
                                            }

                                            if (in_array($requestedParentId, $descendants, true)) {
                                                $fail(__('filament.resources.service_feature_set_parent_failure_body_cycle'));
                                            }
                                        },
                                    ];
                                }),
                        ])->columns(2),
                    ]),
                Tab::make(__('filament.resources.service_feature_tabs.translations'))
                    ->schema($translationSections),
                Tab::make(__('filament.resources.service_feature_tabs.scopes'))
                    ->visible(fn (Get $get): bool => (bool) $get('is_selectable'))
                    ->schema([
                        Section::make('')->schema([
                            CheckboxList::make('scopes')
                                ->label(__('filament.resources.service_feature_fields.scopes'))
                                ->options(
                                    fn () => ServiceType::query()
                                        ->where('active', true)
                                        ->with(['translations.language.locale'])
                                        ->ordered()
                                        ->get()
                                        ->mapWithKeys(fn (ServiceType $type) => [
                                            (string) $type->id => $type->name !== '' ? $type->name : $type->code,
                                        ])
                                )
                                ->columns(2),
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
                    ->label(__('filament.resources.service_feature_columns.id'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('parent.name')
                    ->label(__('filament.resources.service_feature_columns.parent'))
                    ->getStateUsing(fn (ServiceFeature $record): string => $record->parent?->name ?: ''),
                TextColumn::make('serviceFeatureCategory.name')
                    ->label(__('filament.resources.service_feature_columns.category'))
                    ->searchable(query: function ($query, $search): void {
                        $query->whereHas('serviceFeatureCategory.translations', function ($q) use ($search): void {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                    }),
                TextColumn::make('code')
                    ->label(__('filament.resources.service_feature_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.service_feature_columns.name'))
                    ->searchable(query: function ($query, $search): void {
                        $query->whereHas('translations', function ($q) use ($search): void {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                    }),
            ])
            ->filters([
                SelectFilter::make('parent_id')
                    ->label(__('filament.resources.service_feature_columns.parent'))
                    ->options(function (): array {
                        $rootParents = ServiceFeature::query()
                            ->where(function (Builder $query): void {
                                $query->whereNull('parent_id')->orWhere('parent_id', 0);
                            })
                            ->where('active', true)
                            ->ordered()
                            ->get()
                            ->mapWithKeys(fn (ServiceFeature $feature) => [
                                (string) $feature->id => $feature->name !== '' ? $feature->name : $feature->code,
                            ])
                            ->all();

                        return ['__root__' => __('filament.resources.service_feature_parent_none')] + $rootParents;
                    })
                    ->query(function (Builder $query, array $data): void {
                        $value = $data['value'] ?? null;

                        if (blank($value)) {
                            return;
                        }

                        if ($value === '__root__') {
                            $query->whereNull('parent_id');
                            return;
                        }

                        $query->where('parent_id', (int) $value);
                    })
                    ->searchable(),
                SelectFilter::make('service_feature_category_id')
                    ->label(__('filament.resources.service_feature_columns.category'))
                    ->options(
                        fn () => ServiceFeatureCategory::query()
                            ->where('active', true)
                            ->with(['translations.language.locale'])
                            ->ordered()
                            ->get()
                            ->pluck('name', 'id')
                    )
                    ->searchable(),
            ], layout: FiltersLayout::AboveContent)
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('setParentId')
                        ->label(__('filament.resources.service_feature_set_parent'))
                        ->form([
                            Select::make('parent_id')
                                ->label(__('filament.resources.service_feature_fields.parent_id'))
                                ->nullable()
                                ->searchable()
                                ->preload()
                                ->optionsLimit(500)
                                ->options(
                                    fn () => ServiceFeature::query()
                                        ->where('active', true)
                                        ->where(function (Builder $query): void {
                                            $query->whereNull('parent_id')->orWhere('parent_id', 0);
                                        })
                                        ->ordered()
                                        ->get()
                                        ->mapWithKeys(fn (ServiceFeature $feature) => [
                                            (string) $feature->id => $feature->name !== '' ? $feature->name : $feature->code,
                                        ])
                                        ->all()
                                ),
                        ])
                        ->failureNotificationTitle(__('filament.resources.service_feature_set_parent_failure_title'))
                        ->successNotificationTitle(__('filament.resources.service_feature_set_parent_success_title'))
                        ->action(function ($records, array $data): void {
                            /** @var \Illuminate\Support\Collection<int, ServiceFeature> $records */
                            $selectedIds = $records->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
                            $requestedParentId = $data['parent_id'] ?? null;
                            $requestedParentId = blank($requestedParentId) ? null : (int) $requestedParentId;

                            // Collect all descendants of the currently selected nodes (iteratively to avoid recursion).
                            $collectDescendants = static function (array $startingIds): array {
                                $visited = [];
                                foreach ($startingIds as $id) {
                                    $visited[(int) $id] = true;
                                }

                                $queue = array_values(array_map(fn ($id) => (int) $id, $startingIds));
                                $descendants = [];

                                while ($queue) {
                                    $queue = array_values($queue);
                                    $children = ServiceFeature::query()
                                        ->whereIn('parent_id', $queue)
                                        ->pluck('id')
                                        ->map(fn ($id) => (int) $id)
                                        ->all();

                                    $nextQueue = [];

                                    foreach ($children as $childId) {
                                        if (isset($visited[$childId])) {
                                            continue;
                                        }

                                        $visited[$childId] = true;
                                        $descendants[] = $childId;
                                        $nextQueue[] = $childId;
                                    }

                                    $queue = $nextQueue;
                                }

                                return $descendants;
                            };

                            if ($requestedParentId !== null) {
                                if (in_array($requestedParentId, $selectedIds, true)) {
                                    $this->reportCompleteBulkProcessingFailure(
                                        'invalid_parent',
                                        __('filament.resources.service_feature_set_parent_failure_body_self')
                                    );

                                    return;
                                }

                                $descendants = $collectDescendants($selectedIds);

                                if (in_array($requestedParentId, $descendants, true)) {
                                    $this->reportCompleteBulkProcessingFailure(
                                        'invalid_parent',
                                        __('filament.resources.service_feature_set_parent_failure_body_cycle')
                                    );

                                    return;
                                }
                            }

                            foreach ($records as $record) {
                                /** @var ServiceFeature $record */
                                $record->update([
                                    'parent_id' => $requestedParentId,
                                ]);
                            }
                        }),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn ($query) => $query->with([
                'translations.language.locale',
                'serviceFeatureCategory.translations.language.locale',
                'parent.translations.language.locale',
            ]));
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code'];
    }

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    {
        $term = '%' . $search . '%';

        $query->orWhereHas('translations', function ($q) use ($term): void {
            $q->where('name', 'like', $term)
                ->orWhere('description', 'like', $term);
        })->orWhereHas('serviceFeatureCategory.translations', function ($q) use ($term): void {
            $q->where('name', 'like', $term);
        });
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceFeatures::route('/'),
            'create' => Pages\CreateServiceFeature::route('/create'),
            'view' => Pages\ViewServiceFeature::route('/{record}'),
            'edit' => Pages\EditServiceFeature::route('/{record}/edit'),
        ];
    }
}

