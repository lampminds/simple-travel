<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\OperatorServiceCatalog;
use App\Models\ServiceOffer;
use App\Support\AccountBusinessTypeGate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class AccountOperatorPackageServicesController extends Controller
{
    public function index(Request $request): View
    {
        $account = $this->resolveOperatorOwnerAccount($request);
        $operatorId = (int) $account->id;
        $filteredOffer = $this->resolveFilteredServiceOffer($request, $operatorId);
        $serviceOfferFilter = $filteredOffer?->uuid;

        $serviceFilterOptions = $this->serviceFilterOptions($operatorId);

        $packagesQuery = OperatorServiceCatalog::query()
            ->where('operator_id', $operatorId)
            ->with(['translations.language.locale'])
            ->orderByDesc('id');

        if ($filteredOffer !== null) {
            $serviceOfferId = (int) $filteredOffer->id;

            $packagesQuery
                ->whereHas('items', fn (Builder $query) => $query->where('service_offer_id', $serviceOfferId))
                ->with([
                    'items' => fn ($query) => $query
                        ->where('service_offer_id', $serviceOfferId)
                        ->orderBy('sort_order')
                        ->orderBy('id'),
                ]);
        } else {
            $packagesQuery->withCount('items');
        }

        $packages = $packagesQuery->get();

        $selectedServiceLabel = $filteredOffer !== null
            ? $this->serviceLabelForOffer($filteredOffer)
            : null;

        return view('account.operator-package-services.index', [
            'account' => $account,
            'packages' => $packages,
            'serviceOfferFilter' => $serviceOfferFilter,
            'serviceFilterOptions' => $serviceFilterOptions,
            'selectedServiceLabel' => $selectedServiceLabel,
        ]);
    }

    private function resolveFilteredServiceOffer(Request $request, int $operatorId): ?ServiceOffer
    {
        $raw = trim((string) $request->query('service_offer', ''));

        if ($raw === '' || $raw === 'all') {
            return null;
        }

        $query = ServiceOffer::query()
            ->where('operator_id', $operatorId)
            ->where('status', ServiceOffer::STATUS_ACCEPTED)
            ->whereHas('serviceVariant');

        if (! Str::isUuid($raw)) {
            return null;
        }

        return $query->where('uuid', $raw)->first();
    }

    /**
     * @return array<string, string>
     */
    private function serviceFilterOptions(int $operatorId): array
    {
        $offers = ServiceOffer::query()
            ->where('operator_id', $operatorId)
            ->where('status', ServiceOffer::STATUS_ACCEPTED)
            ->whereHas('serviceVariant')
            ->with([
                'providerAccount',
                'serviceVariant.service.translations',
                'serviceVariant.translations.language.locale',
            ])
            ->orderByDesc('offered_at')
            ->orderByDesc('id')
            ->get();

        $options = [];
        foreach ($offers as $offer) {
            $options[(string) $offer->uuid] = $this->serviceLabelForOffer($offer);
        }

        return $options;
    }

    private function serviceLabelForOffer(ServiceOffer $offer): string
    {
        $providerLabel = $offer->providerAccount?->commercial_name
            ?? $offer->providerAccount?->name
            ?? ('#'.$offer->provider_id);

        $variant = $offer->serviceVariant;
        $service = $variant?->service;
        if ($variant === null || $service === null) {
            return $providerLabel;
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
            return $providerLabel.' — '.$serviceName;
        }

        return $providerLabel.' — '.$serviceName.' — '.$detail;
    }

    private function resolveOperatorOwnerAccount(Request $request): Account
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        return AccountBusinessTypeGate::assertOperatorAccount($request);
    }
}
