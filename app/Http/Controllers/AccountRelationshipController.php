<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountRelationship;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountRelationshipController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $relationships = AccountRelationship::query()
            ->where(function ($query) use ($account): void {
                $query->where('operator_account_id', $account->id)
                    ->orWhere('provider_account_id', $account->id);
            })
            ->with(['operatorAccount', 'providerAccount'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('account.relationships', [
            'account' => $account,
            'relationships' => $relationships,
        ]);
    }
}

