<?php

namespace App\Http\Controllers;

use App\Services\OperatorCurrencyRatesChartService;
use App\Support\AccountDashboardLane;
use App\Support\AccountPanelStats;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProviderDashboardController extends Controller
{
    /**
     * Provider dashboard landing; service listing lives under {@see CatalogController}.
     */
    public function show(Request $request, OperatorCurrencyRatesChartService $currencyRatesChart): View|RedirectResponse
    {
        $account = $request->user()?->currentAccount();

        $typeCodes = collect();
        if ($account) {
            $typeCodes = $account->accountTypes()
                ->where('active', true)
                ->pluck('code');
        }

        if (! $typeCodes->contains('provider')) {
            return redirect()->to('/account/dashboard');
        }

        $providerTypeId = AccountDashboardLane::activeTypeIdForLaneCode($account, 'provider');
        if ($providerTypeId === null) {
            return redirect()->to('/account/dashboard');
        }

        AccountDashboardLane::set($account, $providerTypeId);

        return view('provider.dashboard', [
            'panelStats' => AccountPanelStats::forAccount($account),
            'currencyRatesChart' => $currencyRatesChart->build(),
        ]);
    }
}
