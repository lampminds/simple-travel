<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Service;
use App\Models\ServiceType;
use App\Support\AccountDashboardLane;
use App\Support\AccountTypeCategoryIds;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Catalog: provider sees their services (same data as former provider dashboard list);
     * wholesaler sees approved services from linked providers (mock) plus CTA for own services (mock).
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
            $laneId === AccountTypeCategoryIds::PROVIDER => $this->providerCatalog($account),
            AccountTypeCategoryIds::isOperatorLaneTypeId($laneId) => $this->wholesalerCatalog(),
            $laneId === AccountTypeCategoryIds::AGENCY => $this->agencyCatalog(),
            default => redirect()->route('account.dashboard'),
        };
    }

    private function providerCatalog(Account $account): View
    {
        $services = Service::query()
            ->where('account_id', $account->id)
            ->with(['serviceType.translations.language.locale', 'translations.language.locale', 'media'])
            ->orderByDesc('id')
            ->get();

        $serviceTypes = ServiceType::query()
            ->where('active', true)
            ->ordered()
            ->with('translations.language.locale')
            ->get();

        return view('catalog.index', [
            'mode' => 'provider',
            'services' => $services,
            'serviceTypes' => $serviceTypes,
            'wholesalerApprovedRows' => collect(),
        ]);
    }

    private function wholesalerCatalog(): View
    {
        return view('catalog.index', [
            'mode' => 'wholesaler',
            'services' => collect(),
            'serviceTypes' => collect(),
            'wholesalerApprovedRows' => $this->mockWholesalerApprovedServices(),
        ]);
    }

    private function agencyCatalog(): View
    {
        return view('catalog.index', [
            'mode' => 'agency',
            'services' => collect(),
            'serviceTypes' => collect(),
            'wholesalerApprovedRows' => collect(),
        ]);
    }

    /**
     * Mock rows until invitation-linked approved services exist in the domain.
     *
     * @return array<int, array{provider:string, name:string, type:string, status:string}>
     */
    private function mockWholesalerApprovedServices(): array
    {
        return [
            [
                'provider' => 'Hostería del Lago',
                'name' => 'Estadía 2 noches + desayuno',
                'type' => 'Hotel',
                'status' => __('catalog.wholesaler_status_approved'),
            ],
            [
                'provider' => 'Nieve y Montaña',
                'name' => 'Excursión glaciar guiada',
                'type' => 'Excursión',
                'status' => __('catalog.wholesaler_status_approved'),
            ],
            [
                'provider' => 'Sabores Patagónicos',
                'name' => 'Cena degustación regional',
                'type' => 'Gastronomía',
                'status' => __('catalog.wholesaler_status_approved'),
            ],
        ];
    }
}
