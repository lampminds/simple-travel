<?php

namespace App\Filament\Widgets\Concerns;

use App\Models\Account;
use App\Models\User;

trait FormatsUserAccountNames
{
    /**
     * Short account label for the dashboard lists. Prefer the account name
     * over the legal name (commercial_name), which is usually longer.
     */
    protected function formatAccountNames(User $user): string
    {
        $names = $user->accounts
            ->map(function (Account $account): string {
                $name = trim((string) $account->name);

                if ($name !== '') {
                    return $name;
                }

                return trim((string) $account->commercial_name);
            })
            ->filter();

        return $names->isEmpty() ? '—' : $names->join(', ');
    }
}
