<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicePlanResource\Pages;
use App\Filament\Resources\ServicePlanResource\RelationManagers\ServicePlanItemsRelationManager;
use App\Models\Language;
use App\Models\ServicePlan;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServicePlanResource extends LmpResource
{
    protected static ?string $model = ServicePlan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $modelLabel = 'filament.resources.service_plan';

    protected static ?string $pluralModelLabel = 'filament.resources.service_plans';

    protected static ?string $recordTitleAttribute = 'code';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_service_plans';

    public static function getModelLabel(): string
    {
        return (string) __(static::$modelLabel);
    }

    public static function getPluralModelLabel(): string
    {
        return (string) __(static::$pluralModelLabel);
    }

    public static function getRelations(): array
    {
        return [
            ServicePlanItemsRelationManager::class,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        $languages = Language::query()->with('lmpLanguage')->orderBy('id')->get();

        $translationSections = $languages->map(function (Language $lang) {
            return Section::make($lang->display_name)
                ->schema([
                    TextInput::make("translations.{$lang->id}.price")
                        ->label(__('filament.resources.service_plan_fields.price'))
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01),
                    TextInput::make("translations.{$lang->id}.name")
                        ->label(__('filament.resources.service_plan_fields.name'))
                        ->maxLength(255),
                    Textarea::make("translations.{$lang->id}.description")
                        ->label(__('filament.resources.service_plan_fields.description'))
                        ->rows(2),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.service_plan_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('code')
                                    ->label(__('filament.resources.service_plan_fields.code'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Toggle::make('active')
                                    ->label(__('filament.resources.service_plan_fields.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.service_plan_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.service_plan_columns.id'))
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.service_plan_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.service_plan_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label(__('filament.resources.service_plan_columns.price'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.resources.service_plan_columns.active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn ($query) => $query->with(['translations.language.lmpLanguage']));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServicePlans::route('/'),
            'create' => Pages\CreateServicePlan::route('/create'),
            'view' => Pages\ViewServicePlan::route('/{record}'),
            'edit' => Pages\EditServicePlan::route('/{record}/edit'),
        ];
    }
}
