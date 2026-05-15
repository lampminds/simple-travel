<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CuentasCluster;
use App\Filament\Resources\ServiceTransferRouteResource\Pages;
use App\Models\ServiceTransfer;
use App\Models\ServiceTransferLocation;
use App\Models\ServiceTransferRoute;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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

class ServiceTransferRouteResource extends LmpResource
{
    protected static ?string $model = ServiceTransferRoute::class;

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $modelLabel = 'filament.resources.service_transfer_route';

    protected static ?string $pluralModelLabel = 'filament.resources.service_transfer_routes';

    protected static ?string $recordTitleAttribute = 'id';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_accounts_transfer';

    protected static ?int $navigationSort = 5;

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

    /**
     * @return array<int, string>
     */
    protected static function locationSelectOptions(): array
    {
        return ServiceTransferLocation::query()
            ->with(['translations.language.locale', 'city'])
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (ServiceTransferLocation $loc) => [$loc->id => $loc->wizard_label])
            ->all();
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.service_transfer_route_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                Select::make('service_transfer_id')
                                    ->label(__('filament.resources.service_transfer_route_fields.service_transfer_id'))
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
                                    ->required(),
                                Select::make('origin_location_id')
                                    ->label(__('filament.resources.service_transfer_route_fields.origin_location_id'))
                                    ->options(fn () => static::locationSelectOptions())
                                    ->searchable()
                                    ->required(),
                                Select::make('destination_location_id')
                                    ->label(__('filament.resources.service_transfer_route_fields.destination_location_id'))
                                    ->options(fn () => static::locationSelectOptions())
                                    ->searchable()
                                    ->required()
                                    ->unique(
                                        table: 'service_transfer_routes',
                                        column: 'destination_location_id',
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn ($rule, callable $get) => $rule
                                            ->where('service_transfer_id', $get('service_transfer_id'))
                                            ->where('origin_location_id', $get('origin_location_id'))
                                    )
                                    ->rules([
                                        fn (Get $get): \Closure => function (string $attribute, mixed $value, \Closure $fail) use ($get): void {
                                            if ((string) $value === (string) $get('origin_location_id')) {
                                                $fail(__('filament.resources.service_transfer_route_validation.different_endpoints'));
                                            }
                                        },
                                    ]),
                                Toggle::make('is_active')
                                    ->label(__('filament.resources.service_transfer_route_fields.is_active'))
                                    ->default(true),
                                TextInput::make('distance_km')
                                    ->label(__('filament.resources.service_transfer_route_fields.distance_km'))
                                    ->numeric()
                                    ->nullable(),
                                TextInput::make('duration_minutes')
                                    ->label(__('filament.resources.service_transfer_route_fields.duration_minutes'))
                                    ->numeric()
                                    ->minValue(0)
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
                    ->label(__('filament.resources.service_transfer_route_columns.id'))
                    ->sortable(),
                TextColumn::make('serviceTransfer.service.name')
                    ->label(__('filament.resources.service_transfer_route_columns.transfer'))
                    ->limit(40),
                TextColumn::make('origin.wizard_label')
                    ->label(__('filament.resources.service_transfer_route_columns.origin'))
                    ->toggleable(),
                TextColumn::make('destination.wizard_label')
                    ->label(__('filament.resources.service_transfer_route_columns.destination'))
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label(__('filament.resources.service_transfer_route_fields.is_active'))
                    ->boolean(),
            ])
            ->defaultSort('id')
            ->modifyQueryUsing(fn ($query) => $query->with([
                'serviceTransfer.service.translations.language.locale',
                'origin.translations.language.locale',
                'origin.city',
                'destination.translations.language.locale',
                'destination.city',
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
            'index' => Pages\ListServiceTransferRoutes::route('/'),
            'create' => Pages\CreateServiceTransferRoute::route('/create'),
            'view' => Pages\ViewServiceTransferRoute::route('/{record}'),
            'edit' => Pages\EditServiceTransferRoute::route('/{record}/edit'),
        ];
    }
}
