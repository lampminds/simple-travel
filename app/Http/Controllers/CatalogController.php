<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Service;
use App\Models\ServiceType;
use App\Support\AccountBusinessTypeGate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CatalogController extends Controller
{
    /**
     * Provider catalog: list and manage this account's services (wizard-backed).
     */
    public function index(Request $request): View|RedirectResponse
    {
        $account = $request->user()?->currentAccount();
        if ($account === null) {
            return redirect()->route('account.dashboard');
        }

        AccountBusinessTypeGate::assertHasActiveType($account, 'provider');

        $serviceTypes = ServiceType::query()
            ->where('active', true)
            ->ordered()
            ->with('translations.language.locale')
            ->get();

        $typeFilter = $this->resolveCatalogTypeFilter($request, $serviceTypes);
        $catalogFilterView = [
            'catalogTypeFilter' => $typeFilter,
            'catalogServiceTypeOptions' => $this->catalogServiceTypeOptions($serviceTypes),
        ];

        return $this->accountServicesCatalog($account, 'provider', $typeFilter, $catalogFilterView, $serviceTypes);
    }

    /** Service type `code` for catalog list filter; null means all types. */
    private function resolveCatalogTypeFilter(Request $request, Collection $serviceTypes): ?string
    {
        $raw = trim((string) $request->query('type', ''));
        if ($raw === '' || strcasecmp($raw, 'all') === 0) {
            return null;
        }

        $allowed = $serviceTypes->pluck('code')->map(fn ($c) => (string) $c)->all();

        return in_array($raw, $allowed, true) ? $raw : null;
    }

    /**
     * @param  Collection<int, ServiceType>  $serviceTypes
     * @return array<string, string>
     */
    private function catalogServiceTypeOptions(Collection $serviceTypes): array
    {
        $out = [];
        foreach ($serviceTypes as $type) {
            $out[(string) $type->code] = $type->dropdown_label;
        }

        return $out;
    }

    /**
     * @param  'provider'|'agency'  $mode
     * @param  array<string, mixed>  $catalogFilterView
     * @param  Collection<int, ServiceType>  $serviceTypes
     */
    private function accountServicesCatalog(Account $account, string $mode, ?string $typeFilter, array $catalogFilterView, Collection $serviceTypes): View
    {
        $typeId = null;
        if ($typeFilter !== null) {
            $typeId = $serviceTypes->firstWhere('code', $typeFilter)?->id;
        }

        $services = Service::query()
            ->where('account_id', $account->id)
            ->when($typeId !== null, fn ($query) => $query->where('service_type_id', $typeId))
            ->with(['serviceType.translations.language.locale', 'translations.language.locale', 'media'])
            ->withCount('serviceVariants')
            ->orderByDesc('id')
            ->get();

        return view('catalog.index', array_merge($catalogFilterView, [
            'mode' => $mode,
            'services' => $services,
            'serviceTypes' => $serviceTypes,
            'linkedCatalog' => null,
        ]));
    }

}
