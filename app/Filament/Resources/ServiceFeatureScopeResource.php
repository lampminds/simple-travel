<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceFeatureScopeResource\Pages;
use App\Models\ServiceFeature;
use App\Models\ServiceFeatureScope;
use App\Models\ServiceType;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceFeatureScopeResource extends LmpResource
{
    protected static ?string $model = ServiceFeatureScope::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $modelLabel = 'filament.resources.service_feature_scope';

    protected static ?string $pluralModelLabel = 'filament.resources.service_feature_scopes';

    protected static ?string $recordTitleAttribute = 'id';

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
        return [
            Section::make('')
                ->schema([
                    Select::make('service_type_id')
                        ->label(__('filament.resources.service_feature_scope_fields.type'))
                        ->options(
                            fn () => ServiceType::query()
                                ->with(['translations.language.locale'])
                                ->ordered()
                                ->where('active', true)
                                ->get()
                                ->mapWithKeys(fn (ServiceType $type) => [
                                    $type->id => $type->name !== '' ? $type->name : $type->code,
                                ])
                        )
                        ->required()
                        ->live()
                        ->searchable(),
                    Select::make('service_feature_id')
                        ->label(__('filament.resources.service_feature_scope_fields.feature'))
                        ->options(
                            fn () => ServiceFeature::query()
                                ->with(['translations.language.locale'])
                                ->ordered()
                                ->where('active', true)
                                ->where('is_selectable', true)
                                ->get()
                                ->mapWithKeys(fn (ServiceFeature $feature) => [
                                    $feature->id => $feature->name !== '' ? $feature->name : $feature->code,
                                ])
                        )
                        ->required()
                        ->unique(
                            table: 'cat_service_feature_scopes',
                            column: 'service_feature_id',
                            ignoreRecord: true,
                            modifyRuleUsing: fn ($rule, callable $get) => $rule
                                ->where('service_type_id', $get('service_type_id'))
                        )
                        ->validationMessages([
                            'unique' => __('filament.resources.service_feature_scope_validation.unique_pair'),
                        ])
                        ->searchable(),
                ])
                ->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('serviceType.name')
                    ->label(__('filament.resources.service_feature_scope_columns.type'))
                    ->searchable(query: function ($query, string $search): void {
                        $query->whereHas('serviceType.translations', function ($q) use ($search): void {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                    }),
                TextColumn::make('serviceFeature.name')
                    ->label(__('filament.resources.service_feature_scope_columns.feature'))
                    ->searchable(query: function ($query, string $search): void {
                        $query->whereHas('serviceFeature.translations', function ($q) use ($search): void {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                    }),
            ])
            ->filters([
                SelectFilter::make('service_type_id')
                    ->label(__('filament.resources.service_feature_scope_filters.type'))
                    ->options(
                        fn () => ServiceType::query()
                            ->with(['translations.language.locale'])
                            ->ordered()
                            ->where('active', true)
                            ->get()
                            ->mapWithKeys(fn (ServiceType $type) => [
                                $type->id => $type->name !== '' ? $type->name : $type->code,
                            ])
                    )
                    ->searchable(),
                SelectFilter::make('service_feature_id')
                    ->label(__('filament.resources.service_feature_scope_filters.feature'))
                    ->options(
                        fn () => ServiceFeature::query()
                            ->with(['translations.language.locale'])
                            ->ordered()
                            ->where('active', true)
                            ->where('is_selectable', true)
                            ->get()
                            ->mapWithKeys(fn (ServiceFeature $feature) => [
                                $feature->id => $feature->name !== '' ? $feature->name : $feature->code,
                            ])
                    )
                    ->searchable(),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn ($query) => $query
                ->whereHas('serviceFeature', fn ($q) => $q->where('is_selectable', true))
                ->with([
                    'serviceType.translations.language.locale',
                    'serviceFeature.translations.language.locale',
                ]));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceFeatureScopes::route('/'),
            'create' => Pages\CreateServiceFeatureScope::route('/create'),
            'view' => Pages\ViewServiceFeatureScope::route('/{record}'),
            'edit' => Pages\EditServiceFeatureScope::route('/{record}/edit'),
        ];
    }
}

