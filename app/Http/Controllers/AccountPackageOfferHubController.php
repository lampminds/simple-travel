<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Single entry URL for operator → agency package offers.
 */
final class AccountPackageOfferHubController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $typeCodes = $account->accountTypes()
            ->where('active', true)
            ->pluck('code');

        $hasOperator = $typeCodes->contains('operator');
        $hasAgency = $typeCodes->contains('agency');

        if ($hasOperator && $hasAgency) {
            $as = (string) $request->query('as', '');
            if ($as === 'agency') {
                return app(AccountAgencyPackageOfferController::class)->index($request);
            }
            if ($as === 'operator') {
                return app(AccountOperatorPackageOfferController::class)->agenciesIndex($request);
            }

            return view('account.package-offers.hub-pick', [
                'account' => $account,
            ]);
        }

        if ($hasOperator) {
            return app(AccountOperatorPackageOfferController::class)->agenciesIndex($request);
        }

        if ($hasAgency) {
            return app(AccountAgencyPackageOfferController::class)->index($request);
        }

        abort(403, 'Account has neither operator nor agency type.');
    }
}
