<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\ServiceOffer;
use Illuminate\Database\Eloquent\Builder;

/**
 * Accepted, active variant offers an operator may add to commercial packages.
 */
final class OperatorPackageOfferCatalog
{
    /**
     * @return array<int, string> provider_account_id => label
     */
    public function providerOptionsForOperator(int $operatorAccountId): array
    {
        $providerIds = $this->eligibleOffersQuery($operatorAccountId)
            ->distinct()
            ->orderBy('provider_id')
            ->pluck('provider_id');

        $options = [];
        foreach (Account::query()->whereIn('id', $providerIds)->orderBy('id')->get() as $provider) {
            $id = (int) $provider->id;
            $options[$id] = (string) ($provider->commercial_name ?? $provider->name ?? $provider->nick ?? ('#'.$id));
        }

        return $options;
    }

    /**
     * @return list<array{
     *     offer_id: int,
     *     service_id: int,
     *     service_variant_id: int,
     *     label: string
     * }>
     */
    public function offerOptionsForProvider(int $operatorAccountId, int $providerAccountId): array
    {
        $offers = $this->eligibleOffersQuery($operatorAccountId)
            ->where('provider_id', $providerAccountId)
            ->with([
                'serviceVariant.service.translations.language.locale',
                'serviceVariant.translations.language.locale',
            ])
            ->orderBy('service_variant_id')
            ->get();

        $out = [];
        foreach ($offers as $offer) {
            $label = $this->labelForOffer($offer);
            if ($label === null) {
                continue;
            }

            $variant = $offer->serviceVariant;
            $serviceId = (int) ($variant?->service_id ?? 0);
            $variantId = (int) ($offer->service_variant_id ?? 0);
            if ($serviceId < 1 || $variantId < 1) {
                continue;
            }

            $out[] = [
                'offer_id' => (int) $offer->id,
                'service_id' => $serviceId,
                'service_variant_id' => $variantId,
                'label' => $label,
            ];
        }

        return $out;
    }

    /**
     * @return array{
     *     providers: array<int, string>,
     *     offersByProvider: array<int, list<array{offer_id: int, service_id: int, service_variant_id: int, label: string}>>
     * }
     */
    public function catalogPayloadForOperator(int $operatorAccountId): array
    {
        $providers = $this->providerOptionsForOperator($operatorAccountId);
        $offersByProvider = [];
        foreach (array_keys($providers) as $providerId) {
            $offersByProvider[$providerId] = $this->offerOptionsForProvider($operatorAccountId, (int) $providerId);
        }

        return [
            'providers' => $providers,
            'offersByProvider' => $offersByProvider,
        ];
    }

    public function findEligibleOffer(int $operatorAccountId, int $offerId): ?ServiceOffer
    {
        return $this->eligibleOffersQuery($operatorAccountId)
            ->whereKey($offerId)
            ->first();
    }

    private function eligibleOffersQuery(int $operatorAccountId): Builder
    {
        return ServiceOffer::query()
            ->where('operator_id', $operatorAccountId)
            ->where('status', ServiceOffer::STATUS_ACCEPTED)
            ->where('availability', ServiceOffer::AVAILABILITY_ACTIVE)
            ->whereHas('serviceVariant');
    }

    private function labelForOffer(ServiceOffer $offer): ?string
    {
        $variant = $offer->serviceVariant;
        $service = $variant?->service;
        if ($variant === null || $service === null) {
            return null;
        }

        $serviceName = trim((string) ($service->name ?? ''));
        if ($serviceName === '') {
            $serviceName = '#'.$service->id;
        }

        $variantLabel = trim((string) ($variant->sku ?? ''));
        if ($variantLabel === '') {
            $variantLabel = trim((string) ($variant->name ?? ''));
        }
        if ($variantLabel === '') {
            $variantLabel = '#'.$variant->id;
        }

        return $serviceName.' — '.$variantLabel;
    }
}
