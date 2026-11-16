<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProviderServiceOffer;
use App\Models\ProviderSupplyRelationship;
use App\Models\ServiceType;
use App\Models\ServiceVariant;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * A buyer combo can be offered to operators only while every third-party
 * component has an accepted, active supply offer and an approved link.
 * Same-account components are unaffected.
 */
final class ProviderSupplyComponentHealth
{
    public const REASON_RELATIONSHIP = 'relationship';

    public const REASON_MISSING = 'missing';

    public const REASON_PENDING = 'pending';

    public const REASON_REJECTED = 'rejected';

    public const REASON_SUSPENDED = 'suspended';

    public const REASON_DISCONTINUED = 'discontinued';

    /**
     * @param  Collection<int, ServiceVariant>  $parents
     * @return array<int, list<array{component_id: int, variant_id: int, label: string, reason: string}>>
     */
    public function issuesByParentId(Collection $parents): array
    {
        $result = [];
        /** @var list<array{parent_id: int, buyer_id: int, component_id: int, variant_id: int, supplier_id: int, label: string}> $thirdParty */
        $thirdParty = [];

        foreach ($parents as $parent) {
            $parent->loadMissing(['service.serviceType', 'components.childVariant.service']);
            $parentId = (int) $parent->id;
            $result[$parentId] = [];

            if (! $parent->service?->isCombo()) {
                continue;
            }

            $buyerId = (int) $parent->service->account_id;

            foreach ($parent->components as $component) {
                $child = $component->childVariant;
                $childService = $child?->service;
                if ($child === null || $childService === null) {
                    $result[$parentId][] = [
                        'component_id' => (int) $component->id,
                        'variant_id' => (int) ($component->child_variant_id ?? 0),
                        'label' => '#'.(int) ($component->child_variant_id ?? 0),
                        'reason' => self::REASON_MISSING,
                    ];

                    continue;
                }

                if ((int) $childService->account_id === $buyerId) {
                    continue;
                }

                $thirdParty[] = [
                    'parent_id' => $parentId,
                    'buyer_id' => $buyerId,
                    'component_id' => (int) $component->id,
                    'variant_id' => (int) $child->id,
                    'supplier_id' => (int) $childService->account_id,
                    'label' => $child->dropdownLabel(),
                ];
            }
        }

        if ($thirdParty === []) {
            return $result;
        }

        $buyerIds = array_values(array_unique(array_column($thirdParty, 'buyer_id')));
        $variantIds = array_values(array_unique(array_column($thirdParty, 'variant_id')));
        $supplierIds = array_values(array_unique(array_column($thirdParty, 'supplier_id')));

        $offers = ProviderServiceOffer::query()
            ->whereIn('buyer_id', $buyerIds)
            ->whereIn('service_variant_id', $variantIds)
            ->get();

        $relationships = ProviderSupplyRelationship::query()
            ->whereIn('buyer_account_id', $buyerIds)
            ->whereIn('supplier_account_id', $supplierIds)
            ->get();

        foreach ($thirdParty as $row) {
            $relationship = $relationships->first(function (ProviderSupplyRelationship $relationship) use ($row): bool {
                return (int) $relationship->buyer_account_id === $row['buyer_id']
                    && (int) $relationship->supplier_account_id === $row['supplier_id'];
            });

            if ($relationship === null || $relationship->status !== ProviderSupplyRelationship::STATUS_APPROVED) {
                $result[$row['parent_id']][] = $this->issue($row, self::REASON_RELATIONSHIP);

                continue;
            }

            $offer = $offers->first(function (ProviderServiceOffer $offer) use ($row): bool {
                return (int) $offer->buyer_id === $row['buyer_id']
                    && (int) $offer->service_variant_id === $row['variant_id']
                    && (int) $offer->supplier_id === $row['supplier_id'];
            });

            $reason = $this->reasonForOffer($offer);
            if ($reason !== null) {
                $result[$row['parent_id']][] = $this->issue($row, $reason);
            }
        }

        return $result;
    }

