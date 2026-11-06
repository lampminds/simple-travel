<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PriceAdjustmentType;
use App\Models\PassengerCategory;
use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\ProviderPassengerPolicy;
use App\Models\ProviderPassengerPolicyRule;
use App\Models\Service;
use App\Models\ServicePriceRule;
use App\Models\ServiceVariant;
use App\Models\ServiceVariantPaxBand;
use App\Models\ServiceVariantPaxBandPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Saves age bands, pax-count prices, and the special fares that hang from a variant or a price-list item.
 * An empty age-band list removes the policy. A price row without an adjustment type is the regular price.
 * Pax bands replace those adjustments for the variants that have them.
 */
final class PassengerPolicyWriter
{
    /**
     * Create or update one named provider policy and its age bands.
     *
     * @param  array<int|string, array<string, mixed>>  $rules
     */
    public function syncPolicy(?ProviderPassengerPolicy $policy, int $accountId, string $name, array $rules): ProviderPassengerPolicy
    {
        $name = trim($name);
        if ($name === '') {
            throw ValidationException::withMessages([
                'name' => $this->message('name_required'),
            ]);
        }

        $duplicate = ProviderPassengerPolicy::query()
            ->where('provider_id', $accountId)
            ->where('name', $name)
            ->when($policy !== null, fn ($query) => $query->whereKeyNot($policy->id))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'name' => $this->message('name_taken'),
            ]);
        }

        $bands = $this->bandsFromInput($rules);
        if ($bands === []) {
            throw ValidationException::withMessages([
                'rules' => $this->message('bands_required'),
            ]);
        }

        if ($policy !== null && (int) $policy->provider_id !== $accountId) {
            throw ValidationException::withMessages([
                'name' => $this->message('policy_invalid'),
            ]);
        }

        return DB::transaction(function () use ($policy, $accountId, $name, $bands): ProviderPassengerPolicy {
            if ($policy === null) {
                $policy = ProviderPassengerPolicy::query()->create([
                    'provider_id' => $accountId,
                    'name' => $name,
                ]);
            } else {
                $policy->name = $name;
                $policy->save();
            }

            $this->syncPolicyBands($policy, $bands);

            return $policy;
        });
    }

    /**
     * Point a service at one of its provider's policies. Null clears passenger pricing.
     * Price rules that no longer belong to the selected policy are removed.
     */
    public function assignServicePolicy(Service $service, ?int $policyId): void
    {
        if ($policyId !== null) {
            $belongs = ProviderPassengerPolicy::query()
                ->whereKey($policyId)
                ->where('provider_id', $service->account_id)
                ->exists();

            if (! $belongs) {
                throw ValidationException::withMessages([
                    'provider_passenger_policy_id' => $this->message('policy_invalid'),
                ]);
            }
        }

        $previous = $service->provider_passenger_policy_id !== null
            ? (int) $service->provider_passenger_policy_id
            : null;

        if ($previous === $policyId) {
            return;
        }

        $service->provider_passenger_policy_id = $policyId;
        $service->save();
        $service->unsetRelation('passengerPolicy');
        $this->deletePricesOutsidePolicy($service);
    }

    /**
     * Replace the variant fares of a service using the age bands of its selected policy.
     *
     * @param  list<array<string, mixed>>  $variantPrices
     */
    public function syncVariantPrices(Service $service, array $variantPrices, bool $onlySubmittedVariants = false): void
    {
        $service->load('passengerPolicy.rules');
        $this->deletePricesOutsidePolicy($service);

        $policy = $service->passengerPolicy;
        if ($policy === null) {
            return;
        }

        $limitIds = null;
        if ($onlySubmittedVariants) {
            $submitted = [];
            foreach ($variantPrices as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $variantId = (int) ($row['service_variant_id'] ?? 0);
                if ($variantId > 0) {
                    $submitted[$variantId] = $variantId;
                }
            }

            $banded = ServiceVariantPaxBand::query()
                ->whereIn('service_variant_id', array_values($submitted) ?: [0])
                ->pluck('service_variant_id')
                ->map(fn ($id): int => (int) $id)
                ->all();

            $limitIds = array_values(array_diff($submitted, $banded));
            $variantPrices = array_values(array_filter(
                $variantPrices,
                fn ($row): bool => is_array($row) && in_array((int) ($row['service_variant_id'] ?? 0), $limitIds, true)
            ));
        }

        $bands = $policy->rules
            ->map(fn (ProviderPassengerPolicyRule $rule): array => [
                'passenger_category_id' => (int) $rule->passenger_category_id,
                'age_min' => (int) $rule->age_min,
                'age_max' => $rule->age_max !== null ? (int) $rule->age_max : null,
            ])
            ->all();

        $prices = $this->priceRows($variantPrices, $bands, 'service_variant_id');
        $this->assertVariantsBelongToService($service, $prices);

        $ruleIds = $policy->rules
            ->mapWithKeys(fn (ProviderPassengerPolicyRule $rule): array => [
                (int) $rule->passenger_category_id => (int) $rule->id,
            ])
            ->all();

        $this->replaceVariantPrices($service, $ruleIds, $prices, $limitIds);
    }

    /**
     * Replace the party-size prices posted for this service.
     * A variant key with no rows clears its bands. Variants absent from the input are left unchanged.
     *
     * @param  array<int|string, mixed>  $input
     */
    public function syncPaxBands(Service $service, array $input): void
    {
        $service->load(['passengerPolicy.rules', 'serviceVariants']);

        $variantIds = $service->serviceVariants
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $rulesByCategory = $service->passengerPolicy?->rules
            ->mapWithKeys(fn (ProviderPassengerPolicyRule $rule): array => [
                (int) $rule->passenger_category_id => (int) $rule->id,
            ])
            ->all() ?? [];

        $errors = [];
        $parsed = [];

        foreach ($input as $variantId => $payload) {
            $variantId = (int) $variantId;
            $key = 'pax_bands.'.$variantId;

            if (! in_array($variantId, $variantIds, true)) {
                $errors[$key] = $this->message('variant_invalid');

                continue;
            }

            if (! is_array($payload)) {
                continue;
            }

            $rows = $payload['rows'] ?? [];
            $variant = $service->serviceVariants->firstWhere('id', $variantId);
            if (! $variant instanceof ServiceVariant) {
                $errors[$key] = $this->message('variant_invalid');

                continue;
            }

            $parsed[$variantId] = $this->paxRows(
                $variant,
                is_array($rows) ? $rows : [],
                $rulesByCategory,
                $errors,
            );
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        DB::transaction(function () use ($parsed, $rulesByCategory): void {
            foreach ($parsed as $variantId => $bands) {
                $existingIds = ServiceVariantPaxBand::query()
                    ->where('service_variant_id', $variantId)
                    ->pluck('id');

                if ($existingIds->isNotEmpty()) {
                    ServiceVariantPaxBandPrice::query()
                        ->whereIn('service_variant_pax_band_id', $existingIds)
                        ->delete();
                    ServiceVariantPaxBand::query()->whereIn('id', $existingIds)->delete();
                }

                foreach ($bands as $index => $band) {
                    $created = ServiceVariantPaxBand::query()->create([
                        'service_variant_id' => $variantId,
                        'min_pax' => $band['min_pax'],
                        'max_pax' => $band['max_pax'],
                        'price' => $band['price'],
                        'sort_order' => $index + 1,
                    ]);

                    foreach ($band['prices'] as $ruleId => $price) {
                        if (! in_array((int) $ruleId, $rulesByCategory, true)) {
                            continue;
                        }

                        $created->prices()->create([
                            'provider_passenger_policy_rule_id' => (int) $ruleId,
                            'price' => $price,
                        ]);
                    }
                }
            }
        });
    }

    /**
     * Drop fares that point at bands outside the service's current policy.
     */
    public function deletePricesOutsidePolicy(Service $service): void
    {
        $variantIds = $service->serviceVariants()->pluck('id');
        if ($variantIds->isEmpty()) {
            return;
        }

        $allowed = $service->passengerPolicy?->rules()->pluck('id') ?? collect();
        $itemIds = PriceListItem::query()
            ->whereIn('service_variant_id', $variantIds)
            ->pluck('id');

        $variantRules = ServicePriceRule::query()->whereIn('service_variant_id', $variantIds);
        if ($allowed->isNotEmpty()) {
            $variantRules->whereNotIn('provider_passenger_policy_rule_id', $allowed);
        }
        $variantRules->delete();

        if ($itemIds->isEmpty()) {
            return;
        }

        $itemRules = ServicePriceRule::query()->whereIn('provider_price_list_item_id', $itemIds);
        if ($allowed->isNotEmpty()) {
            $itemRules->whereNotIn('provider_passenger_policy_rule_id', $allowed);
        }
        $itemRules->delete();

        $bandIds = ServiceVariantPaxBand::query()->whereIn('service_variant_id', $variantIds)->pluck('id');
        if ($bandIds->isEmpty()) {
            return;
        }

        $cells = ServiceVariantPaxBandPrice::query()->whereIn('service_variant_pax_band_id', $bandIds);
        if ($allowed->isNotEmpty()) {
            $cells->whereNotIn('provider_passenger_policy_rule_id', $allowed);
        }
        $cells->delete();
    }

    /**
     * @param  list<array<string, mixed>>  $itemPrices
     */
    public function syncPriceList(PriceList $priceList, array $itemPrices): void
    {
        $items = PriceListItem::query()
            ->where('provider_price_list_id', $priceList->id)
            ->with(['serviceVariant.paxBands', 'serviceVariant.service.passengerPolicy.rules'])
            ->get()
            ->keyBy('id');

        $errors = [];
        $rows = [];

        foreach (array_values($itemPrices) as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $type = trim((string) ($row['adjustment_type'] ?? ''));
            if ($type === '') {
                continue;
            }

            $errorKey = (string) ($row['error_key'] ?? 'passenger_fares.'.$index);
            $itemId = (int) ($row['provider_price_list_item_id'] ?? 0);
            $categoryId = (int) ($row['passenger_category_id'] ?? 0);
            $item = $items->get($itemId);

            if ($item === null) {
                $errors[$errorKey.'.provider_price_list_item_id'] = $this->message('item_invalid');

                continue;
            }

            $policyRule = $item->serviceVariant?->service?->passengerPolicy?->rules
                ?->firstWhere('passenger_category_id', $categoryId);

            if ($policyRule === null) {
                $errors[$errorKey.'.passenger_category_id'] = $this->message('category_not_on_service');

                continue;
            }

            $adjustment = $this->adjustmentError($type, $row['adjustment_value'] ?? null, $this->listItemBase($item));
            if ($adjustment['error'] !== null) {
                $errors[$errorKey.'.adjustment_value'] = $adjustment['error'];

                continue;
            }

            $pair = $itemId.':'.$policyRule->id;
            if (isset($rows[$pair])) {
                $errors[$errorKey.'.passenger_category_id'] = $this->message('duplicate');

                continue;
            }

            $rows[$pair] = [
                'provider_price_list_item_id' => $itemId,
                'provider_passenger_policy_rule_id' => (int) $policyRule->id,
                'adjustment_type' => $type,
                'adjustment_value' => $adjustment['value'],
            ];
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        DB::transaction(function () use ($items, $rows): void {
            $policyRuleIds = $items
                ->flatMap(fn (PriceListItem $item) => $item->serviceVariant?->service?->passengerPolicy?->rules?->pluck('id') ?? collect())
                ->unique()
                ->values();

            $editableIds = $items
                ->reject(fn (PriceListItem $item): bool => (bool) $item->serviceVariant?->usesPaxBands())
                ->keys();

            ServicePriceRule::query()
                ->whereIn('provider_price_list_item_id', $editableIds)
                ->delete();

            foreach ($rows as $row) {
                if (! $editableIds->contains($row['provider_price_list_item_id'])) {
                    continue;
                }

                if (! $policyRuleIds->contains($row['provider_passenger_policy_rule_id'])) {
                    continue;
                }

                ServicePriceRule::query()->create($row);
            }
        });
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $rules
     * @return list<array{passenger_category_id: int, age_min: int, age_max: int|null}>
     */
    public function bandsFromInput(array $rules): array
    {
        $knownIds = PassengerCategory::query()->pluck('id')->map(fn ($id) => (int) $id)->all();
        $bands = [];
        $errors = [];

        foreach ($rules as $categoryId => $row) {
            if (! is_array($row) || ! $this->isIncluded($row)) {
                continue;
            }

            $categoryId = (int) $categoryId;
            $key = 'rules.'.$categoryId;

            if (! in_array($categoryId, $knownIds, true)) {
                $errors[$key.'.passenger_category_id'] = $this->message('category_invalid');

                continue;
            }

            $ageMin = $this->ageValue($row['age_min'] ?? 0);
            $ageMax = $this->ageValue($row['age_max'] ?? null, true);

            if ($ageMin === null || $ageMin < 0 || $ageMin > 255) {
                $errors[$key.'.age_min'] = $this->message('age_min');

                continue;
            }

            if ($ageMax !== null && ($ageMax < $ageMin || $ageMax > 255)) {
                $errors[$key.'.age_max'] = $this->message('age_max');

                continue;
            }

            $bands[] = [
                'passenger_category_id' => $categoryId,
                'age_min' => $ageMin,
                'age_max' => $ageMax,
            ];
        }

        $errors = array_merge($errors, $this->overlapErrors($bands));

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $bands;
    }

    /**
     * @param  array<int|string, mixed>  $row
     */
    private function isIncluded(array $row): bool
    {
        $included = $row['included'] ?? false;

        return $included === true || $included === 1 || $included === '1' || $included === 'on';
    }

    private function ageValue(mixed $value, bool $allowEmpty = false): ?int
    {
        if ($value === null || $value === '') {
            return $allowEmpty ? null : 0;
        }

        if (! is_numeric($value)) {
            return -1;
        }

        return (int) $value;
    }

    /**
     * @param  list<array{passenger_category_id: int, age_min: int, age_max: int|null}>  $bands
     * @return array<string, string>
     */
    private function overlapErrors(array $bands): array
    {
        $categories = PassengerCategory::query()
            ->whereIn('id', array_column($bands, 'passenger_category_id'))
            ->with('translations.language.locale')
            ->get()
            ->keyBy('id');

        $errors = [];

        foreach ($bands as $index => $band) {
            $start = $band['age_min'];
            $end = $band['age_max'] ?? 255;

            foreach ($bands as $otherIndex => $other) {
                if ($otherIndex <= $index) {
                    continue;
                }

                $otherStart = $other['age_min'];
                $otherEnd = $other['age_max'] ?? 255;

                if ($start <= $otherEnd && $otherStart <= $end) {
                    $otherCategory = $categories->get($other['passenger_category_id']);
                    $errors['rules.'.$band['passenger_category_id'].'.age_min'] = $this->message('age_overlap', [
                        'category' => $otherCategory?->name ?? '#'.$other['passenger_category_id'],
                        'range' => $other['age_max'] === null
                            ? $other['age_min'].'+'
                            : $other['age_min'].'–'.$other['age_max'],
                    ]);
                }
            }
        }

        return $errors;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @param  list<array{passenger_category_id: int, age_min: int, age_max: int|null}>  $bands
     * @return list<array{anchor_id: int, passenger_category_id: int, adjustment_type: string, adjustment_value: float, error_key: string}>
     */
    private function priceRows(array $rows, array $bands, string $anchorKey): array
    {
        $bandIds = array_column($bands, 'passenger_category_id');
        $errors = [];
        $normalized = [];
        $variants = ServiceVariant::query()
            ->whereIn('id', collect($rows)->pluck($anchorKey)->filter()->map(fn ($id) => (int) $id)->all())
            ->get()
            ->keyBy('id');

        foreach (array_values($rows) as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $type = trim((string) ($row['adjustment_type'] ?? ''));
            if ($type === '') {
                continue;
            }

            $errorKey = (string) ($row['error_key'] ?? 'variant_prices.'.$index);
            $anchorId = (int) ($row[$anchorKey] ?? 0);
            $categoryId = (int) ($row['passenger_category_id'] ?? 0);

            if (! in_array($categoryId, $bandIds, true)) {
                $errors[$errorKey.'.passenger_category_id'] = $this->message('category_not_on_service');

                continue;
            }

            $base = $variants->get($anchorId)?->base_price;
            $adjustment = $this->adjustmentError($type, $row['adjustment_value'] ?? null, $base !== null ? (float) $base : null);

            if ($adjustment['error'] !== null) {
                $errors[$errorKey.'.adjustment_value'] = $adjustment['error'];

                continue;
            }

            $normalized[] = [
                'anchor_id' => $anchorId,
                'passenger_category_id' => $categoryId,
                'adjustment_type' => $type,
                'adjustment_value' => $adjustment['value'],
                'error_key' => $errorKey,
            ];
        }

        $seen = [];
        foreach ($normalized as $price) {
            $pair = $price['anchor_id'].':'.$price['passenger_category_id'];
            if (isset($seen[$pair])) {
                $errors[$price['error_key'].'.passenger_category_id'] = $this->message('duplicate');

                continue;
            }
            $seen[$pair] = true;
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return $normalized;
    }

    /**
     * @param  list<array{anchor_id: int, passenger_category_id: int, adjustment_type: string, adjustment_value: float, error_key: string}>  $prices
     */
    private function assertVariantsBelongToService(Service $service, array $prices): void
    {
        if ($prices === []) {
            return;
        }

        $allowed = $service->serviceVariants()->pluck('id')->map(fn ($id) => (int) $id)->all();
        $errors = [];

        foreach ($prices as $price) {
            if (! in_array($price['anchor_id'], $allowed, true)) {
                $errors[$price['error_key'].'.service_variant_id'] = $this->message('variant_invalid');
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * @param  list<array{passenger_category_id: int, age_min: int, age_max: int|null}>  $bands
     */
    private function syncPolicyBands(ProviderPassengerPolicy $policy, array $bands): void
    {
        $keepIds = array_column($bands, 'passenger_category_id');
        $removedIds = $policy->rules()->whereNotIn('passenger_category_id', $keepIds)->pluck('id');

        if ($removedIds->isNotEmpty()) {
            ServicePriceRule::query()
                ->whereIn('provider_passenger_policy_rule_id', $removedIds)
                ->delete();
            $policy->rules()->whereIn('id', $removedIds)->delete();
        }

        foreach ($bands as $band) {
            ProviderPassengerPolicyRule::query()->updateOrCreate(
                [
                    'provider_passenger_policy_id' => $policy->id,
                    'passenger_category_id' => $band['passenger_category_id'],
                ],
                [
                    'age_min' => $band['age_min'],
                    'age_max' => $band['age_max'],
                ],
            );
        }
    }

    /**
     * @param  array<int, int>  $ruleIds
     * @param  list<array{anchor_id: int, passenger_category_id: int, adjustment_type: string, adjustment_value: float}>  $prices
     * @param  list<int>|null  $onlyVariantIds
     */
    private function replaceVariantPrices(Service $service, array $ruleIds, array $prices, ?array $onlyVariantIds = null): void
    {
        $variantIds = $service->serviceVariants()->pluck('id');
        if ($onlyVariantIds !== null) {
            $variantIds = $variantIds->intersect($onlyVariantIds)->values();
        }

        ServicePriceRule::query()
            ->whereIn('service_variant_id', $variantIds)
            ->delete();

        foreach ($prices as $price) {
            $ruleId = $ruleIds[$price['passenger_category_id']] ?? null;
            if ($ruleId === null) {
                continue;
            }

            ServicePriceRule::query()->create([
                'service_variant_id' => $price['anchor_id'],
                'provider_price_list_item_id' => null,
                'provider_passenger_policy_rule_id' => $ruleId,
                'adjustment_type' => $price['adjustment_type'],
                'adjustment_value' => $price['adjustment_value'],
            ]);
        }
    }

    /**
     * @param  array<int|string, mixed>  $rows
     * @param  array<int, int>  $rulesByCategory category id => policy rule id
     * @param  array<string, string>  $errors
     * @return list<array{min_pax: int, max_pax: int|null, price: float, prices: array<int, float>}>
     */
    private function paxRows(ServiceVariant $variant, array $rows, array $rulesByCategory, array &$errors): array
    {
        $parsed = [];
        $variantKey = 'pax_bands.'.$variant->id;

        foreach ($rows as $index => $row) {
            if (! is_array($row) || $this->paxRowIsEmpty($row)) {
                continue;
            }

            $key = $variantKey.'.rows.'.$index;
            $min = $this->paxCount($row['min_pax'] ?? null);
            $max = $this->paxCount($row['max_pax'] ?? null, true);
            $price = $this->money($row['price'] ?? null);

            if ($min === null || $min < 1) {
                $errors[$key.'.min_pax'] = $this->message('pax_min');
            }

            if ($max !== null && $min !== null && $max < $min) {
                $errors[$key.'.max_pax'] = $this->message('pax_max');
            }

            if ($price === null) {
                $errors[$key.'.price'] = $this->message('pax_price');
            }

            if ($min !== null && $min > 65535) {
                $errors[$key.'.min_pax'] = $this->message('pax_min');
            }

            if ($max !== null && $max > 65535) {
                $errors[$key.'.max_pax'] = $this->message('pax_max');
            }

            $cells = [];
            $categories = is_array($row['categories'] ?? null) ? $row['categories'] : [];

            foreach ($categories as $categoryId => $rawCell) {
                if ($rawCell === null || $rawCell === '') {
                    continue;
                }

                $categoryId = (int) $categoryId;
                $ruleId = $rulesByCategory[$categoryId] ?? null;
                if ($ruleId === null) {
                    $errors[$key.'.categories.'.$categoryId] = $this->message('category_not_on_service');

                    continue;
                }

                $cell = $this->money($rawCell);
                if ($cell === null) {
                    $errors[$key.'.categories.'.$categoryId] = $this->message('pax_category_price');

                    continue;
                }

                if ($price !== null && round($cell, 2) === round($price, 2)) {
                    continue;
                }

                $cells[$ruleId] = $cell;
            }

            if (isset($errors[$key.'.min_pax']) || isset($errors[$key.'.max_pax']) || isset($errors[$key.'.price'])) {
                continue;
            }

            $parsed[] = [
                'min_pax' => (int) $min,
                'max_pax' => $max,
                'price' => (float) $price,
                'prices' => $cells,
                'error_key' => $key,
            ];
        }

        $openEnd = 65535;

        foreach ($parsed as $index => $band) {
            $start = $band['min_pax'];
            $end = $band['max_pax'] ?? $openEnd;

            foreach ($parsed as $otherIndex => $other) {
                if ($otherIndex <= $index) {
                    continue;
                }

                $otherStart = $other['min_pax'];
                $otherEnd = $other['max_pax'] ?? $openEnd;

                if ($start <= $otherEnd && $otherStart <= $end) {
                    $range = $other['max_pax'] === null
                        ? $other['min_pax'].'+'
                        : ($other['min_pax'] === $other['max_pax']
                            ? (string) $other['min_pax']
                            : $other['min_pax'].'–'.$other['max_pax']);
                    $errors[$band['error_key'].'.min_pax'] = $this->message('pax_overlap', ['range' => $range]);
                }
            }
        }

        return array_map(function (array $band): array {
            unset($band['error_key']);

            return $band;
        }, $parsed);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function paxRowIsEmpty(array $row): bool
    {
        if (! $this->isBlank($row['min_pax'] ?? null) || ! $this->isBlank($row['max_pax'] ?? null) || ! $this->isBlank($row['price'] ?? null)) {
            return false;
        }

        $categories = is_array($row['categories'] ?? null) ? $row['categories'] : [];
        foreach ($categories as $value) {
            if (! $this->isBlank($value)) {
                return false;
            }
        }

        return true;
    }

    private function isBlank(mixed $value): bool
    {
        return $value === null || (is_string($value) && trim($value) === '');
    }

    private function paxCount(mixed $value, bool $allowEmpty = false): ?int
    {
        if ($this->isBlank($value)) {
            return $allowEmpty ? null : -1;
        }

        if (! is_numeric($value) || (int) $value != (float) $value) {
            return -1;
        }

        return (int) $value;
    }

    private function money(mixed $value): ?float
    {
        if ($this->isBlank($value) || ! is_numeric($value)) {
            return null;
        }

        $amount = (float) $value;
        if ($amount < 0) {
            return null;
        }

        return round($amount, 2);
    }

    /**
     * @return array{value: float, error: string|null}
     */
    private function adjustmentError(string $type, mixed $rawValue, ?float $baseAmount): array
    {
        $adjustment = PriceAdjustmentType::tryFrom($type);
        if ($adjustment === null) {
            return ['value' => 0.0, 'error' => $this->message('adjustment_type')];
        }

        if ($rawValue === null || $rawValue === '' || ! is_numeric($rawValue)) {
            return ['value' => 0.0, 'error' => $this->message('adjustment_value')];
        }

        $value = (float) $rawValue;

        if ($value < 0) {
            return ['value' => $value, 'error' => $this->message('adjustment_value')];
        }

        if ($adjustment === PriceAdjustmentType::PercentageDiscount && $value > 100) {
            return ['value' => $value, 'error' => $this->message('percentage_discount')];
        }

        if (
            $adjustment === PriceAdjustmentType::FixedDiscount
            && $baseAmount !== null
            && $value > $baseAmount
        ) {
            return ['value' => $value, 'error' => $this->message('fixed_discount_exceeds_base')];
        }

        return ['value' => $value, 'error' => null];
    }

    private function listItemBase(PriceListItem $item): ?float
    {
        if ($item->pricing_mode === 'fixed' && $item->price !== null) {
            return (float) $item->price;
        }

        if ($item->pricing_mode === null || $item->pricing_mode === '') {
            return $item->serviceVariant?->base_price !== null
                ? (float) $item->serviceVariant->base_price
                : null;
        }

        return null;
    }

    /**
     * @param  array<string, string|int>  $replace
     */
    private function message(string $key, array $replace = []): string
    {
        return (string) __('account.passenger_policies.validation.'.$key, $replace);
    }
}
