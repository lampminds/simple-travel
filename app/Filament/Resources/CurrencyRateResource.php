<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\AdministrationCluster;
use App\Filament\Resources\CurrencyRateResource\Pages;
use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Models\LmpCurrency;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
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

        $record->loadMissing('currency.lmpCurrency');

        $label = $record->currency?->display_name ?? '#'.$record->currency_id;
        $when = $record->starting_at?->format('Y-m-d') ?? '';

        return trim($label.($when !== '' ? ' — '.$when : ''));
    }

    /**
     * Force USD to 1 unit per USD and block duplicate (currency_id, starting_at).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeUsdRate(array $data): array
    {
        $cid = isset($data['currency_id']) ? (int) $data['currency_id'] : null;
        if (Currency::isUsdProjectCurrency($cid)) {
            $data['units_per_usd'] = '1';
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

    public static function assertUniqueStartingAt(int $currencyId, mixed $startingAt, ?int $ignoreId = null): void
    {
        $at = \Illuminate\Support\Carbon::parse($startingAt)->startOfDay();

        $query = CurrencyRate::query()
            ->where('currency_id', $currencyId)
            ->where('starting_at', $at);
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
                            $set('units_per_usd', '1');
                        }
                    }),
                TextInput::make('units_per_usd')
                    ->label(__('filament.resources.currency_rate_fields.units_per_usd'))
                    ->helperText(__('filament.resources.currency_rate_fields.units_per_usd_help'))
                    ->numeric()
                    ->step(0.00000001)
                    ->required()
                    ->default('1')
                    ->disabled(
                        fn (Get $get): bool => Currency::isUsdProjectCurrency(
                            $get('currency_id') !== null && $get('currency_id') !== '' ? (int) $get('currency_id') : null
                        )
                    )
                    ->dehydrated()
                    ->rules([
                        fn (Get $get): \Closure => function (string $attribute, mixed $value, \Closure $fail) use ($get): void {
                            if (Currency::isUsdProjectCurrency(
                                $get('currency_id') !== null && $get('currency_id') !== '' ? (int) $get('currency_id') : null
                            )) {
                                return;
                            }
                            if ((float) $value <= 0) {
                                $fail(__('filament.resources.currency_rate_validation.units_must_be_positive'));
                            }
                        },
                    ]),
                DatePicker::make('starting_at')
                    ->label(__('filament.resources.currency_rate_fields.starting_at'))
                    ->helperText(__('filament.resources.currency_rate_fields.starting_at_help'))
                    ->required()
                    ->native(false)
                    ->default(now()->startOfDay()),
            ])->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->modifyQueryUsing(
                fn (Builder $query): Builder => $query->with(['currency.lmpCurrency'])->orderByDesc('starting_at')
            )
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.currency_rate_columns.id'))
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
                TextColumn::make('units_per_usd')
                    ->label(__('filament.resources.currency_rate_columns.units_per_usd'))
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('starting_at')
                    ->label(__('filament.resources.currency_rate_columns.starting_at'))
                    ->date()
                    ->sortable(),
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
