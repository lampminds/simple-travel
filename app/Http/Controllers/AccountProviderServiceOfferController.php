<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\Service;
use App\Models\ServiceOffer;
use App\Models\ServiceVariant;
use App\Services\OperatorVariantPriceResolver;
use App\Services\ServiceOfferSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
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

        $variantOffers = ServiceOffer::query()
            ->where('provider_id', $account->id)
            ->where('operator_id', $operator->id)
            ->whereNotNull('service_variant_id')
            ->get()
            ->keyBy(fn (ServiceOffer $o) => (int) $o->service_variant_id);

        $serviceOffers = ServiceOffer::query()
            ->where('provider_id', $account->id)
            ->where('operator_id', $operator->id)
            ->whereNull('service_variant_id')
            ->whereNotNull('service_id')
            ->get()
            ->keyBy(fn (ServiceOffer $o) => (int) $o->service_id);

        $serviceStatusOptions = $this->eligibleServiceStatusesForOperatorOffers($account);
        $serviceStatusFilter = $this->resolveServiceStatusFilterForList(
            (string) $request->query('service_status', ''),
            $serviceStatusOptions,
        );

        $services = Service::query()
            ->where('account_id', $account->id)
            ->whereNotIn('status', Service::catalogStatusesOmittedFromOperatorOffers())
            ->when($serviceStatusFilter !== '', fn ($q) => $q->where('status', $serviceStatusFilter))
            ->with([
                'translations',
                'serviceVariants' => fn ($q) => $q
                    ->whereNotIn('status', ServiceVariant::catalogStatusesOmittedFromOperatorOffers())
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->with([
                        'translations.language.locale',
                        'currency.lmpCurrency',
                    ]),
            ])
            ->orderBy('id')
            ->get();

        foreach ($services as $service) {
            $serviceOffer = $serviceOffers->get((int) $service->id);
            $service->setAttribute('offer_status', $serviceOffer?->status ?? 'none');
            $service->setAttribute('has_variants', $service->serviceVariants->isNotEmpty());

            if ($service->serviceVariants->isEmpty()) {
                $service->setAttribute(
                    'operator_price',
                    $priceResolver->resolveForService($service, (int) $account->id, (int) $operator->id),
                );
            }

            foreach ($service->serviceVariants as $variant) {
                $offer = $variantOffers->get((int) $variant->id);
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
            'serviceStatusOptions' => $serviceStatusOptions,
            'serviceStatusFilter' => $serviceStatusFilter,
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

        $allowedStatusesCollection = $this->eligibleServiceStatusesForOperatorOffers($account);
        $allowedServiceStatuses = $allowedStatusesCollection->all();

        $serviceStatusRules = ['nullable', 'string'];
        if ($allowedServiceStatuses !== []) {
            $serviceStatusRules[] = Rule::in($allowedServiceStatuses);
        }

        $omittedServiceStatuses = Service::catalogStatusesOmittedFromOperatorOffers();

        $validated = $request->validate([
            'propose' => ['nullable', 'array'],
            'propose.*' => [
                'integer',
                'distinct',
                Rule::exists('service_variants', 'id')->where(function ($query) use ($account, $omittedServiceStatuses): void {
                    $query->where('status', 'active')
                        ->whereIn(
                            'service_id',
                            Service::query()
                                ->where('account_id', $account->id)
                                ->where('status', 'active')
                                ->whereNotIn('status', $omittedServiceStatuses)
                                ->select('id')
                        );
                }),
            ],
            'propose_services' => ['nullable', 'array'],
            'propose_services.*' => [
                'integer',
                'distinct',
                Rule::exists('services', 'id')->where(function ($query) use ($account, $omittedServiceStatuses): void {
                    $query->where('account_id', $account->id)
                        ->where('status', 'active')
                        ->whereNotIn('status', $omittedServiceStatuses);
                }),
            ],
            'service_status' => $serviceStatusRules,
        ]);

        $user = $request->user();
        abort_unless($user !== null, 401);

        $syncService->syncProposals(
            (int) $account->id,
            (int) $operator->id,
            $validated['propose'] ?? [],
            $validated['propose_services'] ?? [],
            $user,
        );

        $filterForRedirect = $this->resolveServiceStatusFilterForList(
            (string) ($validated['service_status'] ?? ''),
            $allowedStatusesCollection,
        );
        $query = $filterForRedirect !== '' ? ['service_status' => $filterForRedirect] : [];

        return redirect()
            ->route('account.service-offers.operators.edit', array_merge(['operator' => $operator], $query))
            ->with('status', __('account.service_offers.provider_status_saved'));
    }

    /**
     * Distinct service.status values in the operator-offers picker (services with or without variants).
     *
     * @return Collection<int, string>
     */
    private function eligibleServiceStatusesForOperatorOffers(Account $account): Collection
    {
        $omittedServiceStatuses = Service::catalogStatusesOmittedFromOperatorOffers();
        $omittedVariantStatuses = ServiceVariant::catalogStatusesOmittedFromOperatorOffers();

        return Service::query()
            ->where('account_id', $account->id)
            ->whereNotIn('status', $omittedServiceStatuses)
            ->where(function ($q) use ($omittedVariantStatuses): void {
                $q->whereDoesntHave('serviceVariants')
                    ->orWhereHas(
                        'serviceVariants',
                        fn ($vq) => $vq->whereNotIn('status', $omittedVariantStatuses)
                    );
            })
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->values();
    }

    private function resolveServiceStatusFilterForList(string $raw, Collection $allowedStatuses): string
    {
        $raw = trim($raw);
        if ($raw === '' || ! $allowedStatuses->contains($raw)) {
            return '';
        }

        return $raw;
    }

    private function resolveProviderAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $typeCodes = $account->accountTypes()
            ->where('active', true)
            ->pluck('code');
        abort_unless($typeCodes->contains('provider'), 403);

        return $account;
    }
}
