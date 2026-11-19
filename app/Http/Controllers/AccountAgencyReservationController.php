<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\LmpCountry;
use App\Models\PackageOffer;
use App\Models\ProviderBookingField;
use App\Services\AgencyBookingFromPackageOfferService;
use App\Services\BookingPriceBreakdownService;
use App\Services\BookingRequiredFieldsResolver;
use App\Services\OperatorPackageAgencyPriceResolver;
use App\Services\PriceFormatService;
use App\Support\AccountBusinessTypeGate;
use App\Support\BookingFieldAnswersPresenter;
use App\Support\BookingPassengersSnapshot;
use App\Support\ResolvedBookingField;
use Carbon\Carbon;
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
        private readonly BookingRequiredFieldsResolver $fieldResolver,
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

        return view('account.reservations.agency.create', array_merge([
            'account' => $account,
            'offer' => $packageOffer,
            'packageLabel' => $this->packageLabelForOffer($packageOffer),
            'agencyPrice' => $this->resolveAgencyPrice($packageOffer, $agencyId),
            'quotedPrice' => null,
            'rowQuotes' => null,
            'partyRows' => BookingPassengersSnapshot::blankPartyRows(),
            'reservationInput' => null,
            'operatorLabel' => $packageOffer->operatorAccount?->commercial_name
                ?? $packageOffer->operatorAccount?->name
                ?? ('#'.$packageOffer->operator_id),
        ], $this->reservationFormFields($packageOffer)));
    }

    public function preview(Request $request): View
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        $agencyId = (int) $account->id;
        $validated = $this->validatedReservation($request, $agencyId, false);
        $passengersSnapshot = BookingPassengersSnapshot::fromAges($validated['ages']);
        BookingPassengersSnapshot::assertHasPassengers($passengersSnapshot);

        $offer = PackageOffer::query()
            ->where('uuid', $validated['package_offer_uuid'])
            ->firstOrFail();

        $this->assertBookableOffer($offer, $agencyId);
        $offer->load([
            'operatorAccount',
            'catalog.translations.language.locale',
            'priceList.currency.lmpCurrency',
        ]);

        $quotedPrice = $this->resolveQuotedPrice($offer, $agencyId, $passengersSnapshot, $validated['travel_start_date']);

        return view('account.reservations.agency.create', array_merge([
            'account' => $account,
            'offer' => $offer,
            'packageLabel' => $this->packageLabelForOffer($offer),
            'agencyPrice' => $this->resolveAgencyPrice($offer, $agencyId),
            'quotedPrice' => $quotedPrice,
            'rowQuotes' => ($quotedPrice['has_amount'] ?? false)
                ? $this->rowQuotes($offer, $agencyId, $validated['party_rows'], $validated['ages'], $validated['travel_start_date'])
                : null,
            'partyRows' => $validated['party_rows'],
            'operatorLabel' => $offer->operatorAccount?->commercial_name
                ?? $offer->operatorAccount?->name
                ?? ('#'.$offer->operator_id),
            'reservationInput' => $validated,
        ], $this->reservationFormFields($offer)));
    }

    public function store(Request $request): RedirectResponse
    {
        $account = AccountBusinessTypeGate::assertAgencyAccount($request);
        $agencyId = (int) $account->id;

        $validated = $this->validatedReservation($request, $agencyId);
        $passengersSnapshot = BookingPassengersSnapshot::fromAges($validated['ages']);
        BookingPassengersSnapshot::assertHasPassengers($passengersSnapshot);

        $offer = PackageOffer::query()
            ->where('uuid', $validated['package_offer_uuid'])
            ->firstOrFail();

        $booking = $this->bookingCreator->create($offer, $agencyId, [
            'travel_start_date' => $validated['travel_start_date'],
            'travel_end_date' => $validated['travel_end_date'],
            'passengers_snapshot' => $passengersSnapshot,
            'remarks_customer' => $validated['remarks_customer'] ?? null,
            'field_answers' => [
                'passenger_fields' => $validated['passenger_fields'] ?? [],
                'booking_fields' => $validated['booking_fields'] ?? [],
            ],
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
            'fieldAnswers' => BookingFieldAnswersPresenter::present($booking),
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

    /**
     * @param  array{ages: list<int>, total: int}  $passengersSnapshot
     * @return array{has_amount: bool, formatted: string}
     */
    private function resolveQuotedPrice(PackageOffer $offer, int $agencyId, array $passengersSnapshot, string $travelStart): array
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
            Carbon::parse($travelStart)->startOfDay(),
            $passengersSnapshot,
        );

        return [
            'has_amount' => (bool) ($resolved['has_amount'] ?? false) && ! ($resolved['is_zero'] ?? true),
            'formatted' => (string) ($resolved['formatted'] ?? '—'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @param  list<array{age: string, quantity: int}>  $partyRows
     * @param  list<int>  $ages
     * @return list<array{base_formatted: string, has_discount: bool, discount_formatted: string}|null>
     */
    private function rowQuotes(PackageOffer $offer, int $agencyId, array $partyRows, array $ages, string $travelStart): array
    {
        $catalog = $offer->catalog;
        $priceList = $offer->priceList;
        if ($catalog === null || $priceList === null) {
            return array_fill(0, count($partyRows), null);
        }

        $perAge = $this->packagePriceResolver->quotePassengerAges(
            $catalog,
            $priceList,
            $agencyId,
            (int) $offer->operator_id,
            Carbon::parse($travelStart)->startOfDay(),
            $ages,
        );

        $quotes = [];
        $cursor = 0;

        foreach ($partyRows as $row) {
            if (($row['age'] ?? '') === '') {
                $quotes[] = null;

                continue;
            }

            $quantity = max(1, (int) ($row['quantity'] ?? 1));
            $base = 0.0;
            $amount = 0.0;
            $priced = false;

            for ($copy = 0; $copy < $quantity; $copy++) {
                $slice = $perAge[$cursor] ?? null;
                $cursor++;
                if (! is_array($slice) || ! ($slice['priced'] ?? false)) {
                    continue;
                }

                $base += (float) $slice['base'];
                $amount += (float) $slice['amount'];
                $priced = true;
            }

            if (! $priced) {
                $quotes[] = null;

                continue;
            }

            $discount = round($base - $amount, 2);
            $quotes[] = [
                'base_formatted' => $this->priceFormatService->formatWithCurrency(
                    $base,
                    currency: $priceList->currency,
                    accountId: (int) $offer->operator_id,
                ),
                'has_discount' => $discount > 0.009,
                'discount_formatted' => $this->priceFormatService->formatWithCurrency(
                    max(0, $discount),
                    currency: $priceList->currency,
                    accountId: (int) $offer->operator_id,
                ),
            ];
        }

        return $quotes;
    }

    private function validatedReservation(Request $request, int $agencyId, bool $requireExtraFields = true): array
    {
        $validated = $request->validate(
            array_merge([
                'package_offer_uuid' => ['required', 'string', 'uuid'],
                'travel_start_date' => ['required', 'date'],
                'travel_end_date' => ['required', 'date', 'after_or_equal:travel_start_date'],
                'remarks_customer' => ['nullable', 'string', 'max:2000'],
            ], BookingPassengersSnapshot::partyRowRules()),
            [],
            BookingPassengersSnapshot::partyRowAttributes(),
        );

        $expanded = BookingPassengersSnapshot::expandPartyRows(
            is_array($validated['party_rows'] ?? null) ? $validated['party_rows'] : [],
        );
        $validated['party_rows'] = $expanded['rows'];
        $validated['ages'] = $expanded['ages'];

        $offer = PackageOffer::query()
            ->where('uuid', $validated['package_offer_uuid'])
            ->firstOrFail();
        $this->assertBookableOffer($offer, $agencyId);

        $resolved = $this->fieldResolver->forPackageOffer($offer);
        $spec = $this->fieldResolver->validationSpec($resolved, count($validated['ages']));
        if (! $requireExtraFields || $spec['rules'] === []) {
            $postedPassengers = $request->input('passenger_fields', []);
            $postedBooking = $request->input('booking_fields', []);
            $answers = [
                'passenger_fields' => is_array($postedPassengers) ? $postedPassengers : [],
                'booking_fields' => is_array($postedBooking) ? $postedBooking : [],
            ];
        } else {
            $answers = $request->validate($spec['rules'], $spec['messages'], $spec['attributes']);
        }

        $validated['passenger_fields'] = is_array($answers['passenger_fields'] ?? null) ? $answers['passenger_fields'] : [];
        $validated['booking_fields'] = is_array($answers['booking_fields'] ?? null) ? $answers['booking_fields'] : [];

        return $validated;
    }

    /**
     * @return array{
     *     passengerFields: list<ResolvedBookingField>,
     *     bookingFields: list<ResolvedBookingField>,
     *     countries: \Illuminate\Support\Collection<int, LmpCountry>
     * }
     */
    private function reservationFormFields(PackageOffer $offer): array
    {
        $resolved = $this->fieldResolver->forPackageOffer($offer);
        $needsCountries = false;
        foreach (array_merge($resolved['passenger'], $resolved['booking']) as $field) {
            if ($field->inputType === ProviderBookingField::TYPE_COUNTRY) {
                $needsCountries = true;
                break;
            }
        }

        return [
            'passengerFields' => $resolved['passenger'],
            'bookingFields' => $resolved['booking'],
            'countries' => $needsCountries
                ? LmpCountry::query()->orderBy('name')->get(['id', 'name'])
                : collect(),
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
