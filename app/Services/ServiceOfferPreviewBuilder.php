<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\LmpCity;
use App\Models\Service;
use App\Models\ServiceDetail;
use App\Models\ServiceOffer;
use App\Models\ServiceTransferPrice;
use App\Models\ServiceVariant;
use App\Support\ServiceCopySections;
use App\Support\ServiceWizardSkipsVariantsStep;
use App\Support\ServiceWizardStepEight;

/**
 * Builds a structured preview of all wizard data for an operator-facing catalog proposal.
 */
final class ServiceOfferPreviewBuilder
{
    public function __construct(
        private readonly OperatorVariantPriceResolver $priceResolver,
        private readonly OperatorPreviewLocalePriceService $localePriceService,
    ) {
    }

    public function build(ServiceOffer $offer, ?array $operatorPrice = null): array
    {
        $offer->loadMissing([
            'providerAccount',
            'serviceVariant.translations.language.locale',
            'serviceVariant.currency.lmpCurrency',
            'serviceVariant.media',
        ]);

        $variant = $offer->serviceVariant;
        abort_unless($variant !== null, 404);

        $service = $this->loadService($variant);
        $serviceTypeCode = strtolower((string) ($service->serviceType?->code ?? ''));

        $providerLabel = trim((string) ($offer->providerAccount?->commercial_name
            ?? $offer->providerAccount?->name
            ?? ('#'.$offer->provider_id)));

        $operatorId = (int) $offer->operator_id;

        if ($operatorPrice === null) {
            $operatorPrice = $this->priceResolver->resolve(
                $variant,
                (int) $offer->provider_id,
                $operatorId,
            );
        }

        $sections = [];
        $advancedSummary = null;

        [$baseFields, $baseLocales] = $this->baseSectionData($service);
        $statusFields = $this->statusFields($service);
        $variantFields = ServiceWizardSkipsVariantsStep::isSkippedForServiceTypeCode($serviceTypeCode)
            ? []
            : $this->variantFields($variant, $operatorPrice);

        $featureGroups = $this->featureGroups($service);
        if ($featureGroups !== []) {
            $sections[] = [
                'key' => ServiceCopySections::FEATURES,
                'title' => __('wizard.provider_services_action_step3'),
                'groups' => $featureGroups,
            ];
        }

        $galleries = $this->galleryGroups($service, $variant);
        if ($galleries !== []) {
            $sections[] = [
                'key' => ServiceCopySections::IMAGES,
                'title' => __('wizard.provider_services_action_step5'),
                'galleries' => $galleries,
            ];
        }

        $details = $this->detailRows($service);
        if ($details !== []) {
            $sections[] = [
                'key' => ServiceCopySections::DETAILS,
                'title' => __('wizard.provider_services_action_step6'),
                'details' => $details,
            ];
        }

        $experienceNames = $service->experiences
            ->map(fn ($exp) => trim((string) $exp->name))
            ->filter(fn (string $name) => $name !== '')
            ->values()
            ->all();

        if ($experienceNames !== []) {
            $sections[] = [
                'key' => ServiceCopySections::EXPERIENCES,
                'title' => __('wizard.provider_services_action_step7'),
                'groups' => [[
                    'title' => __('wizard.experiences_field_experiences'),
                    'items' => $experienceNames,
                ]],
            ];
        }

        if (ServiceWizardStepEight::isEnabledForServiceTypeCode($serviceTypeCode)) {
            $advancedSummary = $this->advancedSummary($service, $serviceTypeCode, $variant);
        }

        $priceFormatted = is_array($operatorPrice) && ($operatorPrice['has_amount'] ?? false)
            ? (string) ($operatorPrice['formatted'] ?? '—')
            : '—';

        $usdPriceHint = $this->localePriceService->buildUsd($operatorPrice, $operatorId);

        return [
            'title' => $this->serviceLabel($service, $variant),
            'provider_name' => $providerLabel,
            'service_type_label' => trim((string) ($service->serviceType?->name ?? '')),
            'proposed_variant_id' => (int) $variant->id,
            'operator_price' => $priceFormatted,
            'operator_price_usd_hint' => $usdPriceHint,
            'price_list' => $this->assignedPriceListSummary((int) $offer->provider_id, $operatorId),
            'hero_images' => $this->heroImages($service, $variant),
            'summary' => [
                'base_fields' => $baseFields,
                'locales' => $baseLocales,
                'status_fields' => $statusFields,
                'variant_fields' => $variantFields,
            ],
            'advanced_summary' => $advancedSummary,
            'sections' => $sections,
        ];
    }

