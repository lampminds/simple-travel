<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\TransportCluster;
use App\Filament\Resources\ServiceTransferVehicleResource\Pages;
use App\Models\ServiceTransfer;
use App\Models\ServiceTransferVehicle;
use App\Models\ServiceTransferVehicleType;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceTransferVehicleResource extends LmpResource
{
    protected static ?string $model = ServiceTransferVehicle::class;

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $modelLabel = 'filament.resources.service_transfer_vehicle';

    protected static ?string $pluralModelLabel = 'filament.resources.service_transfer_vehicles';

    protected static ?string $recordTitleAttribute = 'id';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_transport';

    protected static ?int $navigationSort = 6;

    protected static ?string $cluster = TransportCluster::class;

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

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.service_transfer_vehicle_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                Select::make('service_transfer_id')
                                    ->label(__('filament.resources.service_transfer_vehicle_fields.service_transfer_id'))
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
                                Select::make('service_transfer_vehicle_type_id')
                                    ->label(__('filament.resources.service_transfer_vehicle_fields.service_transfer_vehicle_type_id'))
                                    ->options(
                                        fn () => ServiceTransferVehicleType::query()
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                    )
                                    ->searchable()
                                    ->required()
                                    ->unique(
                                        table: 'service_transfer_vehicles',
                                        column: 'service_transfer_vehicle_type_id',
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn ($rule, callable $get) => $rule
                                            ->where('service_transfer_id', $get('service_transfer_id'))
                                    ),
                                Toggle::make('is_default')
                                    ->label(__('filament.resources.service_transfer_vehicle_fields.is_default'))
                                    ->default(false),
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
                    ->label(__('filament.resources.service_transfer_vehicle_columns.id'))
                    ->sortable(),
                TextColumn::make('serviceTransfer.service.name')
                    ->label(__('filament.resources.service_transfer_vehicle_columns.transfer'))
                    ->limit(40),
                TextColumn::make('vehicleType.name')
                    ->label(__('filament.resources.service_transfer_vehicle_columns.vehicle_type')),
                IconColumn::make('is_default')
                    ->label(__('filament.resources.service_transfer_vehicle_fields.is_default'))
                    ->boolean(),
            ])
            ->defaultSort('id')
            ->modifyQueryUsing(fn ($query) => $query->with([
                'serviceTransfer.service.translations.language.locale',
                'vehicleType',
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
            'index' => Pages\ListServiceTransferVehicles::route('/'),
            'create' => Pages\CreateServiceTransferVehicle::route('/create'),
            'view' => Pages\ViewServiceTransferVehicle::route('/{record}'),
            'edit' => Pages\EditServiceTransferVehicle::route('/{record}/edit'),
        ];
    }
}
