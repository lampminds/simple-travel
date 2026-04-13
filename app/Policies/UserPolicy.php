<?php

namespace App\Policies;

use App\Models\User;

/**
 * Authorization for User records on the website (and Filament).
 *
 * Platform-linked actors are evaluated with platform Spatie team (middleware). Tenant checks that
 * depend on “owner” use {@see User::hasRoleForCurrentAccount()} so the active session account matches
 * invitations and the navbar.
 */
class UserPolicy
{
    /**
     * Platform operators see everyone; tenant owners (for the current account) may list users they share an account with.
     */
    public function viewAny(User $user): bool
    {
        return $user->belongsToPlatformAccount() || $user->hasRoleForCurrentAccount('owner');
    }

    /**
     * Whether the user can view the given user.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->belongsToPlatformAccount()) {
            return true;
        }

        return $this->sharesAccountWith($user, $model);
    }

    /**
     * Whether the user can create users. Platform staff and tenant owners (invite team members).
     */
    public function create(User $user): bool
    {
        return $user->belongsToPlatformAccount() || $user->hasRoleForCurrentAccount('owner');
    }

    /**
     * Whether the user can update the given user.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->belongsToPlatformAccount()) {
            return true;
        }

        return $this->sharesAccountWith($user, $model);
    }

    /**
     * Whether the user can delete the given user.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->belongsToPlatformAccount()) {
            return true;
        }

        if (! $user->hasRoleForCurrentAccount('owner')) {
            return false;
        }

        return $this->sharesAccountWith($user, $model);
    }

    private function sharesAccountWith(User $actor, User $target): bool
    {
        return $actor->accounts()->whereIn(
            'accounts.id',
            $target->accounts()->pluck('accounts.id')
        )->exists();
    }
}
