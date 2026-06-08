<?php

namespace App\Http\Controllers;

use App\Services\OperatorCurrencyRatesChartService;
use App\Support\AccountDashboardLane;
use App\Support\AccountPanelStats;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgencyDashboardController extends Controller
{
    /**
     * Agency account dashboard landing.
     */
    public function show(Request $request, OperatorCurrencyRatesChartService $currencyRatesChart): View|RedirectResponse
    {
        $account = $request->user()?->currentAccount();

        $allowed = false;
        if ($account) {
            $typeCodes = $account->accountTypes()
                ->where('active', true)
                ->pluck('code');
            $allowed = $typeCodes->contains('agency');
        }

        if (! $allowed) {
            return redirect()->route('account.dashboard');
        }

        $agencyTypeId = AccountDashboardLane::activeTypeIdForLaneCode($account, 'agency');
        if ($agencyTypeId !== null) {
            AccountDashboardLane::set($account, $agencyTypeId);
        }

        return view('agency.dashboard', [
            'panelStats' => AccountPanelStats::forAccount($account),
            'currencyRatesChart' => $currencyRatesChart->build(),
        ]);
    }
}
