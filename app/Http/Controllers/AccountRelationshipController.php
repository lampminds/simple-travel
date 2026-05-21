<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountRelationshipsListingService;
use App\Support\AccountBusinessTypeGate;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountRelationshipController extends Controller
{
    public function __construct(
        private readonly AccountRelationshipsListingService $listing,
    ) {
    }

    public function index(Request $request): View
    {
        $account = AccountBusinessTypeGate::resolveCurrentAccount($request);

        $typeCodes = $account->accountTypes()
            ->where('active', true)
            ->pluck('code');

        $hasProvider = $typeCodes->contains('provider');
        $hasOperator = $typeCodes->contains('operator');

        abort_unless($hasProvider || $hasOperator, 403);

        if ($hasProvider && $hasOperator) {
            $as = (string) $request->query('as', '');
            if ($as === '') {
                return view('account.relationships.hub-pick', [
                    'account' => $account,
                ]);
            }

            if ($as === 'provider') {
                return $this->listView($account, 'provider');
            }

            if ($as === 'operator') {
                return $this->listView($account, 'operator');
            }
        }

        if ($hasProvider) {
            return $this->listView($account, 'provider');
        }

        return $this->listView($account, 'operator');
    }

    private function listView(Account $account, string $perspective): View
    {
        $typeCodes = $account->accountTypes()
            ->where('active', true)
            ->pluck('code');

        return view('account.relationships.index', [
            'account' => $account,
            'accountLabel' => $this->listing->accountDisplayName($account),
            'perspective' => $perspective,
            'rows' => $this->listing->forAccount((int) $account->id, $perspective),
            'showRoleColumn' => false,
            'showHubBack' => $typeCodes->contains('provider') && $typeCodes->contains('operator'),
        ]);
    }
}
