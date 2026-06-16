<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\FormatsUserAccountNames;
use App\Filament\Widgets\Concerns\InteractsWithWidgetActions;
use App\Filament\Widgets\Concerns\MapsRecentUserRows;
use App\Models\User;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class LatestLoginsWidget extends Widget implements HasActions, HasSchemas
{
    use FormatsUserAccountNames;
    use InteractsWithWidgetActions;
    use InteractsWithSchemas;
    use MapsRecentUserRows;

    protected static ?string $heading = 'Últimos accesos';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 1;

    protected string $view = 'filament.widgets.latest-logins-widget';

    protected static int $limit = 6;

    /**
     * @return Collection<int, array{id: int, can_impersonate: bool, name: string, account: string, time: string|null, time_formatted: string|null}>
     */
    public function getUsers(): Collection
    {
        return User::query()
            ->with('accounts')
            ->whereNotNull('last_login_at')
            ->latest('last_login_at')
            ->limit(static::$limit)
            ->get()
            ->map(fn (User $user): array => $this->mapRecentUserRow(
                $user,
                $user->last_login_at?->diffForHumans(),
                $user->last_login_at ? locale_datetime($user->last_login_at) : null,
            ));
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'users' => $this->getUsers(),
            'heading' => static::$heading,
            'showImpersonation' => $this->canImpersonateUsers(),
        ];
    }
}
