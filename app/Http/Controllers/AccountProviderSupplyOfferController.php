<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\PriceList;
use App\Models\ProviderServiceOffer;
use App\Models\ProviderSupplyPriceListAssignment;
use App\Models\ProviderSupplyRelationship;
use App\Services\AccountRelationshipsListingService;
use App\Services\ProviderSupplyOfferSyncService;
use App\Support\AccountBusinessTypeGate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class AccountProviderSupplyOfferController extends Controller
{
    public function __construct(
        private readonly AccountRelationshipsListingService $accountLabels,
        private readonly ProviderSupplyOfferSyncService $offerSync,
    ) {
    }

    public function editOffers(Request $request, Account $buyer): View
    {
        $supplier = AccountBusinessTypeGate::assertProviderAccount($request);
        $this->assertApprovedLink((int) $buyer->id, (int) $supplier->id);

        $offers = ProviderServiceOffer::query()
            ->where('supplier_id', $supplier->id)
            ->where('buyer_id', $buyer->id)
            ->get()
            ->keyBy(fn (ProviderServiceOffer $offer) => (int) $offer->service_variant_id);

        $services = $this->offerSync->catalogForSupplier((int) $supplier->id);
        foreach ($services as $service) {
            foreach ($service->serviceVariants as $variant) {
                $offer = $offers->get((int) $variant->id);
                $variant->setAttribute('supply_offer_status', $offer?->status ?? 'none');
                $variant->setAttribute('supply_offer_id', $offer?->id);
                $variant->setAttribute('supply_offer_availability', $offer?->availability ?? ProviderServiceOffer::AVAILABILITY_ACTIVE);
            }
        }

        return view('account.provider-supply.offers', [
            'mode' => 'supplier',
            'account' => $supplier,
            'counterpart' => $buyer,
            'counterpartLabel' => $this->accountLabels->accountDisplayName($buyer),
            'services' => $services,
            'offers' => $offers,
        ]);
    }

    public function updateOffers(Request $request, Account $buyer): RedirectResponse
    {
        $supplier = AccountBusinessTypeGate::assertProviderAccount($request);
        $user = $request->user();
        abort_unless($user !== null, 401);
        $this->assertApprovedLink((int) $buyer->id, (int) $supplier->id);

        $validated = $request->validate([
            'propose' => ['nullable', 'array'],
            'propose.*' => ['integer', 'distinct'],
            'availability' => ['nullable', 'array'],
            'availability.*' => ['string', Rule::in([
                ProviderServiceOffer::AVAILABILITY_ACTIVE,
                ProviderServiceOffer::AVAILABILITY_SUSPENDED,
                ProviderServiceOffer::AVAILABILITY_DISCONTINUED,
            ])],
        ]);

        $proposed = array_map('intval', $validated['propose'] ?? []);
        $this->offerSync->syncProposals((int) $supplier->id, (int) $buyer->id, $proposed, $user);

        $availability = $validated['availability'] ?? [];
        $acceptedOffers = ProviderServiceOffer::query()
            ->where('supplier_id', $supplier->id)
            ->where('buyer_id', $buyer->id)
            ->where('status', ProviderServiceOffer::STATUS_ACCEPTED)
            ->get();

        foreach ($acceptedOffers as $offer) {
            $next = $availability[(string) $offer->service_variant_id] ?? $availability[$offer->service_variant_id] ?? null;
            if (! is_string($next) || $next === $offer->availability) {
                continue;
            }

            $this->offerSync->updateAvailability(
                (int) $supplier->id,
                (int) $buyer->id,
                $offer,
                $next,
                $user,
            );
        }

        return redirect()
            ->route('account.provider-supply.offers.edit', $buyer)
            ->with('status', __('account.provider_supply.offers_saved'));
    }

    public function reviewOffers(Request $request, Account $supplier): View
    {
        $buyer = AccountBusinessTypeGate::assertProviderAccount($request);
        $this->assertApprovedLink((int) $buyer->id, (int) $supplier->id);

        $offers = ProviderServiceOffer::query()
            ->where('buyer_id', $buyer->id)
            ->where('supplier_id', $supplier->id)
            ->with([
                'serviceVariant.translations.language.locale',
                'serviceVariant.service.translations.language.locale',
            ])
            ->orderByDesc('offered_at')
            ->orderByDesc('id')
            ->get();

        return view('account.provider-supply.review', [
            'account' => $buyer,
            'counterpart' => $supplier,
            'counterpartLabel' => $this->accountLabels->accountDisplayName($supplier),
            'offers' => $offers,
        ]);
    }

    public function acceptOffer(Request $request, Account $supplier, ProviderServiceOffer $offer): RedirectResponse
    {
        $buyer = AccountBusinessTypeGate::assertProviderAccount($request);
        $this->assertOfferForBuyer($offer, (int) $buyer->id, (int) $supplier->id);

        if ($offer->status !== ProviderServiceOffer::STATUS_PENDING) {
            return redirect()
                ->route('account.provider-supply.offers.review', $supplier)
                ->withErrors(['offer' => __('account.provider_supply.validation.offer_not_pending')]);
        }

        $offer->update([
            'status' => ProviderServiceOffer::STATUS_ACCEPTED,
            'availability' => ProviderServiceOffer::AVAILABILITY_ACTIVE,
        ]);

        return redirect()
            ->route('account.provider-supply.offers.review', $supplier)
            ->with('status', __('account.provider_supply.offer_accepted'));
    }

    public function rejectOffer(Request $request, Account $supplier, ProviderServiceOffer $offer): RedirectResponse
    {
        $buyer = AccountBusinessTypeGate::assertProviderAccount($request);
        $this->assertOfferForBuyer($offer, (int) $buyer->id, (int) $supplier->id);

        if ($offer->status !== ProviderServiceOffer::STATUS_PENDING) {
            return redirect()
                ->route('account.provider-supply.offers.review', $supplier)
                ->withErrors(['offer' => __('account.provider_supply.validation.offer_not_pending')]);
        }

        $offer->update([
            'status' => ProviderServiceOffer::STATUS_REJECTED,
        ]);

        return redirect()
            ->route('account.provider-supply.offers.review', $supplier)
            ->with('status', __('account.provider_supply.offer_rejected'));
    }

    public function editPriceList(Request $request, Account $buyer): View
    {
        $supplier = AccountBusinessTypeGate::assertProviderAccount($request);
        $this->assertApprovedLink((int) $buyer->id, (int) $supplier->id);

        $lists = PriceList::query()
            ->where('provider_id', $supplier->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $assignment = $this->currentAssignment((int) $supplier->id, (int) $buyer->id);

        return view('account.provider-supply.price-list', [
            'account' => $supplier,
            'counterpart' => $buyer,
            'counterpartLabel' => $this->accountLabels->accountDisplayName($buyer),
            'lists' => $lists,
            'assignment' => $assignment,
        ]);
    }

    public function updatePriceList(Request $request, Account $buyer): RedirectResponse
    {
        $supplier = AccountBusinessTypeGate::assertProviderAccount($request);
        $this->assertApprovedLink((int) $buyer->id, (int) $supplier->id);

        $validated = $request->validate([
            'provider_price_list_id' => [
                'nullable',
                'integer',
                Rule::exists('provider_price_lists', 'id')->where(function ($query) use ($supplier): void {
                    $query->where('provider_id', $supplier->id)->where('is_active', true);
                }),
            ],
            'valid_from' => ['nullable', 'date'],
            'valid_to' => ['nullable', 'date', 'after_or_equal:valid_from'],
        ]);

        $listIds = PriceList::query()
            ->where('provider_id', $supplier->id)
            ->pluck('id');

        ProviderSupplyPriceListAssignment::query()
            ->where('buyer_account_id', $buyer->id)
            ->whereIn('provider_price_list_id', $listIds)
            ->update(['is_active' => false]);

        $listId = (int) ($validated['provider_price_list_id'] ?? 0);
        if ($listId > 0) {
            ProviderSupplyPriceListAssignment::query()->updateOrCreate(
                [
                    'provider_price_list_id' => $listId,
                    'buyer_account_id' => (int) $buyer->id,
                ],
                [
                    'is_active' => true,
                    'adjustment_type' => 'none',
                    'adjustment_value' => null,
                    'valid_from' => $this->dateStart($validated['valid_from'] ?? null),
                    'valid_to' => $this->dateEnd($validated['valid_to'] ?? null),
                ]
            );
        }

        return redirect()
            ->route('account.provider-supply.price-list.edit', $buyer)
            ->with('status', __('account.provider_supply.price_list_saved'));
    }

    private function assertApprovedLink(int $buyerAccountId, int $supplierAccountId): void
    {
        $linked = ProviderSupplyRelationship::query()
            ->where('buyer_account_id', $buyerAccountId)
            ->where('supplier_account_id', $supplierAccountId)
            ->where('status', ProviderSupplyRelationship::STATUS_APPROVED)
            ->exists();

        abort_unless($linked, 404);
    }

    private function assertOfferForBuyer(ProviderServiceOffer $offer, int $buyerAccountId, int $supplierAccountId): void
    {
        $this->assertApprovedLink($buyerAccountId, $supplierAccountId);
        abort_unless((int) $offer->buyer_id === $buyerAccountId, 404);
        abort_unless((int) $offer->supplier_id === $supplierAccountId, 404);
    }

    private function currentAssignment(int $supplierAccountId, int $buyerAccountId): ?ProviderSupplyPriceListAssignment
    {
        return ProviderSupplyPriceListAssignment::query()
            ->where('buyer_account_id', $buyerAccountId)
            ->where('is_active', true)
            ->whereHas('priceList', fn ($query) => $query->where('provider_id', $supplierAccountId))
            ->with('priceList')
            ->orderByDesc('id')
            ->first();
    }

    private function dateStart(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return Carbon::parse($value)->startOfDay();
    }

    private function dateEnd(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return Carbon::parse($value)->endOfDay();
    }
}