    private function loadService(ServiceVariant $variant): Service
    {
        return Service::query()
            ->whereKey($variant->service_id)
            ->with([
                'translations.language.locale',
                'serviceType.translations.language.locale',
                'city.state.country',
                'features.serviceFeatureCategory.translations.language.locale',
                'features.translations.language.locale',
                'serviceDetails.serviceDetailTopic.category.translations.language.locale',
                'serviceDetails.serviceDetailTopic.translations.language.locale',
                'serviceDetails.conditionKey',
                'serviceDetails.language.locale',
                'experiences.translations.language.locale',
                'media',
                'hotel.hotelTypes.translations.language.locale',
                'gastronomy.city.state.country',
                'gastronomy.gastronomyTypes.translations.language.locale',
                'gastronomy.cuisines.translations.language.locale',
                'gastronomy.venues.translations.language.locale',
                'gastronomy.menus.translations.language.locale',
                'gastronomy.experience',
                'activity.activityTypes.translations.language.locale',
            ])
            ->firstOrFail();
    }

    /**
     * @return array{0: list<array{label: string, value: string, html?: bool}>, 1: list<array{language: string, name: string, description: string}>}
     */
    private function baseSectionData(Service $service): array
    {
        $fields = [];
        $locales = [];

        if ($service->city !== null) {
            $fields[] = [
                'label' => __('wizard.validation.city_id'),
                'value' => $this->formatCityLabel($service->city),
            ];
        }

        foreach ($service->translations->sortBy(fn ($t) => (int) ($t->language?->list_order ?? 0)) as $translation) {
            $translation->language?->loadMissing('locale');
            $name = trim((string) ($translation->name ?? ''));
            $desc = trim((string) ($translation->description ?? ''));

            if ($name === '' && $desc === '') {
                continue;
            }

            $locales[] = [
                'language' => trim((string) ($translation->language?->display_name ?? '')) ?: '—',
                'name' => $name,
                'description' => $desc,
            ];
        }

        return [$fields, $locales];
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    private function statusFields(Service $service): array
    {
        $fields = [];

        $bookingMode = (string) ($service->booking_mode ?? '');
        if ($bookingMode !== '') {
            $fields[] = [
                'label' => __('wizard.step2_fields.booking_mode'),
                'value' => __('wizard.step2_booking_mode.'.$bookingMode),
            ];
        }

        if ($service->confirmation_time_hours !== null) {
            $fields[] = [
                'label' => __('wizard.step2_fields.confirmation_time_hours'),
                'value' => (string) $service->confirmation_time_hours,
            ];
        }

        return $fields;
    }

    /**
     * @return list<array{title: string, items: list<string>}>
     */
    private function featureGroups(Service $service): array
    {
        if ($service->features->isEmpty()) {
            return [];
        }

        $grouped = $service->features
            ->sortBy(fn ($feature) => [
                (int) ($feature->serviceFeatureCategory?->sort_order ?? 0),
                (int) $feature->sort_order,
                (int) $feature->id,
            ])
            ->groupBy(fn ($feature) => trim((string) ($feature->serviceFeatureCategory?->name ?? ''))
                ?: __('wizard.features_category_fallback'));

        $groups = [];
        foreach ($grouped as $categoryName => $features) {
            $items = $features
                ->map(fn ($feature) => trim((string) $feature->name))
                ->filter(fn (string $name) => $name !== '')
                ->values()
                ->all();

            if ($items !== []) {
                $groups[] = [
                    'title' => (string) $categoryName,
                    'items' => $items,
                ];
            }
        }

        return $groups;
    }

    /**
     * @param  array<string, mixed>|null  $operatorPrice
     * @return list<array{label: string, value: string, html?: bool}>
     */
    private function variantFields(ServiceVariant $variant, ?array $operatorPrice = null): array
    {
        $f = 'filament.resources.service_variant_fields';
        $fields = [];

        if ($variant->sku) {
            $fields[] = ['label' => __($f.'.sku'), 'value' => (string) $variant->sku];
        }

        if ($variant->pricing_type) {
            $pricingKey = 'filament.resources.service_variant_pricing_type.'.$variant->pricing_type;
            $fields[] = [
                'label' => __($f.'.pricing_type'),
                'value' => __($pricingKey) !== $pricingKey ? __($pricingKey) : (string) $variant->pricing_type,
            ];
        }

        if (is_array($operatorPrice) && ($operatorPrice['has_amount'] ?? false)) {
            $fields[] = [
                'label' => __('account.service_offers.operator_preview_operator_price'),
                'value' => (string) ($operatorPrice['formatted'] ?? '—'),
            ];
        } elseif ($variant->base_price !== null) {
            $currency = $variant->currency?->currency_code ?? '';
            $fields[] = [
                'label' => __($f.'.base_price'),
                'value' => trim($variant->base_price.' '.$currency),
            ];
        }

        if ($variant->inventory_type) {
            $inventoryKey = 'wizard.variant_inventory.'.$variant->inventory_type;
            $fields[] = [
                'label' => __($f.'.inventory_type'),
                'value' => __($inventoryKey) !== $inventoryKey ? __($inventoryKey) : (string) $variant->inventory_type,
            ];
        }

        if ($variant->inventory_total !== null) {
            $fields[] = ['label' => __($f.'.inventory_total'), 'value' => (string) $variant->inventory_total];
        }

        if ($variant->capacity_min !== null) {
            $fields[] = ['label' => __($f.'.capacity_min'), 'value' => (string) $variant->capacity_min];
        }

        if ($variant->capacity_max !== null) {
            $fields[] = ['label' => __($f.'.capacity_max'), 'value' => (string) $variant->capacity_max];
        }

        if ($variant->min_advance_booking_hours !== null) {
            $fields[] = ['label' => __($f.'.min_advance_booking_hours'), 'value' => (string) $variant->min_advance_booking_hours];
        }

        if ($variant->max_advance_booking_days !== null) {
            $fields[] = ['label' => __($f.'.max_advance_booking_days'), 'value' => (string) $variant->max_advance_booking_days];
        }

        if ($variant->start_time) {
            $fields[] = ['label' => __($f.'.start_time'), 'value' => (string) $variant->start_time];
        }

        if ($variant->end_time) {
            $fields[] = ['label' => __($f.'.end_time'), 'value' => (string) $variant->end_time];
        }

        return $fields;
    }

    /**
     * @return list<array{url: string, label: string}>
     */
    private function heroImages(Service $service, ServiceVariant $proposedVariant): array
    {
        $images = [];

        $serviceMain = $this->mediaUrl($service->mainImageUrl(Service::MEDIA_CONVERSION_REGULAR))
            ?? $this->mediaUrl($service->mainImageUrl(Service::MEDIA_CONVERSION_SMALL));
        if ($serviceMain !== null) {
            $images[] = [
                'url' => $serviceMain,
                'label' => __('account.service_offers.operator_preview_service_main_image'),
            ];
        }

        $variantMain = $this->mediaUrl($proposedVariant->mainImageUrl(ServiceVariant::MEDIA_CONVERSION_REGULAR))
            ?? $this->mediaUrl($proposedVariant->mainImageUrl(ServiceVariant::MEDIA_CONVERSION_SMALL));
        if ($variantMain !== null) {
            $images[] = [
                'url' => $variantMain,
                'label' => __('account.service_offers.operator_preview_variant_main_image_short'),
            ];
        }

        return $images;
    }

    /**
     * @return list<array{title: string, images: list<array{url: string}>}>
     */
    private function galleryGroups(Service $service, ServiceVariant $proposedVariant): array
    {
        $groups = [];

        $serviceGallery = [];
        foreach ($service->galleryMedia() as $media) {
            $url = $this->mediaUrlFromMedia($media, Service::MEDIA_CONVERSION_REGULAR)
                ?? $this->mediaUrlFromMedia($media, Service::MEDIA_CONVERSION_SMALL);
            if ($url !== null) {
                $serviceGallery[] = ['url' => $url];
            }
        }

        if ($serviceGallery !== []) {
            $groups[] = [
                'title' => __('account.service_offers.operator_preview_gallery_service'),
                'images' => $serviceGallery,
            ];
        }

        $variantGallery = [];
        foreach ($proposedVariant->galleryMedia() as $media) {
            $url = $this->mediaUrlFromMedia($media, ServiceVariant::MEDIA_CONVERSION_REGULAR)
                ?? $this->mediaUrlFromMedia($media, ServiceVariant::MEDIA_CONVERSION_SMALL);
            if ($url !== null) {
                $variantGallery[] = ['url' => $url];
            }
        }

        if ($variantGallery !== []) {
            $groups[] = [
                'title' => __('account.service_offers.operator_preview_gallery_variant'),
                'images' => $variantGallery,
            ];
        }

        return $groups;
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
     * @return list<array{context: string, mandatory: bool, description: string}>
     */
    private function detailRows(Service $service): array
    {
        if ($service->serviceDetails->isEmpty()) {
            return [];
        }

        $allowedVisibilities = ['public', 'operator'];
        $rows = [];

        $grouped = $service->serviceDetails
            ->filter(fn (ServiceDetail $detail) => in_array(
                (string) ($detail->serviceDetailTopic?->visibility ?? 'public'),
                $allowedVisibilities,
                true,
            ))
            ->groupBy(fn (ServiceDetail $detail) => implode('|', [
                (int) $detail->service_detail_topic_id,
                (int) ($detail->condition_key_id ?? 0),
                (int) $detail->sort_order,
            ]));

        foreach ($grouped as $details) {
            /** @var ServiceDetail $first */
            $first = $details->first();
            if (! $first->active) {
                continue;
            }

            $description = $this->detailDescriptionForDisplay($details);
            if ($description === '') {
                continue;
            }

            $visibility = (string) ($first->serviceDetailTopic?->visibility ?? 'public');
            $visibilityLabel = $visibility === 'operator'
                ? __('account.service_offers.operator_preview_visibility_operator')
                : __('account.service_offers.operator_preview_visibility_public');
            $category = trim((string) ($first->serviceDetailTopic?->category?->name ?? '')) ?: '—';
            $topic = trim((string) ($first->serviceDetailTopic?->name ?? '')) ?: '—';

            $rows[] = [
                'context' => $visibilityLabel.' · '.$category.' · '.$topic,
                'mandatory' => (bool) $first->is_mandatory,
                'description' => $description,
            ];
        }

        usort($rows, fn (array $a, array $b) => ($a['context'] ?? '') <=> ($b['context'] ?? ''));

        return $rows;
    }

    /**
     * @param  iterable<int, ServiceDetail>  $details
     */
    private function detailDescriptionForDisplay(iterable $details): string
    {
        $locale = app()->getLocale();
        $fallback = '';

        foreach ($details as $detail) {
            $text = trim((string) ($detail->description ?? ''));
            if ($text === '') {
                continue;
            }

            $detailLocale = $detail->language?->locale?->code;
            if ($detailLocale !== null && str_starts_with(strtolower($detailLocale), strtolower($locale))) {
                return $text;
            }

            if ($fallback === '') {
                $fallback = $text;
            }
        }

        return $fallback;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function advancedSummary(Service $service, string $serviceTypeCode, ServiceVariant $variant): ?array
    {
        $section = [
            'fields' => [],
            'groups' => [],
            'tables' => [],
        ];

        if (in_array($serviceTypeCode, ['accomodation'], true)) {
            $this->appendHotelAdvanced($section, $service);
        } elseif ($serviceTypeCode === 'gastronomy') {
            $this->appendGastronomyAdvanced($section, $service);
        } elseif (in_array($serviceTypeCode, ['activity', 'event'], true)) {
            $this->appendActivityAdvanced($section, $service);
        } elseif ($serviceTypeCode === 'transfer') {
            $this->appendTransferAdvanced($section, $variant);
        }

        if ($section['fields'] === [] && $section['groups'] === [] && $section['tables'] === []) {
            return null;
        }

        return $section;
    }

    /**
     * @param  array{fields: list<array{label: string, value: string}>, groups: list<array{title: string, items: list<string>}>, tables: list<array{title: string, headers: list<string>, rows: list<list<string>>}>}  $section
     */
    private function appendHotelAdvanced(array &$section, Service $service): void
    {
        $hotel = $service->hotel;
        if ($hotel === null) {
            return;
        }

        $types = $hotel->hotelTypes
            ->map(fn ($type) => trim((string) $type->name))
            ->filter(fn (string $name) => $name !== '')
            ->values()
            ->all();

        if ($types !== []) {
            $section['groups'][] = [
                'title' => __('account.service_offers.operator_preview_hotel_type'),
                'items' => $types,
            ];
        }

        if ($hotel->stars !== null) {
            $section['fields'][] = [
                'label' => __('wizard.step7_hotel_field_stars'),
                'value' => (string) $hotel->stars,
            ];
        }

        if ($hotel->check_in_time) {
            $section['fields'][] = [
                'label' => __('wizard.step7_hotel_field_check_in'),
                'value' => (string) $hotel->check_in_time,
            ];
        }

        if ($hotel->check_out_time) {
            $section['fields'][] = [
                'label' => __('wizard.step7_hotel_field_check_out'),
                'value' => (string) $hotel->check_out_time,
            ];
        }
    }

    /**
     * @param  array{fields: list<array{label: string, value: string}>, groups: list<array{title: string, items: list<string>}>, tables: list<array{title: string, headers: list<string>, rows: list<list<string>>}>}  $section
     */
    private function appendGastronomyAdvanced(array &$section, Service $service): void
    {
        $row = $service->gastronomy;
        if ($row === null) {
            return;
        }

        $this->appendNamedCatalogGroup($section, __('wizard.step7_field_gastronomy_types'), $row->gastronomyTypes);
        $this->appendNamedCatalogGroup($section, __('wizard.step7_tab_cuisines'), $row->cuisines);
        $this->appendNamedCatalogGroup($section, __('wizard.step7_tab_venues'), $row->venues);
        $this->appendNamedCatalogGroup($section, __('wizard.step7_tab_menus'), $row->menus);

        if ($row->city !== null) {
            $section['fields'][] = [
                'label' => __('wizard.step7_field_city'),
                'value' => $this->formatCityLabel($row->city),
            ];
        }

        if ($row->address) {
            $section['fields'][] = [
                'label' => __('wizard.step7_field_address'),
                'value' => (string) $row->address,
            ];
        }

        if ($row->latitude !== null && $row->longitude !== null) {
            $section['fields'][] = [
                'label' => __('wizard.step7_field_latitude').' / '.__('wizard.step7_field_longitude'),
                'value' => $row->latitude.', '.$row->longitude,
            ];
        }

        $section['fields'][] = ['label' => __('wizard.step7_field_is_indoor'), 'value' => $this->boolLabel((bool) $row->is_indoor)];
        $section['fields'][] = ['label' => __('wizard.step7_field_is_outdoor'), 'value' => $this->boolLabel((bool) $row->is_outdoor)];
        $section['fields'][] = ['label' => __('wizard.step7_field_has_takeaway'), 'value' => $this->boolLabel((bool) $row->has_takeaway)];
        $section['fields'][] = ['label' => __('wizard.step7_field_has_delivery'), 'value' => $this->boolLabel((bool) $row->has_delivery)];

        $experience = $row->experience;
        if ($experience !== null) {
            if ($experience->duration_minutes !== null) {
                $section['fields'][] = [
                    'label' => __('wizard.step2_fields.duration_minutes'),
                    'value' => (string) $experience->duration_minutes,
                ];
            }
            $section['fields'][] = [
                'label' => __('account.service_offers.operator_preview_includes_food'),
                'value' => $this->boolLabel((bool) $experience->includes_food),
            ];
            $section['fields'][] = [
                'label' => __('account.service_offers.operator_preview_includes_drinks'),
                'value' => $this->boolLabel((bool) $experience->includes_drinks),
            ];
            $section['fields'][] = [
                'label' => __('account.service_offers.operator_preview_is_guided'),
                'value' => $this->boolLabel((bool) $experience->is_guided),
            ];
        }
    }

    /**
     * @param  array{fields: list<array{label: string, value: string}>, groups: list<array{title: string, items: list<string>}>, tables: list<array{title: string, headers: list<string>, rows: list<list<string>>}>}  $section
     */
    private function appendActivityAdvanced(array &$section, Service $service): void
    {
        $row = $service->activity;
        if ($row === null) {
            return;
        }

        $this->appendNamedCatalogGroup($section, __('wizard.step7_activity_field_types'), $row->activityTypes);

        $section['fields'][] = ['label' => __('wizard.step7_activity_field_guide'), 'value' => $this->boolLabel((bool) $row->guide_included)];
        $section['fields'][] = ['label' => __('wizard.step7_activity_field_transport'), 'value' => $this->boolLabel((bool) $row->transport_included)];
        $section['fields'][] = ['label' => __('wizard.step7_activity_field_outdoor'), 'value' => $this->boolLabel((bool) $row->outdoor_activity)];

        if ($row->difficulty_level !== null) {
            $section['fields'][] = [
                'label' => __('account.service_offers.operator_preview_difficulty'),
                'value' => (string) $row->difficulty_level,
            ];
        }

        if ($row->min_age !== null) {
            $section['fields'][] = [
                'label' => __('account.service_offers.operator_preview_min_age'),
                'value' => (string) $row->min_age,
            ];
        }

        if ($row->max_age !== null) {
            $section['fields'][] = [
                'label' => __('account.service_offers.operator_preview_max_age'),
                'value' => (string) $row->max_age,
            ];
        }
    }

    /**
     * @param  array{fields: list<array{label: string, value: string}>, groups: list<array{title: string, items: list<string>}>, tables: list<array{title: string, headers: list<string>, rows: list<list<string>>}>}  $section
     */
    private function appendTransferAdvanced(array &$section, ServiceVariant $variant): void
    {
        $variant->loadMissing([
            'transfer.routes.origin.translations.language.locale',
            'transfer.routes.destination.translations.language.locale',
            'transfer.vehicles.vehicleType',
            'transfer.prices.route.origin.translations.language.locale',
            'transfer.prices.route.destination.translations.language.locale',
            'transfer.prices.vehicleType',
            'transfer.prices.currency.lmpCurrency',
        ]);

        $transfer = $variant->transfer;
        if ($transfer === null) {
            return;
        }

        $typeKey = $transfer->transfer_type === 'round_trip'
            ? 'wizard.step7_transfer_type_round_trip'
            : 'wizard.step7_transfer_type_one_way';
        $modalityKey = $transfer->modality === 'shared'
            ? 'wizard.step7_transfer_modality_shared'
            : 'wizard.step7_transfer_modality_private';

        $section['fields'][] = ['label' => __('wizard.step7_transfer_field_transfer_type'), 'value' => __($typeKey)];
        $section['fields'][] = ['label' => __('wizard.step7_transfer_field_modality'), 'value' => __($modalityKey)];
        $section['fields'][] = [
            'label' => __('wizard.step7_transfer_field_allows_multi_stops'),
            'value' => $this->boolLabel((bool) $transfer->allows_multiple_stops),
        ];

        if ($transfer->default_duration_minutes !== null) {
            $section['fields'][] = [
                'label' => __('wizard.step7_transfer_field_default_duration'),
                'value' => (string) $transfer->default_duration_minutes,
            ];
        }

        $section['fields'][] = [
            'label' => __('wizard.step7_transfer_field_requires_flight'),
            'value' => $this->boolLabel((bool) $transfer->requires_flight_info),
        ];
        $section['fields'][] = [
            'label' => __('wizard.step7_transfer_field_requires_pickup'),
            'value' => $this->boolLabel((bool) $transfer->requires_pickup_time),
        ];
        $section['fields'][] = [
            'label' => __('wizard.step7_transfer_field_requires_dropoff'),
            'value' => $this->boolLabel((bool) $transfer->requires_dropoff_time),
        ];

        $routeRows = [];
        foreach ($transfer->routes as $route) {
            $origin = trim((string) ($route->origin?->name ?? ''));
            $destination = trim((string) ($route->destination?->name ?? ''));
            $routeRows[] = [
                $origin !== '' ? $origin : '—',
                $destination !== '' ? $destination : '—',
                $route->distance_km !== null ? (string) $route->distance_km : '—',
                $route->duration_minutes !== null ? (string) $route->duration_minutes : '—',
                $this->boolLabel((bool) $route->is_active),
            ];
        }

        if ($routeRows !== []) {
            $section['tables'][] = [
                'title' => __('wizard.step7_transfer_tab_routes'),
                'headers' => [
                    __('wizard.step7_transfer_route_origin'),
                    __('wizard.step7_transfer_route_destination'),
                    __('account.service_offers.operator_preview_distance_km'),
                    __('account.service_offers.operator_preview_duration_min'),
                    __('wizard.step6_col_active'),
                ],
                'rows' => $routeRows,
            ];
        }

        $vehicleNames = $transfer->vehicles
            ->map(fn ($vehicle) => trim((string) ($vehicle->vehicleType?->name ?? '')))
            ->filter(fn (string $name) => $name !== '')
            ->values()
            ->all();

        if ($vehicleNames !== []) {
            $section['groups'][] = [
                'title' => __('wizard.step7_transfer_tab_vehicles'),
                'items' => $vehicleNames,
            ];
        }

        $priceRows = [];
        foreach ($transfer->prices as $price) {
            $priceRows[] = $this->transferPriceRow($price);
        }

        if ($priceRows !== []) {
            $section['tables'][] = [
                'title' => __('wizard.step7_transfer_tab_prices'),
                'headers' => [
                    __('wizard.step7_transfer_col_route'),
                    __('wizard.step7_transfer_col_vehicle'),
                    __('wizard.step7_transfer_col_pricing'),
                    __('wizard.step7_transfer_col_amounts'),
                ],
                'rows' => $priceRows,
            ];
        }
    }

    /**
     * @return list<string>
     */
    private function transferPriceRow(ServiceTransferPrice $price): array
    {
        $routeLabel = '—';
        if ($price->route !== null) {
            $origin = trim((string) ($price->route->origin?->name ?? ''));
            $destination = trim((string) ($price->route->destination?->name ?? ''));
            if ($origin !== '' || $destination !== '') {
                $routeLabel = $origin.' → '.$destination;
            }
        } else {
            $routeLabel = __('wizard.step7_transfer_price_all_routes');
        }

        $vehicleLabel = trim((string) ($price->vehicleType?->name ?? ''));
        if ($vehicleLabel === '') {
            $vehicleLabel = __('wizard.step7_transfer_price_any_vehicle');
        }

        $pricingLabel = $price->pricing_type === ServiceTransferPrice::PRICING_PER_PERSON
            ? __('wizard.step7_transfer_pricing_per_person')
            : __('wizard.step7_transfer_pricing_per_vehicle');

        $currency = $price->currency instanceof Currency ? $price->currency->currency_code : '';
        $amountParts = [];

        if ($price->base_price !== null) {
            $amountParts[] = __('wizard.step7_transfer_col_base').': '.$price->base_price.' '.$currency;
        }

        if ($price->price_per_person !== null) {
            $amountParts[] = __('wizard.step7_transfer_col_per_person').': '.$price->price_per_person.' '.$currency;
        }

        return [
            $routeLabel,
            $vehicleLabel,
            $pricingLabel,
            $amountParts !== [] ? implode(' · ', $amountParts) : '—',
        ];
    }

    /**
     * @param  iterable<int, object>  $items
     */
    private function appendNamedCatalogGroup(array &$section, string $title, iterable $items): void
    {
        $names = collect($items)
            ->map(fn ($item) => trim((string) ($item->name ?? '')))
            ->filter(fn (string $name) => $name !== '')
            ->values()
            ->all();

        if ($names !== []) {
            $section['groups'][] = [
                'title' => $title,
                'items' => $names,
            ];
        }
    }

    private function serviceLabel(Service $service, ServiceVariant $variant): string
    {
        $serviceName = trim((string) $service->name);
        if ($serviceName === '') {
            $serviceName = __('wizard.service_unnamed');
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

    private function formatCityLabel(LmpCity $city): string
    {
        $city->loadMissing('state.country');
        $stateName = $city->state?->name;
        $countryName = $city->state?->country?->name;
        $tail = array_filter([$stateName, $countryName], fn ($v) => $v !== null && $v !== '');

        if ($tail === []) {
            return $city->name;
        }

        return $city->name.' — '.implode(', ', $tail);
    }

    private function boolLabel(bool $value): string
    {
        return $value
            ? __('account.service_offers.operator_preview_yes')
            : __('account.service_offers.operator_preview_no');
    }

    /**
     * @return array<string, string|null>|null
     */
    private function assignedPriceListSummary(int $providerId, int $operatorId): ?array
    {
        $assignment = $this->priceResolver->activeAssignment($providerId, $operatorId);
        if ($assignment === null) {
            return null;
        }

        $assignment->loadMissing('priceList');
        $list = $assignment->priceList;
        if ($list === null) {
            return null;
        }

        return [
            'list_validity' => locale_date_range($list->valid_from, $list->valid_to),
            'assignment_validity' => locale_date_range($assignment->valid_from, $assignment->valid_to),
        ];
    }

}
