<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use App\Models\LmpCurrency;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CurrencyResource extends BaseResource
{
    protected static ?string $model = Currency::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $modelLabel = 'filament.resources.currency';

    protected static ?string $pluralModelLabel = 'filament.resources.currencies';

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
        return $record?->display_name ?? (string) $record?->id;
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Section::make('')->schema([
                Select::make('currency_id')
                    ->label(__('filament.resources.currency_fields.currency'))
                    ->relationship(
                        'lmpCurrency',
                        'name',
                        fn ($query) => $query->orderBy('code')
                    )
                    ->getOptionLabelFromRecordUsing(fn (LmpCurrency $record) => "{$record->code} — {$record->name}")
                    ->searchable(['code', 'name'])
                    ->preload()
                    ->required()
                    ->unique(ignoreRecord: true),
            ])->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.currency_columns.id'))
                    ->sortable(),
                TextColumn::make('lmpCurrency.code')
                    ->label(__('filament.resources.currency_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lmpCurrency.symbol')
                    ->label(__('filament.resources.currency_columns.symbol'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lmpCurrency.name')
                    ->label(__('filament.resources.currency_columns.name'))
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'view' => Pages\ViewCurrency::route('/{record}'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
