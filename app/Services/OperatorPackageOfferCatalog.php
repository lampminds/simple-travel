<?php

namespace App\Services;

use App\Models\Account;
use App\Models\ServiceOffer;
use Illuminate\Support\Collection;

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
     *     service_variant_id: int|null,
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
            $variant = $offer->serviceVariant;
            $service = $variant?->service;
            if ($variant === null || $service === null) {
                continue;
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

            $out[] = [
                'offer_id' => (int) $offer->id,
                'service_id' => (int) $service->id,
                'service_variant_id' => (int) $variant->id,
                'label' => $serviceName.' — '.$variantLabel,
            ];
        }

        return $out;
    }

    /**
     * @return array{
     *     providers: array<int, string>,
     *     offersByProvider: array<int, list<array{offer_id: int, service_id: int, service_variant_id: int|null, label: string}>>
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

    private function eligibleOffersQuery(int $operatorAccountId)
    {
        return ServiceOffer::query()
            ->where('operator_id', $operatorAccountId)
            ->where('status', ServiceOffer::STATUS_ACCEPTED)
            ->where('availability', ServiceOffer::AVAILABILITY_ACTIVE)
            ->whereNotNull('service_variant_id')
            ->whereHas('serviceVariant');
    }
}
