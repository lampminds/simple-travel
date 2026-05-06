<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\Service;
use App\Models\ServiceOffer;
use App\Services\OperatorVariantPriceResolver;
use App\Services\ServiceOfferSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountProviderServiceOfferController extends Controller
{
    public function operatorsIndex(Request $request): View
    {
        $account = $this->resolveProviderAccount($request);

        $relationships = AccountRelationship::query()
            ->where('provider_account_id', $account->id)
            ->where('status', AccountRelationship::STATUS_APPROVED)
            ->with('operatorAccount')
            ->orderBy('id')
            ->get();

        return view('account.service-offers.provider.operators', [
            'account' => $account,
            'relationships' => $relationships,
        ]);
    }

    public function edit(Request $request, Account $operator, OperatorVariantPriceResolver $priceResolver): View
    {
        $account = $this->resolveProviderAccount($request);
        abort_unless(
            AccountRelationship::query()
                ->where('provider_account_id', $account->id)
                ->where('operator_account_id', $operator->id)
                ->where('status', AccountRelationship::STATUS_APPROVED)
                ->exists(),
            404
        );

        $offers = ServiceOffer::query()
            ->where('provider_id', $account->id)
            ->where('operator_id', $operator->id)
            ->whereNotNull('service_variant_id')
            ->get()
            ->keyBy(fn (ServiceOffer $o) => (int) $o->service_variant_id);

        $services = Service::query()
            ->where('account_id', $account->id)
            ->with([
                'translations',
                'serviceVariants' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')->with([
                    'translations.language.locale',
                    'currency.lmpCurrency',
                ]),
            ])
            ->orderBy('id')
            ->get();

        foreach ($services as $service) {
            foreach ($service->serviceVariants as $variant) {
                $offer = $offers->get((int) $variant->id);
                $variant->setAttribute('offer_status', $offer?->status ?? 'none');
                $variant->setAttribute(
                    'operator_price',
                    $priceResolver->resolve($variant, (int) $account->id, (int) $operator->id),
                );
            }
        }

        return view('account.service-offers.provider.edit', [
            'account' => $account,
            'operator' => $operator,
            'services' => $services,
        ]);
    }

    public function update(Request $request, Account $operator, ServiceOfferSyncService $syncService): RedirectResponse
    {
        $account = $this->resolveProviderAccount($request);
        abort_unless(
            AccountRelationship::query()
                ->where('provider_account_id', $account->id)
                ->where('operator_account_id', $operator->id)
                ->where('status', AccountRelationship::STATUS_APPROVED)
                ->exists(),
            404
        );

        $validated = $request->validate([
            'propose' => ['nullable', 'array'],
            'propose.*' => ['integer', 'distinct', 'exists:service_variants,id'],
        ]);

        $user = $request->user();
        abort_unless($user !== null, 401);

        $syncService->syncVariantProposals(
            (int) $account->id,
            (int) $operator->id,
            $validated['propose'] ?? [],
            $user,
        );

        return redirect()
            ->route('account.service-offers.operators.edit', $operator)
            ->with('status', __('account.service_offers.provider_status_saved'));
    }

    private function resolveProviderAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $typeCodes = $account->categories()
            ->where('group', 'type')
            ->where('active', true)
            ->pluck('code');
        abort_unless($typeCodes->contains('provider'), 403);

        return $account;
    }
}
