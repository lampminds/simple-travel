<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

/**
 * Account-type scope for catalog helper resolution (first assigned type on current account).
 */
final class CurrentCatalogHelperAccountContext
{
    public static function primaryAccountTypeId(): ?int
    {
        $user = Auth::user();
        if ($user === null) {
            return null;
        }

        $account = $user->currentAccount();
        if ($account === null) {
            return null;
        }

        return $account->accountTypes()
            ->orderBy('cat_account_types.id')
            ->first()
            ?->getKey();
    }
}
