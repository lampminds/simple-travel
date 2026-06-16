<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OperatorPackageItem;
use App\Models\OperatorPriceList;
use App\Models\OperatorPriceListAssignment;
use App\Models\OperatorPriceListItem;
use App\Models\OperatorServiceCatalog;
use App\Support\BookingPassengersSnapshot;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Resolves agency-facing package totals from an operator price list and assignment.
 */
final class OperatorPackageAgencyPriceResolver
{
    public function __construct(
        private readonly OperatorPriceListItemPricingService $itemPricingService,
        private readonly PriceFormatService $priceFormatService,
    ) {
    }

    /**
     * @return array{
     *     amount: float|null,
     *     has_amount: bool,
     *     formatted: string,
     *     currency_id: int|null,
     *     currency_code: string|null,
     *     is_zero: bool
     * }
     */
    public function resolvePackageTotal(
        OperatorServiceCatalog $package,
        OperatorPriceList $priceList,
        int $agencyAccountId,
        int $operatorAccountId,
        ?CarbonInterface $pricingDate = null,
        ?array $passengersSnapshot = null,
    ): array {
        $pricingDate ??= Carbon::today();
        $empty = [
            'amount' => null,
            'has_amount' => false,
            'formatted' => '—',
            'currency_id' => null,
            'currency_code' => null,
            'is_zero' => true,
        ];

        if ((int) $priceList->operator_id !== $operatorAccountId) {
            return $empty;
        }

        $assignment = $this->findActiveAssignment($priceList, $agencyAccountId, $pricingDate);
        if ($assignment === null) {
            return $empty;
        }

        $package->loadMissing([
            'items.serviceVariant',
            'items.serviceOffer',
        ]);

        $includedItems = $package->items
            ->filter(fn (OperatorPackageItem $item): bool => $item->inclusion_mode === 'included')
            ->values();

        if ($includedItems->isEmpty()) {
            return $empty;
        }

        $listItems = OperatorPriceListItem::query()
            ->where('operator_price_list_id', $priceList->id)
            ->get()
            ->keyBy(fn (OperatorPriceListItem $row) => (int) $row->operator_package_item_id);

        $listCurrencyId = (int) $priceList->currency_id;
        $subtotal = 0.0;
        $hasLine = false;

        foreach ($includedItems as $packageItem) {
            $listItem = $listItems->get((int) $packageItem->id);
            if ($listItem === null) {
                return $empty;
            }

            $line = $this->itemPricingService->calculate(
                $packageItem,
                $operatorAccountId,
                $listCurrencyId,
                $this->normalizeListItemMode($listItem),
                (float) $listItem->price,
                $pricingDate,
            );

            if (! $line['final_price_has'] || $line['final_price'] === null) {
                return $empty;
            }

            $unit = (float) $line['final_price'];
            $qty = BookingPassengersSnapshot::lineQuantity($packageItem, $passengersSnapshot);
            $subtotal += $unit * $qty;
            $hasLine = true;
        }

        if (! $hasLine) {
            return $empty;
        }

        $total = $this->applyAssignmentAdjustment($subtotal, $assignment);
        $pricingMeta = $this->pricingMeta($subtotal, $total, $assignment);

        if ($total <= 0.0) {
            return array_merge([
                'amount' => $total,
                'has_amount' => true,
                'formatted' => $this->priceFormatService->formatWithCurrency(
                    $total,
                    currency: $priceList->currency,
                    accountId: $operatorAccountId,
                ),
                'currency_id' => $listCurrencyId,
                'currency_code' => $priceList->currency?->currency_code,
                'is_zero' => true,
            ], $pricingMeta);
        }

        return array_merge([
            'amount' => $total,
            'has_amount' => true,
            'formatted' => $this->priceFormatService->formatWithCurrency(
                $total,
                currency: $priceList->currency,
                accountId: $operatorAccountId,
            ),
            'currency_id' => $listCurrencyId,
            'currency_code' => $priceList->currency?->currency_code,
            'is_zero' => false,
        ], $pricingMeta);
    }

