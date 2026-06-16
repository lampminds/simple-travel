<?php

namespace App\Filament\Widgets\Concerns;

use App\Models\User;

trait MapsRecentUserRows
{
    /**
     * @return array{id: int, can_impersonate: bool, name: string, account: string, time: string|null, time_formatted: string|null}
     */
    protected function mapRecentUserRow(User $user, ?string $time, ?string $timeFormatted): array
    {
        return [
            'id' => $user->id,
            'can_impersonate' => $this->canImpersonateUser($user),
            'name' => $user->name,
            'account' => $this->formatAccountNames($user),
            'time' => $time,
            'time_formatted' => $timeFormatted,
        ];
    }

    /**
     * @return array{cells: array<int, string|null>, titles: array<int, string|null>, id: int, can_impersonate: bool}
     */
    protected function toCompactTableRow(array $row): array
    {
        return [
            'id' => $row['id'],
            'can_impersonate' => $row['can_impersonate'],
            'cells' => [
                $row['name'],
                $row['account'],
                $row['time'],
            ],
            'titles' => [
                $row['name'],
                $row['account'],
                $row['time_formatted'],
            ],
        ];
    }
}
