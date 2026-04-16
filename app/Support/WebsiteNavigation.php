<?php

namespace App\Support;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\Menu;
use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Builds the authenticated website navbar menu from {@see Menu} rows, scoped by account type
 * (assignments in cat_menu_account_type_assignments).
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

        $routeTypeId = self::routeMenuTypeId(request()->route(), $account);
        if ($routeTypeId === null) {
            return null;
        }

        $typeIds = self::resolvedTypeCategoryIds($account, $routeTypeId);

        return self::buildMenuTree($typeIds);
    }

    /**
     * Resolve the current route type ID if it maps to a supported account type.
     */
    private static function routeMenuTypeId(mixed $route, Account $account): ?int
    {
        if (! $route instanceof IlluminateRoute) {
            return null;
        }

        $typeId = data_get($route->getAction(), 'defaults.menu_type_id')
            ?? data_get($route->getAction(), 'menu_type_id')
            ?? $route->parameter('menu_type_id');

        if (is_numeric($typeId)) {
            $typeId = (int) $typeId;

            return in_array($typeId, AccountTypeCategoryIds::allowed(), true) ? $typeId : null;
        }

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

        $typeId = match ($scope) {
            'provider' => AccountTypeCategoryIds::PROVIDER,
            'agency' => AccountTypeCategoryIds::AGENCY,
            'operator' => AccountTypeCategoryIds::WHOLESALER,
            default => null,
        };

        if (is_numeric($typeId)) {
            $typeId = (int) $typeId;

            return in_array($typeId, AccountTypeCategoryIds::allowed(), true) ? $typeId : null;
        }

        // Generic routes (/catalog, /relationships): use persisted dashboard lane (cookie) or single-type default.
        if (in_array($routeName, ['catalog', 'relationships'], true)) {
            $lane = AccountDashboardLane::resolvedLaneTypeId(request(), $account);
            if ($lane !== null && in_array($lane, AccountTypeCategoryIds::allowed(), true)) {
                return $lane;
            }

            return null;
        }

        // Services area and wizard routes: provider lane by default; fall back to session/lane resolution.
        if (str_starts_with($routeName, 'services.') || $firstSegment === 'services') {
            $sessionTypeIds = CurrentAccountSession::typeIds(request());
            if (in_array(AccountTypeCategoryIds::PROVIDER, $sessionTypeIds, true)) {
                return AccountTypeCategoryIds::PROVIDER;
            }

            $lane = AccountDashboardLane::resolvedLaneTypeId(request(), $account);
            if ($lane !== null && in_array($lane, AccountTypeCategoryIds::allowed(), true)) {
                return $lane;
            }

            if (count($sessionTypeIds) === 1) {
                $only = (int) $sessionTypeIds[0];

                return in_array($only, AccountTypeCategoryIds::allowed(), true) ? $only : null;
            }

            return null;
        }

        // Account area routes should keep dynamic menu based on active session account + selected lane.
        if (str_starts_with($routeName, 'account.')) {
            $sessionTypeIds = CurrentAccountSession::typeIds(request());

            if (count($sessionTypeIds) === 1) {
                $only = (int) $sessionTypeIds[0];

                return in_array($only, AccountTypeCategoryIds::allowed(), true) ? $only : null;
            }

            $lane = AccountDashboardLane::resolvedLaneTypeId(request(), $account);
            if ($lane !== null && in_array($lane, AccountTypeCategoryIds::allowed(), true)) {
                return $lane;
            }

            return null;
        }

        return null;
    }

    /**
     * Resolve active category IDs for account business types (cat_account_categories.group = type),
     * constrained to the type expected by the current route.
     *
     * @return \Illuminate\Support\Collection<int>
     */
    public static function resolvedTypeCategoryIds(Account $account, int $routeTypeId): Collection
    {
        return $account->typeCategories()
            ->where((new AccountCategory)->getTable().'.active', true)
            ->where((new AccountCategory)->getTable().'.id', $routeTypeId)
            ->pluck((new AccountCategory)->getTable().'.id')
            ->values();
    }

    /**
     * @param  \Illuminate\Support\Collection<int|string, int>  $typeIds
     * @return \Illuminate\Support\Collection<int, Menu>
     */
    private static function buildMenuTree(Collection $typeIds): Collection
    {
        $typeIds = $typeIds->map(fn ($id) => (int) $id)->filter(fn (int $id) => $id > 0)->unique()->values();
        if ($typeIds->isEmpty()) {
            return collect();
        }

        $table = (new AccountCategory)->getTable();

        $menus = Menu::query()
            ->where('active', true)
            ->whereHas(
                'accountTypes',
                fn ($q) => $q->whereIn($table.'.id', $typeIds)
            )
            ->with(['translations.language.locale'])
            ->orderBy('sort_order')
            ->orderBy('id')
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
            return $byParentKey
                ->get($parentKey, collect())
                ->sortBy(fn (Menu $m) => [$m->sort_order, $m->id])
                ->values()
                ->map(function (Menu $menu) use (&$attach): Menu {
                    $menu->setRelation('nav_children', $attach((string) (int) $menu->id));

                    return $menu;
                });
        };

        return $attach('__root__');
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
