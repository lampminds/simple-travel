<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\AdministrationCluster;
use App\Filament\Resources\LmpCountryResource\Pages;
use App\Models\LmpCountry;
use App\Models\LmpCurrency;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class LmpCountryResource extends BaseResource
{
    protected static ?string $model = LmpCountry::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $modelLabel = 'Country';

    protected static ?string $pluralModelLabel = 'Countries';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_parameters';

    protected static ?string $cluster = AdministrationCluster::class;

    public static function getNavigationGroup(): ?string
    {
        $group = static::$navigationGroup;

        return $group instanceof \UnitEnum ? $group->value : ($group !== null ? (string) __($group) : null);
    }

    public static function form(Schema $schema): Schema
    {
        if (property_exists(static::getModel(), 'dont_use_audit')) {
            return $schema->schema(
                array_map(fn ($component) => $component->columnSpanFull(), static::getMainFormSchema($schema)),
            );
        }

        $main = array_map(fn ($component) => $component->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($component) => $component->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Section::make('')->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('iso_2')
                    ->label('ISO 2')
                    ->maxLength(2),
                TextInput::make('iso_3')
                    ->label('ISO 3')
                    ->maxLength(3),
                TextInput::make('phonecode')
                    ->label('Phone Code')
                    ->maxLength(20),
                TextInput::make('capital')
                    ->label('Capital')
                    ->maxLength(255),
                Select::make('currency_id')
                    ->label('Currency')
                    ->options(fn () => LmpCurrency::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                TextInput::make('tld')
                    ->label('Top Level Domain')
                    ->maxLength(10),
                TextInput::make('emoji')
                    ->label('Emoji')
                    ->maxLength(20),
            ])->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('iso_2')
                    ->label('ISO 2')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('iso_3')
                    ->label('ISO 3')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('capital')
                    ->label('Capital')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('currency.name')
                    ->label('Currency')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->modifyQueryUsing(fn ($query) => $query->with('currency'))
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
            'index' => Pages\ListLmpCountries::route('/'),
            'create' => Pages\CreateLmpCountry::route('/create'),
            'edit' => Pages\EditLmpCountry::route('/{record}/edit'),
        ];
    }
}
