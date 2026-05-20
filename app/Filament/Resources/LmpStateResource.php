<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\AdministrationCluster;
use App\Filament\Resources\LmpStateResource\Pages;
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

class LmpStateResource extends BaseResource
{
    protected static ?string $model = LmpState::class;

    protected static ?string $cluster = AdministrationCluster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?string $modelLabel = 'filament.resources.lmp_state';

    protected static ?string $pluralModelLabel = 'filament.resources.lmp_states';

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
                    ->label(__('filament.resources.lmp_state_fields.name'))
                    ->required()
                    ->maxLength(255),
                Select::make('country_id')
                    ->label(__('filament.resources.lmp_state_fields.country_id'))
                    ->options(fn () => LmpCountry::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('level')
                    ->label(__('filament.resources.lmp_state_fields.level'))
                    ->numeric(),
                TextInput::make('latitude')
                    ->label(__('filament.resources.lmp_state_fields.latitude'))
                    ->numeric(),
                TextInput::make('longitude')
                    ->label(__('filament.resources.lmp_state_fields.longitude'))
                    ->numeric(),
                TextInput::make('timezone_id')
                    ->label(__('filament.resources.lmp_state_fields.timezone_id'))
                    ->numeric(),
                Select::make('parent_id')
                    ->label(__('filament.resources.lmp_state_fields.parent_id'))
                    ->options(fn () => LmpState::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
            ])->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.lmp_state_columns.id'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.lmp_state_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('country.name')
                    ->label(__('filament.resources.lmp_state_columns.country'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent.name')
                    ->label(__('filament.resources.lmp_state_columns.parent'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('level')
                    ->label(__('filament.resources.lmp_state_columns.level'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('country_id')
                    ->label(__('filament.resources.lmp_state_filters.country_id'))
                    ->options(fn () => LmpCountry::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('name')
            ->modifyQueryUsing(fn ($query) => $query->with(['country', 'parent']))
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
            'index' => Pages\ListLmpStates::route('/'),
            'create' => Pages\CreateLmpState::route('/create'),
            'edit' => Pages\EditLmpState::route('/{record}/edit'),
        ];
    }
}
