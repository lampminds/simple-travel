<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Middleware\SetPermissionsTeamForRequest;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lampminds\Customization\Models\User as BaseUser;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as MediaModel;
use Spatie\Permission\PermissionRegistrar;

/**
 * Application User model. Account access is only via the account_user pivot (Spatie teams).
 */
class User extends BaseUser implements FilamentUser, HasMedia, MustVerifyEmail
{
    use HasFactory, InteractsWithMedia, LogsActivity;

    /**
     * Send the email verification notification (custom welcome + verification link).
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }

    /** No created_by/updated_by columns on users table; skip audit section in LmpResource form. */
    protected $dont_use_audit = true;

    /**
     * Mass assignable attributes (parent fillable + password). Account membership is not mass-assigned here.
     *
     * @var list<string>
     */
    protected $fillable = [];

    public function __construct(array $attributes = [])
    {
        $this->fillable = array_merge(BaseUser::getFillableAttributes(), ['password']);
        parent::__construct($attributes);
    }

    /**
     * Accounts this user may access (pivot; Spatie teams / account switch).
     */
    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class)->withTimestamps();
    }

    /**
     * Active account id for the current session (team), or first linked account.
     */
    public function currentAccountId(): ?int
    {
        if (session(SetPermissionsTeamForRequest::SESSION_REQUIRES_ACCOUNT_SELECTION, false)) {
            return null;
        }

        $sessionId = session(SetPermissionsTeamForRequest::SESSION_CURRENT_ACCOUNT_ID);
        if ($sessionId !== null && $this->belongsToAccount((int) $sessionId)) {
            return (int) $sessionId;
        }

        return $this->accounts()->orderBy('accounts.id')->value('accounts.id');
    }

    /**
     * Active Account model for the current request context.
     */
    public function currentAccount(): ?Account
    {
        $id = $this->currentAccountId();

        return $id !== null ? Account::query()->find($id) : null;
    }

    /**
     * Whether the user may act in the given account (pivot membership).
     */
    public function belongsToAccount(int $accountId): bool
    {
        return $this->accounts()->where('accounts.id', $accountId)->exists();
    }

    /**
     * Membership in the reserved platform account (Spatie team for global roles).
     */
    public function belongsToPlatformAccount(): bool
    {
        return $this->belongsToAccount((int) config('permission.platform_account_id', 1));
    }

    /**
     * Whether the user has the role for the active session account (Spatie team).
     *
     * Use this for tenant-scoped website features. {@see SetPermissionsTeamForRequest} pins the
     * registrar to the platform team for platform-linked users, so plain {@see hasRole()} would
     * ignore the switched tenant.
     */
    public function hasRoleForCurrentAccount(string $role): bool
    {
        $accountId = $this->currentAccountId();
        if ($accountId === null) {
            return false;
        }

        return $this->hasRoleForAccountId($role, $accountId);
    }

    /**
     * Check whether the user has a role in a specific account/team, regardless of current registrar team.
     */
    public function hasRoleForAccountId(string $role, int $accountId): bool
    {
        $pivot = config('permission.table_names.model_has_roles');
        $rolesTable = config('permission.table_names.roles');
        $roleIdCol = config('permission.column_names.role_pivot_key') ?: 'role_id';
        $morphKey = config('permission.column_names.model_morph_key');
        $teamKey = config('permission.column_names.team_foreign_key');

        return DB::table($pivot)
            ->join($rolesTable, $rolesTable.'.id', '=', $pivot.'.'.$roleIdCol)
            ->where($pivot.'.'.$morphKey, $this->getKey())
            ->where($pivot.'.model_type', static::class)
            ->where($pivot.'.'.$teamKey, $accountId)
            ->where($rolesTable.'.name', $role)
            ->exists();
    }

    /**
     * Role names for the active session account only (navbar subtitle, etc.).
     */
    public function roleNamesForCurrentAccount(): Collection
    {
        $accountId = $this->currentAccountId();
        if ($accountId === null) {
            return collect();
        }

        $registrar = app(PermissionRegistrar::class);
        $previous = $registrar->getPermissionsTeamId();
        $registrar->setPermissionsTeamId($accountId);

        try {
            return $this->getRoleNames();
        } finally {
            $registrar->setPermissionsTeamId($previous);
        }
    }

    /**
     * Filament admin panel: only users linked to the platform account (see docs/permissions.md).
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'smpl_adm' && $this->belongsToPlatformAccount();
    }

    /**
     * Role IDs assigned in {@see config('permission.table_names.model_has_roles')} for a given account (Spatie team).
     *
     * @return list<int>
     */
    public function roleIdsForAccount(int $accountId): array
    {
        $pivot = config('permission.table_names.model_has_roles');
        $roleCol = config('permission.column_names.role_pivot_key') ?: 'role_id';
        $morphKey = config('permission.column_names.model_morph_key');
        $teamKey = config('permission.column_names.team_foreign_key');

        return DB::table($pivot)
            ->where($morphKey, $this->getKey())
            ->where('model_type', static::class)
            ->where($teamKey, $accountId)
            ->pluck($roleCol)
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    /**
     * Sync `account_user` and per-account roles from Filament "memberships" state (one row per account).
     *
     * @param  array<int, array{account_id?: int|null, role_ids?: array<int|string>|null}>  $memberships
     */
    public function syncAccountMemberships(array $memberships): void
    {
        $merged = [];
        foreach ($memberships as $row) {
            $accountId = (int) ($row['account_id'] ?? 0);
            if ($accountId < 1) {
                continue;
            }
            $ids = array_map(
                static fn ($id) => (int) $id,
                array_values(array_filter(Arr::wrap($row['role_ids'] ?? []), static fn ($v) => $v !== null && $v !== ''))
            );
            if (! isset($merged[$accountId])) {
                $merged[$accountId] = [];
            }
            $merged[$accountId] = array_values(array_unique(array_merge($merged[$accountId], $ids)));
        }

        $accountIds = array_keys($merged);
        sort($accountIds);

        $this->accounts()->sync($accountIds);

        $pivot = config('permission.table_names.model_has_roles');
        $morphKey = config('permission.column_names.model_morph_key');
        $teamKey = config('permission.column_names.team_foreign_key');

        $pivotQuery = DB::table($pivot)
            ->where($morphKey, $this->getKey())
            ->where('model_type', static::class);

        if ($accountIds === []) {
            $pivotQuery->delete();
            $this->forgetCachedPermissions();

            return;
        }

        $pivotQuery->whereNotIn($teamKey, $accountIds)->delete();

        $registrar = app(PermissionRegistrar::class);
        $previous = $registrar->getPermissionsTeamId();
        $roleModel = config('permission.models.role');

        try {
            foreach ($merged as $accountId => $roleIds) {
                $registrar->setPermissionsTeamId($accountId);
                $roles = $roleIds === []
                    ? []
                    : $roleModel::query()->whereIn('id', $roleIds)->get()->all();
                $this->syncRoles($roles);
            }
        } finally {
            $registrar->setPermissionsTeamId($previous);
        }

        $this->forgetCachedPermissions();
    }

    /**
     * Role names across all teams (pivot rows), for listings without mutating the permission registrar.
     */
    public function roleNamesAcrossTeams(): string
    {
        $pivot = config('permission.table_names.model_has_roles');
        $rolesTable = config('permission.table_names.roles');
        $roleIdCol = config('permission.column_names.role_pivot_key') ?: 'role_id';
        $morphKey = config('permission.column_names.model_morph_key');

        return DB::table($pivot)
            ->join($rolesTable, $rolesTable.'.id', '=', $pivot.'.'.$roleIdCol)
            ->where($pivot.'.'.$morphKey, $this->getKey())
            ->where($pivot.'.model_type', static::class)
            ->orderBy($rolesTable.'.name')
            ->pluck($rolesTable.'.name')
            ->unique()
            ->implode(', ');
    }

    /**
     * Accounts the user can switch to (navbar).
     */
    public function switchableAccounts(): Collection
    {
        return $this->accounts()->orderBy('commercial_name')->orderBy('name')->orderBy('nick')->get();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useDisk('avatars')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    /**
     * Thumbnail for navbars; preview for profile page. Generated on upload (sync, no queue worker required).
     */
    public function registerMediaConversions(?MediaModel $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(80)
            ->height(80)
            ->nonQueued();

        $this->addMediaConversion('preview')
            ->width(256)
            ->height(256)
            ->nonQueued();
    }

    /**
     * True when the user uploaded a file; false when only the DiceBear fallback applies.
     */
    public function hasUploadedAvatar(): bool
    {
        return $this->getFirstMedia('avatar') !== null;
    }

    /**
     * Small avatar URL (navbar): uploaded thumb or DiceBear.
     */
    public function avatarThumbUrl(): string
    {
        $media = $this->getFirstMedia('avatar');
        if ($media !== null) {
            return $media->getUrl('thumb');
        }

        return $this->dicebearAvatarUrl(80);
    }

    /**
     * Larger avatar for profile header: uploaded preview or DiceBear.
     */
    public function avatarDisplayUrl(): string
    {
        $media = $this->getFirstMedia('avatar');
        if ($media !== null) {
            return $media->getUrl('preview');
        }

        return $this->dicebearAvatarUrl(256);
    }

    /**
     * Deterministic DiceBear HTTP API URL (free tier) when no file is stored.
     *
     * @see https://www.dicebear.com/how-to-use/http-api/
     */
    protected function dicebearAvatarUrl(int $size): string
    {
        $seed = substr(hash('sha256', (string) $this->getKey().'|'.$this->email), 0, 32);

        return 'https://api.dicebear.com/9.x/avataaars/svg?'
            .http_build_query([
                'seed' => $seed,
                'size' => $size,
            ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
