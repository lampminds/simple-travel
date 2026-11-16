<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\ProviderServiceOffer;
use App\Models\ProviderSupplyRelationship;
use App\Models\Service;
use App\Models\ServiceComponent;
use App\Models\ServiceType;
use App\Models\ServiceVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Composition rules for combo services (parent variant -> child variants).
 *
 * Price comes from the parent variant only. Capacity is meant to count the
 * parent variant AND every child variant; the actual deduction happens at
 * booking/availability stage (see {@see capacityRequirements()}).
 */
final class ServiceComponentService
{
    /**
     * Variants that may be added as children of a combo.
     *
     * Own account: any non-combo service except the parent service.
     * Other providers: only variants with an accepted, active supply offer.
     * Combos cannot contain combos.
     *
     * @return Collection<int, ServiceVariant>
     */
    public function eligibleChildVariants(Service $parentService): Collection
    {
        $own = ServiceVariant::query()
            ->whereHas('service', function ($query) use ($parentService): void {
                $query->where('account_id', $parentService->account_id)
                    ->where('id', '!=', $parentService->id)
                    ->whereHas('serviceType', fn ($typeQuery) => $typeQuery->where('code', '!=', ServiceType::CODE_COMBO));
            })
            ->with(['service.translations.language.locale', 'service.account', 'translations.language.locale', 'currency.lmpCurrency'])
            ->orderBy('service_id')
            ->orderBy('sort_order')
            ->get();

        $supplierIds = ProviderSupplyRelationship::query()
            ->where('buyer_account_id', $parentService->account_id)
            ->where('status', ProviderSupplyRelationship::STATUS_APPROVED)
            ->pluck('supplier_account_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($supplierIds === []) {
            return $own;
        }

        $offeredVariantIds = ProviderServiceOffer::query()
            ->where('buyer_id', $parentService->account_id)
            ->whereIn('supplier_id', $supplierIds)
            ->where('status', ProviderServiceOffer::STATUS_ACCEPTED)
            ->where('availability', ProviderServiceOffer::AVAILABILITY_ACTIVE)
            ->pluck('service_variant_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($offeredVariantIds === []) {
            return $own;
        }

        $external = ServiceVariant::query()
            ->whereIn('id', $offeredVariantIds)
            ->whereHas('service', function ($query) use ($parentService, $supplierIds): void {
                $query->whereIn('account_id', $supplierIds)
                    ->where('id', '!=', $parentService->id)
                    ->whereHas('serviceType', fn ($typeQuery) => $typeQuery->where('code', '!=', ServiceType::CODE_COMBO));
            })
            ->with(['service.translations.language.locale', 'service.account', 'translations.language.locale', 'currency.lmpCurrency'])
            ->orderBy('service_id')
            ->orderBy('sort_order')
            ->get();

        $accounts = Account::query()
            ->whereIn('id', $supplierIds)
            ->get()
            ->keyBy(fn (Account $account) => (int) $account->id);

        $external->each(function (ServiceVariant $variant) use ($accounts): void {
            $supplier = $accounts->get((int) ($variant->service?->account_id ?? 0));
            $name = trim((string) ($supplier?->commercial_name ?: $supplier?->name ?: ''));
            if ($name !== '') {
                $variant->setAttribute('supply_supplier_name', $name);
            }
        });

        return $own->concat($external)->values();
    }

