<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountExchangeRateService;
use App\Support\CurrentAccountSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountExchangeRateController extends Controller
{
    public function __construct(private readonly AccountExchangeRateService $exchangeRates)
    {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $account = $this->resolveAccountForView($request);
        $rateDay = $this->exchangeRates->parseRateDay($request->query('date'));
        $canEdit = $this->userCanEdit($request);

        return view('account.exchange-rates.index', [
            'account' => $account,
            'rateDay' => $rateDay,
            'rateDayInput' => $rateDay->toDateString(),
            'canEdit' => $canEdit,
            'systemRows' => $this->exchangeRates->systemRowsForDay($rateDay),
            'tenantRows' => $this->exchangeRates->tenantRowsForDay((int) $account->id, $rateDay),
            'tenantHasRates' => $this->exchangeRates->tenantHasRatesForDay((int) $account->id, $rateDay),
            'historyDays' => $this->exchangeRates->tenantRateDaySummaries((int) $account->id),
        ]);
    }

    public function edit(Request $request): View|RedirectResponse
    {
        $this->assertOwner($request);
        $account = $this->resolveAccountForView($request);
        $rateDay = $this->exchangeRates->parseRateDay($request->query('date'));
        $fromSystem = $request->boolean('from_system');

        return view('account.exchange-rates.edit', [
            'account' => $account,
            'rateDay' => $rateDay,
            'rateDayInput' => $rateDay->toDateString(),
            'fromSystem' => $fromSystem,
            'formRows' => $this->exchangeRates->formRowsForEdit((int) $account->id, $rateDay, $fromSystem),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->assertOwner($request);
        $account = $this->resolveAccountForView($request);
        $rateDay = $this->exchangeRates->parseRateDay($request->input('rate_date'));

        $validated = $request->validate([
            'rate_date' => ['required', 'date'],
            'rates' => ['required', 'array', 'min:1'],
            'rates.*.currency_id' => ['required', 'integer', 'exists:cat_currencies,id'],
            'rates.*.units_per_usd_buy' => ['required', 'numeric', 'gt:0'],
            'rates.*.units_per_usd_sell' => ['required', 'numeric', 'gt:0'],
            'rates.*.is_active' => ['nullable', 'boolean'],
        ]);

        $normalized = [];
        foreach ($validated['rates'] as $row) {
            $currencyId = (int) $row['currency_id'];
            if (\App\Models\Currency::isUsdProjectCurrency($currencyId)) {
                $normalized[] = [
                    'currency_id' => $currencyId,
                    'units_per_usd_buy' => 1,
                    'units_per_usd_sell' => 1,
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ];

                continue;
            }

            $normalized[] = [
                'currency_id' => $currencyId,
                'units_per_usd_buy' => (float) $row['units_per_usd_buy'],
                'units_per_usd_sell' => (float) $row['units_per_usd_sell'],
                'is_active' => (bool) ($row['is_active'] ?? true),
            ];
        }

        $this->exchangeRates->saveTenantRatesForDay((int) $account->id, $rateDay, $normalized);

        return redirect()
            ->route('account.exchange-rates.index', ['date' => $rateDay->toDateString()])
            ->with('status', __('exchange_rates.saved'));
    }

    private function resolveAccountForView(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $accountId = CurrentAccountSession::accountId($request);
        abort_unless($accountId !== null, 404);

        $account = $user->accounts()->whereKey($accountId)->first();
        abort_unless($account instanceof Account, 404);

        return $account;
    }

    private function userCanEdit(Request $request): bool
    {
        return (bool) $request->user()?->hasRoleForCurrentAccount('owner');
    }

    private function assertOwner(Request $request): void
    {
        abort_unless($this->userCanEdit($request), 403);
    }
}
