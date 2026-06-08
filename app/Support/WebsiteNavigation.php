<?php

namespace App\Support;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Menu;
use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Builds the authenticated website navbar menu from {@see Menu} rows, scoped by account type
 * (exclusions in cat_menu_account_type_exclusions).
 */
final class WebsiteNavigation
{
    /**
     * Root menu rows with {@see Menu::$nav_children} collections attached (recursive), or null when the menu must not be shown.
     *
     * @return \Illuminate\Support\Collection<int, Menu>|null
     */
    public static function navbarMenuRoots(): ?Collection
    {
        if (! Auth::check()) {
            return null;
        }

        // Account type selection dashboard: do not show DB-driven menu yet.
        if (request()->route()?->getName() === 'account.dashboard') {
            return null;
        }

        $user = Auth::user();
        $account = $user->currentAccount();
        if ($account === null) {
            return null;
        }

        return self::buildMenuTree(self::resolvedExclusionTypeIds($account));
    }

    /**
     * Account type IDs from {@see cat_account_types} used to evaluate menu exclusions.
     * Uses lane codes and session ids (never hardcoded category constants).
     * Empty collection means no exclusion filter (all active menu rows are eligible).
     *
     * @return \Illuminate\Support\Collection<int, int>
     */
    private static function resolvedExclusionTypeIds(Account $account): Collection
    {
        $request = request();
        $route = $request->route();

        if ($route instanceof IlluminateRoute) {
            $laneCode = self::routeLaneCode($route);
            if ($laneCode !== null) {
                $typeId = AccountDashboardLane::activeTypeIdForLaneCode($account, $laneCode);
                if ($typeId !== null) {
                    return collect([$typeId]);
                }
            }
        }

        $laneTypeId = AccountDashboardLane::resolvedLaneTypeId($request, $account);
        if ($laneTypeId !== null) {
            return collect([$laneTypeId]);
        }

        $sessionTypeIds = CurrentAccountSession::typeIds($request);
        if ($sessionTypeIds === []) {
            return collect();
        }

        return collect($sessionTypeIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();
    }

    /**
     * Canonical lane code for the current route (provider|operator|agency), or null.
     */
    private static function routeLaneCode(IlluminateRoute $route): ?string
    {
        $firstSegment = (string) request()->segment(1);
        $firstParam = (string) $route->parameter('first');
        $routeName = (string) $route->getName();

        $scope = $firstSegment !== '' ? $firstSegment : $firstParam;
        if ($scope === '' && str_starts_with($routeName, 'provider.')) {
            $scope = 'provider';
        } elseif ($scope === '' && str_starts_with($routeName, 'agency.')) {
            $scope = 'agency';
        } elseif ($scope === '' && str_starts_with($routeName, 'operator.')) {
            $scope = 'operator';
        }

        if (in_array($scope, ['provider', 'agency', 'operator'], true)) {
            return $scope;
        }

        if (str_starts_with($routeName, 'services.') || $firstSegment === 'services') {
            return 'provider';
        }

        return null;
    }

    /**
     * Resolve the current route type ID if it maps to a supported account type.
     *
     * @deprecated Prefer {@see resolvedExclusionTypeIds()} — uses real {@see cat_account_types} ids.
     */
    public static function routeMenuTypeId(mixed $route, Account $account): ?int
    {
        if (! $route instanceof IlluminateRoute) {
            return null;
        }

        $laneCode = self::routeLaneCode($route);
        if ($laneCode !== null) {
            return AccountDashboardLane::activeTypeIdForLaneCode($account, $laneCode);
        }

        return AccountDashboardLane::resolvedLaneTypeId(request(), $account);
    }

    /**
     * Resolve active account type IDs for the account,
     * constrained to the type expected by the current route.
     *
     * @return \Illuminate\Support\Collection<int>
     */
    public static function resolvedTypeCategoryIds(Account $account, int $routeTypeId): Collection
    {
        return $account->accountTypes()
            ->where((new AccountType)->getTable().'.active', true)
            ->where((new AccountType)->getTable().'.id', $routeTypeId)
            ->pluck((new AccountType)->getTable().'.id')
            ->values();
    }

    /**
     * @param  \Illuminate\Support\Collection<int|string, int>  $typeIds
     * @return \Illuminate\Support\Collection<int, Menu>
     */
    private static function buildMenuTree(Collection $typeIds): Collection
    {
        $typeIds = $typeIds->map(fn ($id) => (int) $id)->filter(fn (int $id) => $id > 0)->unique()->values();

        $table = (new AccountType)->getTable();

        $query = Menu::query()->where('active', true);

        if ($typeIds->isNotEmpty()) {
            $query->whereDoesntHave(
                'excludedAccountTypes',
                fn ($q) => $q->whereIn($table.'.id', $typeIds)
            );
        }

        $menus = $query
            ->with(['translations.language.locale'])
            ->ordered()
            ->get();

        $idsInSet = $menus->modelKeys();
        $visible = $menus->filter(
            fn (Menu $m) => $m->parent_id === null || in_array((int) $m->parent_id, $idsInSet, true)
        );

        $byParentKey = $visible->groupBy(function (Menu $m): string {
            return $m->parent_id === null ? '__root__' : (string) (int) $m->parent_id;
        });

        $attach = null;
        $attach = function (string $parentKey) use (&$attach, $byParentKey): Collection {
            return static::sortSiblingMenus($byParentKey->get($parentKey, collect()))
                ->map(function (Menu $menu) use (&$attach): Menu {
                    $menu->setRelation('nav_children', $attach((string) (int) $menu->id));

                    return $menu;
                });
        };

        return $attach('__root__');
    }

    /**
     * Sort menus within one parent (roots use parent key {@see buildMenuTree}).
     *
     * @param  \Illuminate\Support\Collection<int, Menu>  $menus
     * @return \Illuminate\Support\Collection<int, Menu>
     */
    private static function sortSiblingMenus(Collection $menus): Collection
    {
        return $menus
            ->sortBy(fn (Menu $m): array => [(int) ($m->sort_order ?? 9999), (int) $m->id])
            ->values();
    }

    public static function urlForMenu(Menu $menu): string
    {
        $name = $menu->route;
        if (! is_string($name)) {
            return '#';
        }

        $name = trim($name);
        if ($name === '') {
            return '#';
        }

        $placeholder = fn (): string => route('website.menu.placeholder', ['missingRoute' => $name]);

        // Absolute URLs: use as-is (e.g. external links pasted in admin).
        if (str_starts_with($name, 'http://') || str_starts_with($name, 'https://')) {
            return $name;
        }

        $candidates = array_unique(array_filter([
            $name,
            str_starts_with($name, '/') ? ltrim($name, '/') : null,
        ]));

        foreach ($candidates as $candidate) {
            if (! Route::has($candidate)) {
                continue;
            }

            try {
                return route($candidate);
            } catch (\Throwable) {
                // Missing route parameters or other URL generation issues → placeholder.
                return $placeholder();
            }
        }

        return $placeholder();
    }
}