    /**
     * @return array{
     *     lines_subtotal: float,
     *     assignment_adjustment_type: string|null,
     *     assignment_adjustment_value: float|null,
     *     assignment_adjustment_amount: float
     * }
     */
    private function pricingMeta(
        float $linesSubtotal,
        float $grandTotal,
        OperatorPriceListAssignment $assignment,
    ): array {
        $adjustmentType = trim((string) ($assignment->adjustment_type ?? ''));
        $adjustmentValue = $assignment->adjustment_value !== null
            ? (float) $assignment->adjustment_value
            : null;

        return [
            'lines_subtotal' => round($linesSubtotal, 2),
            'assignment_adjustment_type' => $adjustmentType !== '' ? $adjustmentType : null,
            'assignment_adjustment_value' => $adjustmentValue,
            'assignment_adjustment_amount' => round($grandTotal - $linesSubtotal, 2),
        ];
    }

    /**
     * @return Collection<int, OperatorPriceList>
     */
    public function eligiblePriceListsForPackageAndAgency(
        OperatorServiceCatalog $package,
        int $agencyAccountId,
        int $operatorAccountId,
        ?CarbonInterface $pricingDate = null,
    ): Collection {
        $pricingDate ??= Carbon::today();

        $lists = OperatorPriceList::query()
            ->where('operator_id', $operatorAccountId)
            ->where('is_active', true)
            ->whereHas('assignments', function ($query) use ($agencyAccountId, $pricingDate): void {
                $query->where('agency_id', $agencyAccountId)
                    ->where('is_active', true)
                    ->where(function ($q) use ($pricingDate): void {
                        $q->whereNull('valid_from')
                            ->orWhere('valid_from', '<=', $pricingDate);
                    })
                    ->where(function ($q) use ($pricingDate): void {
                        $q->whereNull('valid_to')
                            ->orWhere('valid_to', '>=', $pricingDate);
                    });
            })
            ->with(['currency.lmpCurrency', 'assignments'])
            ->orderBy('name')
            ->get();

        return $lists->filter(function (OperatorPriceList $list) use ($package, $agencyAccountId, $operatorAccountId, $pricingDate): bool {
            $resolved = $this->resolvePackageTotal($package, $list, $agencyAccountId, $operatorAccountId, $pricingDate);

            return $resolved['has_amount'] && ! $resolved['is_zero'];
        })->values();
    }

    public function packageIsOfferableToAgency(
        OperatorServiceCatalog $package,
        OperatorPriceList $priceList,
        int $agencyAccountId,
        int $operatorAccountId,
        ?CarbonInterface $pricingDate = null,
    ): bool {
        $resolved = $this->resolvePackageTotal(
            $package,
            $priceList,
            $agencyAccountId,
            $operatorAccountId,
            $pricingDate,
        );

        return $resolved['has_amount'] && ! $resolved['is_zero'];
    }

    public function resolvedAmountIsZero(array $resolved): bool
    {
        return ($resolved['is_zero'] ?? true) === true;
    }

