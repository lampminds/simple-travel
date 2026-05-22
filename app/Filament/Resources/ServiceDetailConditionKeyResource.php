<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CatalogCluster;
use App\Filament\Resources\ServiceDetailConditionKeyResource\Pages;
use App\Models\ServiceDetailConditionKey;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceDetailConditionKeyResource extends LmpResource
{
    protected static ?string $model = ServiceDetailConditionKey::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $modelLabel = 'filament.resources.service_detail_condition_key';

    protected static ?string $pluralModelLabel = 'filament.resources.service_detail_condition_keys';

    protected static ?string $recordTitleAttribute = 'code';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_catalog_conditions';

    protected static ?int $navigationSort = 0;

    protected static ?string $cluster = CatalogCluster::class;

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

        return $group instanceof \UnitEnum
            ? $group->value
            : ($group !== null ? (string) __($group) : null);
    }

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        $categoryOptions = collect(ServiceDetailConditionKey::CATEGORIES)
            ->mapWithKeys(fn (string $category): array => [
                $category => __('filament.resources.service_detail_condition_key_categories.'.$category),
            ])
            ->all();

        return [
            Section::make('')
                ->schema([
                    Select::make('category')
                        ->label(__('filament.resources.service_detail_condition_key_fields.category'))
                        ->options($categoryOptions)
                        ->required()
                        ->searchable()
                        ->native(false),
                    TextInput::make('code')
                        ->label(__('filament.resources.service_detail_condition_key_fields.code'))
                        ->required()
                        ->maxLength(255)
                        ->rules(['alpha_dash'])
                        ->unique(ignoreRecord: true)
                        ->helperText(__('filament.resources.service_detail_condition_key_fields.code_help')),
                    Textarea::make('description')
                        ->label(__('filament.resources.service_detail_condition_key_fields.description'))
                        ->required()
                        ->rows(4)
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
                    ->label(__('filament.resources.service_detail_condition_key_columns.id'))
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.service_detail_condition_key_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label(__('filament.resources.service_detail_condition_key_columns.category'))
                    ->formatStateUsing(function (string $state): string {
                        $key = 'filament.resources.service_detail_condition_key_categories.'.$state;
                        $label = __($key);

                        return $label !== $key ? $label : $state;
                    })
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('filament.resources.service_detail_condition_key_columns.description'))
                    ->wrap()
                    ->searchable()
                    ->limit(80),
            ])
            ->defaultSort('category')
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
        return ['code', 'category', 'description'];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceDetailConditionKeys::route('/'),
            'create' => Pages\CreateServiceDetailConditionKey::route('/create'),
            'view' => Pages\ViewServiceDetailConditionKey::route('/{record}'),
            'edit' => Pages\EditServiceDetailConditionKey::route('/{record}/edit'),
        ];
    }
}
