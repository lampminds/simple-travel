<?php

namespace App\Http\Controllers;

use App\Support\AccountDashboardLane;
use App\Support\AccountPanelStats;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperatorDashboardController extends Controller
{
    /**
     * Operator account dashboard shell (content TBD).
     */
    public function show(Request $request): View|RedirectResponse
    {
        $account = $request->user()?->currentAccount();

        $allowed = false;
        if ($account) {
            $codes = $account->categories()
                ->where('group', 'type')
                ->where('active', true)
                ->pluck('code');
            $allowed = $codes->contains('operator');
        }

        if (! $allowed) {
            return redirect()->route('account.dashboard');
        }

        $laneTypeId = AccountDashboardLane::resolveOperatorLaneTypeId($account);
        if ($laneTypeId !== null) {
            AccountDashboardLane::set($account, $laneTypeId);
        }

        return view('operator.dashboard', [
            'panelStats' => AccountPanelStats::forAccount($account),
        ]);
    }
}
