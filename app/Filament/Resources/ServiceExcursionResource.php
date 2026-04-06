<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceExcursionResource\Pages;
use App\Models\Service;
use App\Models\ServiceExcursion;
use App\Models\ServiceExcursionType;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceExcursionResource extends LmpResource
{
    protected static ?string $model = ServiceExcursion::class;

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $modelLabel = 'filament.resources.service_excursion';

    protected static ?string $pluralModelLabel = 'filament.resources.service_excursions';

    protected static ?string $recordTitleAttribute = 'service.name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_excursions';

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

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.service_excursion_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                Select::make('service_id')
                                    ->label(__('filament.resources.service_excursion_fields.service_id'))
                                    ->options(
                                        fn () => Service::query()
                                            ->with(['translations.language.locale'])
                                            ->orderBy('id')
                                            ->get()
                                            ->mapWithKeys(fn (Service $s) => [$s->id => $s->name ?: "Service #{$s->id}"])
                                    )
                                    ->searchable()
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Select::make('service_excursion_type_id')
                                    ->label(__('filament.resources.service_excursion_fields.service_excursion_type_id'))
                                    ->options(
                                        fn () => ServiceExcursionType::query()
                                            ->with(['translations.language.locale'])
                                            ->ordered()
                                            ->where('active', true)
                                            ->get()
                                            ->mapWithKeys(fn (ServiceExcursionType $t) => [$t->id => $t->name ?: $t->code])
                                    )
                                    ->searchable()
                                    ->nullable(),
                                Select::make('difficulty_level')
                                    ->label(__('filament.resources.service_excursion_fields.difficulty_level'))
                                    ->options([
                                        1 => __('filament.resources.service_excursion_difficulty.easy'),
                                        2 => __('filament.resources.service_excursion_difficulty.moderate'),
                                        3 => __('filament.resources.service_excursion_difficulty.difficult'),
                                    ])
                                    ->nullable(),
                                TextInput::make('min_age')
                                    ->label(__('filament.resources.service_excursion_fields.min_age'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->nullable(),
                                TextInput::make('max_age')
                                    ->label(__('filament.resources.service_excursion_fields.max_age'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->nullable(),
                                Toggle::make('guide_included')
                                    ->label(__('filament.resources.service_excursion_fields.guide_included'))
                                    ->default(false),
                                Toggle::make('transport_included')
                                    ->label(__('filament.resources.service_excursion_fields.transport_included'))
                                    ->default(false),
                                Toggle::make('outdoor_activity')
                                    ->label(__('filament.resources.service_excursion_fields.outdoor_activity'))
                                    ->default(true),
                                Toggle::make('requires_good_weather')
                                    ->label(__('filament.resources.service_excursion_fields.requires_good_weather'))
                                    ->default(false),
                                Toggle::make('active')
                                    ->label(__('filament.common.active'))
                                    ->default(true),
                            ])->columns(2),
                            Section::make(__('filament.resources.service_excursion_tabs.technical'))
                                ->schema([
                                    TextInput::make('max_altitude_m')
                                        ->label(__('filament.resources.service_excursion_fields.max_altitude_m'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->suffix(' m')
                                        ->nullable(),
                                    TextInput::make('distance_km')
                                        ->label(__('filament.resources.service_excursion_fields.distance_km'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->suffix(' km')
                                        ->nullable(),
                                ])->columns(2),
                        ]),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.service_excursion_columns.id'))
                    ->sortable(),
                TextColumn::make('service.name')
                    ->label(__('filament.resources.service_excursion_columns.service'))
                    ->searchable(query: function ($query, $search): void {
                        $query->whereHas('service.translations', function ($q) use ($search): void {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                    })
                    ->sortable(),
                TextColumn::make('serviceExcursionType.name')
                    ->label(__('filament.resources.service_excursion_columns.type'))
                    ->placeholder('—')
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('service_excursion_type_id', $direction)),
                TextColumn::make('difficulty_level')
                    ->label(__('filament.resources.service_excursion_columns.difficulty'))
                    ->formatStateUsing(fn (?int $state) => match ($state) {
                    1 => __('filament.resources.service_excursion_difficulty.easy'),
                    2 => __('filament.resources.service_excursion_difficulty.moderate'),
                    3 => __('filament.resources.service_excursion_difficulty.difficult'),
                    default => '—',
                })
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('service_excursion_type_id')
                    ->label(__('filament.resources.service_excursion_columns.type'))
                    ->options(fn () => ServiceExcursionType::query()
                        ->where('active', true)
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (ServiceExcursionType $t) => [$t->id => $t->code ?: $t->name]))
                    ->searchable(),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['service.translations.language.locale', 'serviceExcursionType']));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceExcursions::route('/'),
            'create' => Pages\CreateServiceExcursion::route('/create'),
            'view' => Pages\ViewServiceExcursion::route('/{record}'),
            'edit' => Pages\EditServiceExcursion::route('/{record}/edit'),
        ];
    }
}
