<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OperatorPackageItem;
use App\Models\OperatorServiceCatalog;
use App\Models\PackageOffer;
use App\Models\Service;
use App\Models\ServiceVariant;

/**
 * Builds a structured preview of a commercial package proposal for agency-facing review.
 */
final class PackageOfferPreviewBuilder
{
    public function __construct(
        private readonly OperatorPackageAgencyPriceResolver $packagePriceResolver,
        private readonly OperatorPreviewLocalePriceService $localePriceService,
        private readonly PackageConditionResolver $conditionResolver,
    ) {
    }

    /**
     * @param  array<string, mixed>|null  $agencyPrice
     * @return array<string, mixed>
     */
    public function build(PackageOffer $offer, ?array $agencyPrice = null): array
    {
        $offer->loadMissing([
            'operatorAccount',
            'catalog.translations.language.locale',
            'catalog.items.serviceVariant.translations.language.locale',
            'catalog.items.serviceVariant.media',
            'catalog.items.service.translations.language.locale',
            'catalog.items.service.media',
            'catalog.items.serviceOffer.providerAccount',
            'priceList',
        ]);

        $catalog = $offer->catalog;
        abort_unless($catalog instanceof OperatorServiceCatalog, 404);

        $agencyId = (int) $offer->agency_id;
        $operatorId = (int) $offer->operator_id;
        $priceList = $offer->priceList;

        if ($agencyPrice === null && $priceList !== null) {
            $agencyPrice = $this->packagePriceResolver->resolvePackageTotal(
                $catalog,
                $priceList,
                $agencyId,
                $operatorId,
            );
        }

        $operatorLabel = trim((string) ($offer->operatorAccount?->commercial_name
            ?? $offer->operatorAccount?->name
            ?? ('#'.$operatorId)));

        $priceFormatted = is_array($agencyPrice) && ($agencyPrice['has_amount'] ?? false)
            ? (string) ($agencyPrice['formatted'] ?? '—')
            : '—';

        $conditions = $this->conditionResolver->resolveForPackage($catalog);
        $conditionRows = $this->displayConditions(
            $conditions['consolidated_by_topic'] ?? [],
            $conditions['package_level'] ?? [],
        );

        ['hero_images' => $heroImages, 'galleries' => $galleries] = $this->packageMedia($catalog);

        return [
            'title' => $catalog->displayLabel(),
            'operator_name' => $operatorLabel,
            'agency_price' => $priceFormatted,
            'agency_price_usd_hint' => $this->localePriceService->buildUsd($agencyPrice, $agencyId),
            'price_list_name' => trim((string) ($priceList?->name ?? '')),
            'locales' => $this->packageLocales($catalog),
            'items' => $this->packageItems($catalog),
            'conditions' => $conditionRows,
            'hero_images' => $heroImages,
            'galleries' => $galleries,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $byTopic
     * @param  list<array<string, mixed>>  $packageLevel
     * @return list<array{label: string, text: string, sort_order: int}>
     */
    private function displayConditions(array $byTopic, array $packageLevel): array
    {
        $rows = [];
        $seen = [];

        foreach (array_merge($byTopic, $packageLevel) as $condition) {
            $text = trim((string) ($condition['effective_text'] ?? ''));
            if ($text === '') {
                continue;
            }

            $label = trim((string) ($condition['topic_name'] ?? ''));
            if ($label === '' || $label === '—') {
                continue;
            }

            $dedupeKey = mb_strtolower($label).'|'.mb_strtolower($text);
            if (isset($seen[$dedupeKey])) {
                continue;
            }
            $seen[$dedupeKey] = true;

            $rows[] = [
                'label' => $label,
                'text' => preg_replace('/\s+/u', ' ', $text) ?? $text,
                'sort_order' => (int) ($condition['sort_order'] ?? 9999),
            ];
        }

        usort(
            $rows,
            fn (array $a, array $b): int => [$a['sort_order'], $a['label']] <=> [$b['sort_order'], $b['label']],
        );

        return $rows;
    }

    /**
     * @return array{hero_images: list<array{url: string, label: string}>, galleries: list<array{title: string, images: list<array{url: string}>}>}
     */
    private function packageMedia(OperatorServiceCatalog $catalog): array
    {
        $heroImages = [];
        $galleries = [];
        $seenHeroUrls = [];

        foreach ($catalog->items->sortBy(['sort_order', 'id']) as $item) {
            if (! $item instanceof OperatorPackageItem || $item->inclusion_mode !== 'included') {
                continue;
            }

            $service = $item->service instanceof Service
                ? $item->service
                : $item->serviceVariant?->service;
            if (! $service instanceof Service) {
                continue;
            }

            $variant = $item->serviceVariant;
            $label = $this->itemMediaLabel($service, $variant);

            $serviceMain = $this->mediaUrl($service->mainImageUrl(Service::MEDIA_CONVERSION_REGULAR))
                ?? $this->mediaUrl($service->mainImageUrl(Service::MEDIA_CONVERSION_SMALL));
            if ($serviceMain !== null && ! isset($seenHeroUrls[$serviceMain])) {
                $heroImages[] = ['url' => $serviceMain, 'label' => $label];
                $seenHeroUrls[$serviceMain] = true;
            }

            if ($variant instanceof ServiceVariant) {
                $variantMain = $this->mediaUrl($variant->mainImageUrl(ServiceVariant::MEDIA_CONVERSION_REGULAR))
                    ?? $this->mediaUrl($variant->mainImageUrl(ServiceVariant::MEDIA_CONVERSION_SMALL));
                if ($variantMain !== null && ! isset($seenHeroUrls[$variantMain])) {
                    $heroImages[] = [
                        'url' => $variantMain,
                        'label' => $label.' — '.__('account.service_offers.operator_preview_variant_main_image_short'),
                    ];
                    $seenHeroUrls[$variantMain] = true;
                }
            }

            $galleryImages = [];
            foreach ($service->galleryMedia() as $media) {
                $url = $this->mediaUrlFromMedia($media, Service::MEDIA_CONVERSION_REGULAR)
                    ?? $this->mediaUrlFromMedia($media, Service::MEDIA_CONVERSION_SMALL);
                if ($url !== null) {
                    $galleryImages[] = ['url' => $url];
                }
            }

            if ($galleryImages !== []) {
                $galleries[] = [
                    'title' => $label,
                    'images' => $galleryImages,
                ];
            }
        }

        return [
            'hero_images' => $heroImages,
            'galleries' => $galleries,
        ];
    }

    private function itemMediaLabel(Service $service, ?ServiceVariant $variant): string
    {
        $serviceName = trim((string) $service->name);
        if ($serviceName === '') {
            $serviceName = __('wizard.service_unnamed');
        }

        if (! $variant instanceof ServiceVariant) {
            return $serviceName;
        }

        $detail = trim((string) $variant->name);
        if ($detail === '') {
            $detail = trim((string) ($variant->sku ?? ''));
        }

        if ($detail === '' || strcasecmp($detail, $serviceName) === 0) {
            return $serviceName;
        }

        return $serviceName.' — '.$detail;
    }

    private function mediaUrl(?string $url): ?string
    {
        $url = trim((string) ($url ?? ''));

        return $url !== '' ? $url : null;
    }

    private function mediaUrlFromMedia(object $media, string $conversion): ?string
    {
        $url = $media->hasGeneratedConversion($conversion)
            ? $media->getUrl($conversion)
            : $media->getUrl();

        return $this->mediaUrl($url);
    }

    /**
     * @return list<array{language: string, name: string, description: string}>
     */
    private function packageLocales(OperatorServiceCatalog $catalog): array
    {
        $locales = [];

        foreach ($catalog->translations->sortBy(fn ($translation) => (int) ($translation->language?->list_order ?? 0)) as $translation) {
            $translation->language?->loadMissing('locale');
            $name = trim((string) ($translation->name ?? ''));
            $description = trim((string) ($translation->description ?? ''));

            if ($name === '' && $description === '') {
                continue;
            }

            $locales[] = [
                'language' => trim((string) ($translation->language?->display_name ?? '')) ?: '—',
                'name' => $name,
                'description' => $description,
            ];
        }

        return $locales;
    }

    /**
     * @return list<array{
     *     day_number: int|null,
     *     service_name: string,
     *     variant_sku: string,
     *     provider_name: string,
     *     quantity: int,
     *     inclusion_mode: string,
     *     inclusion_mode_label: string,
     *     notes: string
     * }>
     */
    private function packageItems(OperatorServiceCatalog $catalog): array
    {
        $rows = [];

        foreach ($catalog->items->sortBy(['sort_order', 'id']) as $item) {
            if (! $item instanceof OperatorPackageItem) {
                continue;
            }

            $service = $item->service instanceof Service
                ? $item->service
                : $item->serviceVariant?->service;
            $serviceName = trim((string) ($service?->name ?? ''));
            $variantSku = trim((string) ($item->serviceVariant?->sku ?? ''));

            $provider = $item->serviceOffer?->providerAccount;
            $providerLabel = trim((string) ($provider?->commercial_name
                ?? $provider?->name
                ?? ''));

            $rows[] = [
                'day_number' => $item->day_number !== null ? (int) $item->day_number : null,
                'service_name' => $serviceName !== '' ? $serviceName : '—',
                'variant_sku' => $variantSku,
                'provider_name' => $providerLabel,
                'quantity' => max(1, (int) $item->quantity),
                'inclusion_mode' => (string) $item->inclusion_mode,
                'inclusion_mode_label' => (string) __('account.operator_packages.inclusion_mode.'.$item->inclusion_mode),
                'notes' => trim((string) ($item->notes ?? '')),
            ];
        }

        return $rows;
    }
}
