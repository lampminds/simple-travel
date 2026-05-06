<?php

namespace App\Services;

use App\Models\AccountPerson;
use App\Models\Person;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Reverts internal invitation provisioning: pending stubs are deleted; existing users lose only
 * the role, account_person row, and account membership added for that invitation when applicable.
 */
final class PendingInvitationUserCleanup
{
    public function __construct(
        private readonly PermissionRegistrar $permissionRegistrar,
    ) {
    }

    public function deleteStubForInvitation(UserInvitation $invitation): void
    {
        if ($invitation->type !== UserInvitation::TYPE_INTERNAL) {
            return;
        }

        $userId = $invitation->invited_user_id;
        if ($userId === null) {
            return;
        }

        $user = User::query()->find($userId);
        if ($user === null) {
            return;
        }

        if ($user->activation_state === User::ACTIVATION_PENDING_INVITATION) {
            $accountId = (int) $invitation->account_id;
            $this->deletePendingEmployeeForAccount($user, $accountId);

            return;
        }

        $this->revokeProvisionedMembershipForActiveUser($invitation, $user);
    }

    /**
     * Undo account access granted when inviting an already-registered user.
     */
    private function revokeProvisionedMembershipForActiveUser(UserInvitation $invitation, User $user): void
    {
        $accountId = (int) $invitation->account_id;
        if (! $user->belongsToAccount($accountId)) {
            return;
        }

        DB::transaction(function () use ($invitation, $user, $accountId) {
            $personId = $invitation->invited_person_id;
            if ($personId !== null) {
                AccountPerson::query()
                    ->where('account_id', $accountId)
                    ->where('person_id', $personId)
                    ->delete();
            }

            $role = Role::query()
                ->where('account_id', $accountId)
                ->whereKey($invitation->role_id)
                ->first();

            $previousTeam = $this->permissionRegistrar->getPermissionsTeamId();
            $this->permissionRegistrar->setPermissionsTeamId($accountId);

            try {
                if ($role !== null) {
                    $user->removeRole($role);
                }
            } finally {
                $this->permissionRegistrar->setPermissionsTeamId($previousTeam);
            }

            if ($user->roleIdsForAccount($accountId) === []) {
                $user->accounts()->detach($accountId);
            }
        });
    }

    /**
     * When a pending user has no usable invitation row, drop their tenant membership stub for this account.
     */
    public function deleteOrphanStubForAccountIfStale(User $user, int $accountId): void
    {
        if ($user->activation_state !== User::ACTIVATION_PENDING_INVITATION) {
            return;
        }

        if (! $user->belongsToAccount($accountId)) {
            return;
        }

        $this->deletePendingEmployeeForAccount($user, $accountId);
    }

    private function deletePendingEmployeeForAccount(User $user, int $accountId): void
    {
        $accountIds = $user->accounts()->pluck('accounts.id')->map(fn ($id) => (int) $id)->values()->all();
        if ($accountIds !== [$accountId]) {
            return;
        }

        DB::transaction(function () use ($user, $accountId) {
            $personIds = $user->persons()->pluck('persons.id')->all();

            foreach ($personIds as $personId) {
                AccountPerson::query()
                    ->where('account_id', $accountId)
                    ->where('person_id', $personId)
                    ->delete();
            }

            $previousTeam = $this->permissionRegistrar->getPermissionsTeamId();
            $this->permissionRegistrar->setPermissionsTeamId($accountId);

            try {
                $user->syncRoles([]);
            } finally {
                $this->permissionRegistrar->setPermissionsTeamId($previousTeam);
            }

            $user->accounts()->detach($accountId);
            $user->persons()->detach();

            $user->delete();

            foreach ($personIds as $personId) {
                $stillLinked = AccountPerson::query()->where('person_id', $personId)->exists()
                    || DB::table('user_person')->where('person_id', $personId)->exists();

                if (! $stillLinked) {
                    Person::query()->whereKey($personId)->delete();
                }
            }
        });
    }
}
