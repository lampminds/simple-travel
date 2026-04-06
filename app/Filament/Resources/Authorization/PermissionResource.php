<?php

namespace App\Filament\Resources\Authorization;

use App\Filament\Resources\Authorization\PermissionResource\Pages;
use App\Filament\Resources\BaseResource;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;

class PermissionResource extends BaseResource
{
    protected static ?string $model = Permission::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $modelLabel = 'filament.resources.permission';

    protected static ?string $pluralModelLabel = 'filament.resources.permissions';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_authorization';

    protected static ?int $navigationSort = 2;

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
        return parent::getEloquentQuery()->withCount('roles');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('')
                ->schema([
                    TextInput::make('name')
                        ->label(__('filament.resources.permission_fields.name'))
                        ->required()
                        ->maxLength(255)
                        ->scopedUnique(
                            model: Permission::class,
                            column: 'name',
                            ignoreRecord: true,
                            modifyQueryUsing: fn (Builder $query) => $query->where('guard_name', 'web'),
                        )
                        ->helperText(__('filament.resources.permission_fields.name_help')),
                    TextInput::make('guard_name')
                        ->default('web')
                        ->hidden(),
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
                    ->label(__('filament.resources.permission_columns.id'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.permission_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles_count')
                    ->label(__('filament.resources.permission_columns.roles_count'))
                    ->sortable(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'view' => Pages\ViewPermission::route('/{record}'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
