<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Account;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;
use Spatie\Permission\PermissionRegistrar;

class UserResource extends LmpResource
{
    protected static ?string $model = User::class;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['accounts']);

        $auth = Filament::auth()->user();
        if ($auth instanceof User && ! $auth->belongsToPlatformAccount()) {
            $accountId = $auth->currentAccountId();
            if ($accountId !== null) {
                $query->whereHas('accounts', fn (Builder $q) => $q->where('accounts.id', $accountId));
            }
        }

        return $query;
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'filament.resources.user';

    protected static ?string $pluralModelLabel = 'filament.resources.users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static \UnitEnum|string|null $navigationGroup = 'filament.resources.nav_users';

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
            Section::make('')->schema([
                Select::make('accounts')
                    ->label(__('filament.resources.user_fields.accounts'))
                    ->relationship(
                        name: 'accounts',
                        titleAttribute: 'commercial_name',
                        modifyQueryUsing: function (Builder $query): void {
                            $user = Filament::auth()->user();
                            if (! $user instanceof User || $user->belongsToPlatformAccount()) {
                                return;
                            }
                            $accountId = $user->currentAccountId();
                            if ($accountId !== null) {
                                $query->where('accounts.id', $accountId);
                            }
                        }
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (Account $record): string => $record->commercial_name
                            ?: $record->name
                            ?: $record->code
                            ?: (string) $record->id
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->required()
                    ->saveRelationshipsUsing(static function (Select $component, Model $record, $state): void {
                        if (! $record instanceof User) {
                            return;
                        }

                        $record->accounts()->sync(array_values(array_filter(Arr::wrap($state ?? []))));
                    }),
                TextInput::make('name')
                    ->label(__('filament.resources.user_fields.name'))
                    ->placeholder(__('filament.resources.user_fields.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('filament.resources.user_fields.email'))
                    ->placeholder(__('filament.resources.user_fields.email'))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label(__('filament.resources.user_fields.password'))
                    ->placeholder(__('filament.resources.user_fields.password'))
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255)
                    ->hiddenOn('edit'),
                Select::make('roles')
                    ->label(__('filament.resources.user_fields.roles'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(false)
                    ->saveRelationshipsUsing(static function (Select $component, Model $record, $state): void {
                        if (! $record instanceof User) {
                            return;
                        }

                        $accountId = $record->accounts()->orderBy('accounts.id')->value('accounts.id');
                        if ($accountId === null) {
                            return;
                        }

                        app(PermissionRegistrar::class)->setPermissionsTeamId((int) $accountId);

                        $roleModel = config('permission.models.role');
                        $roleIds = array_values(array_filter(Arr::wrap($state ?? [])));
                        $roles = $roleIds === []
                            ? []
                            : $roleModel::query()->whereIn('id', $roleIds)->get()->all();

                        $record->syncRoles($roles);
                    }),
            ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->headerActions([
                CreateAction::make(),
            ])
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.user_columns.id'))
                    ->sortable(),
                TextColumn::make('accounts_list')
                    ->label(__('filament.resources.user_columns.accounts'))
                    ->getStateUsing(fn (User $record) => $record->accounts->pluck('commercial_name')->filter()->join(', ') ?: $record->accounts->pluck('name')->filter()->join(', '))
                    ->badge(),
                TextColumn::make('name')
                    ->label(__('filament.resources.user_columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('filament.resources.user_columns.email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles_list')
                    ->label(__('filament.resources.user_columns.roles'))
                    ->getStateUsing(fn (User $record) => $record->roleNamesAcrossTeams() ?: '—')
                    ->badge(),
            ])
            ->defaultSort('id');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
