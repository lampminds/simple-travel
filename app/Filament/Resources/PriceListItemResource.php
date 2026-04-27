<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceListItemResource\Pages;
use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\ServiceVariant;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class PriceListItemResource extends LmpResource
{
    protected static ?string $model = PriceListItem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $modelLabel = 'filament.resources.price_list_item';

    protected static ?string $pluralModelLabel = 'filament.resources.price_list_items';

    protected static ?string $recordTitleAttribute = 'id';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_services';

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
                    Select::make('price_list_id')
                        ->label(__('filament.resources.price_list_item_fields.price_list_id'))
                        ->options(fn () => PriceList::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    Select::make('service_variant_id')
                        ->label(__('filament.resources.price_list_item_fields.service_variant_id'))
                        ->options(fn () => ServiceVariant::query()->orderBy('sku')->pluck('sku', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('price')
                        ->label(__('filament.resources.price_list_item_fields.price'))
                        ->numeric()
                        ->step(0.01)
                        ->minValue(0)
                        ->required(),
                    Select::make('pricing_mode')
                        ->label(__('filament.resources.price_list_item_fields.pricing_mode'))
                        ->options([
                            'fixed' => __('filament.resources.price_list_item_pricing_mode.fixed'),
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
                    ->label(__('filament.resources.price_list_item_columns.id'))
                    ->sortable(),
                TextColumn::make('priceList.name')
                    ->label(__('filament.resources.price_list_item_columns.price_list'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serviceVariant.sku')
                    ->label(__('filament.resources.price_list_item_columns.service_variant'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label(__('filament.resources.price_list_item_columns.price'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('pricing_mode')
                    ->label(__('filament.resources.price_list_item_columns.pricing_mode'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __('filament.resources.price_list_item_pricing_mode.'.$state) : '—')
                    ->badge(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('price_list_id')
                    ->label(__('filament.resources.price_list_item_filters.price_list_id'))
                    ->relationship('priceList', 'name')
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->modifyQueryUsing(fn ($query) => $query->with(['priceList', 'serviceVariant']))
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
            'index' => Pages\ListPriceListItems::route('/'),
            'create' => Pages\CreatePriceListItem::route('/create'),
            'edit' => Pages\EditPriceListItem::route('/{record}/edit'),
        ];
    }
}
