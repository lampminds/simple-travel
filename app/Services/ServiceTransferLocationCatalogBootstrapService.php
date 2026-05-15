<?php

namespace App\Services;

use App\Models\LmpCity;
use App\Models\ServiceTransferLocation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Website transfer wizard: copy transfer locations from the template (system) account
 * into the current account, scoped by city.
 */
final class ServiceTransferLocationCatalogBootstrapService
{
    public function templateAccountId(): int
    {
        return (int) config('services.transfer_vehicle_template_account_id', 1);
    }

    public function accountHasCatalogLocations(int $accountId): bool
    {
        return ServiceTransferLocation::query()
            ->where('account_id', $accountId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * True when the template account has at least one active location linked to a city.
     */
    public function templateHasImportableLocations(): bool
    {
        return ServiceTransferLocation::query()
            ->where('account_id', $this->templateAccountId())
            ->where('is_active', true)
            ->whereNotNull('city_id')
            ->exists();
    }

    /**
     * Offer the import dialog when the account has no catalogue locations yet, is not the template account,
     * and the template has at least one city-scoped location to copy from.
     */
    public function shouldShowBootstrapModal(int $accountId): bool
    {
        if ($accountId === $this->templateAccountId()) {
            return false;
        }

        if ($this->accountHasCatalogLocations($accountId)) {
            return false;
        }

        return $this->templateHasImportableLocations();
    }

    /**
     * @return array<int, string> city id => city name
     */
    public function templateCityOptions(): array
    {
        $tid = $this->templateAccountId();
        $ids = ServiceTransferLocation::query()
            ->where('account_id', $tid)
            ->where('is_active', true)
            ->whereNotNull('city_id')
            ->distinct()
            ->pluck('city_id');

        if ($ids->isEmpty()) {
            return [];
        }

        return LmpCity::query()
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * Active template locations for a city (for wizard checkboxes).
     *
     * @return Collection<int, ServiceTransferLocation>
     */
    public function templateLocationsInCity(int $cityId): Collection
    {
        if ($cityId < 1) {
            return collect();
        }

        return ServiceTransferLocation::query()
            ->where('account_id', $this->templateAccountId())
            ->where('city_id', $cityId)
            ->where('is_active', true)
            ->with(['translations.language.locale', 'city', 'locationType.translations.language.locale'])
            ->orderBy('id')
            ->get();
    }

    /**
     * @return list<string>
     */
    public function templateLocationIdStringsInCity(int $cityId): array
    {
        return $this->templateLocationsInCity($cityId)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
    }

    /**
     * Copy selected template locations into the target account (same city, new rows, translations duplicated).
     *
     * @param  list<int>  $sourceLocationIds
     */
    public function importLocationsIntoAccount(int $fromAccountId, int $toAccountId, int $cityId, array $sourceLocationIds): int
    {
        $sourceLocationIds = array_values(array_unique(array_filter(
            array_map(static fn ($id): int => (int) $id, $sourceLocationIds),
            static fn (int $id): bool => $id > 0
        )));

        if ($sourceLocationIds === [] || $cityId < 1) {
            return 0;
        }

        $sources = ServiceTransferLocation::query()
            ->where('account_id', $fromAccountId)
            ->where('city_id', $cityId)
            ->whereIn('id', $sourceLocationIds)
            ->where('is_active', true)
            ->with('translations')
            ->orderBy('id')
            ->get();

        if ($sources->isEmpty()) {
            return 0;
        }

        $created = 0;

        DB::transaction(function () use ($sources, $toAccountId, &$created): void {
            foreach ($sources as $src) {
                $src->loadMissing('translations');

                $preferredSlug = trim((string) ($src->slug ?? ''));
                if ($preferredSlug !== '' && ServiceTransferLocation::query()
                    ->where('account_id', $toAccountId)
                    ->where('slug', $preferredSlug)
                    ->exists()) {
                    continue;
                }

                $fallbackName = (string) ($src->translations->first()?->name ?? $src->id);
                $slug = $preferredSlug !== ''
                    ? $preferredSlug
                    : $this->allocateUniqueSlug($toAccountId, $fallbackName);

                $new = $src->replicate(['id', 'account_id', 'parent_id']);
                $new->account_id = $toAccountId;
                $new->slug = $slug;
                $new->parent_id = null;
                $new->save();

                foreach ($src->translations as $tr) {
                    $new->translations()->create([
                        'language_id' => $tr->language_id,
                        'name' => $tr->name,
                    ]);
                }

                $created++;
            }
        });

        return $created;
    }

    private function allocateUniqueSlug(int $toAccountId, string $fallbackName): string
    {
        $base = Str::slug($fallbackName, '-', 'en');
        if ($base === '') {
            $base = 'location';
        }
        $slug = $base;
        $n = 1;
        while (ServiceTransferLocation::query()->where('account_id', $toAccountId)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$n++;
        }

        return $slug;
    }
}
