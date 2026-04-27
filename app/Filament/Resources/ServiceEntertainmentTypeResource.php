<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceEntertainmentTypeResource\Pages;
use App\Models\Language;
use App\Models\ServiceEntertainmentType;
use App\Models\ServiceEntertainmentTypeCategory;
use BackedEnum;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceEntertainmentTypeResource extends LmpResource
{
    protected static ?string $model = ServiceEntertainmentType::class;

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?string $modelLabel = 'filament.resources.service_entertainment_type';

    protected static ?string $pluralModelLabel = 'filament.resources.service_entertainment_types';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_entertainments';

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
                        ->label(__('filament.resources.service_entertainment_type_fields.name'))
                        ->maxLength(255),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.service_entertainment_type_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                Select::make('service_entertainment_type_category_id')
                                    ->label(__('filament.resources.service_entertainment_type_fields.category'))
                                    ->options(
                                        fn () => ServiceEntertainmentTypeCategory::query()
                                            ->with(['translations.language.locale'])
                                            ->ordered()
                                            ->where('active', true)
                                            ->get()
                                            ->mapWithKeys(fn (ServiceEntertainmentTypeCategory $cat) => [$cat->id => $cat->name ?: $cat->code])
                                    )
                                    ->searchable()
                                    ->required(),
                                TextInput::make('code')
                                    ->label(__('filament.resources.service_entertainment_type_fields.code'))
                                    ->placeholder(__('filament.resources.service_entertainment_type_fields.code'))
                                    ->required()
                                    ->maxLength(255),
                                Toggle::make('active')
                                    ->label(__('filament.common.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.service_entertainment_type_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.service_entertainment_type_columns.id'))
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('filament.resources.service_entertainment_type_columns.category'))
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('service_entertainment_type_category_id', $direction)),
                IconColumn::make('active')
                    ->label(__('filament.common.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.service_entertainment_type_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.service_entertainment_type_columns.name'))
                    ->searchable(query: function ($query, $search): void {
                        $query->whereHas('translations', function ($q) use ($search): void {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                    }),
            ])
            ->filters([
                SelectFilter::make('service_entertainment_type_category_id')
                    ->label(__('filament.resources.service_entertainment_type_columns.category'))
                    ->options(
                        fn () => ServiceEntertainmentTypeCategory::query()
                            ->with(['translations.language.locale'])
                            ->ordered()
                            ->where('active', true)
                            ->get()
                            ->mapWithKeys(fn (ServiceEntertainmentTypeCategory $cat) => [$cat->id => $cat->name ?: $cat->code])
                    )
                    ->searchable(),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn ($query) => $query->with(['translations.language.locale', 'category']));
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code'];
    }

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    {
        $term = '%' . $search . '%';
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
            'index' => Pages\ListServiceEntertainmentTypes::route('/'),
            'create' => Pages\CreateServiceEntertainmentType::route('/create'),
            'view' => Pages\ViewServiceEntertainmentType::route('/{record}'),
            'edit' => Pages\EditServiceEntertainmentType::route('/{record}/edit'),
        ];
    }
}

