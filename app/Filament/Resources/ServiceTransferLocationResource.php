<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\TransportCluster;
use App\Filament\Resources\ServiceTransferLocationResource\Pages;
use App\Models\Language;
use App\Models\LmpCity;
use App\Models\ServiceTransferLocation;
use App\Models\ServiceTransferLocationType;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceTransferLocationResource extends LmpResource
{
    protected static ?string $model = ServiceTransferLocation::class;

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $modelLabel = 'filament.resources.service_transfer_location';

    protected static ?string $pluralModelLabel = 'filament.resources.service_transfer_locations';

    protected static ?string $recordTitleAttribute = 'wizard_label';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_transport';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = TransportCluster::class;

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
        $languages = Language::query()->with('locale')->orderBy('id')->get();

        $translationSections = $languages->map(function (Language $lang) use ($languages) {
            $firstId = $languages->first()?->id;

            return Section::make($lang->display_name)
                ->schema([
                    TextInput::make("translations.{$lang->id}.name")
                        ->label(__('filament.resources.service_transfer_location_columns.name'))
                        ->required((int) $lang->id === (int) $firstId)
                        ->maxLength(255),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.service_transfer_location_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                Select::make('service_transfer_location_type_id')
                                    ->label(__('filament.resources.service_transfer_location_fields.service_transfer_location_type_id'))
                                    ->options(
                                        fn () => ServiceTransferLocationType::query()
                                            ->ordered()
                                            ->where('active', true)
                                            ->with(['translations.language.locale'])
                                            ->get()
                                            ->mapWithKeys(fn (ServiceTransferLocationType $t) => [$t->id => ($t->name !== '' ? $t->name : $t->code)])
                                    )
                                    ->searchable()
                                    ->required(),
                                TextInput::make('address')
                                    ->label(__('filament.resources.service_transfer_location_fields.address'))
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Select::make('city_id')
                                    ->label(__('filament.resources.service_transfer_location_fields.city_id'))
                                    ->searchable()
                                    ->getSearchResultsUsing(function (string $search): array {
                                        return LmpCity::query()
                                            ->where('name', 'like', '%'.$search.'%')
                                            ->orderBy('name')
                                            ->limit(50)
                                            ->pluck('name', 'id')
                                            ->all();
                                    })
                                    ->getOptionLabelUsing(fn ($value): ?string => $value ? LmpCity::find($value)?->name : null)
                                    ->nullable(),
                                TextInput::make('latitude')
                                    ->label(__('filament.resources.service_transfer_location_fields.latitude'))
                                    ->numeric()
                                    ->nullable(),
                                TextInput::make('longitude')
                                    ->label(__('filament.resources.service_transfer_location_fields.longitude'))
                                    ->numeric()
                                    ->nullable(),
                                TextInput::make('airport_code')
                                    ->label(__('filament.resources.service_transfer_location_fields.airport_code'))
                                    ->maxLength(10)
                                    ->nullable(),
                                Toggle::make('is_active')
                                    ->label(__('filament.resources.service_transfer_location_fields.is_active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.service_transfer_location_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.service_transfer_location_columns.id'))
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('filament.resources.service_transfer_location_fields.is_active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('locationType.name')
                    ->label(__('filament.resources.service_transfer_location_columns.type'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.service_transfer_location_columns.name'))
                    ->searchable(query: function ($query, $search): void {
                        $query->whereHas('translations', function ($q) use ($search): void {
                            $q->where('name', 'like', '%'.$search.'%');
                        });
                    }),
                TextColumn::make('airport_code')
                    ->label(__('filament.resources.service_transfer_location_columns.airport_code'))
                    ->searchable(),
                TextColumn::make('city.name')
                    ->label(__('filament.resources.service_transfer_location_columns.city')),
            ])
            ->filters([
                SelectFilter::make('service_transfer_location_type_id')
                    ->label(__('filament.resources.service_transfer_location_columns.type'))
                    ->options(
                        fn () => ServiceTransferLocationType::query()
                            ->ordered()
                            ->where('active', true)
                            ->with(['translations.language.locale'])
                            ->get()
                            ->mapWithKeys(fn (ServiceTransferLocationType $t) => [$t->id => ($t->name !== '' ? $t->name : $t->code)])
                            ->all()
                    )
                    ->searchable()
                    ->preload(),
                SelectFilter::make('city_id')
                    ->label(__('filament.resources.service_transfer_location_columns.city'))
                    ->options(function (): array {
                        $ids = ServiceTransferLocation::query()
                            ->whereNotNull('city_id')
                            ->distinct()
                            ->pluck('city_id');

                        if ($ids->isEmpty()) {
                            return [];
                        }

                        return LmpCity::query()
                            ->whereIn('id', $ids)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all();
                    })
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('id')
            ->modifyQueryUsing(fn ($query) => $query->with(['translations.language.locale', 'locationType.translations.language.locale', 'city']))
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['airport_code', 'address'];
    }

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    {
        $term = '%'.$search.'%';
        $query->orWhereHas('translations', function ($q) use ($term): void {
            $q->where('name', 'like', $term);
        });
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceTransferLocations::route('/'),
            'create' => Pages\CreateServiceTransferLocation::route('/create'),
            'view' => Pages\ViewServiceTransferLocation::route('/{record}'),
            'edit' => Pages\EditServiceTransferLocation::route('/{record}/edit'),
        ];
    }
}
