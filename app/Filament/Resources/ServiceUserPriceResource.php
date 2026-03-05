<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceUserPriceResource\Pages;
use App\Models\Language;
use App\Models\ServiceUserPrice;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceUserPriceResource extends LmpResource
{
    protected static ?string $model = ServiceUserPrice::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'filament.resources.service_user_price';

    protected static ?string $pluralModelLabel = 'filament.resources.service_user_prices';

    protected static ?string $recordTitleAttribute = 'up_to';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_service_plans';

    public static function getModelLabel(): string
    {
        return (string) __(static::$modelLabel);
    }

    public static function getPluralModelLabel(): string
    {
        return (string) __(static::$pluralModelLabel);
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
                        ->label(__('filament.resources.service_user_price_fields.price'))
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01),
                    TextInput::make("translations.{$lang->id}.description")
                        ->label(__('filament.resources.service_user_price_fields.description'))
                        ->maxLength(255),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.service_user_price_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('up_to')
                                    ->label(__('filament.resources.service_user_price_fields.up_to'))
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->unique(ignoreRecord: true)
                                    ->helperText(__('filament.resources.service_user_price_fields.up_to_help')),
                            ]),
                        ]),
                    Tab::make(__('filament.resources.service_user_price_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.service_user_price_columns.id'))
                    ->sortable(),
                TextColumn::make('up_to')
                    ->label(__('filament.resources.service_user_price_columns.up_to'))
                    ->sortable()
                    ->formatStateUsing(fn ($state) => __('filament.resources.service_user_price_columns.up_to_format', ['count' => $state])),
                TextColumn::make('price')
                    ->label(__('filament.resources.service_user_price_columns.price'))
                    ->sortable(false),
            ])
            ->defaultSort('up_to')
            ->modifyQueryUsing(fn ($query) => $query->with(['translations.language.lmpLanguage']));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceUserPrices::route('/'),
            'create' => Pages\CreateServiceUserPrice::route('/create'),
            'edit' => Pages\EditServiceUserPrice::route('/{record}/edit'),
        ];
    }
}
