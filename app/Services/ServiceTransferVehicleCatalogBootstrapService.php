<?php

namespace App\Services;

use App\Models\ServiceTransferVehicleType;
use App\Models\ServiceTransferVehicleTypeCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Website transfer wizard: detect accounts without transfer vehicle setup and
 * copy vehicle types from the template (system) account catalog.
 */
final class ServiceTransferVehicleCatalogBootstrapService
{
    public function templateAccountId(): int
    {
        return (int) config('services.transfer_vehicle_template_account_id', 1);
    }

    /**
     * True when the account already owns at least one transfer vehicle type row.
     */
    public function accountOwnsVehicleTypes(int $accountId): bool
    {
        return ServiceTransferVehicleType::query()
            ->where('account_id', $accountId)
            ->exists();
    }

    /**
     * True when any service_transfer_vehicles row exists for a service belonging to the account.
     */
    public function accountHasTransferVehicleAssignments(int $accountId): bool
    {
        return DB::table('service_transfer_vehicles as stv')
            ->join('service_transfers as st', 'st.id', '=', 'stv.service_transfer_id')
            ->join('services as s', 's.id', '=', 'st.service_id')
            ->where('s.account_id', $accountId)
            ->exists();
    }

    public function templateAccountHasVehicleTypes(): bool
    {
        $tid = $this->templateAccountId();

        return ServiceTransferVehicleType::query()
            ->where('account_id', $tid)
            ->where('active', true)
            ->exists();
    }

    /**
     * Offer the import dialog when the account has no assignments in service_transfer_vehicles
     * for its services, no own catalog types yet, and is not the template account.
     */
    public function shouldShowBootstrapModal(int $accountId): bool
    {
        if ($accountId === $this->templateAccountId()) {
            return false;
        }

        if ($this->accountOwnsVehicleTypes($accountId)) {
            return false;
        }

        if ($this->accountHasTransferVehicleAssignments($accountId)) {
            return false;
        }

        return $this->templateAccountHasVehicleTypes();
    }

    /**
     * Active template vehicle types, ordered for display.
     *
     * @return Collection<int, ServiceTransferVehicleType>
     */
    public function templateVehicleTypes(int $templateAccountId): Collection
    {
        return ServiceTransferVehicleType::query()
            ->where('account_id', $templateAccountId)
            ->where('active', true)
            ->with('category.translations.language.locale')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Collection<int, ServiceTransferVehicleType>>
     */
    public function templateTypesGrouped(int $templateAccountId): Collection
    {
        return $this->templateVehicleTypes($templateAccountId)
            ->groupBy(fn (ServiceTransferVehicleType $t): int => (int) ($t->service_transfer_vehicle_type_category_id ?? 0));
    }

    /**
     * Same grouping as {@see templateTypesGrouped} with categories ordered by {@see ServiceTransferVehicleTypeCategory::scopeOrdered}
     * and uncategorized (0) last — used by the transfer wizard and account website import UI.
     *
     * @return Collection<int, Collection<int, ServiceTransferVehicleType>>
     */
    public function orderedTemplateTypesGrouped(int $templateAccountId): Collection
    {
        $rawGrouped = $this->templateTypesGrouped($templateAccountId);
        $ordered = collect();

        $orderedCategoryIds = ServiceTransferVehicleTypeCategory::query()
            ->whereIn('id', $rawGrouped->keys()->map(fn ($k) => (int) $k)->filter(fn (int $id) => $id > 0)->values()->all())
            ->ordered()
            ->pluck('id');

        foreach ($orderedCategoryIds as $cid) {
            if ($rawGrouped->has($cid)) {
                $ordered->put($cid, $rawGrouped->get($cid));
            }
        }

        if ($rawGrouped->has(0)) {
            $ordered->put(0, $rawGrouped->get(0));
        }

        return $ordered;
    }

    /**
     * Category id => label for category filter checkboxes (like wizard features step).
     *
     * @return array<int, string>
     */
    public function templateCategoryCheckboxOptions(int $templateAccountId): array
    {
        $grouped = $this->templateTypesGrouped($templateAccountId);
        $options = [];
        foreach ($grouped->keys() as $categoryId) {
            $cid = (int) $categoryId;
            if ($cid === 0) {
                $options[$cid] = (string) __('wizard.transfer_bootstrap_category_other');

                continue;
            }
            $label = $this->categoryLabel($grouped->get($cid)?->first()?->category);
            $options[$cid] = $label !== '' ? $label : (string) __('wizard.transfer_bootstrap_category_other');
        }

        ksort($options);

        return $options;
    }

    /**
     * Copy selected template types into the target account. Skips codes that already exist for the target.
     *
     * @param  list<int>  $sourceTypeIds
     */
    public function importTypesIntoAccount(int $fromAccountId, int $toAccountId, array $sourceTypeIds): int
    {
        $sourceTypeIds = array_values(array_unique(array_filter(
            array_map(static fn ($id): int => (int) $id, $sourceTypeIds),
            static fn (int $id): bool => $id > 0
        )));

        if ($sourceTypeIds === []) {
            return 0;
        }

        $sources = ServiceTransferVehicleType::query()
            ->where('account_id', $fromAccountId)
            ->whereIn('id', $sourceTypeIds)
            ->where('active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $created = 0;

        DB::transaction(function () use ($sources, $toAccountId, &$created): void {
            foreach ($sources as $src) {
                $code = $src->code;
                if ($code !== null && $code !== '') {
                    $exists = ServiceTransferVehicleType::query()
                        ->where('account_id', $toAccountId)
                        ->where('code', $code)
                        ->exists();
                    if ($exists) {
                        continue;
                    }
                } else {
                    // Unique index is (account_id, code); null codes are allowed multiple times in MySQL — avoid blind copies.
                    continue;
                }

                ServiceTransferVehicleType::query()->create([
                    'account_id' => $toAccountId,
                    'code' => $code,
                    'service_transfer_vehicle_type_category_id' => $src->service_transfer_vehicle_type_category_id,
                    'name' => $src->name,
                    'sort_order' => $src->sort_order,
                    'max_passengers' => $src->max_passengers,
                    'max_luggage' => $src->max_luggage,
                    'active' => (bool) $src->active,
                ]);
                $created++;
            }
        });

        return $created;
    }

    private function categoryLabel(?ServiceTransferVehicleTypeCategory $category): string
    {
        if ($category === null) {
            return '';
        }

        return $category->name ?? '';
    }
}
