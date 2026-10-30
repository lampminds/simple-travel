<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CuentasCluster;
use App\Filament\Resources\PassengerCategoryResource\Pages;
use App\Models\Account;
use App\Models\PassengerCategory;
use BackedEnum;
use Closure;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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
use Illuminate\Validation\Rules\Unique;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class PassengerCategoryResource extends LmpResource
{
    protected static ?string $model = PassengerCategory::class;

    protected static ?string $slug = 'passenger-categories';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'filament.resources.passenger_category';

    protected static ?string $pluralModelLabel = 'filament.resources.passenger_categories';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_accounts_price_lists';

    protected static ?int $navigationSort = 20;

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
        $main = array_map(fn ($component) => $component->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($component) => $component->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.passenger_category_tabs.general'))
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Select::make('account_id')
                                        ->label(__('filament.resources.passenger_category_fields.account_id'))
                                        ->options(fn (): array => static::accountOptions())
                                        ->searchable()
                                        ->required()
                                        ->live(),
                                    TextInput::make('name')
                                        ->label(__('filament.resources.passenger_category_fields.name'))
                                        ->required()
                                        ->maxLength(100),
                                    TextInput::make('code')
                                        ->label(__('filament.resources.passenger_category_fields.code'))
                                        ->required()
                                        ->maxLength(50)
                                        ->unique(
                                            table: 'passenger_categories',
                                            column: 'code',
                                            ignoreRecord: true,
                                            modifyRuleUsing: fn (Unique $rule, callable $get): Unique => $rule->where(
                                                'account_id',
                                                $get('account_id'),
                                            ),
                                        ),
                                    TextInput::make('age_min')
                                        ->label(__('filament.resources.passenger_category_fields.age_min'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(65535)
                                        ->nullable()
                                        ->rules([
                                            fn (Get $get, ?PassengerCategory $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                                                $message = PassengerCategory::ageRangeConflictMessage(
                                                    (int) $get('account_id'),
                                                    PassengerCategory::normalizeAge($get('age_min')),
                                                    PassengerCategory::normalizeAge($get('age_max')),
                                                    $record?->getKey() !== null ? (int) $record->getKey() : null,
                                                );

                                                if ($message !== null) {
                                                    $fail($message);
                                                }
                                            },
                                        ]),
                                    TextInput::make('age_max')
                                        ->label(__('filament.resources.passenger_category_fields.age_max'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(65535)
                                        ->nullable(),
                                    Toggle::make('is_active')
                                        ->label(__('filament.resources.passenger_category_fields.is_active'))
                                        ->default(true),
                                ])
                                ->columns(2),
                        ]),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('account_id')
                    ->label(__('filament.resources.passenger_category_columns.account'))
                    ->state(fn (PassengerCategory $record): string => static::accountLabel($record->account)),
                TextColumn::make('name')
                    ->label(__('filament.resources.passenger_category_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.passenger_category_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('age_range')
                    ->label(__('filament.resources.passenger_category_columns.age_range'))
                    ->state(fn (PassengerCategory $record): string => $record->ageRangeLabel()),
                IconColumn::make('is_active')
                    ->label(__('filament.resources.passenger_category_columns.is_active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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
            'index' => Pages\ListPassengerCategories::route('/'),
            'create' => Pages\CreatePassengerCategory::route('/create'),
            'edit' => Pages\EditPassengerCategory::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function accountOptions(): array
    {
        return Account::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Account $account): array => [$account->id => static::accountLabel($account)])
            ->all();
    }

    public static function accountLabel(?Account $account): string
    {
        if ($account === null) {
            return '—';
        }

        $label = trim((string) ($account->commercial_name ?: $account->name ?: $account->nick));

        return $label !== '' ? $label : '#'.$account->id;
    }
}
