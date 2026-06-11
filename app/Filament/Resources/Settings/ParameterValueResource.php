<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Clusters\AdministrationCluster;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Settings\ParameterValueResource\Pages;
use App\Models\ParameterDefinition;
use App\Models\ParameterValue;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
class ParameterValueResource extends BaseResource
{
    protected static ?string $model = ParameterValue::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $modelLabel = 'filament.resources.parameter_value';

    protected static ?string $pluralModelLabel = 'filament.resources.parameter_values';

    protected static ?string $recordTitleAttribute = 'id';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_parameters';

    protected static ?string $cluster = AdministrationCluster::class;

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
        if (property_exists(static::getModel(), 'dont_use_audit')) {
            return $schema->schema(
                array_map(
                    fn ($c) => $c->columnSpanFull(),
                    static::getMainFormSchema($schema),
                ),
            );
        }

        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    /**
     * @return array<string, string>
     */
    protected static function valueSelectOptions(int $definitionId): array
    {
        if ($definitionId <= 0) {
            return [];
        }

        $def = ParameterDefinition::query()
            ->with(['parameterOptions.translations.language.locale'])
            ->find($definitionId);

        return $def ? $def->optionValueToLabelMap() : [];
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Section::make('')
                ->schema([
                    Select::make('parameter_definition_id')
                        ->label(__('filament.resources.parameter_value_fields.parameter_definition_id'))
                        ->relationship(
                            name: 'parameterDefinition',
                            titleAttribute: 'code',
                            modifyQueryUsing: fn (Builder $query) => $query
                                ->orderBy('category')
                                ->orderBy('subcategory')
                                ->orderBy('code')
                        )
                        ->getOptionLabelFromRecordUsing(
                            fn (ParameterDefinition $record): string => implode(' — ', array_filter(
                                [$record->category, $record->subcategory, $record->code],
                                fn (?string $part): bool => filled($part),
                            ))
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live(onBlur: false)
                        ->disabled(fn (string $operation): bool => $operation === 'edit')
                        ->dehydrated(true)
                        ->helperText(__('filament.resources.parameter_value_fields.definition_help')),
                    Select::make('account_id')
                        ->label(__('filament.resources.parameter_value_fields.account_id'))
                        ->relationship('account', 'name', modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'))
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->placeholder(__('filament.resources.parameter_value_fields.account_placeholder'))
                        ->visible(fn (Get $get): bool => (bool) $get('parameter_definition_id'))
                        ->disabled(function (Get $get): bool {
                            $id = $get('parameter_definition_id');
                            if (! $id) {
                                return true;
                            }

                            return ParameterDefinition::query()->whereKey($id)->value('scope') === 'system';
                        })
                        ->dehydrated(function (Get $get): bool {
                            $id = $get('parameter_definition_id');
                            if (! $id) {
                                return false;
                            }

                            return ParameterDefinition::query()->whereKey($id)->value('scope') === 'tenant';
                        })
                        ->helperText(function (Get $get): ?string {
                            $id = $get('parameter_definition_id');
                            if (! $id) {
                                return null;
                            }

                            $scope = ParameterDefinition::query()->whereKey($id)->value('scope');

                            return $scope === 'system'
                                ? __('filament.resources.parameter_value_fields.account_help_system')
                                : __('filament.resources.parameter_value_fields.account_help');
                        }),
                    Select::make('value_select')
                        ->label(__('filament.resources.parameter_value_fields.value'))
                        ->options(fn (Get $get): array => static::valueSelectOptions((int) $get('parameter_definition_id')))
                        ->searchable()
                        ->nullable()
                        ->visible(fn (Get $get): bool => ParameterDefinition::queryUsesOptionBackedValue((int) $get('parameter_definition_id')))
                        ->helperText(__('filament.resources.parameter_value_fields.value_help')),
                    Textarea::make('value_free')
                        ->label(__('filament.resources.parameter_value_fields.value'))
                        ->rows(6)
                        ->nullable()
                        ->visible(fn (Get $get): bool => ! ParameterDefinition::queryUsesOptionBackedValue((int) $get('parameter_definition_id')))
                        ->helperText(__('filament.resources.parameter_value_fields.value_help')),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.parameter_value_columns.id'))
                    ->sortable(),
                TextColumn::make('parameterDefinition.category')
                    ->label(__('filament.resources.parameter_definition_columns.category'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parameterDefinition.code')
                    ->label(__('filament.resources.parameter_definition_columns.code'))
                    ->searchable()
                    ->sortable()
                    ->httpSafeCopyableUsing(
                        fn (ParameterValue $record): string => (string) ($record->parameterDefinition?->code ?? ''),
                    ),
                TextColumn::make('parameterDefinition.scope')
                    ->label(__('filament.resources.parameter_definition_columns.scope'))
                    ->sortable(),
                TextColumn::make('account.name')
                    ->label(__('filament.resources.parameter_value_columns.account'))
                    ->formatStateUsing(function (?string $state, ParameterValue $record): string {
                        if ($record->account_id === null) {
                            return (string) __('filament.resources.parameter_value_account_system');
                        }

                        return (string) ($state ?? '—');
                    })
                    ->sortable(),
                TextColumn::make('value')
                    ->label(__('filament.resources.parameter_value_columns.value'))
                    ->limit(60)
                    ->wrap(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('parameter_definition_id')
                    ->label(__('filament.resources.parameter_value_fields.parameter_definition_id'))
                    ->relationship('parameterDefinition', 'code')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('account_id')
                    ->label(__('filament.resources.parameter_value_fields.account_id'))
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['parameterDefinition', 'account']));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParameterValues::route('/'),
            'create' => Pages\CreateParameterValue::route('/create'),
            'view' => Pages\ViewParameterValue::route('/{record}'),
            'edit' => Pages\EditParameterValue::route('/{record}/edit'),
        ];
    }
}
