<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProviderDashboardController extends Controller
{
    /**
     * Provider dashboard: list account services and entry points to create new ones.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $account = $request->user()?->currentAccount();

        $typeCodes = collect();
        if ($account) {
            $typeCodes = $account->categories()
                ->where('group', 'type')
                ->where('active', true)
                ->pluck('code');
        }

        if (! $typeCodes->contains('provider')) {
            return redirect()->to('/account/dashboard');
        }

        $services = collect();
        if ($account) {
            $services = Service::query()
                ->where('account_id', $account->id)
                ->with(['serviceType.translations.language.locale', 'translations.language.locale', 'media'])
                ->orderByDesc('id')
                ->get();
        }

        $serviceTypes = ServiceType::query()
            ->where('active', true)
            ->ordered()
            ->with('translations.language.locale')
            ->get();

        return view('provider.dashboard', [
            'services' => $services,
            'serviceTypes' => $serviceTypes,
        ]);
    }
}
