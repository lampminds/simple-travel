<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountRelationship;
use App\Models\Service;
use App\Services\ProviderServiceStatusReportBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class AccountProviderServiceStatusReportController extends Controller
{
    public function show(
        Request $request,
        Service $service,
        ProviderServiceStatusReportBuilder $report,
    ): View {
        $account = $this->resolveProviderAccount($request);
        abort_unless((int) $service->account_id === (int) $account->id, 404);

        return view('account.service-status-report.show', [
            'account' => $account,
            'service' => $service,
            'report' => $report->build($service),
            'backUrl' => $this->resolveBackUrl($request, $account),
        ]);
    }

    private function resolveBackUrl(Request $request, Account $provider): string
    {
        if (trim((string) $request->query('from', '')) === 'catalog') {
            $query = [];
            $type = trim((string) $request->query('type', ''));
            $status = trim((string) $request->query('status', ''));
            if ($type !== '' && strcasecmp($type, 'all') !== 0) {
                $query['type'] = $type;
            }
            if ($status !== '' && strcasecmp($status, 'all') !== 0) {
                $query['status'] = $status;
            }

            return route('catalog', $query);
        }

        $operator = $this->resolveReturnOperator($request, $provider);

        return $operator instanceof Account
            ? route('account.service-offers.operators.edit', $operator)
            : route('account.service-offers.index', ['as' => 'provider']);
    }

    private function resolveReturnOperator(Request $request, Account $provider): ?Account
    {
        $raw = trim((string) $request->query('operator', ''));
        if ($raw === '' || ! Str::isUuid($raw)) {
            return null;
        }

        $operator = Account::query()->where('uuid', $raw)->first();
        if ($operator === null) {
            return null;
        }

        $approved = AccountRelationship::query()
            ->where('provider_account_id', $provider->id)
            ->where('operator_account_id', $operator->id)
            ->where('status', AccountRelationship::STATUS_APPROVED)
            ->exists();

        return $approved ? $operator : null;
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
