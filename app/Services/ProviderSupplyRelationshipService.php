<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProviderSupplyRelationship;
use App\Models\UserInvitation;

final class ProviderSupplyRelationshipService
{
    public function approveFromInvitation(
        UserInvitation $invitation,
        int $supplierAccountId,
        ?int $approvedByUserId = null,
    ): ProviderSupplyRelationship {
        $buyerAccountId = (int) ($invitation->account_inviting ?: $invitation->account_id);

        return ProviderSupplyRelationship::query()->updateOrCreate(
            [
                'buyer_account_id' => $buyerAccountId,
                'supplier_account_id' => $supplierAccountId,
            ],
            [
                'status' => ProviderSupplyRelationship::STATUS_APPROVED,
                'created_via' => ProviderSupplyRelationship::CREATED_VIA_INVITATION,
                'source_invitation_id' => $invitation->id,
                'approved_by_user_id' => $approvedByUserId,
                'approved_at' => now(),
                'suspended_at' => null,
                'terminated_at' => null,
            ]
        );
    }
}