    /**
     * Attach a child variant to a parent (combo) variant.
     *
     * @param  array{quantity?: int, day_number?: int|null, inclusion_mode?: string, notes?: string|null}  $attributes
     *
     * @throws ValidationException
     */
    public function addComponent(ServiceVariant $parentVariant, ServiceVariant $childVariant, array $attributes = []): ServiceComponent
    {
        $this->assertAttachable($parentVariant, $childVariant);

        $quantity = (int) ($attributes['quantity'] ?? 1);
        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'child_variant_id' => __('wizard.components_quantity_min'),
            ]);
        }

        $inclusionMode = (string) ($attributes['inclusion_mode'] ?? ServiceComponent::INCLUSION_INCLUDED);
        if (! in_array($inclusionMode, [ServiceComponent::INCLUSION_INCLUDED, ServiceComponent::INCLUSION_OPTIONAL, ServiceComponent::INCLUSION_UPGRADE], true)) {
            throw ValidationException::withMessages([
                'child_variant_id' => __('wizard.components_invalid_inclusion_mode'),
            ]);
        }

        $sortOrder = (int) (ServiceComponent::query()
            ->where('parent_variant_id', $parentVariant->id)
            ->max('sort_order') ?? 0) + 10;

        return ServiceComponent::query()->create([
            'parent_variant_id' => $parentVariant->id,
            'child_variant_id' => $childVariant->id,
            'quantity' => $quantity,
            'day_number' => isset($attributes['day_number']) && $attributes['day_number'] !== null && $attributes['day_number'] !== ''
                ? (int) $attributes['day_number']
                : null,
            'inclusion_mode' => $inclusionMode,
            'sort_order' => $sortOrder,
            'notes' => isset($attributes['notes']) && trim((string) $attributes['notes']) !== ''
                ? trim((string) $attributes['notes'])
                : null,
        ]);
    }

    /**
     * Guard rails for a parent -> child link.
     *
     * @throws ValidationException
     */
    public function assertAttachable(ServiceVariant $parentVariant, ServiceVariant $childVariant): void
    {
        $parentService = $parentVariant->service;
        $childService = $childVariant->service;

        if ($parentService === null || $childService === null) {
            throw ValidationException::withMessages([
                'child_variant_id' => __('wizard.components_variant_without_service'),
            ]);
        }

        if (! $parentService->isCombo()) {
            throw ValidationException::withMessages([
                'child_variant_id' => __('wizard.components_parent_not_combo'),
            ]);
        }

        if ((int) $parentVariant->id === (int) $childVariant->id
            || (int) $parentService->id === (int) $childService->id) {
            throw ValidationException::withMessages([
                'child_variant_id' => __('wizard.components_cannot_include_self'),
            ]);
        }

        // No nesting in v1: a combo cannot contain another combo, own or third-party.
        if ($childService->isCombo()) {
            throw ValidationException::withMessages([
                'child_variant_id' => __('wizard.components_cannot_include_combo'),
            ]);
        }

        if ((int) $parentService->account_id !== (int) $childService->account_id) {
            $this->assertThirdPartyChild($parentService, $childVariant);
        }

        $alreadyAttached = ServiceComponent::query()
            ->where('parent_variant_id', $parentVariant->id)
            ->where('child_variant_id', $childVariant->id)
            ->exists();

        if ($alreadyAttached) {
            throw ValidationException::withMessages([
                'child_variant_id' => __('wizard.components_already_added'),
            ]);
        }
    }

    /**
     * Variant ids whose capacity must be checked/consumed when the parent
     * variant is booked: the parent itself plus every child, with the
     * quantity each line consumes. Booking integration is a later stage;
     * this is the contract that stage must consume.
     *
     * @return array<int, int> variant id => quantity (parent consumes 1 unit of itself)
     */
    public function capacityRequirements(ServiceVariant $parentVariant): array
    {
        $requirements = [(int) $parentVariant->id => 1];

        foreach ($parentVariant->components as $component) {
            $childId = (int) $component->child_variant_id;
            $requirements[$childId] = ($requirements[$childId] ?? 0) + max(1, (int) $component->quantity);
        }

        return $requirements;
    }

    /**
     * A component from another provider requires an approved supply link,
     * an accepted active offer of that exact variant, and no supply circle.
     */
    private function assertThirdPartyChild(Service $parentService, ServiceVariant $childVariant): void
    {
        $childService = $childVariant->service;
        $buyerId = (int) $parentService->account_id;
        $supplierId = (int) ($childService?->account_id ?? 0);

        $linked = ProviderSupplyRelationship::query()
            ->where('buyer_account_id', $buyerId)
            ->where('supplier_account_id', $supplierId)
            ->where('status', ProviderSupplyRelationship::STATUS_APPROVED)
            ->exists();

        if (! $linked) {
            throw ValidationException::withMessages([
                'child_variant_id' => __('wizard.components_supply_not_linked'),
            ]);
        }

        $offer = ProviderServiceOffer::query()
            ->where('buyer_id', $buyerId)
            ->where('supplier_id', $supplierId)
            ->where('service_variant_id', $childVariant->id)
            ->first();

        if ($offer === null || ! $offer->isUsableInCombo()) {
            throw ValidationException::withMessages([
                'child_variant_id' => __('wizard.components_supply_offer_required'),
            ]);
        }

        if ($this->supplyEdgeWouldCycle($buyerId, $supplierId)) {
            throw ValidationException::withMessages([
                'child_variant_id' => __('wizard.components_supply_cycle'),
            ]);
        }
    }

    /**
     * Directed edge: buyer combo uses a variant owned by the supplier.
     * Adding buyer -> supplier cycles when the supplier can already reach the buyer.
     */
    private function supplyEdgeWouldCycle(int $buyerAccountId, int $supplierAccountId): bool
    {
        if ($buyerAccountId === $supplierAccountId) {
            return false;
        }

        $edges = [];
        $pairs = DB::table('service_components as sc')
            ->join('service_variants as parent_variant', 'parent_variant.id', '=', 'sc.parent_variant_id')
            ->join('services as parent_service', 'parent_service.id', '=', 'parent_variant.service_id')
            ->join('service_variants as child_variant', 'child_variant.id', '=', 'sc.child_variant_id')
            ->join('services as child_service', 'child_service.id', '=', 'child_variant.service_id')
            ->whereColumn('parent_service.account_id', '!=', 'child_service.account_id')
            ->select('parent_service.account_id as buyer_id', 'child_service.account_id as supplier_id')
            ->distinct()
            ->get();

        foreach ($pairs as $pair) {
            $from = (int) $pair->buyer_id;
            $to = (int) $pair->supplier_id;
            $edges[$from][$to] = $to;
        }

        $seen = [];
        $stack = [$supplierAccountId];

        while ($stack !== []) {
            $current = array_pop($stack);
            if ($current === $buyerAccountId) {
                return true;
            }

            if (isset($seen[$current])) {
                continue;
            }

            $seen[$current] = true;

            foreach ($edges[$current] ?? [] as $next) {
                $stack[] = $next;
            }
        }

        return false;
    }
}
