<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CuentasCluster;
use App\Filament\Resources\ServiceGastronomyTypeAssignmentResource\Pages;
use App\Models\ServiceGastronomy;
use App\Models\ServiceGastronomyType;
use App\Models\ServiceGastronomyTypeAssignment;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ServiceGastronomyTypeAssignmentResource extends LmpResource
{
    protected static ?string $model = ServiceGastronomyTypeAssignment::class;

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $modelLabel = 'filament.resources.service_gastronomy_type_assignment';

    protected static ?string $pluralModelLabel = 'filament.resources.service_gastronomy_type_assignments';

    protected static ?string $recordTitleAttribute = 'id';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_services';

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = CuentasCluster::class;

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
        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.service_gastronomy_type_assignment_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
                                Select::make('service_gastronomy_id')
                                    ->label(__('filament.resources.service_gastronomy_type_assignment_fields.service_gastronomy_id'))
                                    ->options(
                                        fn () => ServiceGastronomy::query()
                                            ->with(['service.translations.language.locale'])
                                            ->orderBy('id')
                                            ->get()
                                            ->mapWithKeys(fn (ServiceGastronomy $g) => [
                                                $g->id => ($g->service?->name !== '' && $g->service?->name !== null)
                                                    ? $g->service->name
                                                    : __('filament.resources.service').' #'.($g->service_id ?? $g->id),
                                            ])
                                    )
                                    ->searchable()
                                    ->required(),
                                Select::make('service_gastronomy_type_id')
                                    ->label(__('filament.resources.service_gastronomy_type_assignment_fields.service_gastronomy_type_id'))
                                    ->options(
                                        fn () => ServiceGastronomyType::query()
                                            ->where('active', true)
                                            ->ordered()
                                            ->with(['translations.language.locale'])
                                            ->get()
                                            ->mapWithKeys(fn (ServiceGastronomyType $t) => [
                                                $t->id => $t->name !== '' ? $t->name : $t->code,
                                            ])
                                    )
                                    ->searchable()
                                    ->required()
                                    ->unique(
                                        table: 'service_gastronomy_type_assignments',
                                        column: 'service_gastronomy_type_id',
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn ($rule, callable $get) => $rule
                                            ->where('service_gastronomy_id', $get('service_gastronomy_id'))
                                    ),
                            ])->columns(2),
                        ]),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.service_gastronomy_type_assignment_columns.id'))
                    ->sortable(),
                TextColumn::make('serviceGastronomy.service.name')
                    ->label(__('filament.resources.service_gastronomy_type_assignment_columns.service'))
                    ->searchable(query: function ($query, $search): void {
                        $query->whereHas('serviceGastronomy.service.translations', function ($q) use ($search): void {
                            $q->where('name', 'like', '%'.$search.'%');
                        });
                    })
                    ->limit(40),
                TextColumn::make('gastronomyType.name')
                    ->label(__('filament.resources.service_gastronomy_type_assignment_columns.type'))
                    ->searchable(query: function ($query, $search): void {
                        $query->whereHas('gastronomyType.translations', function ($q) use ($search): void {
                            $q->where('name', 'like', '%'.$search.'%');
                        });
                    }),
            ])
            ->defaultSort('id')
            ->modifyQueryUsing(fn ($query) => $query->with([
                'serviceGastronomy.service.translations.language.locale',
                'gastronomyType.translations.language.locale',
            ]))
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceGastronomyTypeAssignments::route('/'),
            'create' => Pages\CreateServiceGastronomyTypeAssignment::route('/create'),
            'view' => Pages\ViewServiceGastronomyTypeAssignment::route('/{record}'),
            'edit' => Pages\EditServiceGastronomyTypeAssignment::route('/{record}/edit'),
        ];
    }
}
