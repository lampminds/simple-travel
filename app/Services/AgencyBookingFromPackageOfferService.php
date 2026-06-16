<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\CatBookingStatus;
use App\Models\OperatorPackageItem;
use App\Models\OperatorPriceListItem;
use App\Models\PackageOffer;
use App\Support\BookingPassengersSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Creates an agency booking from an accepted package offer.
 */
final class AgencyBookingFromPackageOfferService
{
    public function __construct(
        private readonly BookingCodeGenerator $bookingCodeGenerator,
        private readonly OperatorPackageAgencyPriceResolver $packagePriceResolver,
        private readonly OperatorPriceListItemPricingService $itemPricingService,
        private readonly BookingNotificationService $bookingNotifications,
        private readonly BookingAvailabilityValidationService $availabilityValidation,
    ) {
    }

    /**
     * @param  array{
     *     travel_start_date: string,
     *     travel_end_date: string,
     *     passengers_snapshot: array{adult: int, child: int, infant: int, senior: int, total: int},
     *     remarks_customer?: string|null
     * }  $payload
     */
    public function create(PackageOffer $offer, int $agencyAccountId, array $payload): Booking
    {
        $this->assertOfferIsBookable($offer, $agencyAccountId);

        $passengersSnapshot = BookingPassengersSnapshot::normalize($payload['passengers_snapshot']);
        BookingPassengersSnapshot::assertHasPassengers($passengersSnapshot);

        $catalog = $offer->catalog;
        $priceList = $offer->priceList;
        if ($catalog === null || $priceList === null) {
            throw ValidationException::withMessages([
                'package_offer' => __('account.reservations.validation.offer_not_bookable'),
            ]);
        }

        $travelStart = Carbon::parse($payload['travel_start_date'])->startOfDay();
        $travelEnd = Carbon::parse($payload['travel_end_date'])->startOfDay();
        if ($travelEnd->lt($travelStart)) {
            throw ValidationException::withMessages([
                'travel_end_date' => __('account.reservations.validation.travel_end_before_start'),
            ]);
        }

        $packageTotal = $this->packagePriceResolver->resolvePackageTotal(
            $catalog,
            $priceList,
            $agencyAccountId,
            (int) $offer->operator_id,
            $travelStart,
            $passengersSnapshot,
        );

        if (! ($packageTotal['has_amount'] ?? false) || ($packageTotal['is_zero'] ?? true)) {
            throw ValidationException::withMessages([
                'package_offer' => __('account.reservations.validation.price_not_available'),
            ]);
        }

        $mainStatusId = CatBookingStatus::query()
            ->where('type', CatBookingStatus::TYPE_MAIN)
            ->where('code', 'pending_validation')
            ->value('id');
        $itemStatusId = CatBookingStatus::query()
            ->where('type', CatBookingStatus::TYPE_ITEM)
            ->where('code', 'draft')
            ->value('id');

        abort_unless($mainStatusId !== null && $itemStatusId !== null, 500);

        $catalog->loadMissing([
            'items.serviceVariant.service',
            'items.serviceOffer.providerAccount',
        ]);

        $includedItems = $catalog->items
            ->filter(fn (OperatorPackageItem $item): bool => $item->inclusion_mode === 'included')
            ->values();

        $this->availabilityValidation->assertPackageOfferAvailable(
            $offer,
            $travelStart,
            $travelEnd,
        );

        $this->availabilityValidation->assertPackageItemsAvailable(
            (int) $offer->operator_id,
            $includedItems,
            $travelStart,
            $travelEnd,
            $passengersSnapshot,
        );

        $listItems = OperatorPriceListItem::query()
            ->where('operator_price_list_id', $priceList->id)
            ->get()
            ->keyBy(fn (OperatorPriceListItem $row) => (int) $row->operator_package_item_id);

        $currencyId = (int) ($packageTotal['currency_id'] ?? $priceList->currency_id);
        $remarksCustomer = trim((string) ($payload['remarks_customer'] ?? ''));

        $pricingMeta = [
            'lines_subtotal' => (float) ($packageTotal['lines_subtotal'] ?? 0),
            'assignment_adjustment_type' => $packageTotal['assignment_adjustment_type'] ?? null,
            'assignment_adjustment_value' => $packageTotal['assignment_adjustment_value'] ?? null,
            'assignment_adjustment_amount' => (float) ($packageTotal['assignment_adjustment_amount'] ?? 0),
        ];

        $booking = DB::transaction(function () use (
            $offer,
            $agencyAccountId,
            $travelStart,
            $travelEnd,
            $includedItems,
            $listItems,
            $mainStatusId,
            $itemStatusId,
            $packageTotal,
            $currencyId,
            $remarksCustomer,
            $catalog,
            $passengersSnapshot,
            $pricingMeta,
        ): Booking {
            $booking = Booking::query()->create([
                'booking_code' => $this->bookingCodeGenerator->generate(),
                'operator_id' => (int) $offer->operator_id,
                'agency_id' => $agencyAccountId,
                'package_offer_id' => (int) $offer->id,
                'status_id' => (int) $mainStatusId,
                'booking_source' => 'agency',
                'travel_start_date' => $travelStart->toDateString(),
                'travel_end_date' => $travelEnd->toDateString(),
                'passengers_snapshot' => $passengersSnapshot,
                'subtotal' => (float) $packageTotal['amount'],
                'currency_id' => $currencyId,
                'billing_type' => 'agency',
                'remarks_customer' => $remarksCustomer !== '' ? ['note' => $remarksCustomer] : null,
                'remarks_internal' => [
                    'package_label' => $catalog->displayLabel(),
                    'operator_price_list_id' => (int) $offer->operator_price_list_id,
                    'pricing' => $pricingMeta,
                ],
            ]);

            foreach ($includedItems as $packageItem) {
                $listItem = $listItems->get((int) $packageItem->id);
                if ($listItem === null) {
                    continue;
                }

                $line = $this->itemPricingService->calculate(
                    $packageItem,
                    (int) $offer->operator_id,
                    $currencyId,
                    $this->normalizeListItemMode($listItem),
                    (float) $listItem->price,
                    $travelStart,
                );

                $unitPrice = $line['final_price_has'] && $line['final_price'] !== null
                    ? (float) $line['final_price']
                    : 0.0;
                $quantity = BookingPassengersSnapshot::lineQuantity($packageItem, $passengersSnapshot);
                $lineTotal = $unitPrice * $quantity;

                BookingItem::query()->create([
                    'booking_id' => (int) $booking->id,
                    'operator_package_item_id' => (int) $packageItem->id,
                    'status_id' => (int) $itemStatusId,
                    'start_date' => $travelStart->toDateString(),
                    'end_date' => $travelEnd->toDateString(),
                    'quantity' => $quantity,
                    'price' => $unitPrice,
                    'currency_id' => $currencyId,
                    'package_snapshot' => $this->buildPackageSnapshot($packageItem),
                    'total' => $lineTotal,
                ]);
            }

            return $booking->fresh([
                'operatorAccount',
                'agencyAccount',
                'packageOffer.catalog.translations.language.locale',
                'status.translations.language.locale',
                'currency',
                'items',
            ]);
        });

        $this->bookingNotifications->notifyOperatorOfCreatedBooking($booking);

        return $booking;
    }

