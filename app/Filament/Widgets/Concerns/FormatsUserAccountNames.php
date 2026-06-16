<?php

namespace App\Filament\Widgets\Concerns;

use App\Models\User;

trait FormatsUserAccountNames
{
    protected function formatAccountNames(User $user): string
    {
        $names = $user->accounts->pluck('commercial_name')->filter();

        if ($names->isEmpty()) {
            $names = $user->accounts->pluck('name')->filter();
        }

        return $names->isEmpty() ? '—' : $names->join(', ');
    }
}
