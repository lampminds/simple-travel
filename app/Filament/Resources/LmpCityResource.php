<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\AdministrationCluster;
use App\Filament\Resources\LmpCityResource\Pages;
use App\Models\LmpCity;
use App\Models\LmpCountry;
use App\Models\LmpState;
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

class LmpCityResource extends BaseResource
{
    protected static ?string $model = LmpCity::class;

    protected static ?string $cluster = AdministrationCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $modelLabel = 'filament.resources.lmp_city';

    protected static ?string $pluralModelLabel = 'filament.resources.lmp_cities';

    protected static ?string $recordTitleAttribute = 'name';

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

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($component) => $component->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($component) => $component->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Section::make('')->schema([
                TextInput::make('name')
                    ->label(__('filament.resources.lmp_city_fields.name'))
                    ->required()
                    ->maxLength(255),
                Select::make('state_id')
                    ->label(__('filament.resources.lmp_city_fields.state_id'))
                    ->options(fn () => LmpState::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('latitude')
                    ->label(__('filament.resources.lmp_city_fields.latitude'))
                    ->numeric(),
                TextInput::make('longitude')
                    ->label(__('filament.resources.lmp_city_fields.longitude'))
                    ->numeric(),
                TextInput::make('timezone_id')
                    ->label(__('filament.resources.lmp_city_fields.timezone_id'))
                    ->numeric(),
            ])->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.lmp_city_columns.id'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.lmp_city_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state.name')
                    ->label(__('filament.resources.lmp_city_columns.state'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state.country.name')
                    ->label(__('filament.resources.lmp_city_columns.country'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('country_id')
                    ->label(__('filament.resources.lmp_city_filters.country_id'))
                    ->options(fn () => LmpCountry::query()->orderBy('name')->pluck('name', 'id'))
                    ->query(function ($query, array $data) {
                        $countryId = $data['value'] ?? null;

                        if (! $countryId) {
                            return $query;
                        }

                        return $query->whereHas('state', fn ($stateQuery) => $stateQuery->where('country_id', $countryId));
                    })
                    ->searchable(),
                SelectFilter::make('state_id')
                    ->label(__('filament.resources.lmp_city_filters.state_id'))
                    ->options(fn () => LmpState::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('name')
            ->modifyQueryUsing(fn ($query) => $query->with(['state.country']))
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLmpCities::route('/'),
            'create' => Pages\CreateLmpCity::route('/create'),
            'edit' => Pages\EditLmpCity::route('/{record}/edit'),
        ];
    }
}
