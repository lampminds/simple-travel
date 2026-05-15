<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Service;
use App\Models\ServiceOffer;
use App\Models\ServiceType;
use App\Support\AccountDashboardLane;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CatalogController extends Controller
{
    /**
     * Catalog: provider, operator, and agency see this account's services (wizard-backed).
     * Operator/agency also see a placeholder control to request linked providers' services (TBD).
     */
    public function index(Request $request): View|RedirectResponse
    {
        $account = $request->user()?->currentAccount();
        if ($account === null) {
            return redirect()->route('account.dashboard');
        }

        $laneId = AccountDashboardLane::resolvedLaneTypeId($request, $account);
        if ($laneId === null) {
            return redirect()->route('account.dashboard');
        }

        $serviceTypes = ServiceType::query()
            ->where('active', true)
            ->ordered()
            ->with('translations.language.locale')
            ->get();

        $typeFilter = $this->resolveCatalogTypeFilter($request, $serviceTypes);
        $catalogFilterView = [
            'catalogTypeFilter' => $typeFilter,
            'catalogServiceTypeOptions' => $this->catalogServiceTypeOptions($serviceTypes),
        ];

        $laneType = AccountType::query()->whereKey($laneId)->first();
        if ($laneType === null || ! $laneType->active) {
            return redirect()->route('account.dashboard');
        }

        $laneCode = strtolower(trim((string) $laneType->code));

        return match ($laneCode) {
            'provider' => $this->accountServicesCatalog($account, 'provider', $typeFilter, $catalogFilterView, $serviceTypes),
            'operator' => $this->operatorCatalog($account, $typeFilter, $catalogFilterView, $serviceTypes),
            'agency' => $this->accountServicesCatalog($account, 'agency', $typeFilter, $catalogFilterView, $serviceTypes),
            default => redirect()->route('account.dashboard'),
        };
    }

    /** Service type `code` for catalog list filter; null means all types. */
    private function resolveCatalogTypeFilter(Request $request, Collection $serviceTypes): ?string
    {
        $raw = trim((string) $request->query('type', ''));
        if ($raw === '' || strcasecmp($raw, 'all') === 0) {
            return null;
        }

        $allowed = $serviceTypes->pluck('code')->map(fn ($c) => (string) $c)->all();

        return in_array($raw, $allowed, true) ? $raw : null;
    }

    /**
     * @param  Collection<int, ServiceType>  $serviceTypes
     * @return array<string, string>
     */
    private function catalogServiceTypeOptions(Collection $serviceTypes): array
    {
        $out = [];
        foreach ($serviceTypes as $type) {
            $out[(string) $type->code] = $type->dropdown_label;
        }

        return $out;
    }

    /**
     * @param  'provider'|'agency'  $mode
     * @param  array<string, mixed>  $catalogFilterView
     * @param  Collection<int, ServiceType>  $serviceTypes
     */
    private function accountServicesCatalog(Account $account, string $mode, ?string $typeFilter, array $catalogFilterView, Collection $serviceTypes): View
    {
        $typeId = null;
        if ($typeFilter !== null) {
            $typeId = $serviceTypes->firstWhere('code', $typeFilter)?->id;
        }

        $services = Service::query()
            ->where('account_id', $account->id)
            ->when($typeId !== null, fn ($query) => $query->where('service_type_id', $typeId))
            ->with(['serviceType.translations.language.locale', 'translations.language.locale', 'media'])
            ->withCount('serviceVariants')
            ->orderByDesc('id')
            ->get();

        return view('catalog.index', array_merge($catalogFilterView, [
            'mode' => $mode,
            'services' => $services,
            'serviceTypes' => $serviceTypes,
            'linkedCatalog' => null,
        ]));
    }

    /**
     * Operator catalog: own services plus accepted variant offers from linked providers.
     *
     * @param  array<string, mixed>  $catalogFilterView
     * @param  Collection<int, ServiceType>  $serviceTypes
     */
    private function operatorCatalog(Account $account, ?string $typeFilter, array $catalogFilterView, Collection $serviceTypes): View
    {
        $typeId = null;
        if ($typeFilter !== null) {
            $typeId = $serviceTypes->firstWhere('code', $typeFilter)?->id;
        }

        $services = Service::query()
            ->where('account_id', $account->id)
            ->when($typeId !== null, fn ($query) => $query->where('service_type_id', $typeId))
            ->with(['serviceType.translations.language.locale', 'translations.language.locale', 'media'])
            ->withCount('serviceVariants')
            ->orderByDesc('id')
            ->get();

        $linkedCatalog = $this->linkedAcceptedCatalogForOperator($account);

        return view('catalog.index', array_merge($catalogFilterView, [
            'mode' => 'operator',
            'services' => $services,
            'serviceTypes' => $serviceTypes,
            'linkedCatalog' => $linkedCatalog,
        ]));
    }

    /**
     * Accepted variant offers grouped by provider; each item is one offer row (variant + operator availability).
     *
     * @return Collection<int, array{provider: Account|null, items: Collection<int, array{offer: ServiceOffer, service: Service, variant: \App\Models\ServiceVariant}>}>
     */
    private function linkedAcceptedCatalogForOperator(Account $operatorAccount): Collection
    {
        $offers = ServiceOffer::query()
            ->where('operator_id', $operatorAccount->id)
            ->where('status', ServiceOffer::STATUS_ACCEPTED)
            ->whereNotNull('service_variant_id')
            ->whereHas('serviceVariant')
            ->with([
                'providerAccount',
                'serviceVariant.service.serviceType.translations.language.locale',
                'serviceVariant.service.translations.language.locale',
                'serviceVariant.translations.language.locale',
            ])
            ->orderBy('provider_id')
            ->orderBy('service_variant_id')
            ->get();

        return $offers
            ->groupBy('provider_id')
            ->map(function (Collection $providerOffers): array {
                /** @var Account|null $provider */
                $provider = $providerOffers->first()?->providerAccount;
                $items = $providerOffers
                    ->map(function (ServiceOffer $offer): ?array {
                        $variant = $offer->serviceVariant;
                        if ($variant === null) {
                            return null;
                        }
                        $service = $variant->service;
                        if (! $service instanceof Service) {
                            return null;
                        }

                        return [
                            'offer' => $offer,
                            'service' => $service,
                            'variant' => $variant,
                        ];
                    })
                    ->filter()
                    ->values();

                return [
                    'provider' => $provider,
                    'items' => $items,
                ];
            })
            ->filter(fn (array $group): bool => $group['items']->isNotEmpty())
            ->values();
    }
}
