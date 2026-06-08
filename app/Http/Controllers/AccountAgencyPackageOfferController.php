<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\OperatorPriceList;
use App\Models\PackageOffer;
use App\Services\OperatorPackageAgencyPriceResolver;
use App\Services\PackageOfferPreviewPdfGenerator;
use App\Services\PackageOfferSyncService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class AccountAgencyPackageOfferController extends Controller
{
    public const OFFER_FILTER_PENDING = 'pending';

    public const OFFER_FILTER_ACCEPTED = 'accepted';

    public const OFFER_FILTER_ALL = 'all';

    public function __construct(
        private readonly OperatorPackageAgencyPriceResolver $packagePriceResolver,
    ) {
    }

    public function index(Request $request): View
    {
        $account = $this->resolveAgencyAccount($request);
        $statusFilter = $this->resolveOfferStatusFilter($request);

        $offers = $this->offersQueryForAgency((int) $account->id, $statusFilter)
            ->with([
                'operatorAccount',
                'catalog.translations.language.locale',
                'priceList.currency.lmpCurrency',
            ])
            ->orderByDesc('offered_at')
            ->orderByDesc('id')
            ->get();

        foreach ($offers as $offer) {
            $catalog = $offer->catalog;
            $priceList = $offer->priceList;
            $agencyPrice = ['has_amount' => false, 'formatted' => '—'];

            if ($catalog !== null && $priceList instanceof OperatorPriceList) {
                $agencyPrice = $this->packagePriceResolver->resolvePackageTotal(
                    $catalog,
                    $priceList,
                    (int) $account->id,
                    (int) $offer->operator_id,
                );
            }

            $offer->setAttribute('agency_price', $agencyPrice);
            $offer->setAttribute('package_label', $this->packageLabelForOffer($offer));
        }

        return view('account.package-offers.agency.index', [
            'account' => $account,
            'offers' => $offers,
            'statusFilter' => $statusFilter,
            'statusFilterOptions' => $this->offerStatusFilterOptions(),
        ]);
    }

    public function previewPdf(
        Request $request,
        PackageOffer $offer,
        PackageOfferPreviewPdfGenerator $pdfGenerator,
    ): Response {
        $account = $this->resolveAgencyAccount($request);
        abort_unless((int) $offer->agency_id === (int) $account->id, 404);
        abort_unless($offer->catalog !== null, 404);

        return $pdfGenerator->downloadResponse($offer, $request->boolean('photos', true));
    }

    public function accept(Request $request, PackageOffer $offer, PackageOfferSyncService $syncService): RedirectResponse
    {
        $account = $this->resolveAgencyAccount($request);
        $this->assertOfferForAgency($offer, (int) $account->id);

        $user = $request->user();
        abort_unless($user !== null, 401);

        $offer->update([
            'status' => PackageOffer::STATUS_ACCEPTED,
            'availability' => PackageOffer::AVAILABILITY_ACTIVE,
        ]);

        $syncService->notifyOperatorOfAcceptedOffer($offer, $user);

        return redirect()
            ->route('account.package-offers.index', $this->agencyIndexRouteParams($request))
            ->with('status', __('account.package_offers.agency_status_accepted'));
    }

    public function reject(Request $request, PackageOffer $offer): RedirectResponse
    {
        $account = $this->resolveAgencyAccount($request);
        $this->assertOfferForAgency($offer, (int) $account->id);

        $offer->update([
            'status' => PackageOffer::STATUS_REJECTED,
        ]);

        return redirect()
            ->route('account.package-offers.index', $this->agencyIndexRouteParams($request))
            ->with('status', __('account.package_offers.agency_status_rejected'));
    }

    private function assertOfferForAgency(PackageOffer $offer, int $agencyAccountId): void
    {
        abort_unless((int) $offer->agency_id === $agencyAccountId, 404);
        abort_unless($offer->status === PackageOffer::STATUS_PENDING, 404);
    }

    private function resolveOfferStatusFilter(Request $request): string
    {
        $raw = trim((string) $request->query('status', self::OFFER_FILTER_PENDING));

        return in_array($raw, [
            self::OFFER_FILTER_PENDING,
            self::OFFER_FILTER_ACCEPTED,
            self::OFFER_FILTER_ALL,
        ], true) ? $raw : self::OFFER_FILTER_PENDING;
    }

    /**
     * @return array<string, string>
     */
    private function offerStatusFilterOptions(): array
    {
        return [
            self::OFFER_FILTER_PENDING => __('account.package_offers.agency_index_filter_pending'),
            self::OFFER_FILTER_ACCEPTED => __('account.package_offers.agency_index_filter_accepted'),
            self::OFFER_FILTER_ALL => __('account.package_offers.agency_index_filter_all'),
        ];
    }

    private function offersQueryForAgency(int $agencyAccountId, string $statusFilter): Builder
    {
        $query = PackageOffer::query()
            ->where('agency_id', $agencyAccountId)
            ->whereHas('catalog');

        if ($statusFilter === self::OFFER_FILTER_PENDING) {
            $query->where('status', PackageOffer::STATUS_PENDING);
        } elseif ($statusFilter === self::OFFER_FILTER_ACCEPTED) {
            $query->where('status', PackageOffer::STATUS_ACCEPTED);
        } else {
            $query->whereIn('status', [
                PackageOffer::STATUS_PENDING,
                PackageOffer::STATUS_ACCEPTED,
            ]);
        }

        return $query;
    }

    private function packageLabelForOffer(PackageOffer $offer): string
    {
        $catalog = $offer->catalog;
        if ($catalog === null) {
            return '—';
        }

        $label = $catalog->displayLabel();

        return $label !== '' ? $label : ('Package #'.$catalog->id);
    }

    /**
     * @return array<string, string>
     */
    private function agencyIndexRouteParams(Request $request): array
    {
        $params = ['as' => 'agency'];
        $raw = trim((string) ($request->query('status', $request->input('status', self::OFFER_FILTER_PENDING))));
        $statusFilter = in_array($raw, [
            self::OFFER_FILTER_PENDING,
            self::OFFER_FILTER_ACCEPTED,
            self::OFFER_FILTER_ALL,
        ], true) ? $raw : self::OFFER_FILTER_PENDING;

        if ($statusFilter !== self::OFFER_FILTER_PENDING) {
            $params['status'] = $statusFilter;
        }

        return $params;
    }

    private function resolveAgencyAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $typeCodes = $account->accountTypes()
            ->where('active', true)
            ->pluck('code');
        abort_unless($typeCodes->contains('agency'), 403);

        return $account;
    }
}
