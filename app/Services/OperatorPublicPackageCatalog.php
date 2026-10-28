<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\LmpCity;
use App\Models\OperatorPackageItem;
use App\Models\OperatorPriceList;
use App\Models\OperatorPriceListAssignment;
use App\Models\OperatorServiceCatalog;
use App\Models\PackageOffer;
use App\Models\Service;
use App\Models\ServiceVariant;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Public packages of an operator website.
 *
 * Location is the city of each included service (services.city_id), in package order,
 * without duplicates. Optional and upgrade lines are ignored. Transfer pickup points
 * are not used: those are operational stops, not the commercial destination.
 *
 * Guests do not see a price. An authenticated visitor sees the total from the operator
 * price list assigned to their agency, including that assignment's adjustment, in the
 * list currency. Per-person lines count one passenger. When several assigned lists share
 * a currency, the oldest list is kept. A list that cannot price every included line is skipped.
 *
 * The detail sheet can start a booking only when the signed-in account is that agency
 * and it has an accepted, active offer for the package. A pending offer is reported
 * so the page can send the agency to accept it first.
 */
final class OperatorPublicPackageCatalog
{
    public function __construct(
        private readonly OperatorPackageAgencyPriceResolver $priceResolver,
        private readonly PackageOfferPreviewBuilder $previewBuilder,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function cards(Account $account): array
    {
        $catalogs = $this->publicQuery($account)->get();
        $cities = $this->citiesById($catalogs);
        $agencyId = $this->viewerAgencyId($account);
        $priceLists = $this->assignedPriceLists($account, $agencyId);

        $cards = [];
        foreach ($catalogs as $catalog) {
            $cards[] = $this->card($account, $catalog, $cities, $priceLists, $agencyId, url('/#paquetes'));
        }

        return $cards;
    }

    public function find(Account $account, string $uuid): OperatorServiceCatalog
    {
        $catalog = $this->publicQuery($account)
            ->where('uuid', $uuid)
            ->first();

        abort_unless($catalog instanceof OperatorServiceCatalog, 404);

        return $catalog;
    }

    /**
     * Detail sheet for the public page and the PDF download.
     *
     * @return array<string, mixed>
     */
    public function sheet(Account $account, OperatorServiceCatalog $catalog): array
    {
        $cities = $this->citiesById(collect([$catalog]));
        $agencyId = $this->viewerAgencyId($account);
        $priceLists = $this->assignedPriceLists($account, $agencyId);
        $card = $this->card(
            $account,
            $catalog,
            $cities,
            $priceLists,
            $agencyId,
            route('site.packages.show', ['package' => $catalog->uuid]),
        );

        $preview = $this->previewBuilder->buildForCatalog($catalog);
        $items = [];
        foreach ($preview['items'] ?? [] as $item) {
            if (! is_array($item)) {
                continue;
            }
            unset($item['provider_name']);
            $items[] = $item;
        }

        $description = (string) ($card['description'] ?? '');
        $operatorName = trim((string) ($account->commercial_name ?? $account->name ?? $account->nick ?? ''));
        $priceLabel = $card['prices'] !== []
            ? implode(' · ', $card['prices'])
            : (string) __('tenant.package_price_on_request');

        $locales = [];
        if ($description !== '') {
            $locales[] = [
                'language' => '',
                'name' => '',
                'description' => $description,
            ];
        }

        $card['items'] = $items;
        $card['feature_groups'] = $preview['feature_groups'] ?? [];
        $card['conditions'] = $preview['conditions'] ?? [];
        $card['hero_images'] = $preview['hero_images'] ?? [];
        $card['galleries'] = $preview['galleries'] ?? [];
        $card['booking'] = $this->bookingPanel($account, $catalog, $agencyId);
        $card['pdf_preview'] = array_merge($preview, [
            'title' => $card['title'],
            'operator_name' => $operatorName,
            'agency_price' => $priceLabel,
            'agency_price_usd_hint' => '',
            'price_list_name' => '',
            'locales' => $locales,
            'items' => $items,
        ]);

        return $card;
    }

    /**
     * @return Builder<OperatorServiceCatalog>
     */
    private function publicQuery(Account $account): Builder
    {
        return OperatorServiceCatalog::query()
            ->where('operator_id', $account->id)
            ->where('catalog_kind', OperatorServiceCatalog::KIND_PACKAGE)
            ->where('is_public', true)
            ->where('status', 'active')
            ->with([
                'translations.language.locale',
                'items.serviceVariant.service.translations.language.locale',
                'items.serviceVariant.service.media',
                'items.serviceVariant.media',
                'items.serviceOffer',
            ])
            ->orderByDesc('is_featured')
            ->orderBy('id');
    }

    /**
     * Agency account of the signed-in visitor that has a price list from this operator.
     * Prefers the account selected in the session when several match.
     */
    private function viewerAgencyId(Account $operator): ?int
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            return null;
        }

