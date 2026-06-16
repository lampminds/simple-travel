<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\OperatorPackageItem;
use App\Models\ServiceOffer;
use App\Services\OperatorPreviewLocalePriceService;
use App\Services\OperatorVariantPriceResolver;
use App\Services\ServiceOfferPreviewPdfGenerator;
use App\Services\ServiceOfferSyncService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class AccountOperatorServiceOfferController extends Controller
{
    public const OFFER_FILTER_PENDING = 'pending';

    public const OFFER_FILTER_ACCEPTED = 'accepted';

    public const OFFER_FILTER_ALL = 'all';

    public function index(Request $request): View
    {
        $account = $this->resolveOperatorAccount($request);
        $priceResolver = app(OperatorVariantPriceResolver::class);
        $usdHintService = app(OperatorPreviewLocalePriceService::class);
        $statusFilter = $this->resolveOfferStatusFilter($request);

        $offers = $this->offersQueryForOperator((int) $account->id, $statusFilter)
            ->with([
                'providerAccount',
                'serviceVariant.service.translations',
                'serviceVariant.translations.language.locale',
                'serviceVariant.currency.lmpCurrency',
            ])
            ->orderByDesc('offered_at')
            ->orderByDesc('id')
            ->get();

        $operatorId = (int) $account->id;
        $packageCountsByOfferId = $this->packageCountsByOfferIds(
            $offers->pluck('id')->map(fn ($id): int => (int) $id)->all(),
        );

        foreach ($offers as $offer) {
            $operatorPrice = $this->resolveOperatorPriceForOffer($offer, $operatorId, $priceResolver);
            $operatorPrice['usd_hint'] = $usdHintService->buildUsd($operatorPrice, $operatorId) ?? '';

            $offer->setAttribute('operator_price', $operatorPrice);
            $offer->setAttribute('operator_service_label', $this->serviceLabelForOffer($offer));
            $offer->setAttribute('packages_count', $packageCountsByOfferId[(int) $offer->id] ?? 0);
        }

        return view('account.service-offers.operator.index', [
            'account' => $account,
            'offers' => $offers,
            'statusFilter' => $statusFilter,
            'statusFilterOptions' => $this->offerStatusFilterOptions(),
        ]);
    }

    public function previewPdf(Request $request, ServiceOffer $offer, ServiceOfferPreviewPdfGenerator $pdfGenerator): Response
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertOfferPreviewForOperator($offer, (int) $account->id);

        return $pdfGenerator->downloadResponse($offer, $request->boolean('photos', true));
    }

    /**
     * @return array{amount: float|null, has_amount: bool, formatted: string, breakdown_html: string}
     */
    private function resolveOperatorPriceForOffer(
        ServiceOffer $offer,
        int $operatorAccountId,
        OperatorVariantPriceResolver $priceResolver,
    ): array {
        $providerId = (int) $offer->provider_id;
        $empty = [
            'amount' => null,
            'has_amount' => false,
            'formatted' => '—',
            'breakdown_html' => '<div class="price-breakdown-popover text-start small"><div>—</div></div>',
        ];

        $variant = $offer->serviceVariant;
        if ($variant === null) {
            return $empty;
        }

        return $priceResolver->resolve($variant, $providerId, $operatorAccountId);
    }

    public function accept(Request $request, ServiceOffer $offer, ServiceOfferSyncService $serviceOfferSync): RedirectResponse
    {
        $account = $this->resolveOperatorAccount($request);
        $this->assertOfferForOperator($offer, $account->id);

        $user = $request->user();
        abort_unless($user !== null, 401);

        $offer->update([
            'status' => ServiceOffer::STATUS_ACCEPTED,
            'availability' => ServiceOffer::AVAILABILITY_ACTIVE,
        ]);

        $serviceOfferSync->notifyProviderOfAcceptedOffer($offer, $user);

        return redirect()
            ->route('account.service-offers.index', $this->operatorIndexRouteParams($request))
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
            ->route('account.service-offers.index', $this->operatorIndexRouteParams($request))
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
        abort_unless($offer->service_variant_id !== null, 404);
    }

    private function assertOfferPreviewForOperator(ServiceOffer $offer, int $operatorAccountId): void
    {
        abort_unless((int) $offer->operator_id === (int) $operatorAccountId, 404);
        abort_unless($offer->service_variant_id !== null, 404);
        abort_unless(in_array($offer->status, [
            ServiceOffer::STATUS_PENDING,
            ServiceOffer::STATUS_ACCEPTED,
        ], true), 404);
    }

    private function assertAcceptedOfferForOperator(ServiceOffer $offer, int $operatorAccountId): void
    {
        abort_unless((int) $offer->operator_id === (int) $operatorAccountId, 404);
        abort_unless($offer->status === ServiceOffer::STATUS_ACCEPTED, 404);
        abort_unless($offer->service_variant_id !== null, 404);
    }

    private function resolveOfferStatusFilter(Request $request): string
    {
        $raw = trim((string) $request->query('status', self::OFFER_FILTER_ALL));

        return in_array($raw, [
            self::OFFER_FILTER_PENDING,
            self::OFFER_FILTER_ACCEPTED,
            self::OFFER_FILTER_ALL,
        ], true) ? $raw : self::OFFER_FILTER_ALL;
    }

    /**
     * @return array<string, string>
     */
    private function offerStatusFilterOptions(): array
    {
        return [
            self::OFFER_FILTER_ALL => __('account.service_offers.operator_index_filter_all'),
            self::OFFER_FILTER_PENDING => __('account.service_offers.operator_index_filter_pending'),
            self::OFFER_FILTER_ACCEPTED => __('account.service_offers.operator_index_filter_accepted'),
        ];
    }

    private function offersQueryForOperator(int $operatorAccountId, string $statusFilter): Builder
    {
        $query = ServiceOffer::query()
            ->where('operator_id', $operatorAccountId)
            ->whereHas('serviceVariant');

        if ($statusFilter === self::OFFER_FILTER_PENDING) {
            $query->where('status', ServiceOffer::STATUS_PENDING);
        } elseif ($statusFilter === self::OFFER_FILTER_ACCEPTED) {
            $query->where('status', ServiceOffer::STATUS_ACCEPTED);
        } else {
            $query->whereIn('status', [
                ServiceOffer::STATUS_PENDING,
                ServiceOffer::STATUS_ACCEPTED,
            ]);
        }

        return $query;
    }

    /**
     * @param  list<int>  $offerIds
     * @return array<int, int>
     */
    private function packageCountsByOfferIds(array $offerIds): array
    {
        if ($offerIds === []) {
            return [];
        }

        return OperatorPackageItem::query()
            ->whereIn('service_offer_id', $offerIds)
            ->selectRaw('service_offer_id, COUNT(DISTINCT operator_service_catalog_id) as packages_count')
            ->groupBy('service_offer_id')
            ->pluck('packages_count', 'service_offer_id')
            ->map(fn ($count): int => (int) $count)
            ->all();
    }

    private function serviceLabelForOffer(ServiceOffer $offer): string
    {
        $variant = $offer->serviceVariant;
        $service = $variant?->service;
        if ($variant === null || $service === null) {
            return '—';
        }

        $serviceName = trim((string) ($service->name ?? ''));
        if ($serviceName === '') {
            $serviceName = 'Service #'.$service->id;
        }

        $detail = trim((string) ($variant->name ?? ''));
        if ($detail === '') {
            $detail = trim((string) ($variant->sku ?? ''));
        }
        if ($detail === '') {
            $detail = 'Variant #'.$variant->id;
        }

        if (strcasecmp($detail, $serviceName) === 0) {
            return $serviceName;
        }

        return $serviceName.' — '.$detail;
    }

    /**
     * @return array<string, string>
     */
    private function operatorIndexRouteParams(Request $request): array
    {
        $params = ['as' => 'operator'];
        $raw = trim((string) ($request->query('status', $request->input('status', self::OFFER_FILTER_ALL))));
        $statusFilter = in_array($raw, [
            self::OFFER_FILTER_PENDING,
            self::OFFER_FILTER_ACCEPTED,
            self::OFFER_FILTER_ALL,
        ], true) ? $raw : self::OFFER_FILTER_ALL;

        if ($statusFilter !== self::OFFER_FILTER_ALL) {
            $params['status'] = $statusFilter;
        }

        return $params;
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
