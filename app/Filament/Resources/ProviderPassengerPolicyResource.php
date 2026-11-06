<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CuentasCluster;
use App\Filament\Resources\ProviderPassengerPolicyResource\Pages;
use App\Filament\Support\PassengerPolicyForm;
use App\Models\Account;
use App\Models\ProviderPassengerPolicy;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class ProviderPassengerPolicyResource extends LmpResource
{
    protected static ?string $model = ProviderPassengerPolicy::class;

    protected static ?string $slug = 'provider-passenger-policies';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $modelLabel = 'filament.resources.provider_passenger_policy';

    protected static ?string $pluralModelLabel = 'filament.resources.provider_passenger_policies';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_accounts_price_lists';

    protected static ?int $navigationSort = 21;

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

    public static function getRecordTitle(?Model $record): ?string
    {
        if (! $record instanceof ProviderPassengerPolicy) {
            return null;
        }

        $name = trim((string) $record->name);

        return $name !== '' ? $name : ('#'.$record->id);
    }

    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($component) => $component->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($component) => $component->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.passenger_policy_tabs.ages'))
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Select::make('provider_id')
                                        ->label(__('filament.resources.passenger_policy_fields.provider'))
                                        ->options(fn (): array => Account::query()->orderBy('name')->pluck('name', 'id')->all())
                                        ->required()
                                        ->disabledOn('edit'),
                                    TextInput::make('name')
                                        ->label(__('filament.resources.passenger_policy_fields.name'))
                                        ->required()
                                        ->maxLength(120),
                                ])
                                ->columns(2),
                            ...PassengerPolicyForm::ageBandSections(),
                        ]),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.resources.passenger_policy_fields.name'))
                    ->searchable(),
                TextColumn::make('provider.name')
                    ->label(__('filament.resources.passenger_policy_fields.provider'))
                    ->searchable(),
                TextColumn::make('rules_count')
                    ->label(__('filament.resources.passenger_policy_columns.bands'))
                    ->counts('rules'),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with('provider')->withCount('rules'))
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviderPassengerPolicies::route('/'),
            'create' => Pages\CreateProviderPassengerPolicy::route('/create'),
            'edit' => Pages\EditProviderPassengerPolicy::route('/{record}/edit'),
        ];
    }
}
