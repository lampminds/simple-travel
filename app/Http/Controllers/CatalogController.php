<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Service;
use App\Models\ServiceOffer;
use App\Models\ServiceType;
use App\Support\AccountDashboardLane;
use App\Support\AccountTypeCategoryIds;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

        return match (true) {
            $laneId === AccountTypeCategoryIds::PROVIDER => $this->accountServicesCatalog($account, 'provider'),
            AccountTypeCategoryIds::isOperatorLaneTypeId($laneId) => $this->operatorCatalog($account),
            $laneId === AccountTypeCategoryIds::AGENCY => $this->accountServicesCatalog($account, 'agency'),
            default => redirect()->route('account.dashboard'),
        };
    }

    /**
     * @param  'provider'|'operator'|'agency'  $mode
     */
    private function accountServicesCatalog(Account $account, string $mode): View
    {
        $services = Service::query()
            ->where('account_id', $account->id)
            ->with(['serviceType.translations.language.locale', 'translations.language.locale', 'media'])
            ->withCount('serviceVariants')
            ->orderByDesc('id')
            ->get();

        $serviceTypes = ServiceType::query()
            ->where('active', true)
            ->ordered()
            ->with('translations.language.locale')
            ->get();

        return view('catalog.index', [
            'mode' => $mode,
            'services' => $services,
            'serviceTypes' => $serviceTypes,
            'linkedCatalog' => null,
        ]);
    }

    /**
     * Operator catalog: own services plus accepted variant offers from linked providers.
     */
    private function operatorCatalog(Account $account): View
    {
        $services = Service::query()
            ->where('account_id', $account->id)
            ->with(['serviceType.translations.language.locale', 'translations.language.locale', 'media'])
            ->withCount('serviceVariants')
            ->orderByDesc('id')
            ->get();

        $serviceTypes = ServiceType::query()
            ->where('active', true)
            ->ordered()
            ->with('translations.language.locale')
            ->get();

        $linkedCatalog = $this->linkedAcceptedCatalogForOperator($account);

        return view('catalog.index', [
            'mode' => 'operator',
            'services' => $services,
            'serviceTypes' => $serviceTypes,
            'linkedCatalog' => $linkedCatalog,
        ]);
    }

    /**
     * Accepted variant offers grouped by provider; each item is one offer row (variant + operator availability).
     *
     * @return \Illuminate\Support\Collection<int, array{provider: Account|null, items: \Illuminate\Support\Collection<int, array{offer: ServiceOffer, service: Service, variant: \App\Models\ServiceVariant}>}>
     */
    private function linkedAcceptedCatalogForOperator(Account $operatorAccount): \Illuminate\Support\Collection
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
            ->map(function (\Illuminate\Support\Collection $providerOffers): array {
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
