<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->belongsToPlatformAccount();
    }

    public function view(User $user, Role $role): bool
    {
        return $user->belongsToPlatformAccount()
            && $this->isPlatformRole($role);
    }

    public function create(User $user): bool
    {
        return $user->belongsToPlatformAccount();
    }

    public function update(User $user, Role $role): bool
    {
        return $user->belongsToPlatformAccount()
            && $this->isPlatformRole($role);
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->belongsToPlatformAccount()
            && $this->isPlatformRole($role);
    }

    protected function isPlatformRole(Role $role): bool
    {
        $platformId = (int) config('permission.platform_account_id', 1);

        return (int) $role->getAttribute('account_id') === $platformId;
    }
}
