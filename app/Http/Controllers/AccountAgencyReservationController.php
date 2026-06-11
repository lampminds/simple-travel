<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PackageOffer;
use App\Services\AgencyBookingFromPackageOfferService;
use App\Services\BookingPriceBreakdownService;
use App\Services\OperatorPackageAgencyPriceResolver;
use App\Services\PriceFormatService;
use App\Support\AccountBusinessTypeGate;
use App\Support\BookingPassengersSnapshot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountAgencyReservationController extends Controller
{
    public function __construct(
        private readonly OperatorPackageAgencyPriceResolver $packagePriceResolver,
        private readonly AgencyBookingFromPackageOfferService $bookingCreator,
        private readonly BookingPriceBreakdownService $priceBreakdown,
        private readonly PriceFormatService $priceFormatService,
    ) {
    }

    public function index(Request $request): View
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        $agencyId = (int) $account->id;

        $bookableOffers = PackageOffer::query()
            ->where('agency_id', $agencyId)
            ->where('status', PackageOffer::STATUS_ACCEPTED)
            ->where('availability', PackageOffer::AVAILABILITY_ACTIVE)
            ->whereHas('catalog', fn ($query) => $query->where('status', 'active'))
            ->with([
                'operatorAccount',
                'catalog.translations.language.locale',
                'priceList.currency.lmpCurrency',
            ])
            ->orderByDesc('offered_at')
            ->orderByDesc('id')
            ->get();

        foreach ($bookableOffers as $offer) {
            $offer->setAttribute('package_label', $this->packageLabelForOffer($offer));
            $offer->setAttribute('agency_price', $this->resolveAgencyPrice($offer, $agencyId));
        }

        $bookings = Booking::query()
            ->where('agency_id', $agencyId)
            ->with([
                'operatorAccount',
                'packageOffer.catalog.translations.language.locale',
                'status.translations.language.locale',
                'currency',
            ])
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        foreach ($bookings as $booking) {
            $booking->setAttribute('total_formatted', $this->priceFormatService->formatWithCurrency(
                (float) $booking->subtotal,
                currency: $booking->currency,
                accountId: (int) $booking->operator_id,
            ));
        }

        return view('account.reservations.agency.index', [
            'account' => $account,
            'bookableOffers' => $bookableOffers,
            'bookings' => $bookings,
        ]);
    }

    public function create(Request $request, PackageOffer $packageOffer): View
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        $agencyId = (int) $account->id;

        $this->assertBookableOffer($packageOffer, $agencyId);

        $packageOffer->load([
            'operatorAccount',
            'catalog.translations.language.locale',
            'priceList.currency.lmpCurrency',
        ]);

        return view('account.reservations.agency.create', [
            'account' => $account,
            'offer' => $packageOffer,
            'packageLabel' => $this->packageLabelForOffer($packageOffer),
            'agencyPrice' => $this->resolveAgencyPrice($packageOffer, $agencyId),
            'operatorLabel' => $packageOffer->operatorAccount?->commercial_name
                ?? $packageOffer->operatorAccount?->name
                ?? ('#'.$packageOffer->operator_id),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        $agencyId = (int) $account->id;

        $validated = $request->validate(
            array_merge([
                'package_offer_uuid' => ['required', 'string', 'uuid'],
                'travel_start_date' => ['required', 'date'],
                'travel_end_date' => ['required', 'date', 'after_or_equal:travel_start_date'],
                'remarks_customer' => ['nullable', 'string', 'max:2000'],
            ], BookingPassengersSnapshot::validationRules()),
            BookingPassengersSnapshot::validationAttributes(),
        );

        $passengersSnapshot = BookingPassengersSnapshot::normalize($validated['passengers'] ?? []);
        BookingPassengersSnapshot::assertHasPassengers($passengersSnapshot);

        $offer = PackageOffer::query()
            ->where('uuid', $validated['package_offer_uuid'])
            ->firstOrFail();

        $booking = $this->bookingCreator->create($offer, $agencyId, [
            'travel_start_date' => $validated['travel_start_date'],
            'travel_end_date' => $validated['travel_end_date'],
            'passengers_snapshot' => $passengersSnapshot,
            'remarks_customer' => $validated['remarks_customer'] ?? null,
        ]);

        return redirect()
            ->route('account.reservations.show', $booking)
            ->with('status', __('account.reservations.status_created'));
    }

    public function show(Request $request, Booking $booking): View
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        abort_unless((int) $booking->agency_id === (int) $account->id, 404);

        $booking->load([
            'operatorAccount',
            'packageOffer.catalog.translations.language.locale',
            'status.translations.language.locale',
            'currency',
            'items.packageItem',
        ]);

        return view('account.reservations.agency.show', [
            'account' => $account,
            'booking' => $booking,
            'packageLabel' => $this->packageLabelForBooking($booking),
            'priceBreakdown' => $this->priceBreakdown->build($booking),
        ]);
    }

    private function assertBookableOffer(PackageOffer $offer, int $agencyId): void
    {
        abort_unless((int) $offer->agency_id === $agencyId, 404);
        abort_unless($offer->status === PackageOffer::STATUS_ACCEPTED, 404);
        abort_unless($offer->availability === PackageOffer::AVAILABILITY_ACTIVE, 404);
        abort_unless($offer->catalog !== null && $offer->catalog->status === 'active', 404);
    }

    /**
     * @return array{has_amount: bool, formatted: string}
     */
    private function resolveAgencyPrice(PackageOffer $offer, int $agencyId): array
    {
        $catalog = $offer->catalog;
        $priceList = $offer->priceList;
        if ($catalog === null || $priceList === null) {
            return ['has_amount' => false, 'formatted' => '—'];
        }

        $resolved = $this->packagePriceResolver->resolvePackageTotal(
            $catalog,
            $priceList,
            $agencyId,
            (int) $offer->operator_id,
        );

        return [
            'has_amount' => (bool) ($resolved['has_amount'] ?? false),
            'formatted' => (string) ($resolved['formatted'] ?? '—'),
        ];
    }

    private function packageLabelForOffer(PackageOffer $offer): string
    {
        $catalog = $offer->catalog;
        if ($catalog === null) {
            return '—';
        }

        $label = $catalog->displayLabel();

        return $label !== '' ? $label : ('Package #'.$catalog->id);
    }

    private function packageLabelForBooking(Booking $booking): string
    {
        $catalog = $booking->packageOffer?->catalog;
        if ($catalog === null) {
            return '—';
        }

        $label = $catalog->displayLabel();

        return $label !== '' ? $label : ('Package #'.$catalog->id);
    }
}
