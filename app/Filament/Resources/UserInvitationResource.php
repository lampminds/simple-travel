<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserInvitationResource\Pages;
use App\Models\Role;
use App\Models\UserInvitation;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Lampminds\Customization\Filament\LmpCustomization\Resources\LmpResource;

class UserInvitationResource extends LmpResource
{
    protected static ?string $model = UserInvitation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'filament.resources.user_invitation';

    protected static ?string $pluralModelLabel = 'filament.resources.user_invitations';

    protected static ?string $recordTitleAttribute = 'email';

    public static function getModelLabel(): string
    {
        return (string) __(static::$modelLabel);
    }

    public static function getPluralModelLabel(): string
    {
        return (string) __(static::$pluralModelLabel);
    }

    protected static function getMainFormSchema(Schema $schema): array
    {
        return [
            Section::make('')
                ->schema([
                    Select::make('account_id')
                        ->label(__('filament.resources.user_invitation_fields.account_id'))
                        ->relationship('account', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live(),
                    Select::make('account_inviting')
                        ->label(__('filament.resources.user_invitation_fields.account_inviting'))
                        ->relationship('accountInviting', 'name')
                        ->searchable()
                        ->preload()
                        ->helperText(__('filament.resources.user_invitation_fields.account_inviting_helper'))
                        ->nullable(),
                    TextInput::make('email')
                        ->label(__('filament.resources.user_invitation_fields.email'))
                        ->email()
                        ->required()
                        ->maxLength(255),
                    TextInput::make('name')
                        ->label(__('filament.resources.user_invitation_fields.name'))
                        ->required()
                        ->maxLength(255),
                    Select::make('type')
                        ->label(__('filament.resources.user_invitation_fields.type'))
                        ->options([
                            UserInvitation::TYPE_INTERNAL => UserInvitation::TYPE_INTERNAL,
                            UserInvitation::TYPE_EXTERNAL => UserInvitation::TYPE_EXTERNAL,
                        ])
                        ->required()
                        ->live(),
                    Select::make('role_id')
                        ->label(__('filament.resources.user_invitation_fields.role_id'))
                        ->options(function (Get $get): array {
                            if (($get('type') ?? UserInvitation::TYPE_INTERNAL) === UserInvitation::TYPE_EXTERNAL) {
                                $id = Role::platformTemplateRoleId('owner');
                                if ($id === null) {
                                    return [];
                                }

                                return [
                                    $id => __('filament.resources.user_invitation_fields.role_external_owner'),
                                ];
                            }

                            $accountId = (int) ($get('account_id') ?? 0);
                            if ($accountId < 1) {
                                return [];
                            }

                            return getAccountRoles($accountId, ['guest', 'admin']);
                        })
                        ->searchable()
                        ->preload()
                        ->default(function (Get $get): ?int {
                            if (($get('type') ?? UserInvitation::TYPE_INTERNAL) === UserInvitation::TYPE_INTERNAL) {
                                return null;
                            }

                            return Role::platformTemplateRoleId('owner');
                        })
                        ->disabled(fn (Get $get): bool => ($get('type') ?? UserInvitation::TYPE_INTERNAL) === UserInvitation::TYPE_EXTERNAL)
                        ->dehydrated()
                        ->required()
                        ->helperText(fn (Get $get) => ($get('type') ?? null) === UserInvitation::TYPE_EXTERNAL
                            ? __('filament.resources.user_invitation_fields.role_id_external_helper')
                            : null),
                    Select::make('status')
                        ->label(__('filament.resources.user_invitation_fields.status'))
                        ->options([
                            UserInvitation::STATUS_PENDING => UserInvitation::STATUS_PENDING,
                            UserInvitation::STATUS_ACCEPTED => UserInvitation::STATUS_ACCEPTED,
                            UserInvitation::STATUS_DECLINED => UserInvitation::STATUS_DECLINED,
                            UserInvitation::STATUS_EXPIRED => UserInvitation::STATUS_EXPIRED,
                            UserInvitation::STATUS_REVOKED => UserInvitation::STATUS_REVOKED,
                        ])
                        ->default(UserInvitation::STATUS_PENDING)
                        ->required(),
                    DateTimePicker::make('expires_at')
                        ->label(__('filament.resources.user_invitation_fields.expires_at'))
                        ->required(),
                    Select::make('invited_by')
                        ->label(__('filament.resources.user_invitation_fields.invited_by'))
                        ->relationship('invitedBy', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    TextInput::make('token')
                        ->label(__('filament.resources.user_invitation_fields.token'))
                        ->maxLength(255),
                    DateTimePicker::make('accepted_at')
                        ->label(__('filament.resources.user_invitation_fields.accepted_at'))
                        ->nullable(),
                    DateTimePicker::make('declined_at')
                        ->label(__('filament.resources.user_invitation_fields.declined_at'))
                        ->nullable(),
                ])
                ->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['account', 'accountInviting', 'invitedBy', 'role'])->orderByDesc('id'))
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament.resources.user_invitation_columns.id'))
                    ->sortable(),
                TextColumn::make('account_label')
                    ->label(__('filament.resources.user_invitation_columns.account'))
                    ->getStateUsing(fn (UserInvitation $record) => $record->account?->commercial_name ?: $record->account?->name ?: '—')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('account', fn (Builder $q) => $q
                            ->where('commercial_name', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%"));
                    }),
                TextColumn::make('accountInvitingLabel')
                    ->label(__('filament.resources.user_invitation_columns.account_inviting'))
                    ->getStateUsing(
                        fn (UserInvitation $record) => $record->accountInviting?->commercial_name
                            ?: $record->accountInviting?->name
                            ?: '—'
                    )
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('accountInviting', fn (Builder $q) => $q
                            ->where('commercial_name', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%"));
                    }),
                TextColumn::make('email')
                    ->label(__('filament.resources.user_invitation_columns.email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('filament.resources.user_invitation_columns.name'))
                    ->searchable()
                    ->default('—'),
                TextColumn::make('role.name')
                    ->label(__('filament.resources.user_invitation_columns.role'))
                    ->default('—'),
                TextColumn::make('type')
                    ->label(__('filament.resources.user_invitation_columns.type'))
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('filament.resources.user_invitation_columns.status'))
                    ->badge(),
                TextColumn::make('expires_at')
                    ->label(__('filament.resources.user_invitation_columns.expires_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                TextColumn::make('invitedBy.name')
                    ->label(__('filament.resources.user_invitation_columns.invited_by'))
                    ->default('—')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('filament.resources.user_invitation_filters.type'))
                    ->options([
                        UserInvitation::TYPE_INTERNAL => UserInvitation::TYPE_INTERNAL,
                        UserInvitation::TYPE_EXTERNAL => UserInvitation::TYPE_EXTERNAL,
                    ]),
                SelectFilter::make('status')
                    ->label(__('filament.resources.user_invitation_filters.status'))
                    ->options([
                        UserInvitation::STATUS_PENDING => UserInvitation::STATUS_PENDING,
                        UserInvitation::STATUS_ACCEPTED => UserInvitation::STATUS_ACCEPTED,
                        UserInvitation::STATUS_DECLINED => UserInvitation::STATUS_DECLINED,
                        UserInvitation::STATUS_EXPIRED => UserInvitation::STATUS_EXPIRED,
                        UserInvitation::STATUS_REVOKED => UserInvitation::STATUS_REVOKED,
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserInvitations::route('/'),
            'create' => Pages\CreateUserInvitation::route('/create'),
            'view' => Pages\ViewUserInvitation::route('/{record}'),
            'edit' => Pages\EditUserInvitation::route('/{record}/edit'),
        ];
    }
}

