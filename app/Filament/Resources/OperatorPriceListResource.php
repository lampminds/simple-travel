<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CuentasCluster;
use App\Filament\Resources\OperatorPriceListResource\Pages;
use App\Models\Account;
use App\Models\Currency;
use App\Models\OperatorPriceList;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
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

class OperatorPriceListResource extends LmpResource
{
    protected static ?string $model = OperatorPriceList::class;

    protected static ?string $slug = 'operator-price-lists';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'filament.resources.operator_price_list';

    protected static ?string $pluralModelLabel = 'filament.resources.operator_price_lists';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_accounts_price_lists';

    protected static ?int $navigationSort = 12;

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
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        $accountOptions = fn (): array => Account::query()->orderBy('name')->pluck('name', 'id')->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.operator_price_list_tabs.general'))
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Select::make('operator_id')
                                        ->label(__('filament.resources.operator_price_list_fields.operator_id'))
                                        ->options($accountOptions)
                                        ->searchable()
                                        ->required(),
                                    TextInput::make('name')
                                        ->label(__('filament.resources.operator_price_list_fields.name'))
                                        ->required()
                                        ->maxLength(255),
                                    Select::make('currency_id')
                                        ->label(__('filament.resources.operator_price_list_fields.currency_id'))
                                        ->options(fn () => Currency::query()->with('lmpCurrency')->orderBy('id')->get()->mapWithKeys(fn (Currency $c) => [$c->id => $c->display_name]))
                                        ->searchable()
                                        ->required(),
                                    Toggle::make('is_active')
                                        ->label(__('filament.resources.operator_price_list_fields.is_active'))
                                        ->default(true),
                                ])
                                ->columns(2),
                        ]),
                    Tab::make(__('filament.resources.operator_price_list_tabs.assignments'))
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Repeater::make('assignments')
                                        ->relationship()
                                        ->label(__('filament.resources.operator_price_list_fields.assignments'))
                                        ->schema([
                                            Select::make('agency_id')
                                                ->label(__('filament.resources.operator_price_list_assignment_fields.agency_id'))
                                                ->options($accountOptions)
                                                ->searchable()
                                                ->required(),
                                            Select::make('adjustment_type')
                                                ->label(__('filament.resources.operator_price_list_assignment_fields.adjustment_type'))
                                                ->options([
                                                    'none' => __('filament.resources.operator_price_list_assignment_adjustment_type.none'),
                                                    'percentage' => __('filament.resources.operator_price_list_assignment_adjustment_type.percentage'),
                                                    'fixed' => __('filament.resources.operator_price_list_assignment_adjustment_type.fixed'),
                                                ])
                                                ->default('none')
                                                ->required()
                                                ->live(),
                                            TextInput::make('adjustment_value')
                                                ->label(__('filament.resources.operator_price_list_assignment_fields.adjustment_value'))
                                                ->numeric()
                                                ->step(0.01)
                                                ->nullable()
                                                ->visible(fn (Get $get): bool => in_array((string) $get('adjustment_type'), ['percentage', 'fixed'], true)),
                                            DatePicker::make('valid_from')
                                                ->label(__('filament.resources.operator_price_list_assignment_fields.valid_from'))
                                                ->native(false)
                                                ->nullable(),
                                            DatePicker::make('valid_to')
                                                ->label(__('filament.resources.operator_price_list_assignment_fields.valid_to'))
                                                ->native(false)
                                                ->nullable(),
                                            Toggle::make('is_active')
                                                ->label(__('filament.resources.operator_price_list_assignment_fields.is_active'))
                                                ->default(true),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->addActionLabel(__('filament.resources.operator_price_list_assignment_fields.add')),
                                ]),
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
                    ->label(__('filament.resources.operator_price_list_columns.id'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.operator_price_list_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('operator.name')
                    ->label(__('filament.resources.operator_price_list_columns.operator'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('currency.display_name')
                    ->label(__('filament.resources.operator_price_list_columns.currency'))
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('filament.resources.operator_price_list_columns.is_active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label(__('filament.resources.operator_price_list_columns.items_count'))
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with(['operator', 'currency.lmpCurrency'])->withCount('items'))
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
            'index' => Pages\ListOperatorPriceLists::route('/'),
            'create' => Pages\CreateOperatorPriceList::route('/create'),
            'edit' => Pages\EditOperatorPriceList::route('/{record}/edit'),
        ];
    }
}
