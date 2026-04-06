<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceDetailResource\Pages;
use App\Models\Language;
use App\Models\Service;
use App\Models\ServiceDetail;
use App\Models\ServiceDetailTopic;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceDetailResource extends LmpResource
{
    protected static ?string $model = ServiceDetail::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'filament.resources.service_detail';

    protected static ?string $pluralModelLabel = 'filament.resources.service_details';

    protected static ?string $recordTitleAttribute = 'description';

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
                    Select::make('service_id')
                        ->label(__('filament.resources.service_detail_fields.service_id'))
                        ->options(
                            fn () => Service::query()
                                ->with(['translations.language.locale', 'account'])
                                ->orderBy('id')
                                ->get()
                                ->mapWithKeys(fn (Service $s) => [$s->id => ($s->account?->name ? $s->account->name . ' – ' : '') . ($s->name ?: '#' . $s->id)])
                        )
                        ->required()
                        ->searchable(),
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
                ->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.service_detail_columns.id'))
                    ->sortable(),
                TextColumn::make('service.name')
                    ->label(__('filament.resources.service_detail_columns.service'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serviceDetailTopic.name')
                    ->label(__('filament.resources.service_detail_columns.topic'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('language.display_name')
                    ->label(__('filament.resources.service_detail_columns.language'))
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('filament.resources.service_detail_columns.description'))
                    ->limit(40)
                    ->wrap()
                    ->searchable(),
                IconColumn::make('active')
                    ->label(__('filament.resources.service_detail_columns.active'))
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('service_id')
                    ->label(__('filament.resources.service_detail_columns.service'))
                    ->options(
                        fn () => Service::query()
                            ->with(['translations.language.locale', 'account'])
                            ->orderBy('id')
                            ->get()
                            ->mapWithKeys(fn (Service $s) => [$s->id => ($s->account?->name ? $s->account->name . ' – ' : '') . ($s->name ?: '#' . $s->id)])
                    )
                    ->searchable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->modifyQueryUsing(fn ($query) => $query->with(['service.translations', 'service.account', 'language.locale', 'serviceDetailTopic.translations']));
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['description'];
    }

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    {
        $term = '%' . $search . '%';
        $query->where('description', 'like', $term);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceDetails::route('/'),
            'create' => Pages\CreateServiceDetail::route('/create'),
            'view' => Pages\ViewServiceDetail::route('/{record}'),
            'edit' => Pages\EditServiceDetail::route('/{record}/edit'),
        ];
    }
}