    /**
     * Human-readable blockers when no price list can be used to propose this package.
     *
     * @return list<string>
     */
    public function ineligibilityMessages(
        OperatorServiceCatalog $package,
        int $agencyAccountId,
        int $operatorAccountId,
        ?CarbonInterface $pricingDate = null,
    ): array {
        $pricingDate ??= Carbon::today();
        $messages = [];

        $assignedLists = OperatorPriceList::query()
            ->where('operator_id', $operatorAccountId)
            ->whereHas('assignments', fn ($q) => $q->where('agency_id', $agencyAccountId))
            ->with(['assignments' => fn ($q) => $q->where('agency_id', $agencyAccountId)])
            ->orderBy('name')
            ->get();

        if ($assignedLists->isEmpty()) {
            $messages[] = (string) __('account.package_offers.blockers.no_assignment');

            return $messages;
        }

        $package->loadMissing(['items.serviceVariant', 'items.serviceOffer']);
        $includedItems = $package->items
            ->filter(fn (OperatorPackageItem $item): bool => $item->inclusion_mode === 'included');

        if ($includedItems->isEmpty()) {
            $messages[] = (string) __('account.package_offers.blockers.no_included_items');

            return $messages;
        }

        $listIssues = [];

        foreach ($assignedLists as $list) {
            $listLabel = $list->name !== '' ? $list->name : ('#'.$list->id);

            if (! $list->is_active) {
                $listIssues[] = __('account.package_offers.blockers.list_inactive', ['list' => $listLabel]);

                continue;
            }

            $assignment = $list->assignments->first();
            if ($assignment === null) {
                continue;
            }

            if (! $assignment->is_active) {
                $listIssues[] = __('account.package_offers.blockers.assignment_inactive', ['list' => $listLabel]);

                continue;
            }

            if (! $this->assignmentIsValidOnDate($assignment, $pricingDate)) {
                $listIssues[] = __('account.package_offers.blockers.assignment_outside_dates', ['list' => $listLabel]);

                continue;
            }

            $listItems = OperatorPriceListItem::query()
                ->where('operator_price_list_id', $list->id)
                ->get()
                ->keyBy(fn (OperatorPriceListItem $row) => (int) $row->operator_package_item_id);

            $missingLines = [];
            foreach ($includedItems as $packageItem) {
                if (! $listItems->has((int) $packageItem->id)) {
                    $missingLines[] = (int) $packageItem->id;
                }
            }

            if ($missingLines !== []) {
                $listIssues[] = __('account.package_offers.blockers.missing_package_lines', [
                    'list' => $listLabel,
                    'count' => count($missingLines),
                ]);

                continue;
            }

            $resolved = $this->resolvePackageTotal(
                $package,
                $list,
                $agencyAccountId,
                $operatorAccountId,
                $pricingDate,
            );

            if (! $resolved['has_amount']) {
                $listIssues[] = __('account.package_offers.blockers.price_not_computable', ['list' => $listLabel]);

                continue;
            }

            if ($resolved['is_zero']) {
                $listIssues[] = __('account.package_offers.blockers.zero_total', ['list' => $listLabel]);
            }
        }

        if ($listIssues === []) {
            $messages[] = (string) __('account.package_offers.blockers.unknown');
        } else {
            foreach (array_unique($listIssues) as $issue) {
                $messages[] = (string) $issue;
            }
        }

        return $messages;
    }

    private function assignmentIsValidOnDate(OperatorPriceListAssignment $assignment, CarbonInterface $pricingDate): bool
    {
        $date = Carbon::parse($pricingDate)->startOfDay();

        if ($assignment->valid_from !== null && Carbon::parse($assignment->valid_from)->startOfDay()->gt($date)) {
            return false;
        }

        if ($assignment->valid_to !== null && Carbon::parse($assignment->valid_to)->startOfDay()->lt($date)) {
            return false;
        }

        return true;
    }

    private function findActiveAssignment(
        OperatorPriceList $priceList,
        int $agencyAccountId,
        CarbonInterface $pricingDate,
    ): ?OperatorPriceListAssignment {
        $date = Carbon::parse($pricingDate)->startOfDay();

        return OperatorPriceListAssignment::query()
            ->where('operator_price_list_id', $priceList->id)
            ->where('agency_id', $agencyAccountId)
            ->where('is_active', true)
            ->where(function ($query) use ($date): void {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', $date);
            })
            ->where(function ($query) use ($date): void {
                $query->whereNull('valid_to')
                    ->orWhere('valid_to', '>=', $date);
            })
            ->orderByDesc('id')
            ->first();
    }

    private function applyAssignmentAdjustment(float $amount, OperatorPriceListAssignment $assignment): float
    {
        return match ($assignment->adjustment_type) {
            'percentage' => $amount * (1.0 + ((float) ($assignment->adjustment_value ?? 0.0) / 100.0)),
            'fixed' => $amount + (float) ($assignment->adjustment_value ?? 0.0),
            default => $amount,
        };
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
