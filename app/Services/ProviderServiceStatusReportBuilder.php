<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\BookingItem;
use App\Models\OperatorPackageItem;
use App\Models\OperatorServiceCatalog;
use App\Models\Service;
use App\Models\ServiceComponent;
use App\Models\ServiceOffer;
use App\Models\ServiceVariant;
use App\Support\BookingPassengersSnapshot;
use App\Support\ServiceCatalogStatus;

/**
 * Printable status of one provider service: identity, reservations,
 * combo membership, and operator packages that include it.
 */
final class ProviderServiceStatusReportBuilder
{
    public function __construct(
        private readonly PriceFormatService $priceFormat,
    ) {}

    /**
     * @return array{
     *     generated_at: string,
     *     is_combo: bool,
     *     general: array<string, mixed>,
     *     variants: list<array<string, string>>,
     *     composition: list<array<string, mixed>>,
     *     combos: list<array<string, mixed>>,
     *     packages: list<array<string, string>>,
     *     direct_services: list<array<string, string>>,
     *     bookings: list<array<string, string>>
     * }
     */
    public function build(Service $service): array
    {
        $service->load([
            'translations.language.locale',
            'serviceType.translations.language.locale',
            'city',
            'serviceVariants.translations.language.locale',
            'serviceVariants.currency.lmpCurrency',
        ]);

        $variantIds = $service->serviceVariants
            ->map(fn (ServiceVariant $variant): int => (int) $variant->id)
            ->all();

        $bookingsByVariant = $this->bookingsByVariantId($variantIds);
        $ownBookings = [];
        foreach ($variantIds as $variantId) {
            foreach ($bookingsByVariant[$variantId] ?? [] as $row) {
                $ownBookings[] = $row;
            }
        }
        usort($ownBookings, fn (array $a, array $b): int => strcmp($b['sort_date'], $a['sort_date']));

        return [
            'generated_at' => locale_datetime(now()),
            'is_combo' => $service->isCombo(),
            'general' => $this->general($service),
            'variants' => $this->variants($service),
            'composition' => $service->isCombo() ? $this->composition($service) : [],
            'combos' => $this->combos($variantIds),
            'packages' => $this->catalogLines($service, OperatorServiceCatalog::KIND_PACKAGE),
            'direct_services' => $this->catalogLines($service, OperatorServiceCatalog::KIND_SERVICE),
            'bookings' => $ownBookings,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function general(Service $service): array
    {
        $status = ServiceCatalogStatus::forService($service->status);
        $description = trim(strip_tags((string) ($service->description ?? '')));
        $bookingMode = (string) ($service->booking_mode ?? '');
        $bookingModeLabel = $bookingMode === ''
            ? '—'
            : (string) __('wizard.step2_booking_mode.'.$bookingMode);
        if ($bookingModeLabel === 'wizard.step2_booking_mode.'.$bookingMode) {
            $bookingModeLabel = $bookingMode !== '' ? $bookingMode : '—';
        }

        $typeName = trim((string) ($service->serviceType?->name ?? ''));
        if ($typeName === '') {
            $typeName = trim((string) ($service->serviceType?->code ?? ''));
        }

        return [
            'name' => trim((string) $service->name) !== '' ? trim((string) $service->name) : ('#'.$service->id),
            'description' => $description !== '' ? $description : null,
            'type' => $typeName !== '' ? $typeName : '—',
            'status_label' => $status['label'],
            'status_badge' => $status['badge'],
            'city' => trim((string) ($service->city?->name ?? '')) !== '' ? trim((string) $service->city->name) : '—',
            'booking_mode' => $bookingModeLabel,
            'confirmation_hours' => $service->confirmation_time_hours !== null
                ? (string) $service->confirmation_time_hours
                : '—',
            'is_public' => $service->is_public
                ? (string) __('account.service_status_report.yes')
                : (string) __('account.service_status_report.no'),
            'is_featured' => $service->is_featured
                ? (string) __('account.service_status_report.yes')
                : (string) __('account.service_status_report.no'),
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private function variants(Service $service): array
    {
        $rows = [];
        foreach ($service->serviceVariants as $variant) {
            $status = ServiceCatalogStatus::forVariant($variant->status);
            $name = trim((string) $variant->name);
            $rows[] = [
                'sku' => trim((string) ($variant->sku ?? '')) !== '' ? trim((string) $variant->sku) : '—',
                'name' => $name !== '' ? $name : '—',
                'status_label' => $status['label'],
                'status_badge' => $status['badge'],
                'price' => $this->priceFormat->formatWithCurrency(
                    $variant->base_price,
                    $variant->currency,
                    null,
                    (int) $service->account_id,
                ),
            ];
        }

        return $rows;
    }

    /**
     * Components of this combo, grouped by its own variant.
     *
     * @return list<array<string, mixed>>
     */
    private function composition(Service $service): array
    {
        $service->load([
            'components.parentVariant.translations.language.locale',
            'components.childVariant.translations.language.locale',
            'components.childVariant.service.translations.language.locale',
        ]);

        $groups = [];
        foreach ($service->components as $line) {
            $parent = $line->parentVariant;
            $child = $line->childVariant;
            if ($parent === null || $child === null) {
                continue;
            }

            $parentId = (int) $parent->id;
            if (! isset($groups[$parentId])) {
                $variantStatus = ServiceCatalogStatus::forVariant($parent->status);
                $groups[$parentId] = [
                    'variant_label' => $parent->dropdownLabel(false),
                    'variant_status_label' => $variantStatus['label'],
                    'variant_status_badge' => $variantStatus['badge'],
                    'lines' => [],
                ];
            }

            $groups[$parentId]['lines'][] = $this->componentLine($line, true);
        }

        return array_values($groups);
    }

    /**
     * Combos that use a variant of this service, with the reservations of that combo.
     *
     * @param  list<int>  $childVariantIds
     * @return list<array<string, mixed>>
     */
    private function combos(array $childVariantIds): array
    {
        if ($childVariantIds === []) {
            return [];
        }

        $lines = ServiceComponent::query()
            ->whereIn('child_variant_id', $childVariantIds)
            ->with([
                'childVariant.translations.language.locale',
                'parentVariant.translations.language.locale',
                'parentVariant.service.translations.language.locale',
            ])
            ->get();

        if ($lines->isEmpty()) {
            return [];
        }

        $parentVariantIds = $lines
            ->map(fn (ServiceComponent $line): int => (int) $line->parent_variant_id)
            ->unique()
            ->values()
            ->all();
        $bookingsByVariant = $this->bookingsByVariantId($parentVariantIds);

        $groups = [];
        foreach ($lines as $line) {
            $parent = $line->parentVariant;
            $child = $line->childVariant;
            if ($parent === null || $child === null) {
                continue;
            }

            $parentId = (int) $parent->id;
            if (! isset($groups[$parentId])) {
                $serviceStatus = ServiceCatalogStatus::forService($parent->service?->status);
                $variantStatus = ServiceCatalogStatus::forVariant($parent->status);
                $serviceName = trim((string) ($parent->service?->name ?? ''));
                $bookings = $bookingsByVariant[$parentId] ?? [];
                usort($bookings, fn (array $a, array $b): int => strcmp($b['sort_date'], $a['sort_date']));
                $groups[$parentId] = [
                    'service_name' => $serviceName !== '' ? $serviceName : '—',
                    'service_status_label' => $serviceStatus['label'],
                    'service_status_badge' => $serviceStatus['badge'],
                    'variant_label' => $parent->dropdownLabel(false),
                    'variant_status_label' => $variantStatus['label'],
                    'variant_status_badge' => $variantStatus['badge'],
                    'lines' => [],
                    'bookings' => $bookings,
                ];
            }

            $groups[$parentId]['lines'][] = $this->componentLine($line, false);
        }

        $groups = array_values($groups);
        usort($groups, function (array $a, array $b): int {
            return strcmp($a['service_name'].$a['variant_label'], $b['service_name'].$b['variant_label']);
        });

        return $groups;
    }

    /**
     * @return array{label: string, quantity: string, day: string, inclusion: string}
     */
    private function componentLine(ServiceComponent $line, bool $includeChildServiceName): array
    {
        $child = $line->childVariant;

        return [
            'label' => $child?->dropdownLabel($includeChildServiceName) ?? '—',
            'quantity' => (string) max(1, (int) $line->quantity),
            'day' => $line->day_number !== null ? (string) $line->day_number : '—',
            'inclusion' => $this->inclusionLabel((string) $line->inclusion_mode),
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private function catalogLines(Service $service, string $catalogKind): array
    {
        $items = OperatorPackageItem::query()
            ->whereHas('serviceVariant', fn ($query) => $query->where('service_id', $service->id))
            ->whereHas('catalog', fn ($query) => $query->where('catalog_kind', $catalogKind))
            ->with([
                'catalog.translations.language.locale',
                'catalog.operator',
                'serviceVariant.translations.language.locale',
                'serviceVariant.service.translations.language.locale',
                'serviceOffer',
            ])
            ->get();

        $rows = [];
        foreach ($items as $item) {
            $catalog = $item->catalog;
            $variant = $item->serviceVariant;
            if ($catalog === null || $variant === null) {
                continue;
            }

            $packageStatus = (string) ($catalog->status ?? '');
            $packageStatusKey = 'account.operator_packages.status.'.$packageStatus;
            $packageStatusLabel = __($packageStatusKey);
            if ($packageStatusLabel === $packageStatusKey) {
                $packageStatusLabel = $packageStatus !== '' ? $packageStatus : '—';
            }

            $rows[] = [
                'operator' => $this->accountLabel($catalog->operator),
                'package' => $catalog->displayLabel(),
                'package_status' => (string) $packageStatusLabel,
                'variant' => $variant->dropdownLabel(false),
                'day' => $item->day_number !== null ? (string) $item->day_number : '—',
                'quantity' => (string) max(1, (int) $item->quantity),
                'inclusion' => $this->inclusionLabel((string) $item->inclusion_mode),
                'offer' => $this->offerLabel($item->serviceOffer),
            ];
        }

        usort($rows, function (array $a, array $b): int {
            return strcmp($a['operator'].$a['package'].$a['variant'], $b['operator'].$b['package'].$b['variant']);
        });

        return $rows;
    }

    /**
     * @param  list<int>  $variantIds
     * @return array<int, list<array<string, string>>>
     */
    private function bookingsByVariantId(array $variantIds): array
    {
        if ($variantIds === []) {
            return [];
        }

        $items = BookingItem::query()
            ->whereHas('packageItem', fn ($query) => $query->whereIn('service_variant_id', $variantIds))
            ->with([
                'status.translations.language.locale',
                'booking.operatorAccount',
                'booking.agencyAccount',
                'booking.status.translations.language.locale',
                'packageItem.catalog.translations.language.locale',
                'packageItem.serviceVariant.translations.language.locale',
                'packageItem.serviceVariant.service.translations.language.locale',
            ])
            ->get();

        $grouped = [];
        foreach ($items as $item) {
            $variantId = (int) ($item->packageItem?->service_variant_id ?? 0);
            if ($variantId < 1) {
                continue;
            }
            $grouped[$variantId][] = $this->bookingRow($item);
        }

        return $grouped;
    }

    /**
     * @return array<string, string>
     */
    private function bookingRow(BookingItem $item): array
    {
        $booking = $item->booking;
        $catalog = $item->packageItem?->catalog;
        $variant = $item->packageItem?->serviceVariant;
        $packageName = $catalog?->displayLabel() ?? '—';
        if ($catalog?->isDirectService()) {
            $packageName .= ' ('.__('account.service_status_report.direct_service_short').')';
        }

        $start = $item->start_date;
        $sortDate = $start?->format('Y-m-d') ?? '';

        return [
            'sort_date' => $sortDate,
            'code' => trim((string) ($booking?->booking_code ?? '')) !== '' ? trim((string) $booking->booking_code) : '—',
            'service_dates' => $this->dateSpan($item->start_date, $item->end_date),
            'travel_dates' => $this->dateSpan($booking?->travel_start_date, $booking?->travel_end_date),
            'operator' => $this->accountLabel($booking?->operatorAccount),
            'agency' => $this->accountLabel($booking?->agencyAccount),
            'package' => $packageName,
            'variant' => $variant?->dropdownLabel(false) ?? '—',
            'quantity' => (string) max(1, (int) $item->quantity),
            'passengers' => $this->passengersLabel($booking?->passengers_snapshot),
            'booking_status' => $booking?->status?->displayLabel() ?? '—',
            'item_status' => $item->status?->displayLabel() ?? '—',
            'reference' => $this->referenceLabel($item),
        ];
    }

    private function dateSpan(mixed $start, mixed $end): string
    {
        if ($start === null) {
            return '—';
        }

        $startLabel = locale_date($start);
        if ($end === null || $this->dateKey($start) === $this->dateKey($end)) {
            return $startLabel;
        }

        return $startLabel.' – '.locale_date($end);
    }

    private function dateKey(mixed $date): string
    {
        if ($date instanceof \DateTimeInterface) {
            return $date->format('Y-m-d');
        }

        return substr((string) $date, 0, 10);
    }

    /**
     * @param  array<string, mixed>|null  $snapshot
     */
    private function passengersLabel(?array $snapshot): string
    {
        if ($snapshot === null) {
            return '—';
        }

        $normalized = BookingPassengersSnapshot::normalize($snapshot);
        $parts = [];
        foreach (BookingPassengersSnapshot::types() as $type) {
            $count = (int) $normalized[$type];
            if ($count < 1) {
                continue;
            }
            $parts[] = trans_choice(
                'account.reservations.passenger_types_count.'.$type,
                $count,
                ['count' => $count],
            );
        }

        return $parts === [] ? '—' : implode(', ', $parts);
    }

    private function referenceLabel(BookingItem $item): string
    {
        $parts = array_values(array_filter([
            trim((string) ($item->confirmation_code ?? '')),
            trim((string) ($item->provider_reference ?? '')),
        ], fn (string $value): bool => $value !== ''));

        return $parts === [] ? '—' : implode(' · ', $parts);
    }

    private function offerLabel(?ServiceOffer $offer): string
    {
        if ($offer === null) {
            return '—';
        }

        $statusKey = 'account.service_offers.provider_edit_state_'.$offer->status;
        $status = __($statusKey);
        if ($status === $statusKey) {
            $status = (string) $offer->status;
        }

        if ($offer->status === ServiceOffer::STATUS_ACCEPTED && $offer->availability !== ServiceOffer::AVAILABILITY_ACTIVE) {
            $availabilityKey = 'account.service_offers.provider_edit_availability_'.$offer->availability;
            $availability = __($availabilityKey);
            if ($availability !== $availabilityKey) {
                $status .= ' · '.$availability;
            }
        }

        return (string) $status;
    }

    private function inclusionLabel(string $mode): string
    {
        if ($mode === '') {
            return '—';
        }

        $key = 'account.service_status_report.inclusion.'.$mode;
        $label = __($key);

        return $label === $key ? $mode : (string) $label;
    }

    private function accountLabel(?Account $account): string
    {
        if ($account === null) {
            return '—';
        }

        foreach (['commercial_name', 'name', 'nick'] as $attribute) {
            $label = trim((string) ($account->{$attribute} ?? ''));
            if ($label !== '') {
                return $label;
            }
        }

        return '#'.$account->id;
    }
}
