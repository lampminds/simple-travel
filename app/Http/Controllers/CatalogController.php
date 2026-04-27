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
            AccountTypeCategoryIds::isOperatorLaneTypeId($laneId) => $this->accountServicesCatalog($account, 'operator'),
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
        ]);
    }
}
