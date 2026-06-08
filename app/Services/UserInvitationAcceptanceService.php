<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Str;

final class UserInvitationAcceptanceService
{
    /**
     * Try to accept an external invitation for an already registered user.
     * Returns true when accepted (or already accepted but relation ensured), false otherwise.
     */
    /**
     * Mark an internal employee invitation as accepted when the invitee already had an active login.
     * Membership and role were provisioned when the invitation was sent.
     */
    public function acceptInternalForExistingUser(UserInvitation $invitation, User $user): bool
    {
        if ($invitation->type !== UserInvitation::TYPE_INTERNAL) {
            return false;
        }

        if ($invitation->invited_user_id === null || (int) $invitation->invited_user_id !== (int) $user->getKey()) {
            return false;
        }

        $invitationEmail = Str::lower((string) $invitation->email);
        $userEmail = Str::lower((string) $user->email);
        if ($invitationEmail === '' || $invitationEmail !== $userEmail) {
            return false;
        }

        if ($invitation->status !== UserInvitation::STATUS_PENDING || ! $invitation->isUsable()) {
            return false;
        }

        $invitation->forceFill([
            'name' => Str::title((string) $user->name),
            'status' => UserInvitation::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ])->save();

        return true;
    }

    public function acceptExternalForExistingUser(UserInvitation $invitation, User $user): bool
    {
        if ($invitation->type !== UserInvitation::TYPE_EXTERNAL) {
            return false;
        }

        $invitationEmail = Str::lower((string) $invitation->email);
        $userEmail = Str::lower((string) $user->email);
        if ($invitationEmail === '' || $invitationEmail !== $userEmail) {
            return false;
        }

        $providerAccountId = $this->resolveProviderAccountId($user, $invitation);
        if ($providerAccountId === null) {
            return false;
        }

        if ($invitation->status === UserInvitation::STATUS_PENDING && $invitation->isUsable()) {
            $invitation->forceFill([
                'name' => Str::title((string) $user->name),
                'status' => UserInvitation::STATUS_ACCEPTED,
                'accepted_at' => now(),
            ])->save();
        } elseif ($invitation->status !== UserInvitation::STATUS_ACCEPTED) {
            return false;
        }

        app(AccountRelationshipService::class)->approveFromExternalInvitation(
            invitation: $invitation,
            providerAccountId: $providerAccountId,
            approvedByUserId: (int) $user->id
        );

        $providerAccount = Account::query()->find($providerAccountId);
        if ($providerAccount !== null) {
            app(AccountNotificationService::class)->createForExternalInvitationAccepted(
                invitation: $invitation,
                providerAccount: $providerAccount,
                providerAlreadyExisted: true,
            );
        }

        return true;
    }

    private function accountCanReceiveExternalInvitation(Account $account): bool
    {
        return $account->accountTypes()
            ->where('cat_account_types.active', true)
            ->whereIn('cat_account_types.code', ['provider', 'agency'])
            ->exists();
    }

    private function resolveProviderAccountId(User $user, ?UserInvitation $invitation = null): ?int
    {
        if ($invitation !== null && $invitation->invited_account_id !== null) {
            $targetId = (int) $invitation->invited_account_id;
            if ($user->belongsToAccount($targetId)) {
                return $targetId;
            }

            return null;
        }

        $currentAccount = $user->currentAccount();
        if ($currentAccount && $this->accountCanReceiveExternalInvitation($currentAccount)) {
            return (int) $currentAccount->id;
        }

        $invitedAccountId = Account::query()
            ->whereIn('id', $user->accounts()->pluck('accounts.id'))
            ->whereHas(
                'accountTypes',
                fn ($query) => $query
                    ->whereIn('cat_account_types.code', ['provider', 'agency'])
                    ->where('cat_account_types.active', true)
            )
            ->orderBy('id')
            ->value('id');

        return $invitedAccountId !== null ? (int) $invitedAccountId : null;
    }
}

