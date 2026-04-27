<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Account;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;
use App\Models\Role;

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

    /**
     * Full-width form: LmpResource lays out schema in a multi-column grid; span all columns like {@see MenuResource}.
     */
    public static function form(Schema $schema): Schema
    {
        $main = array_map(fn ($c) => $c->columnSpanFull(), static::getMainFormSchema($schema));
        $audit = array_map(fn ($c) => $c->columnSpanFull(), static::getAuditFooterSchema());

        return $schema->schema(array_merge($main, $audit));
    }

    /**
     * Roles assignable for a Spatie team: shared templates (null account_id) or definitions for that account.
     *
     * @return array<int, string>
     */
    protected static function roleOptionsForAccount(?int $accountId): array
    {
        if ($accountId === null || $accountId < 1) {
            return [];
        }

        $teamKey = config('permission.column_names.team_foreign_key');

        return Role::query()
            ->where('guard_name', 'web')
            ->where(function ($q) use ($teamKey, $accountId): void {
                $q->whereNull($teamKey)->orWhere($teamKey, $accountId);
            })
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Role $r): array => [$r->id => $r->name])
            ->all();
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Tabs::make()
                ->tabs([
                    Tab::make(__('filament.resources.user_tabs.general'))
                        ->schema([
                            Section::make('')->schema([
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
                            ]),
                        ]),
                    Tab::make(__('filament.resources.user_tabs.accounts_roles'))
                        ->schema([
                            Section::make('')
                                ->schema([
                                    Repeater::make('memberships')
                                        ->label(__('filament.resources.user_fields.memberships_heading'))
                                        ->helperText(__('filament.resources.user_fields.memberships_help'))
                                        ->schema([
                                            Select::make('account_id')
                                                ->label(__('filament.resources.user_fields.account'))
                                                ->required()
                                                ->searchable()
                                                ->preload()
                                                ->options(function (): array {
                                                    $query = Account::query()
                                                        ->orderBy('commercial_name')
                                                        ->orderBy('name')
                                                        ->orderBy('id');

                                                    $user = Filament::auth()->user();
                                                    if ($user instanceof User && ! $user->belongsToPlatformAccount()) {
                                                        $accountId = $user->currentAccountId();
                                                        if ($accountId !== null) {
                                                            $query->where('accounts.id', $accountId);
                                                        }
                                                    }

                                                    return $query
                                                        ->get()
                                                        ->mapWithKeys(
                                                            fn (Account $a): array => [
                                                                $a->id => $a->commercial_name
                                                                    ?: $a->name
                                                                    ?: $a->code
                                                                    ?: (string) $a->id,
                                                            ]
                                                        )
                                                        ->all();
                                                }),
                                            Select::make('role_ids')
                                                ->label(__('filament.resources.user_fields.roles'))
                                                ->multiple()
                                                ->searchable()
                                                ->preload()
                                                ->options(fn (Get $get): array => static::roleOptionsForAccount(
                                                    filled($get('account_id')) ? (int) $get('account_id') : null
                                                )),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(1)
                                        ->minItems(1)
                                        ->reorderable(false)
                                        ->addActionLabel(__('filament.resources.user_fields.add_membership'))
                                        ->columnSpanFull(),
                                ]),
                        ]),
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
