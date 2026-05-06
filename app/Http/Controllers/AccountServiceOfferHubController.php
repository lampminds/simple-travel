<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Single entry URL for catalog variant offers (menu / header link).
 * Delegates to provider or operator UI based on account type categories.
 */
final class AccountServiceOfferHubController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $typeCodes = $account->categories()
            ->where('group', 'type')
            ->where('active', true)
            ->pluck('code');

        $hasProvider = $typeCodes->contains('provider');
        $hasOperator = $typeCodes->contains('operator');

        if ($hasProvider && $hasOperator) {
            $as = (string) $request->query('as', '');
            if ($as === 'operator') {
                return app(AccountOperatorServiceOfferController::class)->index($request);
            }
            if ($as === 'provider') {
                return app(AccountProviderServiceOfferController::class)->operatorsIndex($request);
            }

            return view('account.service-offers.hub-pick', [
                'account' => $account,
            ]);
        }

        if ($hasProvider) {
            return app(AccountProviderServiceOfferController::class)->operatorsIndex($request);
        }

        if ($hasOperator) {
            return app(AccountOperatorServiceOfferController::class)->index($request);
        }

        abort(403, 'Account has neither provider nor operator type.');
    }
}
