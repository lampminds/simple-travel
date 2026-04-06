<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Settings\ParameterDefinitionResource\Pages;
use App\Filament\Resources\Settings\ParameterDefinitionResource\RelationManagers\ParameterValuesRelationManager;
use App\Models\ParameterDefinition;
use BackedEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParameterDefinitionResource extends BaseResource
{
    protected static ?string $model = ParameterDefinition::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $modelLabel = 'filament.resources.parameter_definition';

    protected static ?string $pluralModelLabel = 'filament.resources.parameter_definitions';

    protected static ?string $recordTitleAttribute = 'code';

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
                    TextInput::make('category')
                        ->label(__('filament.resources.parameter_definition_fields.category'))
                        ->maxLength(50)
                        ->required(),
                    TextInput::make('code')
                        ->label(__('filament.resources.parameter_definition_fields.code'))
                        ->maxLength(200)
                        ->required()
                        ->unique(ignoreRecord: true),
                    Select::make('type')
                        ->label(__('filament.resources.parameter_definition_fields.type'))
                        ->options(array_combine(ParameterDefinition::TYPES, ParameterDefinition::TYPES))
                        ->required(),
                    Select::make('scope')
                        ->label(__('filament.resources.parameter_definition_fields.scope'))
                        ->options(array_combine(ParameterDefinition::SCOPES, ParameterDefinition::SCOPES))
                        ->required(),
                    Toggle::make('has_default')
                        ->label(__('filament.resources.parameter_definition_fields.has_default'))
                        ->default(false),
                    Select::make('ui_component')
                        ->label(__('filament.resources.parameter_definition_fields.ui_component'))
                        ->options(static function (): array {
                            $out = [];
                            foreach (ParameterDefinition::UI_COMPONENTS as $value) {
                                $out[$value] = (string) __(
                                    'filament.resources.parameter_definition_ui_components.'.$value
                                );
                            }

                            return $out;
                        })
                        ->required()
                        ->searchable(),
                    KeyValue::make('ui_options')
                        ->label(__('filament.resources.parameter_definition_fields.ui_options'))
                        ->nullable()
                        ->columnSpanFull(),
                    Textarea::make('help')
                        ->label(__('filament.resources.parameter_definition_fields.help'))
                        ->rows(3)
                        ->nullable()
                        ->columnSpanFull(),
                    Textarea::make('comments')
                        ->label(__('filament.resources.parameter_definition_fields.comments'))
                        ->rows(3)
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.parameter_definition_columns.id'))
                    ->sortable(),
                TextColumn::make('category')
                    ->label(__('filament.resources.parameter_definition_columns.category'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.parameter_definition_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('filament.resources.parameter_definition_columns.type'))
                    ->sortable(),
                TextColumn::make('scope')
                    ->label(__('filament.resources.parameter_definition_columns.scope'))
                    ->sortable(),
                IconColumn::make('has_default')
                    ->label(__('filament.resources.parameter_definition_columns.has_default'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('ui_component')
                    ->label(__('filament.resources.parameter_definition_columns.ui_component'))
                    ->formatStateUsing(fn (?string $state): string => $state !== null && $state !== ''
                        ? (string) __('filament.resources.parameter_definition_ui_components.'.$state)
                        : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('category');
    }

    public static function getRelations(): array
    {
        return [
            ParameterValuesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParameterDefinitions::route('/'),
            'create' => Pages\CreateParameterDefinition::route('/create'),
            'view' => Pages\ViewParameterDefinition::route('/{record}'),
            'edit' => Pages\EditParameterDefinition::route('/{record}/edit'),
        ];
    }
}

