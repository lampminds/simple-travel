<?php

namespace App\Services;

use App\Models\AccountRelationship;
use App\Models\UserInvitation;

final class AccountRelationshipService
{
    public function approveFromExternalInvitation(UserInvitation $invitation, int $providerAccountId, ?int $approvedByUserId = null): AccountRelationship
    {
        $operatorAccountId = (int) ($invitation->account_inviting ?: $invitation->account_id);

        return AccountRelationship::query()->updateOrCreate(
            [
                'operator_account_id' => $operatorAccountId,
                'provider_account_id' => $providerAccountId,
            ],
            [
                'status' => AccountRelationship::STATUS_APPROVED,
                'created_via' => AccountRelationship::CREATED_VIA_INVITATION,
                'source_invitation_id' => $invitation->id,
                'approved_by_user_id' => $approvedByUserId,
                'approved_at' => now(),
                'suspended_at' => null,
                'terminated_at' => null,
            ]
        );
    }
}

