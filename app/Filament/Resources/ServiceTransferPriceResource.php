<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CuentasCluster;
use App\Filament\Resources\ServiceTransferPriceResource\Pages;
use App\Models\Currency;
use App\Models\ServiceTransfer;
use App\Models\ServiceTransferPrice;
use App\Models\ServiceTransferRoute;
use App\Models\ServiceTransferVehicleType;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceTransferPriceResource extends LmpResource
{
    protected static ?string $model = ServiceTransferPrice::class;

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $modelLabel = 'filament.resources.service_transfer_price';

    protected static ?string $pluralModelLabel = 'filament.resources.service_transfer_prices';

    protected static ?string $recordTitleAttribute = 'id';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_accounts_transfer';

    /** After transfer routes in the same Cuentas → Transporte group. */
    protected static ?int $navigationSort = 6;

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

    protected static function routeOptionsForTransfer(?int $transferId): array
    {
        if (! $transferId) {
            return [];
        }

        return ServiceTransferRoute::query()
            ->where('service_transfer_id', $transferId)
            ->with([
                'origin.translations.language.locale',
                'origin.city',
                'destination.translations.language.locale',
                'destination.city',
            ])
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function (ServiceTransferRoute $route) {
                $o = $route->origin?->wizard_label ?? '#'.$route->origin_location_id;
                $d = $route->destination?->wizard_label ?? '#'.$route->destination_location_id;

                return [$route->id => $o.' → '.$d];
            })
            ->all();
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.service_transfer_price_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                Select::make('service_transfer_id')
                                    ->label(__('filament.resources.service_transfer_price_fields.service_transfer_id'))
                                    ->options(
                                        fn () => ServiceTransfer::query()
                                            ->with(['service.translations.language.locale'])
                                            ->orderBy('id')
                                            ->get()
                                            ->mapWithKeys(fn (ServiceTransfer $t) => [
                                                $t->id => ($t->service?->name !== '' && $t->service?->name !== null)
                                                    ? $t->service->name
                                                    : __('filament.resources.service_transfer').' #'.$t->id,
                                            ])
                                    )
                                    ->searchable()
                                    ->required()
                                    ->live(onBlur: true),
                                Select::make('route_id')
                                    ->label(__('filament.resources.service_transfer_price_fields.route_id'))
                                    ->options(fn (Get $get) => static::routeOptionsForTransfer(
                                        $get('service_transfer_id') !== null && $get('service_transfer_id') !== ''
                                            ? (int) $get('service_transfer_id')
                                            : null
                                    ))
                                    ->searchable()
                                    ->nullable()
                                    ->rules([
                                        fn (Get $get): \Closure => function (string $attribute, mixed $value, \Closure $fail) use ($get): void {
                                            if ($value === null || $value === '') {
                                                return;
                                            }
                                            $tid = $get('service_transfer_id');
                                            if (! $tid) {
                                                return;
                                            }
                                            if (! ServiceTransferRoute::query()
                                                ->whereKey($value)
                                                ->where('service_transfer_id', $tid)
                                                ->exists()) {
                                                $fail(__('filament.resources.service_transfer_price_validation.route_belongs_to_transfer'));
                                            }
                                        },
                                    ]),
                                Select::make('service_transfer_vehicle_type_id')
                                    ->label(__('filament.resources.service_transfer_price_fields.service_transfer_vehicle_type_id'))
                                    ->options(
                                        fn () => ServiceTransferVehicleType::query()
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                    )
                                    ->searchable()
                                    ->nullable(),
                                Select::make('pricing_type')
                                    ->label(__('filament.resources.service_transfer_price_fields.pricing_type'))
                                    ->options([
                                        ServiceTransferPrice::PRICING_PER_VEHICLE => __('filament.resources.service_transfer_price_pricing_type.per_vehicle'),
                                        ServiceTransferPrice::PRICING_PER_PERSON => __('filament.resources.service_transfer_price_pricing_type.per_person'),
                                    ])
                                    ->required(),
                                Select::make('currency_id')
                                    ->label(__('filament.resources.service_transfer_price_fields.currency_id'))
                                    ->options(
                                        fn () => Currency::query()->with('lmpCurrency')->orderBy('id')->get()->mapWithKeys(
                                            fn (Currency $c) => [$c->id => $c->display_name]
                                        )
                                    )
                                    ->searchable()
                                    ->required(),
                                TextInput::make('base_price')
                                    ->label(__('filament.resources.service_transfer_price_fields.base_price'))
                                    ->numeric()
                                    ->nullable(),
                                TextInput::make('price_per_person')
                                    ->label(__('filament.resources.service_transfer_price_fields.price_per_person'))
                                    ->numeric()
                                    ->nullable(),
                                TextInput::make('price_per_extra_passenger')
                                    ->label(__('filament.resources.service_transfer_price_fields.price_per_extra_passenger'))
                                    ->numeric()
                                    ->nullable(),
                                TextInput::make('min_passengers')
                                    ->label(__('filament.resources.service_transfer_price_fields.min_passengers'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->nullable(),
                                TextInput::make('max_passengers')
                                    ->label(__('filament.resources.service_transfer_price_fields.max_passengers'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->nullable(),
                                DatePicker::make('valid_from')
                                    ->label(__('filament.resources.service_transfer_price_fields.valid_from'))
                                    ->native(false)
                                    ->nullable(),
                                DatePicker::make('valid_to')
                                    ->label(__('filament.resources.service_transfer_price_fields.valid_to'))
                                    ->native(false)
                                    ->nullable(),
                            ])->columns(2),
                        ]),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.service_transfer_price_columns.id'))
                    ->sortable(),
                TextColumn::make('serviceTransfer.service.name')
                    ->label(__('filament.resources.service_transfer_price_columns.transfer'))
                    ->limit(36),
                TextColumn::make('route_summary')
                    ->label(__('filament.resources.service_transfer_price_columns.route'))
                    ->getStateUsing(function (ServiceTransferPrice $record): string {
                        if (! $record->route_id) {
                            return '—';
                        }
                        $record->loadMissing([
                            'route.origin.translations.language.locale',
                            'route.origin.city',
                            'route.destination.translations.language.locale',
                            'route.destination.city',
                        ]);
                        $o = $record->route?->origin?->wizard_label;
                        $d = $record->route?->destination?->wizard_label;
                        if ($o || $d) {
                            return ($o ?? '?').' → '.($d ?? '?');
                        }

                        return '#'.$record->route_id;
                    }),
                TextColumn::make('vehicleType.name')
                    ->label(__('filament.resources.service_transfer_price_columns.vehicle_type')),
                TextColumn::make('pricing_type')
                    ->label(__('filament.resources.service_transfer_price_columns.pricing_type'))
                    ->formatStateUsing(fn (string $state) => __('filament.resources.service_transfer_price_pricing_type.'.$state)),
                TextColumn::make('currency.display_name')
                    ->label(__('filament.resources.service_transfer_price_columns.currency')),
                TextColumn::make('base_price')
                    ->label(__('filament.resources.service_transfer_price_columns.base_price')),
            ])
            ->defaultSort('id')
            ->modifyQueryUsing(fn ($query) => $query->with([
                'serviceTransfer.service.translations.language.locale',
                'route',
                'vehicleType',
                'currency.lmpCurrency',
            ]))
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceTransferPrices::route('/'),
            'create' => Pages\CreateServiceTransferPrice::route('/create'),
            'view' => Pages\ViewServiceTransferPrice::route('/{record}'),
            'edit' => Pages\EditServiceTransferPrice::route('/{record}/edit'),
        ];
    }
}
