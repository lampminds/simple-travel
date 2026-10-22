<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceComponent;
use App\Models\ServiceType;
use App\Models\ServiceVariant;
use Illuminate\Support\Collection;
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
     * Variants that may be added as children of a combo service owned by the
     * same account: any non-combo service of that account, never the parent
     * service itself (combos cannot contain combos in v1).
     *
     * @return Collection<int, ServiceVariant>
     */
    public function eligibleChildVariants(Service $parentService): Collection
    {
        return ServiceVariant::query()
            ->whereHas('service', function ($query) use ($parentService): void {
                $query->where('account_id', $parentService->account_id)
                    ->where('id', '!=', $parentService->id)
                    ->whereHas('serviceType', fn ($typeQuery) => $typeQuery->where('code', '!=', ServiceType::CODE_COMBO));
            })
            ->with(['service.translations.language.locale', 'translations.language.locale'])
            ->orderBy('service_id')
            ->orderBy('sort_order')
            ->get();
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

        if ((int) $parentService->account_id !== (int) $childService->account_id) {
            throw ValidationException::withMessages([
                'child_variant_id' => __('wizard.components_different_account'),
            ]);
        }

        // No nesting in v1: a combo cannot contain another combo.
        if ($childService->isCombo()) {
            throw ValidationException::withMessages([
                'child_variant_id' => __('wizard.components_cannot_include_combo'),
            ]);
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
}
