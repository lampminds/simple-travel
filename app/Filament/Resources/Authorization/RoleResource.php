<?php

namespace App\Filament\Resources\Authorization;

use App\Filament\Resources\Authorization\RoleResource\Pages;
use App\Filament\Resources\BaseResource;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class RoleResource extends BaseResource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $modelLabel = 'filament.resources.role';

    protected static ?string $pluralModelLabel = 'filament.resources.roles';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_authorization';

    protected static ?int $navigationSort = 1;

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

    public static function getEloquentQuery(): Builder
    {
        $platformId = (int) config('permission.platform_account_id', 1);

        return parent::getEloquentQuery()
            ->where('account_id', $platformId)
            ->withCount('permissions');
    }

    /**
     * Custom form: platform roles only (no audit stamps; Spatie Role has no LMP audit columns).
     */
    public static function form(Schema $schema): Schema
    {
        $platformId = (int) config('permission.platform_account_id', 1);

        return $schema->schema([
            Section::make('')
                ->schema([
                    TextInput::make('name')
                        ->label(__('filament.resources.role_fields.name'))
                        ->required()
                        ->maxLength(255)
                        ->scopedUnique(
                            model: Role::class,
                            column: 'name',
                            ignoreRecord: true,
                            modifyQueryUsing: fn (Builder $query) => $query->where('account_id', $platformId)->where('guard_name', 'web'),
                        ),
                    TextInput::make('guard_name')
                        ->default('web')
                        ->hidden(),
                    CheckboxList::make('permissions')
                        ->label(__('filament.resources.role_fields.permissions'))
                        ->relationship(
                            'permissions',
                            'name',
                            modifyQueryUsing: fn (Builder $query) => $query
                                ->where('guard_name', 'web')
                                ->orderBy('name'),
                        )
                        ->columns(2)
                        ->gridDirection('row')
                        ->columnSpanFull()
                        ->bulkToggleable(),
                ])
                ->columns(1),
        ]);
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.role_columns.id'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.role_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('permissions_count')
                    ->label(__('filament.resources.role_columns.permissions_count'))
                    ->sortable(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
