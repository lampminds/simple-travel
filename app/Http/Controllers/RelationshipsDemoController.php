<?php

namespace App\Http\Controllers;

use App\Support\AccountDashboardLane;
use App\Support\AccountTypeCategoryIds;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RelationshipsDemoController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $account = $request->user()?->currentAccount();
        if ($account === null) {
            return redirect()->route('account.dashboard');
        }

        $laneId = AccountDashboardLane::resolvedLaneTypeId($request, $account);
        if ($laneId === null) {
            return redirect()->route('account.dashboard');
        }

        return $this->renderForType($request, $laneId);
    }

    public function provider(Request $request): View|RedirectResponse
    {
        return $this->renderForType($request, AccountTypeCategoryIds::PROVIDER);
    }

    public function operator(Request $request): View|RedirectResponse
    {
        $account = $request->user()?->currentAccount();
        $typeId = $account !== null ? AccountDashboardLane::resolveOperatorLaneTypeId($account) : null;
        if ($typeId === null) {
            return redirect()->route('account.dashboard');
        }

        return $this->renderForType($request, $typeId);
    }

    private function renderForType(Request $request, int $typeId): View|RedirectResponse
    {
        $account = $request->user()?->currentAccount();
        if ($account === null) {
            return redirect()->route('account.dashboard');
        }

        $accountTypeIds = $account->typeCategories()
            ->where('cat_account_categories.active', true)
            ->pluck('cat_account_categories.id');

        if (! $accountTypeIds->contains($typeId)) {
            return redirect()->route('account.dashboard');
        }

        if ($typeId === AccountTypeCategoryIds::AGENCY) {
            return view('relationships.index', [
                'isProvider' => false,
                'title' => 'Relaciones',
                'heading' => 'Relaciones',
                'intro' => 'Las relaciones comerciales de tu agencia se mostrarán aquí (demo).',
                'relatedLabel' => 'Empresa',
                'rows' => [],
            ]);
        }

        $isProvider = $typeId === AccountTypeCategoryIds::PROVIDER;

        $rows = $isProvider
            ? $this->providerRelatedOperators()
            : $this->wholesalerRelatedProviders();

        return view('relationships.index', [
            'isProvider' => $isProvider,
            'title' => 'Relaciones',
            'heading' => 'Relaciones',
            'intro' => $isProvider
                ? 'Empresas operadoras vinculadas a tu cuenta de prestador.'
                : 'Prestadores vinculados a tu cuenta mayorista.',
            'relatedLabel' => $isProvider ? 'Operador' : 'Prestador',
            'rows' => $rows,
        ]);
    }

    /**
     * @return array<int, array{company:string, services_count:int, booked_count:int, to_settle_count:int}>
     */
    private function providerRelatedOperators(): array
    {
        return [
            ['company' => 'Andes Travel Hub', 'services_count' => 12, 'booked_count' => 31, 'to_settle_count' => 7],
            ['company' => 'Patagonia Incoming', 'services_count' => 8, 'booked_count' => 19, 'to_settle_count' => 4],
            ['company' => 'Cordillera Tours', 'services_count' => 5, 'booked_count' => 11, 'to_settle_count' => 2],
        ];
    }

    /**
     * @return array<int, array{company:string, services_count:int, booked_count:int, to_settle_count:int}>
     */
    private function wholesalerRelatedProviders(): array
    {
        return [
            ['company' => 'Hostería del Lago', 'services_count' => 14, 'booked_count' => 37, 'to_settle_count' => 9],
            ['company' => 'Nieve y Montaña', 'services_count' => 9, 'booked_count' => 23, 'to_settle_count' => 5],
            ['company' => 'Sabores Patagónicos', 'services_count' => 6, 'booked_count' => 16, 'to_settle_count' => 3],
        ];
    }
}
