<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceGastronomyMenuResource\Pages;
use App\Models\Language;
use App\Models\ServiceGastronomyMenu;
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
use Illuminate\Database\Eloquent\Builder;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceGastronomyMenuResource extends LmpResource
{
    protected static ?string $model = ServiceGastronomyMenu::class;

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $modelLabel = 'filament.resources.service_gastronomy_menu';

    protected static ?string $pluralModelLabel = 'filament.resources.service_gastronomy_menus';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_gastronomy';

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

        $translationSections = $languages->map(function (Language $lang) {
            return Section::make($lang->display_name)
                ->schema([
                    TextInput::make("translations.{$lang->id}.name")
                        ->label(__('filament.resources.service_gastronomy_menu_fields.name'))
                        ->maxLength(255),
                    Textarea::make("translations.{$lang->id}.description")
                        ->label(__('filament.resources.service_gastronomy_menu_fields.description'))
                        ->rows(3),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()->tabs([
                Tab::make(__('filament.resources.service_gastronomy_menu_tabs.general'))
                    ->schema([
                        Section::make('')->schema([
                            TextInput::make('code')
                                ->label(__('filament.resources.service_gastronomy_menu_fields.code'))
                                ->placeholder(__('filament.resources.service_gastronomy_menu_fields.code'))
                                ->required()
                                ->maxLength(255),
                            Toggle::make('is_default')
                                ->label(__('filament.resources.service_gastronomy_menu_fields.is_default'))
                                ->default(false),
                            Toggle::make('active')
                                ->label(__('filament.common.active'))
                                ->default(true),
                        ])->columns(2),
                    ]),
                Tab::make(__('filament.resources.service_gastronomy_menu_tabs.translations'))
                    ->schema($translationSections),
            ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.service_gastronomy_menu_columns.id'))
                    ->sortable(),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_default')
                    ->label(__('filament.resources.service_gastronomy_menu_columns.is_default'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.service_gastronomy_menu_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.service_gastronomy_menu_columns.name'))
                    ->searchable(query: function ($query, $search): void {
                        $query->whereHas('translations', function ($q) use ($search): void {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                    }),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn ($query) => $query->with(['translations.language.locale']));
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code'];
    }

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    {
        $term = '%' . $search . '%';
        $query->orWhere('code', 'like', $term)
            ->orWhereHas('translations', function ($q) use ($term): void {
                $q->where('name', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceGastronomyMenus::route('/'),
            'create' => Pages\CreateServiceGastronomyMenu::route('/create'),
            'view' => Pages\ViewServiceGastronomyMenu::route('/{record}'),
            'edit' => Pages\EditServiceGastronomyMenu::route('/{record}/edit'),
        ];
    }
}

