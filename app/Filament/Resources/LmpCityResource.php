<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\AdministrationCluster;
use App\Filament\Resources\LmpCityResource\Pages;
use App\Models\Language;
use App\Models\LmpCity;
use App\Models\LmpCountry;
use App\Models\LmpState;
use App\Services\CitySystemTransferLocationsGeneratorService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Throwable;

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
                TextColumn::make('system_transfer_locations_count')
                    ->label(__('filament.resources.lmp_city_columns.system_transfer_locations'))
                    ->getStateUsing(fn (LmpCity $record): int => app(CitySystemTransferLocationsGeneratorService::class)
                        ->systemCatalogCountForCity((int) $record->getKey()))
                    ->sortable(false),
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
                    Action::make('generateTransferLocations')
                        ->label(__('filament.resources.lmp_city_actions.generate_transfer_locations'))
                        ->icon('heroicon-o-sparkles')
                        ->modalHeading(__('filament.resources.lmp_city_actions.generate_transfer_locations_heading'))
                        ->modalDescription(__('filament.resources.lmp_city_actions.generate_transfer_locations_description'))
                        ->form([
                            Toggle::make('replace_existing')
                                ->label(__('filament.resources.lmp_city_actions.replace_existing'))
                                ->helperText(__('filament.resources.lmp_city_actions.replace_existing_help'))
                                ->default(false),
                            Toggle::make('translate_to_other_languages')
                                ->label(__('filament.resources.lmp_city_actions.translate_to_other_languages'))
                                ->helperText(__('filament.resources.lmp_city_actions.translate_to_other_languages_help'))
                                ->default(true),
                            Select::make('source_language_id')
                                ->label(__('filament.resources.lmp_city_actions.source_language'))
                                ->options(fn (): array => Language::query()
                                    ->with('locale')
                                    ->orderBy('list_order')
                                    ->orderBy('id')
                                    ->get()
                                    ->mapWithKeys(fn (Language $lang) => [$lang->id => $lang->display_name])
                                    ->all())
                                ->default(fn (): ?int => Language::query()
                                    ->with('locale')
                                    ->get()
                                    ->first(fn (Language $lang): bool => str_starts_with(
                                        strtolower((string) ($lang->locale?->tag ?? '')),
                                        'es'
                                    ))?->id)
                                ->required(),
                            TextInput::make('max_suggestions')
                                ->label(__('filament.resources.lmp_city_actions.max_suggestions'))
                                ->numeric()
                                ->minValue(5)
                                ->maxValue(50)
                                ->default(30)
                                ->required(),
                            Textarea::make('additional_context')
                                ->label(__('filament.resources.lmp_city_actions.additional_context'))
                                ->rows(3)
                                ->maxLength(2000),
                        ])
                        ->action(function (array $data, LmpCity $record): void {
                            $service = app(CitySystemTransferLocationsGeneratorService::class);

                            try {
                                $result = $service->generateFromAi(
                                    $record,
                                    (bool) ($data['replace_existing'] ?? false),
                                    (bool) ($data['translate_to_other_languages'] ?? true),
                                    isset($data['source_language_id']) ? (int) $data['source_language_id'] : null,
                                    (int) ($data['max_suggestions'] ?? 30),
                                    $data['additional_context'] ?? null,
                                );
                            } catch (Throwable $e) {
                                Notification::make()
                                    ->title(__('filament.resources.lmp_city_actions.generate_failed_title'))
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $created = (int) ($result['created'] ?? 0);
                            $skipped = (int) ($result['skipped'] ?? 0);
                            $removed = (int) ($result['removed'] ?? 0);
                            $fallbacks = (int) ($result['translation_fallbacks'] ?? 0);

                            if ($created === 0) {
                                Notification::make()
                                    ->title(__('filament.resources.lmp_city_actions.generate_none_title'))
                                    ->body(__('filament.resources.lmp_city_actions.generate_none_body', [
                                        'skipped' => $skipped,
                                    ]))
                                    ->warning()
                                    ->send();

                                return;
                            }

                            $body = __('filament.resources.lmp_city_actions.generate_success_body', [
                                'created' => $created,
                                'skipped' => $skipped,
                                'removed' => $removed,
                                'ai' => (int) ($result['ai_count'] ?? 0),
                                'openai_calls' => (int) ($result['openai_calls'] ?? 1),
                            ]);

                            if ($fallbacks > 0) {
                                $body .= ' '.__('filament.resources.lmp_city_actions.generate_translation_fallbacks', [
                                    'count' => $fallbacks,
                                ]);
                            }

                            Notification::make()
                                ->title(__('filament.resources.lmp_city_actions.generate_success_title'))
                                ->body($body)
                                ->success()
                                ->send();
                        }),
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
