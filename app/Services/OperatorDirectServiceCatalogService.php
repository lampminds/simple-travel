<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Language;
use App\Models\OperatorServiceCatalog;
use App\Models\ServiceOffer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Creates or pauses the one-line catalog row an agency books as a service.
 */
final class OperatorDirectServiceCatalogService
{
    public function enable(ServiceOffer $offer): OperatorServiceCatalog
    {
        $this->assertAcceptedOffer($offer);

        return DB::transaction(function () use ($offer): OperatorServiceCatalog {
            $catalog = $this->findForOffer($offer);

            if ($catalog === null) {
                $catalog = OperatorServiceCatalog::query()->create([
                    'operator_id' => (int) $offer->operator_id,
                    'catalog_kind' => OperatorServiceCatalog::KIND_SERVICE,
                    'source_service_offer_id' => (int) $offer->id,
                    'status' => 'active',
                    'inventory_type' => 'unlimited',
                ]);
            } else {
                $catalog->update([
                    'catalog_kind' => OperatorServiceCatalog::KIND_SERVICE,
                    'status' => 'active',
                ]);
            }

            $this->syncLine($catalog, $offer);
            $this->syncTranslations($catalog, $offer);

            return $catalog->fresh(['translations', 'items']);
        });
    }

    /**
     * Pauses the generated catalog. Existing offers and bookings stay in place.
     * Agencies cannot book it while the catalog is not active.
     */
    public function disable(ServiceOffer $offer): bool
    {
        $catalog = $this->findForOffer($offer);
        if ($catalog === null || $catalog->status !== 'active') {
            return false;
        }

        $catalog->update(['status' => 'paused']);

        return true;
    }

    private function assertAcceptedOffer(ServiceOffer $offer): void
    {
        if ($offer->status !== ServiceOffer::STATUS_ACCEPTED || $offer->service_variant_id === null) {
            throw ValidationException::withMessages([
                'offer' => __('account.service_offers.direct_service_offer_not_accepted'),
            ]);
        }
    }

    private function findForOffer(ServiceOffer $offer): ?OperatorServiceCatalog
    {
        return OperatorServiceCatalog::query()
            ->where('source_service_offer_id', (int) $offer->id)
            ->first();
    }

    private function syncLine(OperatorServiceCatalog $catalog, ServiceOffer $offer): void
    {
        $payload = [
            'service_variant_id' => (int) $offer->service_variant_id,
            'service_offer_id' => (int) $offer->id,
            'day_number' => null,
            'sort_order' => 10,
            'quantity' => 1,
            'inclusion_mode' => 'included',
        ];

        $item = $catalog->items()->orderBy('id')->first();
        if ($item === null) {
            $catalog->items()->create($payload);

            return;
        }

        $item->update($payload);
    }

    private function syncTranslations(OperatorServiceCatalog $catalog, ServiceOffer $offer): void
    {
        $offer->loadMissing([
            'serviceVariant.translations',
            'serviceVariant.service.translations',
        ]);

        $serviceTranslations = $offer->serviceVariant?->service?->translations?->keyBy('language_id') ?? collect();
        $variantTranslations = $offer->serviceVariant?->translations?->keyBy('language_id') ?? collect();
        $languageIds = $serviceTranslations->keys()->merge($variantTranslations->keys())->unique();

        $rows = [];
        foreach ($languageIds as $languageId) {
            $serviceName = trim((string) ($serviceTranslations->get($languageId)?->name ?? ''));
            $variantName = trim((string) ($variantTranslations->get($languageId)?->name ?? ''));
            $description = trim((string) ($serviceTranslations->get($languageId)?->description ?? ''));

            $name = $this->composeName($serviceName, $variantName);
            if ($name === '') {
                continue;
            }

            $rows[] = [
                'language_id' => (int) $languageId,
                'name' => mb_substr($name, 0, 255),
                'description' => $description !== '' ? $description : null,
            ];
        }

        if ($rows === []) {
            $fallbackLanguageId = Language::query()->orderBy('id')->value('id');
            if ($fallbackLanguageId === null) {
                throw ValidationException::withMessages([
                    'offer' => __('account.service_offers.direct_service_offer_not_accepted'),
                ]);
            }

            $rows[] = [
                'language_id' => (int) $fallbackLanguageId,
                'name' => mb_substr($this->fallbackName($offer), 0, 255),
                'description' => null,
            ];
        }

        $catalog->translations()->delete();
        foreach ($rows as $row) {
            $catalog->translations()->create($row);
        }
    }

    private function composeName(string $serviceName, string $variantName): string
    {
        if ($serviceName === '') {
            return $variantName;
        }

        if ($variantName === '' || strcasecmp($variantName, $serviceName) === 0) {
            return $serviceName;
        }

        return $serviceName.' — '.$variantName;
    }

    private function fallbackName(ServiceOffer $offer): string
    {
        $variant = $offer->serviceVariant;
        $sku = trim((string) ($variant?->sku ?? ''));
        if ($sku !== '') {
            return $sku;
        }

        return 'Service #'.(int) ($variant?->service_id ?? $offer->id);
    }
}
