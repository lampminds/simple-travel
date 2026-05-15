<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CuentasCluster;
use App\Filament\Resources\ServiceTransferResource\Pages;
use App\Models\Service;
use App\Models\ServiceTransfer;
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
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceTransferResource extends LmpResource
{
    protected static ?string $model = ServiceTransfer::class;

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $modelLabel = 'filament.resources.service_transfer';

    protected static ?string $pluralModelLabel = 'filament.resources.service_transfers';

    protected static ?string $recordTitleAttribute = 'id';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_accounts_transfer';

    protected static ?int $navigationSort = 4;

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

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.service_transfer_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                Select::make('service_id')
                                    ->label(__('filament.resources.service_transfer_fields.service_id'))
                                    ->options(
                                        fn () => Service::query()
                                            ->with(['translations.language.locale'])
                                            ->orderBy('id')
                                            ->get()
                                            ->mapWithKeys(fn (Service $s) => [$s->id => ($s->name !== '' ? $s->name : '#'.$s->id)])
                                    )
                                    ->searchable()
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Select::make('transfer_type')
                                    ->label(__('filament.resources.service_transfer_fields.transfer_type'))
                                    ->options([
                                        ServiceTransfer::TRANSFER_ONE_WAY => __('filament.resources.service_transfer_transfer_type.one_way'),
                                        ServiceTransfer::TRANSFER_ROUND_TRIP => __('filament.resources.service_transfer_transfer_type.round_trip'),
                                    ])
                                    ->required(),
                                Select::make('modality')
                                    ->label(__('filament.resources.service_transfer_fields.modality'))
                                    ->options([
                                        ServiceTransfer::MODALITY_PRIVATE => __('filament.resources.service_transfer_modality.private'),
                                        ServiceTransfer::MODALITY_SHARED => __('filament.resources.service_transfer_modality.shared'),
                                    ])
                                    ->required(),
                                Toggle::make('allows_multiple_stops')
                                    ->label(__('filament.resources.service_transfer_fields.allows_multiple_stops'))
                                    ->default(false),
                                TextInput::make('max_passengers')
                                    ->label(__('filament.resources.service_transfer_fields.max_passengers'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->nullable(),
                                TextInput::make('max_luggage')
                                    ->label(__('filament.resources.service_transfer_fields.max_luggage'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->nullable(),
                                TextInput::make('default_duration_minutes')
                                    ->label(__('filament.resources.service_transfer_fields.default_duration_minutes'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->nullable(),
                                Toggle::make('requires_flight_info')
                                    ->label(__('filament.resources.service_transfer_fields.requires_flight_info'))
                                    ->default(false),
                                Toggle::make('requires_pickup_time')
                                    ->label(__('filament.resources.service_transfer_fields.requires_pickup_time'))
                                    ->default(false),
                                Toggle::make('requires_dropoff_time')
                                    ->label(__('filament.resources.service_transfer_fields.requires_dropoff_time'))
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
                    ->label(__('filament.resources.service_transfer_columns.id'))
                    ->sortable(),
                TextColumn::make('service.name')
                    ->label(__('filament.resources.service_transfer_columns.service'))
                    ->searchable(query: function ($query, $search): void {
                        $query->whereHas('service.translations', function ($q) use ($search): void {
                            $q->where('name', 'like', '%'.$search.'%');
                        });
                    }),
                TextColumn::make('transfer_type')
                    ->label(__('filament.resources.service_transfer_columns.transfer_type'))
                    ->formatStateUsing(fn (string $state) => __('filament.resources.service_transfer_transfer_type.'.$state)),
                TextColumn::make('modality')
                    ->label(__('filament.resources.service_transfer_columns.modality'))
                    ->formatStateUsing(fn (string $state) => __('filament.resources.service_transfer_modality.'.$state)),
                IconColumn::make('allows_multiple_stops')
                    ->label(__('filament.resources.service_transfer_fields.allows_multiple_stops'))
                    ->boolean(),
            ])
            ->defaultSort('id')
            ->modifyQueryUsing(fn ($query) => $query->with(['service.translations.language.locale']))
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

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    {
        $term = '%'.$search.'%';
        $query->whereHas('service.translations', function ($q) use ($term): void {
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
            'index' => Pages\ListServiceTransfers::route('/'),
            'create' => Pages\CreateServiceTransfer::route('/create'),
            'view' => Pages\ViewServiceTransfer::route('/{record}'),
            'edit' => Pages\EditServiceTransfer::route('/{record}/edit'),
        ];
    }
}
