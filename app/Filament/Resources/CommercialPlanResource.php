<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CommercialCluster;
use App\Filament\Resources\CommercialPlanResource\Pages;
use App\Models\AccountType;
use App\Models\CommercialPlan;
use App\Models\Module;
use App\Models\Language;
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

class CommercialPlanResource extends LmpResource
{
    protected static ?string $model = CommercialPlan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'filament.resources.plan';

    protected static ?string $pluralModelLabel = 'filament.resources.plans';

    protected static ?string $recordTitleAttribute = 'code';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_plans';

    protected static ?string $cluster = CommercialCluster::class;

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
                        ->label(__('filament.resources.plan_fields.name'))
                        ->maxLength(255),
                    Textarea::make("translations.{$lang->id}.description")
                        ->label(__('filament.resources.plan_fields.description'))
                        ->rows(3),
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
                                    ->label(__('filament.common.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.plan_tabs.translations'))
                        ->schema($translationSections),
                    Tab::make(__('filament.resources.plan_tabs.account_types'))
                        ->schema([
                            Section::make('')->schema([
                                CheckboxList::make('accountTypes')
                                    ->label(__('filament.resources.plan_fields.account_types'))
                                    ->helperText(__('filament.resources.plan_fields.account_types_help'))
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
                    Tab::make(__('filament.resources.plan_tabs.modules'))
                        ->schema([
                            Repeater::make('commercialPlanModules')
                                ->relationship()
                                ->schema([
                                    Select::make('module_id')
                                        ->label(__('filament.resources.plan_relation.module'))
                                        ->options(
                                            fn (): array => Module::query()
                                                ->where('active', true)
                                                ->orderBy('sort_order')
                                                ->with(['translations.language.locale'])
                                                ->get()
                                                ->mapWithKeys(fn (Module $module): array => [
                                                    $module->id => $module->name ?: $module->code,
                                                ])
                                                ->all()
                                        )
                                        ->required()
                                        ->searchable()
                                        ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                ])
                                ->defaultItems(0)
                                ->addActionLabel(__('filament.resources.plan_relation.add_module'))
                                ->reorderable()
                                ->orderColumn('sort_order')
                                ->collapsible(),
                        ])
                        ->visibleOn(['edit']),
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
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.plan_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.plan_columns.name'))
                    ->searchable(query: function (Builder $query, string $search): void {
                        $query->whereHas('translations', function (Builder $q) use ($search): void {
                            $q->where('name', 'like', '%'.$search.'%');
                        });
                    }),
                TextColumn::make('account_types_labels')
                    ->label(__('filament.resources.plan_columns.account_types'))
                    ->getStateUsing(function (CommercialPlan $record): ?string {
                        $codes = $record->accountTypes
                            ->sortBy('sort_order')
                            ->pluck('code')
                            ->filter()
                            ->values();

                        return $codes->isEmpty() ? null : $codes->implode(', ');
                    })
                    ->placeholder(__('filament.resources.plan_columns.account_types_all'))
                    ->wrap(),
                TextColumn::make('commercial_plan_modules_count')
                    ->label(__('filament.resources.plan_columns.modules_count'))
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('account_type_id')
                    ->label(__('filament.resources.plan_filter.account_type'))
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
                    ->placeholder(__('filament.resources.plan_filter.account_type_placeholder'))
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
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with([
                    'translations.language.locale',
                    'accountTypes',
                ])
                ->withCount('commercialPlanModules'))
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $translations
     */
    public static function syncTranslationsFromForm(CommercialPlan $record, array $translations): void
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
            'index' => Pages\ListCommercialPlans::route('/'),
            'create' => Pages\CreateCommercialPlan::route('/create'),
            'edit' => Pages\EditCommercialPlan::route('/{record}/edit'),
        ];
    }
}
