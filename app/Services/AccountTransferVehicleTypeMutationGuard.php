<?php

namespace App\Services;

use App\Models\ServiceTransferVehicleType;

/**
 * Central place for business rules before mutating account-scoped transfer vehicle types.
 */
final class AccountTransferVehicleTypeMutationGuard
{
    /**
     * Future: block or constrain updates when linked bookings / pricing history exist.
     */
    public function assertCanUpdate(ServiceTransferVehicleType $vehicleType): void
    {
        // ToDo: validate impact on bookings and future transactions before allowing update.
    }

    /**
     * Future: block deletes (or force archive) when linked bookings / transactions exist.
     */
    public function assertCanDelete(ServiceTransferVehicleType $vehicleType): void
    {
        // ToDo: validate impact on bookings and future transactions before allowing delete.
    }
}
