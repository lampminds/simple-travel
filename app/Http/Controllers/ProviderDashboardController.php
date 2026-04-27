<?php

namespace App\Http\Controllers;

use App\Support\AccountDashboardLane;
use App\Support\AccountPanelStats;
use App\Support\AccountTypeCategoryIds;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProviderDashboardController extends Controller
{
    /**
     * Provider dashboard landing; service listing lives under {@see CatalogController}.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $account = $request->user()?->currentAccount();

        $typeCodes = collect();
        if ($account) {
            $typeCodes = $account->categories()
                ->where('group', 'type')
                ->where('active', true)
                ->pluck('code');
        }

        if (! $typeCodes->contains('provider')) {
            return redirect()->to('/account/dashboard');
        }

        AccountDashboardLane::set($account, AccountTypeCategoryIds::PROVIDER);

        return view('provider.dashboard', [
            'panelStats' => AccountPanelStats::forAccount($account),
        ]);
    }
}