        $assignedAgencyIds = OperatorPriceListAssignment::query()
            ->where('is_active', true)
            ->whereHas('priceList', function ($query) use ($operator): void {
                $query->where('operator_id', $operator->id)
                    ->where('is_active', true);
            })
            ->pluck('agency_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($assignedAgencyIds->isEmpty()) {
            return null;
        }

        $membershipIds = $user->accounts()
            ->pluck('accounts.id')
            ->map(fn ($id) => (int) $id);

        $matches = $assignedAgencyIds->intersect($membershipIds)->values();
        if ($matches->isEmpty()) {
            return null;
        }

        $current = $user->currentAccountId();
        if ($current !== null && $matches->contains($current)) {
            return $current;
        }

        if ($matches->count() === 1) {
            return (int) $matches->first();
        }

        return null;
    }

    /**
     * Booking box for the detail page.
     *
     * can_book is true only for the agency account currently selected in the session,
     * and only when that agency already accepted an active offer of this package.
     * show_status tells the page to explain why booking is closed (pending offer, or none).
     *
     * @return array{can_book: bool, offer_uuid: ?string, pending: bool, show_status: bool}
     */
    private function bookingPanel(Account $operator, OperatorServiceCatalog $catalog, ?int $agencyId): array
    {
        $closed = [
            'can_book' => false,
            'offer_uuid' => null,
            'pending' => false,
            'show_status' => false,
        ];

        if ($agencyId === null || ! $this->viewerIsCurrentAgency($agencyId)) {
            return $closed;
        }

        $offer = PackageOffer::query()
            ->where('operator_id', $operator->id)
            ->where('agency_id', $agencyId)
            ->where('operator_service_catalog_id', $catalog->id)
            ->where('status', PackageOffer::STATUS_ACCEPTED)
            ->where('availability', PackageOffer::AVAILABILITY_ACTIVE)
            ->whereHas('catalog', fn (Builder $query) => $query->where('status', 'active'))
            ->orderByDesc('id')
            ->first();

        if ($offer instanceof PackageOffer) {
            return [
                'can_book' => true,
                'offer_uuid' => (string) $offer->uuid,
                'pending' => false,
                'show_status' => false,
            ];
        }

        $pending = PackageOffer::query()
            ->where('operator_id', $operator->id)
            ->where('agency_id', $agencyId)
            ->where('operator_service_catalog_id', $catalog->id)
            ->where('status', PackageOffer::STATUS_PENDING)
            ->exists();

        return [
            'can_book' => false,
            'offer_uuid' => null,
            'pending' => $pending,
            'show_status' => true,
        ];
    }

    /**
     * The price shown on the site follows the agency tied to this visit.
     * Booking must use that same account, because the reservation is stored against the session account.
     */
    private function viewerIsCurrentAgency(int $agencyId): bool
    {
        $user = Auth::user();
        if (! $user instanceof User || (int) $user->currentAccountId() !== $agencyId) {
            return false;
        }

        $account = $user->currentAccount();
        if (! $account instanceof Account) {
            return false;
        }

        return $account->accountTypes()
            ->where('cat_account_types.active', true)
            ->where('cat_account_types.code', 'agency')
            ->exists();
    }

    /**
     * @return Collection<int, OperatorPriceList>
     */
    private function assignedPriceLists(Account $operator, ?int $agencyId): Collection
    {
        if ($agencyId === null) {
            return collect();
        }

        return OperatorPriceList::query()
            ->where('operator_id', $operator->id)
            ->where('is_active', true)
            ->whereHas('assignments', function ($query) use ($agencyId): void {
                $query->where('agency_id', $agencyId)
                    ->where('is_active', true);
            })
            ->with('currency.lmpCurrency')
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  Collection<int, LmpCity>  $cities
     * @param  Collection<int, OperatorPriceList>  $priceLists
     * @return array<string, mixed>
     */
    private function card(
        Account $account,
        OperatorServiceCatalog $catalog,
        Collection $cities,
        Collection $priceLists,
        ?int $agencyId,
        string $returnUrl,
    ): array {
        $translation = $catalog->translationForDisplay();
        $title = trim((string) ($translation?->name ?? ''));
        if ($title === '') {
            $title = $catalog->displayLabel();
        }

        $uuid = (string) $catalog->uuid;
        $prices = $this->prices($catalog, $priceLists, (int) $account->id, $agencyId);

        return [
            'uuid' => $uuid,
            'title' => $title,
            'description' => trim((string) ($translation?->description ?? '')),
            'location' => $this->locationLabel($catalog, $cities),
            'cover_url' => $this->coverUrl($catalog),
            'prices' => $prices,
            'show_login' => $agencyId === null,
            'login_url' => route('login', ['return' => $returnUrl]),
            'url' => route('site.packages.show', ['package' => $uuid]),
            'pdf_url' => route('site.packages.pdf', ['package' => $uuid]),
        ];
    }

    /**
     * @param  Collection<int, OperatorPriceList>  $priceLists
     * @return list<string>
     */
    private function prices(
        OperatorServiceCatalog $catalog,
        Collection $priceLists,
        int $operatorId,
        ?int $agencyId,
    ): array {
        if ($agencyId === null || $priceLists->isEmpty()) {
            return [];
        }

        $formatted = [];
        $seenCurrencies = [];

        foreach ($priceLists as $priceList) {
            $currencyId = (int) $priceList->currency_id;
            if (isset($seenCurrencies[$currencyId])) {
                continue;
            }

            $resolved = $this->priceResolver->resolvePackageTotal(
                $catalog,
                $priceList,
                $agencyId,
                $operatorId,
            );
            if (! ($resolved['has_amount'] ?? false) || ($resolved['is_zero'] ?? true)) {
                continue;
            }

            $label = trim((string) ($resolved['formatted'] ?? ''));
            if ($label === '' || $label === '—') {
                continue;
            }

            $seenCurrencies[$currencyId] = true;
            $formatted[] = $label;
        }

        return $formatted;
    }

    /**
     * @param  Collection<int, OperatorServiceCatalog>  $catalogs
     * @return Collection<int, LmpCity>
     */
    private function citiesById(Collection $catalogs): Collection
    {
        $ids = [];
        foreach ($catalogs as $catalog) {
            foreach ($this->includedItems($catalog) as $item) {
                $cityId = (int) ($this->itemService($item)?->city_id ?? 0);
                if ($cityId > 0) {
                    $ids[$cityId] = $cityId;
                }
            }
        }

        if ($ids === []) {
            return collect();
        }

        return LmpCity::query()
            ->whereIn('id', array_values($ids))
            ->get()
            ->keyBy(fn (LmpCity $city): int => (int) $city->id);
    }

    /**
     * @param  Collection<int, LmpCity>  $cities
     */
    private function locationLabel(OperatorServiceCatalog $catalog, Collection $cities): string
    {
        $names = [];
        $seen = [];

        foreach ($this->includedItems($catalog) as $item) {
            $cityId = (int) ($this->itemService($item)?->city_id ?? 0);
            if ($cityId < 1 || isset($seen[$cityId])) {
                continue;
            }
            $seen[$cityId] = true;

            $name = trim((string) ($cities->get($cityId)?->name ?? ''));
            if ($name !== '') {
                $names[] = $name;
            }
        }

        return implode(' · ', $names);
    }

    private function coverUrl(OperatorServiceCatalog $catalog): ?string
    {
        foreach ($this->includedItems($catalog) as $item) {
            $service = $this->itemService($item);
            $serviceUrl = $service?->mainImageUrl(Service::MEDIA_CONVERSION_SMALL)
                ?? $service?->mainImageUrl(Service::MEDIA_CONVERSION_REGULAR);
            if (is_string($serviceUrl) && $serviceUrl !== '') {
                return $serviceUrl;
            }

            $variant = $item->serviceVariant;
            if (! $variant instanceof ServiceVariant) {
                continue;
            }

            $variantUrl = $variant->mainImageUrl(ServiceVariant::MEDIA_CONVERSION_SMALL)
                ?? $variant->mainImageUrl(ServiceVariant::MEDIA_CONVERSION_REGULAR);
            if (is_string($variantUrl) && $variantUrl !== '') {
                return $variantUrl;
            }
        }

        return null;
    }

    /**
     * @return Collection<int, OperatorPackageItem>
     */
    private function includedItems(OperatorServiceCatalog $catalog): Collection
    {
        return $catalog->items
            ->filter(fn (OperatorPackageItem $item): bool => $item->inclusion_mode === 'included')
            ->sortBy([
                ['sort_order', 'asc'],
                ['id', 'asc'],
            ])
            ->values();
    }

    private function itemService(OperatorPackageItem $item): ?Service
    {
        $service = $item->serviceVariant?->service;

        return $service instanceof Service ? $service : null;
    }
}
