<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\AdministrationCluster;
use App\Filament\Resources\CurrencyRateResource\Pages;
use App\Models\Account;
use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Models\LmpCurrency;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CurrencyRateResource extends BaseResource
{
    protected static ?string $model = CurrencyRate::class;

    protected static ?string $cluster = AdministrationCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'filament.resources.currency_rate';

    protected static ?string $pluralModelLabel = 'filament.resources.currency_rates';

    protected static ?string $recordTitleAttribute = 'id';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_parameters';

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

    public static function getRecordTitle(?Model $record): string
    {
        if (! $record instanceof CurrencyRate) {
            return '';
        }

        $record->loadMissing('currency.lmpCurrency', 'account');

        $label = $record->currency?->display_name ?? '#'.$record->currency_id;
        $when = $record->starting_at?->format('Y-m-d') ?? '';
        $scope = $record->isSystemRate()
            ? __('filament.resources.currency_rate_scope.system')
            : ($record->account?->commercial_name ?? $record->account?->name ?? '#'.$record->account_id);

        return trim($label.($when !== '' ? ' — '.$when : '').' ('.$scope.')');
    }

    /**
     * Force USD to 1 unit per USD (buy and sell) and block duplicate keys.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeUsdRate(array $data): array
    {
        $cid = isset($data['currency_id']) ? (int) $data['currency_id'] : null;
        if (Currency::isUsdProjectCurrency($cid)) {
            $data['units_per_usd_buy'] = '1';
            $data['units_per_usd_sell'] = '1';
        }

        return $data;
    }

    /**
     * Store vigencia as date-only: midnight in app timezone (DB keeps 00:00:00).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeStartingAtToDayStart(array $data): array
    {
        if (empty($data['starting_at'])) {
            return $data;
        }

        $data['starting_at'] = \Illuminate\Support\Carbon::parse($data['starting_at'])->startOfDay();

        return $data;
    }

    public static function assertUniqueStartingAt(
        int $currencyId,
        mixed $startingAt,
        ?int $accountId = null,
        ?string $source = null,
        ?int $ignoreId = null,
    ): void {
        $at = \Illuminate\Support\Carbon::parse($startingAt)->startOfDay();
        $source = $source !== null && trim($source) !== '' ? trim($source) : null;

        $query = CurrencyRate::query()
            ->where('currency_id', $currencyId)
            ->where('starting_at', $at);

        if ($accountId === null) {
            $query->whereNull('account_id');
        } else {
            $query->where('account_id', $accountId);
        }

        if ($source === null) {
            $query->whereNull('source');
        } else {
            $query->where('source', $source);
        }

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'starting_at' => __('filament.resources.currency_rate_validation.duplicate_starting_at'),
            ]);
        }
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Section::make('')->schema([
                Select::make('account_id')
                    ->label(__('filament.resources.currency_rate_fields.account_id'))
                    ->helperText(__('filament.resources.currency_rate_fields.account_id_help'))
                    ->options(
                        fn (): array => Account::query()
                            ->orderBy('commercial_name')
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn (Account $a): array => [
                                $a->id => trim($a->commercial_name ?? $a->name ?? $a->nick ?? ('#'.$a->id)),
                            ])
                            ->all()
                    )
                    ->searchable()
                    ->nullable()
                    ->placeholder(__('filament.resources.currency_rate_scope.system')),
                Select::make('currency_id')
                    ->label(__('filament.resources.currency_rate_fields.currency_id'))
                    ->options(
                        fn (): array => Currency::query()
                            ->with('lmpCurrency')
                            ->orderBy('id')
                            ->get()
                            ->mapWithKeys(fn (Currency $c): array => [$c->id => $c->display_name])
                            ->all()
                    )
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set): void {
                        if (Currency::isUsdProjectCurrency($state !== null && $state !== '' ? (int) $state : null)) {
                            $set('units_per_usd_buy', '1');
                            $set('units_per_usd_sell', '1');
                        }
                    }),
                TextInput::make('source')
                    ->label(__('filament.resources.currency_rate_fields.source'))
                    ->helperText(__('filament.resources.currency_rate_fields.source_help'))
                    ->maxLength(20)
                    ->nullable()
                    ->placeholder('dolarapi'),
                TextInput::make('units_per_usd_buy')
                    ->label(__('filament.resources.currency_rate_fields.units_per_usd_buy'))
                    ->helperText(__('filament.resources.currency_rate_fields.units_per_usd_help'))
                    ->numeric()
                    ->step(0.00000001)
                    ->required()
                    ->disabled(
                        fn (Get $get): bool => Currency::isUsdProjectCurrency(
                            $get('currency_id') !== null && $get('currency_id') !== '' ? (int) $get('currency_id') : null
                        )
                    )
                    ->dehydrated()
                    ->rules([self::positiveUnitsRule('units_per_usd_buy')]),
                TextInput::make('units_per_usd_sell')
                    ->label(__('filament.resources.currency_rate_fields.units_per_usd_sell'))
                    ->helperText(__('filament.resources.currency_rate_fields.units_per_usd_help'))
                    ->numeric()
                    ->step(0.00000001)
                    ->required()
                    ->disabled(
                        fn (Get $get): bool => Currency::isUsdProjectCurrency(
                            $get('currency_id') !== null && $get('currency_id') !== '' ? (int) $get('currency_id') : null
                        )
                    )
                    ->dehydrated()
                    ->rules([self::positiveUnitsRule('units_per_usd_sell')]),
                DatePicker::make('starting_at')
                    ->label(__('filament.resources.currency_rate_fields.starting_at'))
                    ->helperText(__('filament.resources.currency_rate_fields.starting_at_help'))
                    ->required()
                    ->native(false)
                    ->default(now()->startOfDay()),
                Toggle::make('is_active')
                    ->label(__('filament.resources.currency_rate_fields.is_active'))
                    ->default(true)
                    ->inline(false),
            ])->columns(2),
        ];
    }

    /**
     * @return array<\Closure|string>
     */
    private static function positiveUnitsRule(string $field): array
    {
        return [
            fn (Get $get): \Closure => function (string $attribute, mixed $value, \Closure $fail) use ($get, $field): void {
                if (Currency::isUsdProjectCurrency(
                    $get('currency_id') !== null && $get('currency_id') !== '' ? (int) $get('currency_id') : null
                )) {
                    return;
                }
                if ((float) $value <= 0) {
                    $fail(__('filament.resources.currency_rate_validation.units_must_be_positive'));
                }
            },
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->modifyQueryUsing(
                fn (Builder $query): Builder => $query->with(['currency.lmpCurrency', 'account'])->orderByDesc('starting_at')
            )
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.currency_rate_columns.id'))
                    ->sortable(),
                TextColumn::make('account.commercial_name')
                    ->label(__('filament.resources.currency_rate_columns.account'))
                    ->placeholder(__('filament.resources.currency_rate_scope.system'))
                    ->searchable(['account.commercial_name', 'account.name', 'account.nick'])
                    ->sortable(),
                TextColumn::make('currency.display_name')
                    ->label(__('filament.resources.currency_rate_columns.currency'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $term = trim($search);
                        if ($term === '') {
                            return $query;
                        }
                        if (ctype_digit($term)) {
                            $id = (int) $term;

                            return $query->whereHas(
                                'currency',
                                fn (Builder $q): Builder => $q->where('id', $id)->orWhere('currency_id', $id)
                            );
                        }

                        $masterIds = LmpCurrency::query()
                            ->where(function (Builder $q) use ($term): void {
                                $q->where('code', 'like', '%'.$term.'%')
                                    ->orWhere('name', 'like', '%'.$term.'%');
                            })
                            ->pluck('id');
                        if ($masterIds->isEmpty()) {
                            return $query->whereRaw('1 = 0');
                        }
                        $catIds = Currency::query()->whereIn('currency_id', $masterIds)->pluck('id');
                        if ($catIds->isEmpty()) {
                            return $query->whereRaw('1 = 0');
                        }

                        return $query->whereIn('currency_id', $catIds);
                    }),
                TextColumn::make('source')
                    ->label(__('filament.resources.currency_rate_fields.source'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('units_per_usd_buy')
                    ->label(__('filament.resources.currency_rate_columns.units_per_usd_buy'))
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('units_per_usd_sell')
                    ->label(__('filament.resources.currency_rate_columns.units_per_usd_sell'))
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('starting_at')
                    ->label(__('filament.resources.currency_rate_columns.starting_at'))
                    ->date()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('filament.resources.currency_rate_columns.is_active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('filament.resources.currency_rate_columns.is_active'))
                    ->placeholder(__('filament.resources.currency_rate_filters.all_active_states'))
                    ->trueLabel(__('filament.resources.currency_rate_filters.active_only'))
                    ->falseLabel(__('filament.resources.currency_rate_filters.inactive_only')),
                TernaryFilter::make('account_id')
                    ->label(__('filament.resources.currency_rate_filters.scope'))
                    ->nullable()
                    ->placeholder(__('filament.resources.currency_rate_filters.all_scopes'))
                    ->trueLabel(__('filament.resources.currency_rate_filters.tenant_only'))
                    ->falseLabel(__('filament.resources.currency_rate_filters.system_only'))
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNotNull('account_id'),
                        false: fn (Builder $query): Builder => $query->whereNull('account_id'),
                        blank: fn (Builder $query): Builder => $query,
                    ),
            ])
            ->defaultSort('starting_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencyRates::route('/'),
            'create' => Pages\CreateCurrencyRate::route('/create'),
            'view' => Pages\ViewCurrencyRate::route('/{record}'),
            'edit' => Pages\EditCurrencyRate::route('/{record}/edit'),
        ];
    }
}
