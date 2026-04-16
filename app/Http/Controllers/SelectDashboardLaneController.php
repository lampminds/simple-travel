<?php

namespace App\Http\Controllers;

use App\Support\AccountDashboardLane;
use App\Support\AccountTypeCategoryIds;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SelectDashboardLaneController extends Controller
{
    /**
     * Set active dashboard lane cookie and redirect to the chosen panel.
     */
    public function __invoke(Request $request, string $lane): RedirectResponse
    {
        $account = $request->user()?->currentAccount();
        if ($account === null) {
            return redirect()->route('account.dashboard');
        }

        $typeId = match ($lane) {
            'provider' => AccountTypeCategoryIds::PROVIDER,
            'agency' => AccountTypeCategoryIds::AGENCY,
            'operator' => AccountDashboardLane::resolveOperatorLaneTypeId($account),
            default => null,
        };

        if ($typeId === null) {
            return redirect()->route('account.dashboard');
        }

        if ($lane !== 'operator' && ! AccountDashboardLane::accountHasActiveTypeId($account, $typeId)) {
            return redirect()->route('account.dashboard');
        }

        AccountDashboardLane::set($account, $typeId);

        return redirect()->route(match ($lane) {
            'provider' => 'provider.dashboard',
            'operator' => 'operator.dashboard',
            'agency' => 'agency.dashboard',
            default => 'account.dashboard',
        });
    }
}
