<?php

namespace App\Filament\Resources\Settings;

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
            Section::make('')
                ->schema([
                    Select::make('parameter_definition_id')
                        ->label(__('filament.resources.parameter_value_fields.parameter_definition_id'))
                        ->relationship(
                            name: 'parameterDefinition',
                            titleAttribute: 'code',
                            modifyQueryUsing: fn (Builder $query) => $query->orderBy('category')->orderBy('code')
                        )
                        ->getOptionLabelFromRecordUsing(
                            fn (ParameterDefinition $record): string => ($record->category !== '' ? $record->category.' — ' : '').$record->code
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
                        ->visible(function (Get $get): bool {
                            $id = $get('parameter_definition_id');

                            return $id && ParameterDefinition::query()->whereKey($id)->value('scope') === 'tenant';
                        })
                        ->required(function (Get $get): bool {
                            $id = $get('parameter_definition_id');

                            return $id && ParameterDefinition::query()->whereKey($id)->value('scope') === 'tenant';
                        })
                        ->helperText(__('filament.resources.parameter_value_fields.account_help')),
                    Textarea::make('value')
                        ->label(__('filament.resources.parameter_value_fields.value'))
                        ->rows(6)
                        ->nullable()
                        ->columnSpanFull()
                        ->helperText(__('filament.resources.parameter_value_fields.value_help')),
                ])
                ->columns(2),
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
                    ->sortable(),
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