    private function assertOfferIsBookable(PackageOffer $offer, int $agencyAccountId): void
    {
        if ((int) $offer->agency_id !== $agencyAccountId) {
            throw ValidationException::withMessages([
                'package_offer' => __('account.reservations.validation.offer_not_bookable'),
            ]);
        }

        if ($offer->status !== PackageOffer::STATUS_ACCEPTED) {
            throw ValidationException::withMessages([
                'package_offer' => __('account.reservations.validation.offer_not_accepted'),
            ]);
        }

        if ($offer->availability !== PackageOffer::AVAILABILITY_ACTIVE) {
            throw ValidationException::withMessages([
                'package_offer' => __('account.reservations.validation.offer_not_active'),
            ]);
        }

        $catalog = $offer->catalog;
        if ($catalog === null || $catalog->status !== 'active') {
            throw ValidationException::withMessages([
                'package_offer' => __('account.reservations.validation.offer_not_bookable'),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPackageSnapshot(OperatorPackageItem $item): array
    {
        $service = $item->serviceVariant?->service;
        $provider = $item->serviceOffer?->providerAccount;

        return [
            'operator_package_item_id' => (int) $item->id,
            'day_number' => $item->day_number,
            'service_name' => trim((string) ($service?->name ?? '')),
            'variant_sku' => trim((string) ($item->serviceVariant?->sku ?? '')),
            'provider_name' => trim((string) ($provider?->commercial_name ?? $provider?->name ?? '')),
            'quantity' => max(1, (int) $item->quantity),
            'pricing_type' => (string) ($item->serviceVariant?->pricing_type ?? ''),
            'inclusion_mode' => (string) $item->inclusion_mode,
            'notes' => trim((string) ($item->notes ?? '')),
        ];
    }

    private function normalizeListItemMode(OperatorPriceListItem $listItem): ?string
    {
        $mode = $listItem->pricing_mode;
        if ($mode === null || $mode === '') {
            return null;
        }

        return $this->itemPricingService->normalizeMode($mode);
    }
}
