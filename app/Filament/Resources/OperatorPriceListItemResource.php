<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CuentasCluster;
use App\Filament\Resources\OperatorPriceListItemResource\Pages;
use App\Models\OperatorPriceList;
use App\Models\OperatorPriceListItem;
use App\Models\OperatorServiceCatalog;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class OperatorPriceListItemResource extends LmpResource
{
    protected static ?string $model = OperatorPriceListItem::class;

    protected static ?string $slug = 'operator-price-list-items';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $modelLabel = 'filament.resources.operator_price_list_item';

    protected static ?string $pluralModelLabel = 'filament.resources.operator_price_list_items';

    protected static ?string $recordTitleAttribute = 'id';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_accounts_price_lists';

    protected static ?int $navigationSort = 13;

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
        return [
            Section::make('')
                ->schema([
                    Select::make('operator_price_list_id')
                        ->label(__('filament.resources.operator_price_list_item_fields.price_list_id'))
                        ->options(fn () => OperatorPriceList::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->live(),
                    Select::make('operator_service_catalog_id')
                        ->label(__('filament.resources.operator_price_list_item_fields.catalog_entry_id'))
                        ->options(function (Get $get): array {
                            $listId = $get('operator_price_list_id');
                            if ($listId === null || $listId === '') {
                                return [];
                            }
                            $operatorId = OperatorPriceList::query()->whereKey($listId)->value('operator_id');
                            if ($operatorId === null) {
                                return [];
                            }

                            return OperatorServiceCatalog::query()
                                ->where('operator_id', (int) $operatorId)
                                ->with('translations')
                                ->orderBy('id')
                                ->get()
                                ->mapWithKeys(fn (OperatorServiceCatalog $row): array => [
                                    $row->id => $row->displayLabel(),
                                ])
                                ->all();
                        })
                        ->searchable()
                        ->required(),
                    TextInput::make('price')
                        ->label(__('filament.resources.operator_price_list_item_fields.price'))
                        ->numeric()
                        ->step(0.01)
                        ->required(),
                    Select::make('pricing_mode')
                        ->label(__('filament.resources.operator_price_list_item_fields.pricing_mode'))
                        ->options([
                            'fixed' => __('filament.resources.operator_price_list_item_pricing_mode.fixed'),
                            'percentage' => __('filament.resources.operator_price_list_item_pricing_mode.percentage'),
                        ])
                        ->required()
                        ->default('fixed'),
                ])
                ->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.operator_price_list_item_columns.id'))
                    ->sortable(),
                TextColumn::make('priceList.name')
                    ->label(__('filament.resources.operator_price_list_item_columns.price_list'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('catalog_label')
                    ->label(__('filament.resources.operator_price_list_item_columns.catalog_entry'))
                    ->getStateUsing(function (OperatorPriceListItem $record): string {
                        $row = $record->catalogEntry;
                        if ($row === null) {
                            return '—';
                        }
                        $provider = trim($row->provider?->commercial_name ?? $row->provider?->name ?? '');
                        $service = trim($row->service?->name ?? '');
                        $sku = trim((string) ($row->serviceVariant?->sku ?? ''));
                        $parts = array_filter([$provider !== '' ? $provider : null, $service !== '' ? $service : null, $sku !== '' ? $sku : null]);

                        return implode(' — ', $parts) ?: ('#'.$row->id);
                    }),
                TextColumn::make('price')
                    ->label(__('filament.resources.operator_price_list_item_columns.price'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('pricing_mode')
                    ->label(__('filament.resources.operator_price_list_item_columns.pricing_mode'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __('filament.resources.operator_price_list_item_pricing_mode.'.$state) : '—')
                    ->badge(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('operator_price_list_id')
                    ->label(__('filament.resources.operator_price_list_item_filters.price_list_id'))
                    ->relationship('priceList', 'name')
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->modifyQueryUsing(fn ($query) => $query->with(['priceList', 'catalogEntry.provider', 'catalogEntry.service', 'catalogEntry.serviceVariant']))
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
            'index' => Pages\ListOperatorPriceListItems::route('/'),
            'create' => Pages\CreateOperatorPriceListItem::route('/create'),
            'edit' => Pages\EditOperatorPriceListItem::route('/{record}/edit'),
        ];
    }
}