    /**
     * @return list<array{component_id: int, variant_id: int, label: string, reason: string}>
     */
    public function issues(ServiceVariant $parent): array
    {
        $grouped = $this->issuesByParentId(collect([$parent]));

        return $grouped[(int) $parent->id] ?? [];
    }

    public function isSellable(ServiceVariant $parent): bool
    {
        return $this->issues($parent) === [];
    }

    /**
     * @param  Collection<int, ServiceVariant>  $variants
     */
    public function assertSellable(Collection $variants): void
    {
        $issuesByParent = $this->issuesByParentId($variants);
        $messages = [];

        foreach ($variants as $variant) {
            $issues = $issuesByParent[(int) $variant->id] ?? [];
            if ($issues === []) {
                continue;
            }

            $messages[] = (string) __('account.provider_supply.combo_not_sellable', [
                'variant' => $variant->dropdownLabel(),
                'reason' => $this->reasonSummary($issues),
            ]);
        }

        if ($messages !== []) {
            throw ValidationException::withMessages([
                'propose' => $messages,
            ]);
        }
    }

    /**
     * @return list<string>
     */
    public function unsellableSummaries(int $accountId): array
    {
        $variants = ServiceVariant::query()
            ->whereHas('service', function ($query) use ($accountId): void {
                $query->where('account_id', $accountId)
                    ->whereHas('serviceType', fn ($typeQuery) => $typeQuery->where('code', ServiceType::CODE_COMBO));
            })
            ->with([
                'translations.language.locale',
                'service.translations.language.locale',
                'service.serviceType',
                'components.childVariant.translations.language.locale',
                'components.childVariant.service.translations.language.locale',
            ])
            ->get();

        $issuesByParent = $this->issuesByParentId($variants);
        $lines = [];

        foreach ($variants as $variant) {
            $issues = $issuesByParent[(int) $variant->id] ?? [];
            if ($issues === []) {
                continue;
            }

            $lines[] = (string) __('account.provider_supply.combo_not_sellable', [
                'variant' => $variant->dropdownLabel(),
                'reason' => $this->reasonSummary($issues),
            ]);
        }

        return $lines;
    }

    /**
     * @param  list<array{label: string, reason: string}>  $issues
     */
    public function reasonSummary(array $issues): string
    {
        return collect($issues)
            ->map(function (array $issue): string {
                return $issue['label'].' ('.__('account.provider_supply.health.'.$issue['reason']).')';
            })
            ->implode(', ');
    }

    private function reasonForOffer(?ProviderServiceOffer $offer): ?string
    {
        if ($offer === null) {
            return self::REASON_MISSING;
        }

        if ($offer->status === ProviderServiceOffer::STATUS_PENDING) {
            return self::REASON_PENDING;
        }

        if ($offer->status === ProviderServiceOffer::STATUS_REJECTED) {
            return self::REASON_REJECTED;
        }

        if ($offer->status !== ProviderServiceOffer::STATUS_ACCEPTED) {
            return self::REASON_MISSING;
        }

        return match ($offer->availability) {
            ProviderServiceOffer::AVAILABILITY_SUSPENDED => self::REASON_SUSPENDED,
            ProviderServiceOffer::AVAILABILITY_DISCONTINUED => self::REASON_DISCONTINUED,
            ProviderServiceOffer::AVAILABILITY_ACTIVE => null,
            default => self::REASON_MISSING,
        };
    }

    /**
     * @param  array{component_id: int, variant_id: int, label: string}  $row
     * @return array{component_id: int, variant_id: int, label: string, reason: string}
     */
    private function issue(array $row, string $reason): array
    {
        return [
            'component_id' => $row['component_id'],
            'variant_id' => $row['variant_id'],
            'label' => $row['label'],
            'reason' => $reason,
        ];
    }
}
