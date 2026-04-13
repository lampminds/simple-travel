<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanUserPriceResource\Pages;
use App\Models\Language;
use App\Models\PlanUserPrice;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class PlanUserPriceResource extends LmpResource
{
    protected static ?string $model = PlanUserPrice::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'filament.resources.plan_user_price';

    protected static ?string $pluralModelLabel = 'filament.resources.plan_user_prices';

    protected static ?string $recordTitleAttribute = 'up_to';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_plans';

    protected static ?int $navigationSort = 3;

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
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        $languages = Language::query()->with('locale')->orderBy('id')->get();

        $translationSections = $languages->map(function (Language $lang) {
            return Section::make($lang->display_name)
                ->schema([
                    TextInput::make("translations.{$lang->id}.price")
                        ->label(__('filament.resources.plan_user_price_fields.price'))
                        ->numeric()
                        ->minValue(0)
                        ->step(0.01),
                    TextInput::make("translations.{$lang->id}.description")
                        ->label(__('filament.resources.plan_user_price_fields.description'))
                        ->maxLength(255),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.plan_user_price_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('up_to')
                                    ->label(__('filament.resources.plan_user_price_fields.up_to'))
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->unique(ignoreRecord: true)
                                    ->helperText(__('filament.resources.plan_user_price_fields.up_to_help')),
                            ]),
                        ]),
                    Tab::make(__('filament.resources.plan_user_price_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.plan_user_price_columns.id'))
                    ->sortable(),
                TextColumn::make('up_to')
                    ->label(__('filament.resources.plan_user_price_columns.up_to'))
                    ->sortable()
                    ->formatStateUsing(fn ($state) => __('filament.resources.plan_user_price_columns.up_to_format', ['count' => $state])),
                TextColumn::make('price')
                    ->label(__('filament.resources.plan_user_price_columns.price'))
                    ->sortable(false),
            ])
            ->defaultSort('up_to')
            ->modifyQueryUsing(fn ($query) => $query->with(['translations.language.locale']));
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlanUserPrices::route('/'),
            'create' => Pages\CreatePlanUserPrice::route('/create'),
            'edit' => Pages\EditPlanUserPrice::route('/{record}/edit'),
        ];
    }
}
