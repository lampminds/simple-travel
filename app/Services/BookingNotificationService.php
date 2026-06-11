<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingCreatedNotification;
use App\Support\BookingPassengersSnapshot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

/**
 * In-app and email notifications for booking lifecycle events.
 */
final class BookingNotificationService
{
    public function __construct(
        private readonly AccountNotificationService $accountNotifications,
        private readonly PriceFormatService $priceFormatService,
    ) {
    }

    public function notifyOperatorOfCreatedBooking(Booking $booking): void
    {
        $booking->loadMissing([
            'agencyAccount',
            'packageOffer.catalog.translations',
            'currency',
        ]);

        $operatorAccountId = (int) $booking->operator_id;
        $agencyLabel = $this->accountLabel($booking->agencyAccount, (int) $booking->agency_id);
        $packageLabel = $this->packageLabel($booking);
        $passengersSummary = BookingPassengersSnapshot::formatSummary($booking->passengers_snapshot);
        $totalFormatted = $this->priceFormatService->formatWithCurrency(
            (float) $booking->subtotal,
            currency: $booking->currency,
            accountId: $operatorAccountId,
        );
        $url = route('account.operator.reservations.show', $booking, true);

        $this->accountNotifications->createForAccount(
            accountId: $operatorAccountId,
            type: 'booking_created',
            title: (string) __('account.reservations.notifications.operator_created_title', [
                'code' => $booking->booking_code,
            ]),
            message: (string) __('account.reservations.notifications.operator_created_message', [
                'agency' => $agencyLabel,
                'package' => $packageLabel,
                'dates' => $this->travelDatesLabel($booking),
                'passengers' => $passengersSummary,
                'total' => $totalFormatted,
                'url' => $url,
            ]),
            recipientUserId: null,
            data: [
                'booking_id' => (int) $booking->id,
                'booking_uuid' => $booking->uuid,
                'booking_code' => $booking->booking_code,
                'agency_account_id' => (int) $booking->agency_id,
                'agency_account_name' => $agencyLabel,
                'package_label' => $packageLabel,
            ],
        );

        $this->sendEmailToAccountOwners(
            accountId: $operatorAccountId,
            notification: new BookingCreatedNotification($booking, $agencyLabel, $packageLabel, $url),
        );
    }

    public function notifyAgencyOfOperatorDecision(
        Booking $booking,
        string $decision,
        User $actingUser,
        ?string $reason = null,
    ): void {
        $booking->loadMissing([
            'operatorAccount',
            'packageOffer.catalog.translations',
            'status.translations.language.locale',
        ]);

        $agencyAccountId = (int) $booking->agency_id;
        $operatorLabel = $this->accountLabel($booking->operatorAccount, (int) $booking->operator_id);
        $packageLabel = $this->packageLabel($booking);
        $url = route('account.reservations.show', $booking, true);
        $reason = trim((string) ($reason ?? ''));

        if ($decision === 'confirmed') {
            $title = (string) __('account.reservations.notifications.agency_confirmed_title', [
                'code' => $booking->booking_code,
            ]);
            $message = (string) __('account.reservations.notifications.agency_confirmed_message', [
                'operator' => $operatorLabel,
                'package' => $packageLabel,
                'url' => $url,
            ]);
            $type = 'booking_confirmed';
        } else {
            $title = (string) __('account.reservations.notifications.agency_rejected_title', [
                'code' => $booking->booking_code,
            ]);
            $message = (string) __('account.reservations.notifications.agency_rejected_message', [
                'operator' => $operatorLabel,
                'package' => $packageLabel,
                'reason' => $reason !== '' ? $reason : __('account.reservations.notifications.no_reason'),
                'url' => $url,
            ]);
            $type = 'booking_rejected';
        }

        $this->accountNotifications->createForAccount(
            accountId: $agencyAccountId,
            type: $type,
            title: $title,
            message: $message,
            recipientUserId: null,
            data: [
                'booking_id' => (int) $booking->id,
                'booking_uuid' => $booking->uuid,
                'booking_code' => $booking->booking_code,
                'operator_account_id' => (int) $booking->operator_id,
                'operator_account_name' => $operatorLabel,
                'decision' => $decision,
                'reason' => $reason !== '' ? $reason : null,
                'decided_by_user_id' => (int) $actingUser->id,
                'decided_by_user_name' => $actingUser->name,
            ],
        );
    }

    private function travelDatesLabel(Booking $booking): string
    {
        return locale_date($booking->travel_start_date).' — '.locale_date($booking->travel_end_date);
    }

    private function packageLabel(Booking $booking): string
    {
        $internal = is_array($booking->remarks_internal) ? $booking->remarks_internal : [];
        $fromRemarks = trim((string) ($internal['package_label'] ?? ''));
        if ($fromRemarks !== '') {
            return $fromRemarks;
        }

        $catalog = $booking->packageOffer?->catalog;
        if ($catalog === null) {
            return '—';
        }

        $label = $catalog->displayLabel();

        return $label !== '' ? $label : ('Package #'.$catalog->id);
    }

    private function accountLabel(?Account $account, int $fallbackId): string
    {
        if ($account instanceof Account) {
            return (string) ($account->commercial_name ?: $account->name ?: $account->nick ?: '#'.$account->id);
        }

        return '#'.$fallbackId;
    }

    private function sendEmailToAccountOwners(int $accountId, object $notification): void
    {
        $owners = $this->ownerUsersForAccount($accountId);
        if ($owners->isEmpty()) {
            return;
        }

        Notification::send($owners, $notification);
    }

    /**
     * @return Collection<int, User>
     */
    private function ownerUsersForAccount(int $accountId): Collection
    {
        return User::query()
            ->whereHas('accounts', fn ($query) => $query->where('accounts.id', $accountId))
            ->where('activation_state', User::ACTIVATION_ACTIVE)
            ->orderBy('name')
            ->get()
            ->filter(fn (User $user): bool => $user->hasRoleForAccountId('owner', $accountId))
            ->values();
    }
}
