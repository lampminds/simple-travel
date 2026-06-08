<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CuentasCluster;
use App\Filament\Resources\ProviderPriceListItemResource\Pages;
use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\ServiceVariant;
use App\Support\CurrentCatalogHelperAccountContext;
use App\Support\ServiceWizardVariantCatalogHelpers;
use BackedEnum;
use Illuminate\Support\HtmlString;
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

class ProviderPriceListItemResource extends LmpResource
{
    protected static ?string $model = PriceListItem::class;

    protected static ?string $slug = 'provider-price-list-items';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $modelLabel = 'filament.resources.provider_price_list_item';

    protected static ?string $pluralModelLabel = 'filament.resources.provider_price_list_items';

    protected static ?string $recordTitleAttribute = 'id';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_accounts_price_lists';

    protected static ?int $navigationSort = 11;

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
                    Select::make('provider_price_list_id')
                        ->label(__('filament.resources.provider_price_list_item_fields.price_list_id'))
                        ->options(fn () => PriceList::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->live(),
                    Select::make('service_variant_id')
                        ->label(__('filament.resources.provider_price_list_item_fields.service_variant_id'))
                        ->options(function (Get $get): array {
                            $listId = $get('provider_price_list_id');
                            if ($listId === null || $listId === '') {
                                return [];
                            }
                            $providerId = PriceList::query()->whereKey($listId)->value('provider_id');
                            if ($providerId === null) {
                                return [];
                            }

                            return ServiceVariant::query()
                                ->whereHas('service', fn ($q) => $q->where('account_id', (int) $providerId))
                                ->orderBy('sku')
                                ->get()
                                ->mapWithKeys(function (ServiceVariant $variant): array {
                                    $serviceName = trim((string) ($variant->service?->name ?? ''));
                                    $sku = trim((string) $variant->sku);
                                    $serviceChunk = $serviceName !== '' ? $serviceName : ('Service #'.$variant->service_id);
                                    $skuChunk = $sku !== '' ? $sku : ('Variant #'.$variant->id);

                                    return [$variant->id => $serviceChunk.' — '.$skuChunk];
                                })
                                ->all();
                        })
                        ->searchable()
                        ->required(),
                    TextInput::make('price')
                        ->label(__('filament.resources.provider_price_list_item_fields.price'))
                        ->numeric()
                        ->step(0.01)
                        ->nullable()
                        ->required(fn (Get $get): bool => filled($get('pricing_mode')))
                        ->disabled(fn (Get $get): bool => blank($get('pricing_mode')))
                        ->dehydrateStateUsing(fn ($state, Get $get) => blank($get('pricing_mode')) ? null : $state),
                    Select::make('pricing_mode')
                        ->label(__('filament.resources.provider_price_list_item_fields.pricing_mode'))
                        ->options([
                            'fixed' => __('filament.resources.provider_price_list_item_pricing_mode.fixed'),
                            'percentage' => __('filament.resources.provider_price_list_item_pricing_mode.percentage'),
                        ])
                        ->nullable()
                        ->placeholder(__('filament.resources.provider_price_list_item_pricing_mode.variant_base'))
                        ->helperText(function (): HtmlString|string|null {
                            $html = ServiceWizardVariantCatalogHelpers::pricingModeHelpHtml(
                                accountTypeId: CurrentCatalogHelperAccountContext::primaryAccountTypeId(),
                            );

                            return filled($html) ? new HtmlString($html) : null;
                        })
                        ->live(),
                ])
                ->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.provider_price_list_item_columns.id'))
                    ->sortable(),
                TextColumn::make('priceList.name')
                    ->label(__('filament.resources.provider_price_list_item_columns.price_list'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('target')
                    ->label(__('filament.resources.provider_price_list_item_columns.target'))
                    ->getStateUsing(function (PriceListItem $record): string {
                        $variant = $record->serviceVariant;
                        if ($variant === null) {
                            return '—';
                        }

                        $serviceName = trim((string) ($variant->service?->name ?? ''));
                        $sku = trim((string) ($variant->sku ?? ''));
                        $serviceChunk = $serviceName !== '' ? $serviceName : ('#'.$variant->service_id);
                        $skuChunk = $sku !== '' ? $sku : ('#'.$variant->id);

                        return $serviceChunk.' — '.$skuChunk;
                    }),
                TextColumn::make('price')
                    ->label(__('filament.resources.provider_price_list_item_columns.price'))
                    ->formatStateUsing(fn ($state, PriceListItem $record): string => $record->pricing_mode === null
                        ? '—'
                        : number_format((float) $state, 2, '.', ''))
                    ->sortable(),
                TextColumn::make('pricing_mode')
                    ->label(__('filament.resources.provider_price_list_item_columns.pricing_mode'))
                    ->formatStateUsing(fn (?string $state): string => $state
                        ? __('filament.resources.provider_price_list_item_pricing_mode.'.$state)
                        : __('filament.resources.provider_price_list_item_pricing_mode.variant_base'))
                    ->badge(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('provider_price_list_id')
                    ->label(__('filament.resources.provider_price_list_item_filters.price_list_id'))
                    ->relationship('priceList', 'name')
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->modifyQueryUsing(fn ($query) => $query->with(['priceList', 'serviceVariant.service']))
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
            'index' => Pages\ListProviderPriceListItems::route('/'),
            'create' => Pages\CreateProviderPriceListItem::route('/create'),
            'edit' => Pages\EditProviderPriceListItem::route('/{record}/edit'),
        ];
    }
}
