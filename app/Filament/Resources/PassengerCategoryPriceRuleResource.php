<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CuentasCluster;
use App\Filament\Resources\PassengerCategoryPriceRuleResource\Pages;
use App\Models\Currency;
use App\Models\PassengerCategory;
use App\Models\PassengerCategoryPriceRule;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class PassengerCategoryPriceRuleResource extends LmpResource
{
    protected static ?string $model = PassengerCategoryPriceRule::class;

    protected static ?string $slug = 'passenger-category-price-rules';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $modelLabel = 'filament.resources.passenger_category_price_rule';

    protected static ?string $pluralModelLabel = 'filament.resources.passenger_category_price_rules';

    protected static ?string $recordTitleAttribute = 'adjustment_type';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_accounts_price_lists';

    protected static ?int $navigationSort = 21;

    protected static ?string $cluster = CuentasCluster::class;

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
        $main = array_map(fn ($component) => $component->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($component) => $component->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.passenger_category_price_rule_tabs.general'))
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Select::make('passenger_category_id')
                                        ->label(__('filament.resources.passenger_category_price_rule_fields.passenger_category_id'))
                                        ->options(fn (?PassengerCategoryPriceRule $record): array => static::categoryOptions($record))
                                        ->searchable()
                                        ->required()
                                        ->unique(ignoreRecord: true),
                                    Select::make('adjustment_type')
                                        ->label(__('filament.resources.passenger_category_price_rule_fields.adjustment_type'))
                                        ->options(PassengerCategoryPriceRule::adjustmentTypeOptions())
                                        ->default(PassengerCategoryPriceRule::TYPE_REGULAR)
                                        ->required()
                                        ->live(),
                                    TextInput::make('adjustment_value')
                                        ->label(__('filament.resources.passenger_category_price_rule_fields.adjustment_value'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->step(0.01)
                                        ->default(0)
                                        ->required(fn (Get $get): bool => $get('adjustment_type') !== PassengerCategoryPriceRule::TYPE_REGULAR)
                                        ->visible(fn (Get $get): bool => $get('adjustment_type') !== PassengerCategoryPriceRule::TYPE_REGULAR),
                                    Select::make('currency_id')
                                        ->label(__('filament.resources.passenger_category_price_rule_fields.currency_id'))
                                        ->options(fn (): array => static::currencyOptions())
                                        ->searchable()
                                        ->required(fn (Get $get): bool => static::typeUsesFixedAmount((string) $get('adjustment_type')))
                                        ->visible(fn (Get $get): bool => static::typeUsesFixedAmount((string) $get('adjustment_type'))),
                                    Toggle::make('is_active')
                                        ->label(__('filament.resources.passenger_category_price_rule_fields.is_active'))
                                        ->default(true),
                                ])
                                ->columns(2),
                        ]),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('category.account_id')
                    ->label(__('filament.resources.passenger_category_price_rule_columns.account'))
                    ->state(fn (PassengerCategoryPriceRule $record): string => PassengerCategoryResource::accountLabel($record->category?->account)),
                TextColumn::make('category.name')
                    ->label(__('filament.resources.passenger_category_price_rule_columns.category'))
                    ->searchable(),
                TextColumn::make('adjustment_type')
                    ->label(__('filament.resources.passenger_category_price_rule_columns.adjustment_type'))
                    ->formatStateUsing(fn (string $state): string => (string) __('filament.resources.passenger_category_price_rule_adjustment_type.'.$state))
                    ->sortable(),
                TextColumn::make('adjustment_value')
                    ->label(__('filament.resources.passenger_category_price_rule_columns.adjustment_value'))
                    ->sortable(),
                TextColumn::make('currency_id')
                    ->label(__('filament.resources.passenger_category_price_rule_columns.currency'))
                    ->state(fn (PassengerCategoryPriceRule $record): string => $record->currency?->currency_code ?: '—'),
                IconColumn::make('is_active')
                    ->label(__('filament.resources.passenger_category_price_rule_columns.is_active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['category.account', 'currency.lmpCurrency']))
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
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
            'index' => Pages\ListPassengerCategoryPriceRules::route('/'),
            'create' => Pages\CreatePassengerCategoryPriceRule::route('/create'),
            'edit' => Pages\EditPassengerCategoryPriceRule::route('/{record}/edit'),
        ];
    }

    /**
     * Categories that do not already have a rule, plus the one being edited.
     *
     * @return array<int, string>
     */
    public static function categoryOptions(?PassengerCategoryPriceRule $record): array
    {
        return PassengerCategory::query()
            ->with('account')
            ->where(function ($query) use ($record): void {
                $query->whereDoesntHave('priceRule');

                if ($record !== null) {
                    $query->orWhere('id', $record->passenger_category_id);
                }
            })
            ->orderBy('account_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function (PassengerCategory $category): array {
                $account = PassengerCategoryResource::accountLabel($category->account);
                $range = $category->ageRangeLabel();
                $suffix = $range === '—' ? '' : ' ('.$range.')';

                return [$category->id => $account.' — '.$category->name.$suffix];
            })
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public static function currencyOptions(): array
    {
        return Currency::query()
            ->with('lmpCurrency')
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (Currency $currency): array => [$currency->id => $currency->display_name])
            ->all();
    }

    public static function typeUsesFixedAmount(string $type): bool
    {
        return in_array($type, PassengerCategoryPriceRule::FIXED_AMOUNT_TYPES, true);
    }
}
