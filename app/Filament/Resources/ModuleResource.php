<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CommercialCluster;
use App\Filament\Resources\ModuleResource\Pages;
use App\Models\AccountType;
use App\Models\Language;
use App\Models\Module;
use App\Models\ModuleFeature;
use Illuminate\Support\Arr;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
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

class ModuleResource extends LmpResource
{
    protected static ?string $model = Module::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $modelLabel = 'filament.resources.module';

    protected static ?string $pluralModelLabel = 'filament.resources.modules';

    protected static ?string $recordTitleAttribute = 'code';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_plans';

    protected static ?string $cluster = CommercialCluster::class;

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
                        ->label(__('filament.resources.module_fields.name'))
                        ->maxLength(255),
                    Textarea::make("translations.{$lang->id}.description")
                        ->label(__('filament.resources.module_fields.description'))
                        ->rows(3),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.module_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('code')
                                    ->label(__('filament.resources.module_fields.code'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Toggle::make('active')
                                    ->label(__('filament.common.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.module_tabs.translations'))
                        ->schema($translationSections),
                    Tab::make(__('filament.resources.module_tabs.account_types'))
                        ->schema([
                            Section::make('')->schema([
                                CheckboxList::make('accountTypes')
                                    ->label(__('filament.resources.module_fields.account_types'))
                                    ->helperText(__('filament.resources.module_fields.account_types_help'))
                                    ->relationship(
                                        'accountTypes',
                                        'code',
                                        modifyQueryUsing: fn (Builder $query) => $query
                                            ->where('active', true)
                                            ->ordered()
                                            ->with(['translations.language.locale']),
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn (AccountType $record): string => $record->name ?: (string) $record->code
                                    )
                                    ->columns(2)
                                    ->gridDirection('row')
                                    ->columnSpanFull()
                                    ->bulkToggleable(),
                            ]),
                        ]),
                    Tab::make(__('filament.resources.module_tabs.features'))
                        ->schema([
                            static::makeFeaturesRepeater($languages),
                        ])
                        ->visibleOn(['edit']),
                    Tab::make(__('filament.resources.module_tabs.pricing'))
                        ->schema([
                            Repeater::make('commercialModulePrices')
                                ->relationship()
                                ->schema(static::getPriceRepeaterSchema())
                                ->defaultItems(0)
                                ->addActionLabel(__('filament.resources.module_price_fields.add'))
                                ->collapsible(),
                        ])
                        ->visibleOn(['edit']),
                ]),
        ];
    }

    protected static function makeFeaturesRepeater(\Illuminate\Support\Collection $languages): Repeater
    {
        return Repeater::make('features')
            ->relationship()
            ->schema(static::getFeatureRepeaterSchema($languages))
            ->defaultItems(0)
            ->addActionLabel(__('filament.resources.module_feature_fields.add'))
            ->reorderable()
            ->orderColumn('sort_order')
            ->collapsible()
            ->mutateRelationshipDataBeforeFillUsing(function (array $data, ModuleFeature $record): array {
                if ($record->exists) {
                    $record->loadMissing('translations');
                }
                $data['featureTranslations'] = static::featureTranslationsFormState(
                    $record->exists ? $record : null,
                );

                return $data;
            })
            ->mutateRelationshipDataBeforeCreateUsing(
                fn (array $data): array => Arr::except($data, ['featureTranslations']),
            )
            ->mutateRelationshipDataBeforeSaveUsing(
                fn (array $data): array => Arr::except($data, ['featureTranslations']),
            )
            ->afterCreate(function (array $data, ModuleFeature $record): void {
                static::syncFeatureTranslationsFromForm($record, $data['featureTranslations'] ?? []);
            })
            ->afterUpdate(function (array $data, ModuleFeature $record): void {
                static::syncFeatureTranslationsFromForm($record, $data['featureTranslations'] ?? []);
            });
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected static function getFeatureRepeaterSchema(\Illuminate\Support\Collection $languages): array
    {
        $translationSections = $languages->map(function (Language $lang) {
            return Section::make($lang->display_name)
                ->schema([
                    TextInput::make("featureTranslations.{$lang->id}.text")
                        ->label(__('filament.resources.module_feature_fields.text'))
                        ->maxLength(255),
                ])
                ->collapsible();
        })->all();

        return [
            Toggle::make('active')
                ->label(__('filament.common.active'))
                ->default(true)
                ->columnSpanFull(),
            ...$translationSections,
        ];
    }

    /**
     * @return array<int|string, array{text: string}>
     */
    public static function featureTranslationsFormState(?ModuleFeature $feature): array
    {
        $state = [];
        foreach (Language::query()->orderBy('id')->get() as $lang) {
            $trans = $feature?->translations->firstWhere('language_id', $lang->id);
            $state[$lang->id] = ['text' => $trans?->text ?? ''];
        }

        return $state;
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $featureTranslations
     */
    public static function syncFeatureTranslationsFromForm(ModuleFeature $feature, array $featureTranslations): void
    {
        $feature->translations()->delete();
        foreach ($featureTranslations as $languageId => $row) {
            $text = trim((string) ($row['text'] ?? ''));
            if ($text !== '') {
                $feature->translations()->create([
                    'language_id' => $languageId,
                    'text' => $text,
                ]);
            }
        }
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $featuresState
     */
    public static function syncAllFeatureTranslationsFromFormState(Module $module, array $featuresState): void
    {
        foreach ($featuresState as $item) {
            if (! is_array($item)) {
                continue;
            }
            $featureId = $item['id'] ?? null;
            if (! $featureId) {
                continue;
            }
            $feature = $module->features()->find($featureId);
            if ($feature) {
                static::syncFeatureTranslationsFromForm($feature, $item['featureTranslations'] ?? []);
            }
        }
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected static function getPriceRepeaterSchema(): array
    {
        return [
            Section::make('')->schema([
                Select::make('billing_type')
                    ->label(__('filament.resources.module_price_fields.billing_type'))
                    ->options([
                        'fixed' => __('filament.resources.module_price_fields.billing_fixed'),
                        'per_user' => __('filament.resources.module_price_fields.billing_per_user'),
                        'hybrid' => __('filament.resources.module_price_fields.billing_hybrid'),
                        'usage' => __('filament.resources.module_price_fields.billing_usage'),
                    ])
                    ->required()
                    ->native(false),
                Toggle::make('active')
                    ->label(__('filament.common.active'))
                    ->default(true),
                TextInput::make('base_price')
                    ->label(__('filament.resources.module_price_fields.base_price'))
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01),
                TextInput::make('included_users')
                    ->label(__('filament.resources.module_price_fields.included_users'))
                    ->numeric()
                    ->minValue(0)
                    ->integer(),
                TextInput::make('price_per_user')
                    ->label(__('filament.resources.module_price_fields.price_per_user'))
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01),
            ])->columns(2),
            Section::make(__('filament.resources.module_price_fields.tiers_section'))
                ->schema([
                    Repeater::make('tiers')
                        ->relationship()
                        ->schema([
                            TextInput::make('from_users')
                                ->label(__('filament.resources.module_price_tier_fields.from_users'))
                                ->numeric()
                                ->minValue(0)
                                ->integer(),
                            TextInput::make('to_users')
                                ->label(__('filament.resources.module_price_tier_fields.to_users'))
                                ->numeric()
                                ->minValue(0)
                                ->integer(),
                            TextInput::make('price_per_user')
                                ->label(__('filament.resources.module_price_tier_fields.price_per_user'))
                                ->numeric()
                                ->minValue(0)
                                ->step(0.01)
                                ->required(),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->addActionLabel(__('filament.resources.module_price_fields.add_tier')),
                ])
                ->collapsible(),
        ];
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $translations
     */
    public static function syncTranslationsFromForm(Module $record, array $translations): void
    {
        $record->translations()->delete();
        foreach ($translations as $languageId => $row) {
            $name = trim((string) ($row['name'] ?? ''));
            $description = $row['description'] ?? null;
            if ($name !== '' || $description !== null) {
                $record->translations()->create([
                    'language_id' => $languageId,
                    'name' => $name,
                    'description' => $description,
                ]);
            }
        }
    }

    /**
     * Related records are edited in form tabs (repeaters), not relation managers.
     */
    public static function getRelations(): array
    {
        return [];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.module_columns.id'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.module_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.module_columns.name'))
                    ->searchable(query: function (Builder $query, string $search): void {
                        $query->whereHas('translations', function (Builder $q) use ($search): void {
                            $q->where('name', 'like', '%'.$search.'%');
                        });
                    }),
                TextColumn::make('account_types_labels')
                    ->label(__('filament.resources.module_columns.account_types'))
                    ->getStateUsing(function (Module $record): ?string {
                        $codes = $record->accountTypes
                            ->pluck('code')
                            ->filter()
                            ->values();

                        return $codes->isEmpty() ? null : $codes->implode(', ');
                    })
                    ->placeholder(__('filament.resources.module_columns.account_types_all'))
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('account_type_id')
                    ->label(__('filament.resources.module_filter.account_type'))
                    ->options(fn (): array => AccountType::query()
                        ->where('active', true)
                        ->ordered()
                        ->with(['translations.language.locale'])
                        ->get()
                        ->mapWithKeys(fn (AccountType $type): array => [
                            (string) $type->getKey() => $type->name !== '' ? $type->name : (string) $type->code,
                        ])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->placeholder(__('filament.resources.module_filter.account_type_placeholder'))
                    ->query(function (Builder $query, array $data): void {
                        $id = $data['value'] ?? null;
                        if ($id === null || $id === '') {
                            return;
                        }
                        $accountTypeId = (int) $id;
                        $query->where(function (Builder $q) use ($accountTypeId): void {
                            $q->whereDoesntHave('accountTypes')
                                ->orWhereHas(
                                    'accountTypes',
                                    fn (Builder $relation): Builder => $relation->whereKey($accountTypeId),
                                );
                        });
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'translations.language.locale',
                'accountTypes',
            ]))
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code'];
    }

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    {
        $term = '%'.$search.'%';
        $query->orWhereHas('translations', function (Builder $q) use ($term): void {
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
            'index' => Pages\ListModules::route('/'),
            'create' => Pages\CreateModule::route('/create'),
            'edit' => Pages\EditModule::route('/{record}/edit'),
        ];
    }
}
