<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceListResource\Pages;
use App\Models\Account;
use App\Models\Currency;
use App\Models\PriceList;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
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

class PriceListResource extends LmpResource
{
    protected static ?string $model = PriceList::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $modelLabel = 'filament.resources.price_list';

    protected static ?string $pluralModelLabel = 'filament.resources.price_lists';

    protected static ?string $recordTitleAttribute = 'name';

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
        $accountOptions = fn (): array => Account::query()->orderBy('name')->pluck('name', 'id')->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.price_list_tabs.general'))
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Hidden::make('owner_type')
                                        ->default(Account::class),
                                    Select::make('owner_id')
                                        ->label(__('filament.resources.price_list_fields.owner_id'))
                                        ->options($accountOptions)
                                        ->searchable()
                                        ->required(),
                                    TextInput::make('name')
                                        ->label(__('filament.resources.price_list_fields.name'))
                                        ->required()
                                        ->maxLength(255),
                                    Select::make('currency_id')
                                        ->label(__('filament.resources.price_list_fields.currency_id'))
                                        ->options(fn () => Currency::query()->with('lmpCurrency')->orderBy('id')->get()->mapWithKeys(fn (Currency $c) => [$c->id => $c->display_name]))
                                        ->searchable()
                                        ->required(),
                                    DatePicker::make('valid_from')
                                        ->label(__('filament.resources.price_list_fields.valid_from'))
                                        ->native(false)
                                        ->nullable(),
                                    DatePicker::make('valid_to')
                                        ->label(__('filament.resources.price_list_fields.valid_to'))
                                        ->native(false)
                                        ->nullable(),
                                    Toggle::make('is_active')
                                        ->label(__('filament.resources.price_list_fields.is_active'))
                                        ->default(true),
                                ])
                                ->columns(2),
                        ]),
                    Tab::make(__('filament.resources.price_list_tabs.assignments'))
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Repeater::make('assignments')
                                        ->relationship()
                                        ->label(__('filament.resources.price_list_fields.assignments'))
                                        ->schema([
                                            Hidden::make('assigned_to_type')
                                                ->default(Account::class),
                                            Select::make('assigned_to_id')
                                                ->label(__('filament.resources.price_list_assignment_fields.assigned_to_id'))
                                                ->options($accountOptions)
                                                ->searchable()
                                                ->required(),
                                            Select::make('adjustment_type')
                                                ->label(__('filament.resources.price_list_assignment_fields.adjustment_type'))
                                                ->options([
                                                    'none' => __('filament.resources.price_list_assignment_adjustment_type.none'),
                                                    'percentage' => __('filament.resources.price_list_assignment_adjustment_type.percentage'),
                                                    'fixed' => __('filament.resources.price_list_assignment_adjustment_type.fixed'),
                                                ])
                                                ->default('none')
                                                ->required()
                                                ->live(),
                                            TextInput::make('adjustment_value')
                                                ->label(__('filament.resources.price_list_assignment_fields.adjustment_value'))
                                                ->numeric()
                                                ->step(0.01)
                                                ->nullable()
                                                ->visible(fn (Get $get): bool => in_array((string) $get('adjustment_type'), ['percentage', 'fixed'], true)),
                                            DatePicker::make('valid_from')
                                                ->label(__('filament.resources.price_list_assignment_fields.valid_from'))
                                                ->native(false)
                                                ->nullable(),
                                            DatePicker::make('valid_to')
                                                ->label(__('filament.resources.price_list_assignment_fields.valid_to'))
                                                ->native(false)
                                                ->nullable(),
                                            Toggle::make('is_active')
                                                ->label(__('filament.resources.price_list_assignment_fields.is_active'))
                                                ->default(true),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->addActionLabel(__('filament.resources.price_list_assignment_fields.add')),
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
                    ->label(__('filament.resources.price_list_columns.id'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.price_list_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('owner')
                    ->label(__('filament.resources.price_list_columns.owner'))
                    ->getStateUsing(function (PriceList $record): string {
                        $owner = $record->owner;
                        if ($owner === null) {
                            return '—';
                        }

                        if ($owner instanceof Account) {
                            return $owner->name ?: ('#'.$owner->id);
                        }

                        return class_basename($record->owner_type).' #'.$record->owner_id;
                    }),
                TextColumn::make('currency.display_name')
                    ->label(__('filament.resources.price_list_columns.currency'))
                    ->sortable(),
                TextColumn::make('valid_from')
                    ->label(__('filament.resources.price_list_columns.valid_from'))
                    ->date()
                    ->sortable(),
                TextColumn::make('valid_to')
                    ->label(__('filament.resources.price_list_columns.valid_to'))
                    ->date()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('filament.resources.price_list_columns.is_active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label(__('filament.resources.price_list_columns.items_count'))
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with(['owner', 'currency.lmpCurrency'])->withCount('items'))
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
            'index' => Pages\ListPriceLists::route('/'),
            'create' => Pages\CreatePriceList::route('/create'),
            'edit' => Pages\EditPriceList::route('/{record}/edit'),
        ];
    }
}
