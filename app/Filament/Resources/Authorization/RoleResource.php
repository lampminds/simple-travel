<?php

namespace App\Filament\Resources\Authorization;

use App\Filament\Resources\Authorization\RoleResource\Pages;
use App\Filament\Resources\BaseResource;
use App\Models\Role;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
        return parent::getEloquentQuery()
            ->withCount('permissions')
            ->with('account');
    }

    /**
     * Spatie roles per account (teams); no LMP audit columns on the role model.
     */
    public static function form(Schema $schema): Schema
    {
        $rolesTable = (string) config('permission.table_names.roles', 'user_roles');

        return $schema->schema([
            Section::make('')
                ->schema([
                    Select::make('account_id')
                        ->label(__('filament.resources.role_fields.account_id'))
                        ->relationship('account', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live(),
                    TextInput::make('name')
                        ->label(__('filament.resources.role_fields.name'))
                        ->required()
                        ->maxLength(255)
                        ->unique(
                            table: $rolesTable,
                            column: 'name',
                            ignoreRecord: true,
                            modifyRuleUsing: fn ($rule, callable $get) => $rule
                                ->where('guard_name', 'web')
                                ->where('account_id', $get('account_id')),
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
                TextColumn::make('account.name')
                    ->label(__('filament.resources.role_columns.account'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('name')
                    ->label(__('filament.resources.role_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('permissions_count')
                    ->label(__('filament.resources.role_columns.permissions_count'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('account_id')
                    ->label(__('filament.resources.role_filters.account_id'))
                    ->relationship('account', 'name')
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
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
