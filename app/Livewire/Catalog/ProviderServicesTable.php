<?php

namespace App\Livewire\Catalog;

use App\Models\Service;
use App\Models\ServiceVariant;
use App\Services\PriceFormatService;
use App\Support\ServiceWizardSkipsVariantsStep;
use App\Support\ServiceWizardStepEight;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Livewire\Component;

class ProviderServicesTable extends Component
{
    /** @var Collection<int, Service> */
    public Collection $services;

    public ?string $catalogType = null;

    public ?string $catalogStatus = null;

    public function mount(Collection $services, ?string $catalogType = null, ?string $catalogStatus = null): void
    {
        $this->services = $services;
        $this->catalogType = $catalogType !== null && $catalogType !== '' ? $catalogType : null;
        $this->catalogStatus = $catalogStatus !== null && $catalogStatus !== '' ? $catalogStatus : null;
    }

    public function requestCopy(int $serviceId): void
    {
        $this->dispatch('open-copy-provider-service-modal', serviceId: $serviceId);
    }

    public function skipsVariantsForType(?string $code): bool
    {
        return ServiceWizardSkipsVariantsStep::isSkippedForServiceTypeCode($code);
    }

    public function hasAdvancedStepForType(?string $code): bool
    {
        return ServiceWizardStepEight::isEnabledForServiceTypeCode($code);
    }

    /**
     * Base price of each variant, as "ARS 14,500", so the catalog row can be recognized by price.
     * has_policy is true when that variant has party-size prices or age fares.
     *
     * @return list<array{label: string, has_policy: bool}>
     */
    public function variantPriceLabels(Service $service): array
    {
        $accountId = $service->account_id !== null ? (int) $service->account_id : null;
        $formatter = app(PriceFormatService::class);
        $labels = [];

        foreach ($service->serviceVariants as $variant) {
            if ($variant->base_price === null || $variant->base_price === '') {
                continue;
            }

            $labels[] = [
                'label' => $this->formatVariantPrice($variant, $formatter, $accountId),
                'has_policy' => $variant->usesPaxBands()
                    || ($variant->relationLoaded('passengerPriceRules') && $variant->passengerPriceRules->isNotEmpty())
                    || (bool) ($variant->passenger_price_rules_exists ?? false),
            ];
        }

        return $labels;
    }

    public function render(): View
    {
        $this->ensureVariantPricesLoaded();

        return view('livewire.catalog.provider-services-table');
    }

    /**
     * Variants travel inside the public services collection and are dropped on the next Livewire request.
     */
    private function ensureVariantPricesLoaded(): void
    {
        if ($this->services->isEmpty()) {
            return;
        }

        $needsLoad = $this->services->contains(function (Service $service): bool {
            if (! $service->relationLoaded('serviceVariants')) {
                return true;
            }

            return $service->serviceVariants->contains(
                fn (ServiceVariant $variant): bool => ! $variant->relationLoaded('paxBands')
                    || ! $variant->relationLoaded('passengerPriceRules')
            );
        });
        if (! $needsLoad) {
            return;
        }

        $variants = ServiceVariant::query()
            ->whereIn('service_id', $this->services->pluck('id')->all())
            ->with(['currency.lmpCurrency', 'paxBands', 'passengerPriceRules'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('service_id');

        foreach ($this->services as $service) {
            $service->setRelation(
                'serviceVariants',
                $variants->get($service->id, new EloquentCollection()),
            );
        }
    }

    private function formatVariantPrice(ServiceVariant $variant, PriceFormatService $formatter, ?int $accountId): string
    {
        $amount = $formatter->format($variant->base_price, $accountId);
        $code = strtoupper(trim((string) ($variant->currency?->currency_code ?? '')));
        if ($code === '' || $code === '—') {
            return $amount;
        }

        return $code.' '.$amount;
    }
}
