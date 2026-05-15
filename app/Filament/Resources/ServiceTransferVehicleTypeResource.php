<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\TransportCluster;
use App\Filament\Resources\ServiceTransferVehicleTypeResource\Pages;
use App\Models\Account;
use App\Models\ServiceTransferVehicleType;
use App\Models\ServiceTransferVehicleTypeCategory;
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
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceTransferVehicleTypeResource extends LmpResource
{
    protected static ?string $model = ServiceTransferVehicleType::class;

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $modelLabel = 'filament.resources.service_transfer_vehicle_type';

    protected static ?string $pluralModelLabel = 'filament.resources.service_transfer_vehicle_types';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_transport';

    protected static ?int $navigationSort = 4;

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
                    Tab::make(__('filament.resources.service_transfer_vehicle_type_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                Select::make('account_id')
                                    ->label(__('filament.resources.service_transfer_vehicle_type_fields.account_id'))
                                    ->options(fn () => Account::query()->orderBy('id')->pluck('nick', 'id')->all())
                                    ->default(1)
                                    ->required()
                                    ->searchable(),
                                Select::make('service_transfer_vehicle_type_category_id')
                                    ->label(__('filament.resources.service_transfer_vehicle_type_fields.category'))
                                    ->options(
                                        fn () => ServiceTransferVehicleTypeCategory::query()
                                            ->with(['translations.language.locale'])
                                            ->ordered()
                                            ->where('active', true)
                                            ->get()
                                            ->mapWithKeys(fn (ServiceTransferVehicleTypeCategory $cat) => [$cat->id => $cat->name ?: $cat->code])
                                    )
                                    ->searchable()
                                    ->required(),
                                TextInput::make('code')
                                    ->label(__('filament.resources.service_transfer_vehicle_type_fields.code'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('name')
                                    ->label(__('filament.resources.service_transfer_vehicle_type_fields.name'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('max_passengers')
                                    ->label(__('filament.resources.service_transfer_vehicle_type_fields.max_passengers'))
                                    ->numeric()
                                    ->minValue(1)
                                    ->required(),
                                TextInput::make('max_luggage')
                                    ->label(__('filament.resources.service_transfer_vehicle_type_fields.max_luggage'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->nullable(),
                                Toggle::make('active')
                                    ->label(__('filament.common.active'))
                                    ->default(true),
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
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.id'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('account.nick')
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.account'))
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.category'))
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('service_transfer_vehicle_type_category_id', $direction)),
                TextColumn::make('code')
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('max_passengers')
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.max_passengers'))
                    ->sortable(),
                TextColumn::make('max_luggage')
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.max_luggage'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('service_transfer_vehicle_type_category_id')
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.category'))
                    ->options(
                        fn () => ServiceTransferVehicleTypeCategory::query()
                            ->with(['translations.language.locale'])
                            ->ordered()
                            ->where('active', true)
                            ->get()
                            ->mapWithKeys(fn (ServiceTransferVehicleTypeCategory $cat) => [$cat->id => $cat->name ?: $cat->code])
                    )
                    ->searchable(),
                SelectFilter::make('account_id')
                    ->label(__('filament.resources.service_transfer_vehicle_type_columns.account'))
                    ->options(fn () => Account::query()->orderBy('id')->pluck('nick', 'id')->all())
                    ->searchable(),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn ($query) => $query->with(['category', 'account']))
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
        return ['code', 'name'];
    }

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    {
        $term = '%'.$search.'%';
        $query->where(function ($q) use ($term): void {
            $q->where('code', 'like', $term)
                ->orWhere('name', 'like', $term);
        });
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceTransferVehicleTypes::route('/'),
            'create' => Pages\CreateServiceTransferVehicleType::route('/create'),
            'view' => Pages\ViewServiceTransferVehicleType::route('/{record}'),
            'edit' => Pages\EditServiceTransferVehicleType::route('/{record}/edit'),
        ];
    }
}
