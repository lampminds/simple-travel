<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Primary key of a role row on the platform (template) account, by role name.
     * Used e.g. for external invitations that will become "owner" on a new company after registration.
     */
    public static function platformTemplateRoleId(string $roleName, ?string $guardName = 'web'): ?int
    {
        $platformId = (int) config('permission.platform_account_id', 1);
        $guard = $guardName ?? 'web';
        $id = static::query()
            ->where('account_id', $platformId)
            ->where('name', $roleName)
            ->where('guard_name', $guard)
            ->value('id');

        return $id !== null ? (int) $id : null;
    }

    public static function platformTemplateRoleIdOrFail(string $roleName, ?string $guardName = 'web'): int
    {
        $id = static::platformTemplateRoleId($roleName, $guardName);
        if ($id === null) {
            throw new \RuntimeException("Template role [{$roleName}] not found for the platform account.");
        }

        return $id;
    }
}
