<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Account;
use App\Models\Currency;
use App\Models\Language;
use App\Models\LmpCity;
use App\Models\Service;
use App\Models\ServiceActivity;
use App\Models\ServiceDetailTopic;
use App\Models\ServiceType;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceResource extends LmpResource
{
    protected static ?string $model = Service::class;

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $modelLabel = 'filament.resources.service';

    protected static ?string $pluralModelLabel = 'filament.resources.services';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_services';

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
                        ->label(__('filament.resources.service_fields.name'))
                        ->maxLength(255),
                    Textarea::make("translations.{$lang->id}.description")
                        ->label(__('filament.resources.service_fields.description'))
                        ->rows(3),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.service_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                Select::make('account_id')
                                    ->label(__('filament.resources.service_fields.account_id'))
                                    ->options(fn () => Account::query()->orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),
                                Select::make('service_type_id')
                                    ->label(__('filament.resources.service_fields.service_type_id'))
                                    ->options(fn () => ServiceType::query()->with(['translations.language.locale'])->ordered()->where('active', true)->get()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),
                                Select::make('city_id')
                                    ->label(__('filament.resources.service_fields.city_id'))
                                    ->searchable()
                                    ->getSearchResultsUsing(function (string $search): array {
                                        return LmpCity::query()
                                            ->where('name', 'like', '%' . $search . '%')
                                            ->orderBy('name')
                                            ->limit(50)
                                            ->pluck('name', 'id')
                                            ->all();
                                    })
                                    ->getOptionLabelUsing(fn ($value): ?string => $value ? LmpCity::find($value)?->name : null)
                                    ->nullable(),
                                Select::make('status')
                                    ->label(__('filament.resources.service_fields.status'))
                                    ->options([
                                        'active' => __('filament.resources.service_status.active'),
                                        'onhold' => __('filament.resources.service_status.onhold'),
                                        'inactive' => __('filament.resources.service_status.inactive'),
                                        'terminated' => __('filament.resources.service_status.terminated'),
                                    ])
                                    ->default('active')
                                    ->required(),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.service_tabs.translations'))
                        ->schema($translationSections),
                    Tab::make(__('filament.resources.service_tabs.variants'))
                        ->schema([
                            Repeater::make('serviceVariants')
                                ->relationship()
                                ->schema([
                                    TextInput::make('sku')
                                        ->label(__('filament.resources.service_variant_fields.sku'))
                                        ->required()
                                        ->maxLength(255),
                                    Select::make('status')
                                        ->label(__('filament.resources.service_variant_fields.status'))
                                        ->options([
                                            'active' => __('filament.resources.service_variant_status.active'),
                                            'inactive' => __('filament.resources.service_variant_status.inactive'),
                                            'hidden' => __('filament.resources.service_variant_status.hidden'),
                                        ])
                                        ->default('active')
                                        ->required(),
                                    TextInput::make('capacity_min')
                                        ->label(__('filament.resources.service_variant_fields.capacity_min'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->nullable(),
                                    TextInput::make('capacity_max')
                                        ->label(__('filament.resources.service_variant_fields.capacity_max'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->nullable(),
                                    TextInput::make('duration_minutes')
                                        ->label(__('filament.resources.service_variant_fields.duration_minutes'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->nullable(),
                                    Select::make('pricing_type')
                                        ->label(__('filament.resources.service_variant_fields.pricing_type'))
                                        ->options([
                                            'per_person' => __('filament.resources.service_variant_pricing_type.per_person'),
                                            'per_unit' => __('filament.resources.service_variant_pricing_type.per_unit'),
                                            'per_room' => __('filament.resources.service_variant_pricing_type.per_room'),
                                            'per_vehicle' => __('filament.resources.service_variant_pricing_type.per_vehicle'),
                                        ])
                                        ->required(),
                                    TextInput::make('base_price')
                                        ->label(__('filament.resources.service_variant_fields.base_price'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->required()
                                        ->step(0.01),
                                    Select::make('currency_id')
                                        ->label(__('filament.resources.service_variant_fields.currency'))
                                        ->options(
                                            fn () => Currency::query()
                                                ->with('lmpCurrency')
                                                ->orderBy('id')
                                                ->get()
                                                ->mapWithKeys(fn (Currency $c) => [$c->id => $c->display_name])
                                        )
                                        ->required()
                                        ->searchable(),
                                    Select::make('inventory_type')
                                        ->label(__('filament.resources.service_variant_fields.inventory_type'))
                                        ->options([
                                            'unlimited' => __('filament.resources.service_variant_inventory_type.unlimited'),
                                            'fixed' => __('filament.resources.service_variant_inventory_type.fixed'),
                                            'request' => __('filament.resources.service_variant_inventory_type.request'),
                                        ])
                                        ->required()
                                        ->live(),
                                    TextInput::make('inventory_total')
                                        ->label(__('filament.resources.service_variant_fields.inventory_total'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->nullable()
                                        ->visible(fn ($get) => $get('inventory_type') === 'fixed'),
                                    Select::make('booking_type')
                                        ->label(__('filament.resources.service_variant_fields.booking_type'))
                                        ->options([
                                            'instant' => __('filament.resources.service_variant_booking_type.instant'),
                                            'request' => __('filament.resources.service_variant_booking_type.request'),
                                        ])
                                        ->default('request')
                                        ->afterStateHydrated(function (Select $component, $state): void {
                                            if (blank($state)) {
                                                $component->state('request');
                                            }
                                        })
                                        ->required(),
                                    TextInput::make('min_advance_booking_hours')
                                        ->label(__('filament.resources.service_variant_fields.min_advance_booking_hours'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->nullable(),
                                    TextInput::make('max_advance_booking_days')
                                        ->label(__('filament.resources.service_variant_fields.max_advance_booking_days'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->nullable(),
                                    TimePicker::make('start_time')
                                        ->label(__('filament.resources.service_variant_fields.start_time'))
                                        ->seconds(false)
                                        ->nullable(),
                                    TimePicker::make('end_time')
                                        ->label(__('filament.resources.service_variant_fields.end_time'))
                                        ->seconds(false)
                                        ->nullable(),
                                    TextInput::make('sort_order')
                                        ->label(__('filament.resources.service_variant_fields.sort_order'))
                                        ->numeric()
                                        ->default(9999)
                                        ->minValue(0),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->addActionLabel(__('filament.resources.service_variant_fields.add'))
                                ->reorderable()
                                ->orderColumn('sort_order'),
                        ])
                        ->visibleOn(['edit', 'view']),
                    Tab::make(__('filament.resources.service_tabs.details'))
                        ->schema([
                            Repeater::make('serviceDetails')
                                ->relationship()
                                ->schema([
                                    Select::make('service_detail_topic_id')
                                        ->label(__('filament.resources.service_detail_fields.service_detail_topic_id'))
                                        ->options(
                                            fn () => ServiceDetailTopic::query()
                                                ->with('translations.language.locale')
                                                ->where('active', true)
                                                ->orderBy('sort_order')
                                                ->get()
                                                ->mapWithKeys(fn (ServiceDetailTopic $topic) => [$topic->id => $topic->name ?: $topic->code])
                                        )
                                        ->required()
                                        ->searchable(),
                                    Select::make('language_id')
                                        ->label(__('filament.resources.service_detail_fields.language_id'))
                                        ->options(
                                            fn () => Language::query()
                                                ->with('locale')
                                                ->orderBy('id')
                                                ->get()
                                                ->mapWithKeys(fn (Language $lang) => [$lang->id => $lang->display_name])
                                        )
                                        ->required()
                                        ->searchable(),
                                    Textarea::make('description')
                                        ->label(__('filament.resources.service_detail_fields.description'))
                                        ->rows(4),
                                    Toggle::make('active')
                                        ->label(__('filament.resources.service_detail_fields.active'))
                                        ->default(true),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->addActionLabel(__('filament.resources.service_detail_fields.add'))
                                    ->reorderable()
                                    ->orderColumn('sort_order'),
                        ])
                        ->visibleOn(['edit', 'view']),
                    Tab::make(__('filament.resources.service_tabs.activities'))
                        ->schema([
                            Section::make('')->schema([
                                Select::make('activities')
                                    ->label(__('filament.resources.service_fields.activities'))
                                    ->relationship(
                                        name: 'activities',
                                        // DB column must exist; `name` is a translation accessor only.
                                        titleAttribute: 'code',
                                        modifyQueryUsing: fn ($query) => $query->with(['translations.language.locale'])->ordered()->where('active', true)
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn (ServiceActivity $record): string => $record->name !== '' ? $record->name : (string) $record->code
                                    )
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->helperText(__('filament.resources.service_fields.activities_help')),
                            ])->columns(1),
                        ])
                        ->visibleOn(['edit', 'view']),
                    Tab::make(__('filament.resources.service_tabs.media'))
                        ->schema([
                            Section::make('')->schema([
                                SpatieMediaLibraryFileUpload::make('main_image')
                                    ->label(__('filament.resources.service_media.main_image'))
                                    ->collection(Service::MEDIA_COLLECTION_MAIN)
                                    ->disk('service_media')
                                    ->conversion(Service::MEDIA_CONVERSION_SMALL)
                                    ->image()
                                    ->maxSize(Service::MEDIA_MAX_FILE_SIZE_KB)
                                    ->helperText(__('filament.resources.service_media.max_image_size_hint'))
                                    ->columnSpanFull(),
                                SpatieMediaLibraryFileUpload::make('gallery_images')
                                    ->label(__('filament.resources.service_media.gallery'))
                                    ->collection(Service::MEDIA_COLLECTION_GALLERY)
                                    ->disk('service_media')
                                    ->conversion(Service::MEDIA_CONVERSION_SMALL)
                                    ->image()
                                    ->multiple()
                                    ->reorderable()
                                    ->appendFiles()
                                    ->maxSize(Service::MEDIA_MAX_FILE_SIZE_KB)
                                    ->helperText(
                                        __('filament.resources.service_media.gallery_help')
                                            .' '
                                            .__('filament.resources.service_media.max_image_size_hint')
                                    )
                                    ->columnSpanFull(),
                            ])->columns(1),
                        ])
                        ->visibleOn(['edit', 'view']),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                SpatieMediaLibraryImageColumn::make('main_thumb')
                    ->label('Thumb')
                    ->collection(Service::MEDIA_COLLECTION_MAIN)
                    ->conversion(Service::MEDIA_CONVERSION_THUMBNAIL)
                    ->square()
                    ->defaultImageUrl(null),
                TextColumn::make('id')
                    ->label(__('filament.resources.service_columns.id'))
                    ->sortable(),
                TextColumn::make('account.name')
                    ->label(__('filament.resources.service_columns.account'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serviceType.name')
                    ->label(__('filament.resources.service_columns.service_type'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.service_columns.name'))
                    ->searchable(query: function ($query, $search): void {
                        $query->whereHas('translations', function ($q) use ($search): void {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                    }),
                TextColumn::make('status')
                    ->label(__('filament.resources.service_columns.status'))
                    ->formatStateUsing(fn (string $state) => __("filament.resources.service_status.{$state}"))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('account_id')
                    ->label(__('filament.resources.service_columns.account'))
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('service_type_id')
                    ->label(__('filament.resources.service_columns.service_type'))
                    ->relationship('serviceType', 'code', fn ($query) => $query->where('active', true))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label(__('filament.resources.service_columns.status'))
                    ->options([
                        'active' => __('filament.resources.service_status.active'),
                        'onhold' => __('filament.resources.service_status.onhold'),
                        'inactive' => __('filament.resources.service_status.inactive'),
                        'terminated' => __('filament.resources.service_status.terminated'),
                    ]),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['translations.language.locale', 'account', 'serviceType', 'media']));
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    {
        $term = '%' . $search . '%';
        $query->whereHas('translations', function ($q) use ($term): void {
            $q->where('name', 'like', $term);
        });
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    /**
     * No relation managers: service details are edited in the "Details" tab (repeater),
     * same as variants.
     */
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
