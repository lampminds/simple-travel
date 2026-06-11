<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CatBookingStatus;
use App\Services\BookingPriceBreakdownService;
use App\Services\BookingStatusTransitionService;
use App\Services\PriceFormatService;
use App\Support\AccountBusinessTypeGate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountOperatorReservationController extends Controller
{
    public function __construct(
        private readonly BookingPriceBreakdownService $priceBreakdown,
        private readonly BookingStatusTransitionService $statusTransitions,
        private readonly PriceFormatService $priceFormatService,
    ) {
    }

    public function index(Request $request): View
    {
        $account = AccountBusinessTypeGate::assertOperatorAccount($request);
        $operatorId = (int) $account->id;
        $statusFilter = (string) $request->query('status', 'pending');

        $query = Booking::query()
            ->where('operator_id', $operatorId)
            ->with([
                'agencyAccount',
                'packageOffer.catalog.translations.language.locale',
                'status.translations.language.locale',
                'currency',
            ])
            ->orderByDesc('id');

        if ($statusFilter === 'pending') {
            $pendingStatusIds = CatBookingStatus::query()
                ->where('type', CatBookingStatus::TYPE_MAIN)
                ->whereIn('code', ['pending_validation', 'pending_availability'])
                ->pluck('id');
            $query->whereIn('status_id', $pendingStatusIds);
        } elseif ($statusFilter !== 'all') {
            $statusId = CatBookingStatus::query()
                ->where('type', CatBookingStatus::TYPE_MAIN)
                ->where('code', $statusFilter)
                ->value('id');
            if ($statusId !== null) {
                $query->where('status_id', (int) $statusId);
            }
        }

        $bookings = $query->limit(100)->get();

        foreach ($bookings as $booking) {
            $booking->setAttribute('total_formatted', $this->priceFormatService->formatWithCurrency(
                (float) $booking->subtotal,
                currency: $booking->currency,
                accountId: $operatorId,
            ));
        }

        return view('account.reservations.operator.index', [
            'account' => $account,
            'bookings' => $bookings,
            'statusFilter' => $statusFilter,
            'statusFilterOptions' => $this->statusFilterOptions(),
        ]);
    }

    public function show(Request $request, Booking $booking): View
    {
        $account = AccountBusinessTypeGate::assertOperatorAccount($request);
        abort_unless((int) $booking->operator_id === (int) $account->id, 404);

        $booking->load([
            'agencyAccount',
            'packageOffer.catalog.translations.language.locale',
            'status.translations.language.locale',
            'currency',
            'items.packageItem',
        ]);

        return view('account.reservations.operator.show', [
            'account' => $account,
            'booking' => $booking,
            'packageLabel' => $this->packageLabelForBooking($booking),
            'priceBreakdown' => $this->priceBreakdown->build($booking),
            'canDecide' => $this->statusTransitions->operatorCanDecide($booking, (int) $account->id),
            'customerRemarks' => $this->customerRemarks($booking),
        ]);
    }

    public function confirm(Request $request, Booking $booking): RedirectResponse
    {
        $account = AccountBusinessTypeGate::assertOperatorAccount($request);
        abort_unless((int) $booking->operator_id === (int) $account->id, 404);

        $user = $request->user();
        abort_unless($user !== null, 401);

        $this->statusTransitions->confirm($booking, $user, (int) $account->id);

        return redirect()
            ->route('account.operator.reservations.show', $booking)
            ->with('status', __('account.reservations.operator_status_confirmed'));
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        $account = AccountBusinessTypeGate::assertOperatorAccount($request);
        abort_unless((int) $booking->operator_id === (int) $account->id, 404);

        $user = $request->user();
        abort_unless($user !== null, 401);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        $this->statusTransitions->reject($booking, $user, (int) $account->id, $validated['reason']);

        return redirect()
            ->route('account.operator.reservations.show', $booking)
            ->with('status', __('account.reservations.operator_status_rejected'));
    }

    /**
     * @return array<string, string>
     */
    private function statusFilterOptions(): array
    {
        return [
            'pending' => (string) __('account.reservations.operator_filter_pending'),
            'confirmed' => (string) __('account.reservations.operator_filter_confirmed'),
            'rejected' => (string) __('account.reservations.operator_filter_rejected'),
            'all' => (string) __('account.reservations.operator_filter_all'),
        ];
    }

    private function packageLabelForBooking(Booking $booking): string
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

    private function customerRemarks(Booking $booking): ?string
    {
        $remarks = $booking->remarks_customer;
        if (! is_array($remarks)) {
            return null;
        }

        $note = trim((string) ($remarks['note'] ?? ''));

        return $note !== '' ? $note : null;
    }
}
