<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Whether the user can view any users (list).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Whether the user can view the given user.
     */
    public function view(User $user, User $model): bool
    {
        return true;
    }

    /**
     * Whether the user can create users. Restricted to admin and account_owner.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'owner']);
    }

    /**
     * Whether the user can update the given user.
     */
    public function update(User $user, User $model): bool
    {
        return true;
    }

    /**
     * Whether the user can delete the given user. Restricted to admin and account_owner.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->hasAnyRole(['admin', 'owner']);
    }
}
