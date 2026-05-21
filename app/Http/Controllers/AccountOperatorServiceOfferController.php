<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ServiceOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class AccountOperatorServiceOfferController extends Controller
{
    public function index(Request $request): View
    {
        $account = $this->resolveOperatorAccount($request);

        $offers = ServiceOffer::query()
            ->where('operator_id', $account->id)
            ->where('status', ServiceOffer::STATUS_PENDING)
            ->where(function ($q): void {
                $q->whereNotNull('service_variant_id')
                    ->orWhere(function ($q2): void {
                        $q2->whereNotNull('service_id')->whereNull('service_variant_id');
                    });
            })
            ->with([
                'providerAccount',
                'service.translations',
                'serviceVariant.service.translations',
                'serviceVariant.translations.language.locale',
            ])
            ->orderByDesc('offered_at')
            ->orderByDesc('id')
            ->get();

        return view('account.service-offers.operator.index', [
            'account' => $account,
            'offers' => $offers,
        ]);
    }

    public function accept(Request $request, ServiceOffer $offer): RedirectResponse
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertOfferForOperator($offer, $account->id);

        $offer->update([
            'status' => ServiceOffer::STATUS_ACCEPTED,
            'availability' => ServiceOffer::AVAILABILITY_ACTIVE,
        ]);

        return redirect()
            ->route('account.service-offers.index', ['as' => 'operator'])
            ->with('status', __('account.service_offers.operator_status_accepted'));
    }

    public function reject(Request $request, ServiceOffer $offer): RedirectResponse
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertOfferForOperator($offer, $account->id);

        $offer->update([
            'status' => ServiceOffer::STATUS_REJECTED,
        ]);

        return redirect()
            ->route('account.service-offers.index', ['as' => 'operator'])
            ->with('status', __('account.service_offers.operator_status_rejected'));
    }

    /**
     * Operator-only: change catalog availability for an already accepted offer.
     */
    public function updateLinkedAvailability(Request $request, ServiceOffer $offer): RedirectResponse
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertAcceptedOfferForOperator($offer, $account->id);

        $validated = $request->validate([
            'availability' => [
                'required',
                'string',
                Rule::in([
                    ServiceOffer::AVAILABILITY_ACTIVE,
                    ServiceOffer::AVAILABILITY_SUSPENDED,
                    ServiceOffer::AVAILABILITY_DISCONTINUED,
                ]),
            ],
        ]);

        $offer->update(['availability' => $validated['availability']]);

        return redirect()
            ->route('catalog')
            ->with('status', __('catalog.operator_linked_availability_updated'));
    }

    private function assertOfferForOperator(ServiceOffer $offer, int $operatorAccountId): void
    {
        abort_unless((int) $offer->operator_id === (int) $operatorAccountId, 404);
        abort_unless($offer->status === ServiceOffer::STATUS_PENDING, 404);
        abort_unless($offer->targetsVariant() || $offer->targetsWholeService(), 404);
    }

    private function assertAcceptedOfferForOperator(ServiceOffer $offer, int $operatorAccountId): void
    {
        abort_unless((int) $offer->operator_id === (int) $operatorAccountId, 404);
        abort_unless($offer->status === ServiceOffer::STATUS_ACCEPTED, 404);
        abort_unless($offer->targetsVariant() || $offer->targetsWholeService(), 404);
    }

    private function resolveOperatorAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $typeCodes = $account->accountTypes()
            ->where('active', true)
            ->pluck('code');
        abort_unless($typeCodes->contains('operator'), 403);

        return $account;
    }
}
