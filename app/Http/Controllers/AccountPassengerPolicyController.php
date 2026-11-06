<?php

namespace App\Http\Controllers;

use App\Enums\PriceAdjustmentType;
use App\Models\Account;
use App\Models\PassengerCategory;
use App\Models\PriceList;
use App\Models\ProviderPassengerPolicy;
use App\Models\Service;
use App\Models\ServiceVariant;
use App\Services\PassengerPolicyWriter;
use App\Services\PriceFormatService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountPassengerPolicyController extends Controller
{
    public function __construct(
        private readonly PassengerPolicyWriter $writer,
    ) {
    }

    public function index(Request $request): View
    {
        $account = $this->account($request);

        return view('account.passenger-policies.index', [
            'account' => $account,
            'policies' => $account->providerPassengerPolicies()->withCount(['rules', 'services'])->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $account = $this->account($request);

        return view('account.passenger-policies.form', [
            'account' => $account,
            'policy' => null,
            'categories' => $this->categories(),
            'rules' => $this->rulesForForm(null, $request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $account = $this->account($request);
        $policy = $this->writer->syncPolicy(null, $account->id, (string) $request->input('name', ''), $request->input('rules', []));

        return redirect()
            ->route('account.passenger-policies.edit', $policy)
            ->with('status', __('account.passenger_policies.status_saved'));
    }

    public function edit(Request $request, ProviderPassengerPolicy $policy): View
    {
        $account = $this->account($request);
        $this->assertPolicy($policy, $account->id);
        $policy->load('rules');

        return view('account.passenger-policies.form', [
            'account' => $account,
            'policy' => $policy,
            'categories' => $this->categories(),
            'rules' => $this->rulesForForm($policy->rules, $request),
        ]);
    }

    public function update(Request $request, ProviderPassengerPolicy $policy): RedirectResponse
    {
        $account = $this->account($request);
        $this->assertPolicy($policy, $account->id);
        $this->writer->syncPolicy($policy, $account->id, (string) $request->input('name', ''), $request->input('rules', []));

        return redirect()
            ->route('account.passenger-policies.edit', $policy)
            ->with('status', __('account.passenger_policies.status_saved'));
    }

    public function destroy(Request $request, ProviderPassengerPolicy $policy): RedirectResponse
    {
        $account = $this->account($request);
        $this->assertPolicy($policy, $account->id);
        $policy->delete();

        return redirect()
            ->route('account.passenger-policies.index')
            ->with('status', __('account.passenger_policies.status_removed'));
    }

    public function editService(Request $request, Service $service): View
    {
        $account = $this->account($request);
        $this->assertService($service, $account->id);

        $service->load([
            'passengerPolicy.rules.category.translations.language.locale',
            'serviceVariants.passengerPriceRules.policyRule',
            'serviceVariants.paxBands.prices.policyRule',
            'serviceVariants.currency.lmpCurrency',
            'translations.language.locale',
        ]);

        return view('account.passenger-policies.service', [
            'account' => $account,
            'service' => $service,
            'adjustmentOptions' => PriceAdjustmentType::options(),
            'variantPrices' => $this->variantPricesForForm($service, $request),
            'paxBands' => $this->paxBandsForForm($service, $request),
            'variantMeta' => $this->variantMeta($service, $account),
        ]);
    }

    public function updateService(Request $request, Service $service): RedirectResponse
    {
        $account = $this->account($request);
        $this->assertService($service, $account->id);
        $this->writer->syncPaxBands($service, is_array($request->input('pax_bands')) ? $request->input('pax_bands') : []);
        $this->writer->syncVariantPrices($service, $this->variantPriceRows($request), true);

        return redirect()
            ->route('account.services.passenger-policy.edit', $service)
            ->with('status', __('account.passenger_policies.status_saved'));
    }

    public function destroyService(Request $request, Service $service): RedirectResponse
    {
        $account = $this->account($request);
        $this->assertService($service, $account->id);
        $this->writer->assignServicePolicy($service, null);

        return redirect()
            ->route('account.services.passenger-policy.edit', $service)
            ->with('status', __('account.passenger_policies.status_removed'));
    }

    public function editPriceList(Request $request, PriceList $priceList): View
    {
        $account = $this->account($request);
        abort_unless((int) $priceList->provider_id === $account->id, 404);

        $priceList->load([
            'items.serviceVariant.service.passengerPolicy.rules.category.translations.language.locale',
            'items.serviceVariant.translations.language.locale',
            'items.serviceVariant.paxBands',
            'items.passengerPriceRules.policyRule',
        ]);

        return view('account.passenger-policies.price-list', [
            'account' => $account,
            'priceList' => $priceList,
            'adjustmentOptions' => PriceAdjustmentType::options(),
        ]);
    }

    public function updatePriceList(Request $request, PriceList $priceList): RedirectResponse
    {
        $account = $this->account($request);
        abort_unless((int) $priceList->provider_id === $account->id, 404);

        $this->writer->syncPriceList($priceList, $this->listPriceRows($request));

        return redirect()
            ->route('account.provider-price-lists.passenger-fares.edit', $priceList)
            ->with('status', __('account.passenger_policies.status_saved'));
    }

    private function account(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        return $account;
    }

    private function assertService(Service $service, int $accountId): void
    {
        abort_unless((int) $service->account_id === $accountId, 404);
    }

    private function assertPolicy(ProviderPassengerPolicy $policy, int $accountId): void
    {
        abort_unless((int) $policy->provider_id === $accountId, 404);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, PassengerCategory>
     */
    private function categories()
    {
        return PassengerCategory::query()
            ->ordered()
            ->with('translations.language.locale')
            ->get();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, mixed>|null  $stored
     * @return array<int, array{included: bool, age_min: int|string|null, age_max: int|string|null}>
     */
    private function rulesForForm($stored, Request $request): array
    {
        $old = $request->old('rules');
        if (is_array($old)) {
            return $old;
        }

        $rules = [];
        foreach ($this->categories() as $category) {
            $band = $stored?->firstWhere('passenger_category_id', $category->id);
            $rules[$category->id] = [
                'included' => $band !== null,
                'age_min' => $band->age_min ?? 0,
                'age_max' => $band->age_max ?? '',
            ];
        }

        return $rules;
    }

    /**
     * @return array<int, array<int, array{adjustment_type: string, adjustment_value: mixed}>>
     */
    private function variantPricesForForm(Service $service, Request $request): array
    {
        $old = $request->old('variant_prices');
        if (is_array($old)) {
            return $old;
        }

        $prices = [];
        foreach ($service->serviceVariants as $variant) {
            foreach ($variant->passengerPriceRules as $price) {
                $categoryId = $price->policyRule?->passenger_category_id;
                if ($categoryId === null) {
                    continue;
                }

                $prices[$variant->id][$categoryId] = [
                    'adjustment_type' => $price->adjustment_type instanceof PriceAdjustmentType
                        ? $price->adjustment_type->value
                        : (string) $price->adjustment_type,
                    'adjustment_value' => $price->adjustment_value,
                ];
            }
        }

        return $prices;
    }

    /**
     * @return array<int, array{base_price: string, currency_code: string, capacity: string|null}>
     */
    private function variantMeta(Service $service, Account $account): array
    {
        $formatter = app(PriceFormatService::class);
        $meta = [];

        foreach ($service->serviceVariants as $variant) {
            $code = strtoupper(trim((string) ($variant->currency?->currency_code ?? '')));
            $amount = $variant->base_price !== null && $variant->base_price !== ''
                ? $formatter->format($variant->base_price, $account->id)
                : '—';

            $meta[$variant->id] = [
                'base_price' => trim($code.' '.$amount),
                'currency_code' => $code,
                'capacity' => $this->capacityReference($variant),
            ];
        }

        return $meta;
    }

    /**
     * Capacity is shown as a reference. It does not limit the party-size prices.
     */
    private function capacityReference(ServiceVariant $variant): ?string
    {
        $min = $variant->capacity_min;
        $max = $variant->capacity_max;

        if ($min === null && $max === null) {
            return null;
        }

        if ($min !== null && $max !== null) {
            $range = $min.'–'.$max;
        } elseif ($min !== null) {
            $range = (string) __('account.passenger_policies.capacity_from', ['min' => $min]);
        } else {
            $range = (string) __('account.passenger_policies.capacity_to', ['max' => $max]);
        }

        return (string) __('account.passenger_policies.capacity_reference', ['range' => $range]);
    }

    /**
     * @return array<int, list<array{min_pax: mixed, max_pax: mixed, price: mixed, categories: array<int|string, mixed>}>>
     */
    private function paxBandsForForm(Service $service, Request $request): array
    {
        $old = $request->old('pax_bands');
        if (is_array($old)) {
            $bands = [];
            foreach ($old as $variantId => $payload) {
                $rows = is_array($payload) ? ($payload['rows'] ?? []) : [];
                $bands[(int) $variantId] = is_array($rows) ? $rows : [];
            }

            return $bands;
        }

        $bands = [];
        $policyId = $service->provider_passenger_policy_id !== null
            ? (int) $service->provider_passenger_policy_id
            : null;

        foreach ($service->serviceVariants as $variant) {
            $bands[$variant->id] = [];
            foreach ($variant->paxBands as $band) {
                $categories = [];
                foreach ($band->prices as $cell) {
                    $rule = $cell->policyRule;
                    if ($rule === null || ($policyId !== null && (int) $rule->provider_passenger_policy_id !== $policyId)) {
                        continue;
                    }

                    $categories[(int) $rule->passenger_category_id] = $cell->price;
                }

                $bands[$variant->id][] = [
                    'min_pax' => $band->min_pax,
                    'max_pax' => $band->max_pax ?? '',
                    'price' => $band->price,
                    'categories' => $categories,
                ];
            }
        }

        return $bands;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function variantPriceRows(Request $request): array
    {
        $rows = [];
        $input = $request->input('variant_prices', []);
        if (! is_array($input)) {
            return [];
        }

        foreach ($input as $variantId => $categories) {
            if (! is_array($categories)) {
                continue;
            }

            foreach ($categories as $categoryId => $row) {
                if (! is_array($row)) {
                    continue;
                }

                $rows[] = [
                    'service_variant_id' => (int) $variantId,
                    'passenger_category_id' => (int) $categoryId,
                    'adjustment_type' => (string) ($row['adjustment_type'] ?? ''),
                    'adjustment_value' => $row['adjustment_value'] ?? null,
                    'error_key' => 'variant_prices.'.$variantId.'.'.$categoryId,
                ];
            }
        }

        return $rows;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function listPriceRows(Request $request): array
    {
        $rows = [];
        $input = $request->input('items', []);
        if (! is_array($input)) {
            return [];
        }

        foreach ($input as $itemId => $item) {
            $categories = is_array($item) ? ($item['categories'] ?? []) : [];
            if (! is_array($categories)) {
                continue;
            }

            foreach ($categories as $categoryId => $row) {
                if (! is_array($row)) {
                    continue;
                }

                $rows[] = [
                    'provider_price_list_item_id' => (int) $itemId,
                    'passenger_category_id' => (int) $categoryId,
                    'adjustment_type' => (string) ($row['adjustment_type'] ?? ''),
                    'adjustment_value' => $row['adjustment_value'] ?? null,
                    'error_key' => 'items.'.$itemId.'.categories.'.$categoryId,
                ];
            }
        }

        return $rows;
    }
}
