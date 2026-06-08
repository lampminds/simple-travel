<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CatalogCluster;
use App\Filament\Resources\ServiceDetailTopicResource\Pages;
use App\Models\Language;
use App\Models\ServiceDetailConditionKey;
use App\Models\ServiceDetailTopic;
use App\Models\ServiceDetailTopicCategory;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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

class ServiceDetailTopicResource extends LmpResource
{
    protected static ?string $model = ServiceDetailTopic::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $modelLabel = 'filament.resources.service_detail_topic';

    protected static ?string $pluralModelLabel = 'filament.resources.service_detail_topics';

    protected static ?string $recordTitleAttribute = 'code';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_catalog_conditions';

    protected static ?int $navigationSort = 2;

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
                    TextInput::make("translations.{$lang->id}.name")
                        ->label(__('filament.resources.service_detail_topic_fields.name'))
                        ->maxLength(255),
                    Textarea::make("translations.{$lang->id}.description")
                        ->label(__('filament.resources.service_detail_topic_fields.description'))
                        ->rows(3),
                ])
                ->columns(2)
                ->collapsible();
        })->all();

        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.service_detail_topic_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                TextInput::make('code')
                                    ->label(__('filament.resources.service_detail_topic_fields.code'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Select::make('service_detail_topic_category_id')
                                    ->label(__('filament.resources.service_detail_topic_fields.category'))
                                    ->relationship(
                                        name: 'category',
                                        titleAttribute: 'code',
                                        modifyQueryUsing: fn ($query) => $query->where('active', true)->orderBy('sort_order')
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (ServiceDetailTopicCategory $record) => $record->name ?: $record->code)
                                    ->placeholder(__('filament.common.select_option'))
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                Select::make('visibility')
                                    ->label(__('filament.resources.service_detail_topic_fields.visibility'))
                                    ->options([
                                        'public' => __('filament.resources.service_detail_topic_visibility.public'),
                                        'operator' => __('filament.resources.service_detail_topic_visibility.operator'),
                                        'internal' => __('filament.resources.service_detail_topic_visibility.internal'),
                                    ])
                                    ->default('public')
                                    ->nullable(),
                                Select::make('scope')
                                    ->label(__('filament.resources.service_detail_topic_fields.scope'))
                                    ->options([
                                        'informational' => __('filament.resources.service_detail_topic_scopes.informational'),
                                        'service' => __('filament.resources.service_detail_topic_scopes.service'),
                                        'commercial' => __('filament.resources.service_detail_topic_scopes.commercial'),
                                        'legal' => __('filament.resources.service_detail_topic_scopes.legal'),
                                    ])
                                    ->default('informational')
                                    ->required(),
                                Select::make('condition_key_id')
                                    ->label(__('filament.resources.service_detail_topic_fields.condition_key'))
                                    ->relationship(
                                        name: 'conditionKey',
                                        titleAttribute: 'code',
                                        modifyQueryUsing: fn ($query) => $query->orderBy('category')->orderBy('code')
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn (ServiceDetailConditionKey $record): string => $record->category.'.'.$record->code
                                    )
                                    ->placeholder(__('filament.common.select_option'))
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                Toggle::make('active')
                                    ->label(__('filament.resources.service_detail_topic_fields.active'))
                                    ->default(true),
                            ])->columns(2),
                        ]),
                    Tab::make(__('filament.resources.service_detail_topic_tabs.translations'))
                        ->schema($translationSections),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.service_detail_topic_columns.id'))
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('filament.resources.service_detail_topic_columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('filament.resources.service_detail_topic_columns.category'))
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('service_detail_topic_category_id', $direction))
                    ->placeholder('—'),
                TextColumn::make('name')
                    ->label(__('filament.resources.service_detail_topic_columns.name'))
                    ->searchable(query: function (Builder $query, string $search): void {
                        $term = '%'.$search.'%';
                        $query->whereHas('translations', function (Builder $q) use ($term): void {
                            $q->where('name', 'like', $term);
                        });
                    }),
                TextColumn::make('visibility')
                    ->label(__('filament.resources.service_detail_topic_columns.visibility'))
                    ->formatStateUsing(fn (?string $state) => $state ? __("filament.resources.service_detail_topic_visibility.{$state}") : '—')
                    ->sortable(),
                TextColumn::make('scope')
                    ->label(__('filament.resources.service_detail_topic_columns.scope'))
                    ->formatStateUsing(fn (?string $state) => $state ? __("filament.resources.service_detail_topic_scopes.{$state}") : '—')
                    ->sortable(),
                TextColumn::make('conditionKey.code')
                    ->label(__('filament.resources.service_detail_topic_columns.condition_key'))
                    ->formatStateUsing(fn ($state, ServiceDetailTopic $record): string => $record->conditionKey
                        ? $record->conditionKey->category.'.'.$record->conditionKey->code
                        : '—')
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('condition_key_id', $direction)),
                IconColumn::make('active')
                    ->label(__('filament.resources.service_detail_topic_columns.active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('service_detail_topic_category_id')
                    ->label(__('filament.resources.service_detail_topic_columns.category'))
                    ->options(
                        fn () => ServiceDetailTopicCategory::query()
                            ->with(['translations.language.locale'])
                            ->where('active', true)
                            ->orderBy('sort_order')
                            ->get()
                            ->mapWithKeys(fn (ServiceDetailTopicCategory $c) => [$c->id => $c->name ?: $c->code])
                    )
                    ->searchable(),
                SelectFilter::make('visibility')
                    ->label(__('filament.resources.service_detail_topic_columns.visibility'))
                    ->options([
                        'public' => __('filament.resources.service_detail_topic_visibility.public'),
                        'operator' => __('filament.resources.service_detail_topic_visibility.operator'),
                        'internal' => __('filament.resources.service_detail_topic_visibility.internal'),
                    ]),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn ($query) => $query->with([
                'translations.language.locale',
                'category.translations.language.locale',
                'conditionKey',
            ]));
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
            'index' => Pages\ListServiceDetailTopics::route('/'),
            'create' => Pages\CreateServiceDetailTopic::route('/create'),
            'view' => Pages\ViewServiceDetailTopic::route('/{record}'),
            'edit' => Pages\EditServiceDetailTopic::route('/{record}/edit'),
        ];
    }
}
