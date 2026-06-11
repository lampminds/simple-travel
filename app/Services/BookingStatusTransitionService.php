<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\CatBookingStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Operator decisions on agency bookings (confirm / reject).
 */
final class BookingStatusTransitionService
{
    /** @var list<string> */
    private const OPERATOR_DECIDABLE_STATUS_CODES = [
        'pending_validation',
        'pending_availability',
    ];

    public function __construct(private readonly BookingNotificationService $notifications)
    {
    }

    public function confirm(Booking $booking, User $actingUser, int $operatorAccountId): Booking
    {
        $this->assertOperatorCanDecide($booking, $operatorAccountId);

        $confirmedStatusId = $this->mainStatusId('confirmed');
        $itemPendingStatusId = $this->itemStatusId('pending');

        DB::transaction(function () use ($booking, $confirmedStatusId, $itemPendingStatusId, $actingUser): void {
            $booking->update(['status_id' => $confirmedStatusId]);

            BookingItem::query()
                ->where('booking_id', (int) $booking->id)
                ->whereHas('status', fn ($query) => $query->where('code', 'draft'))
                ->update(['status_id' => $itemPendingStatusId]);

            $remarksInternal = is_array($booking->remarks_internal) ? $booking->remarks_internal : [];
            $remarksInternal['operator_decision'] = [
                'action' => 'confirmed',
                'user_id' => (int) $actingUser->id,
                'user_name' => $actingUser->name,
                'at' => now()->toIso8601String(),
            ];
            $booking->update(['remarks_internal' => $remarksInternal]);
        });

        $booking = $booking->fresh([
            'agencyAccount',
            'operatorAccount',
            'packageOffer.catalog.translations',
            'status.translations.language.locale',
            'currency',
            'items.status.translations.language.locale',
        ]);

        $this->notifications->notifyAgencyOfOperatorDecision($booking, 'confirmed', $actingUser);

        return $booking;
    }

    public function reject(Booking $booking, User $actingUser, int $operatorAccountId, string $reason): Booking
    {
        $this->assertOperatorCanDecide($booking, $operatorAccountId);

        $reason = trim($reason);
        if ($reason === '') {
            throw ValidationException::withMessages([
                'reason' => (string) __('account.reservations.validation.reject_reason_required'),
            ]);
        }

        $rejectedStatusId = $this->mainStatusId('rejected');
        $itemCancelledStatusId = $this->itemStatusId('cancelled');

        DB::transaction(function () use ($booking, $rejectedStatusId, $itemCancelledStatusId, $actingUser, $reason): void {
            $booking->update(['status_id' => $rejectedStatusId]);

            BookingItem::query()
                ->where('booking_id', (int) $booking->id)
                ->whereHas('status', fn ($query) => $query->whereIn('code', ['draft', 'pending', 'requested', 'on_hold']))
                ->update(['status_id' => $itemCancelledStatusId]);

            $remarksInternal = is_array($booking->remarks_internal) ? $booking->remarks_internal : [];
            $remarksInternal['operator_decision'] = [
                'action' => 'rejected',
                'reason' => $reason,
                'user_id' => (int) $actingUser->id,
                'user_name' => $actingUser->name,
                'at' => now()->toIso8601String(),
            ];
            $booking->update(['remarks_internal' => $remarksInternal]);
        });

        $booking = $booking->fresh([
            'agencyAccount',
            'operatorAccount',
            'packageOffer.catalog.translations',
            'status.translations.language.locale',
            'currency',
            'items.status.translations.language.locale',
        ]);

        $this->notifications->notifyAgencyOfOperatorDecision($booking, 'rejected', $actingUser, $reason);

        return $booking;
    }

    public function operatorCanDecide(Booking $booking, int $operatorAccountId): bool
    {
        if ((int) $booking->operator_id !== $operatorAccountId) {
            return false;
        }

        $booking->loadMissing('status');

        return in_array((string) ($booking->status?->code ?? ''), self::OPERATOR_DECIDABLE_STATUS_CODES, true);
    }

    private function assertOperatorCanDecide(Booking $booking, int $operatorAccountId): void
    {
        abort_unless((int) $booking->operator_id === $operatorAccountId, 404);

        $booking->loadMissing('status');
        $currentCode = (string) ($booking->status?->code ?? '');

        if (! in_array($currentCode, self::OPERATOR_DECIDABLE_STATUS_CODES, true)) {
            throw ValidationException::withMessages([
                'booking' => (string) __('account.reservations.validation.decision_not_allowed'),
            ]);
        }
    }

    private function mainStatusId(string $code): int
    {
        $id = CatBookingStatus::query()
            ->where('type', CatBookingStatus::TYPE_MAIN)
            ->where('code', $code)
            ->value('id');

        abort_unless($id !== null, 500);

        return (int) $id;
    }

    private function itemStatusId(string $code): int
    {
        $id = CatBookingStatus::query()
            ->where('type', CatBookingStatus::TYPE_ITEM)
            ->where('code', $code)
            ->value('id');

        abort_unless($id !== null, 500);

        return (int) $id;
    }
}
