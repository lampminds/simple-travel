<?php

namespace App\Livewire\Account;

use App\Models\Account;
use App\Models\PackageOffer;
use App\Services\OperatorPackageAgencyPriceResolver;
use App\Services\PackageOfferPreviewBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PackageOfferPreviewModal extends Component
{
    public bool $showModal = false;

    public bool $loading = false;

    /** @var array<string, mixed>|null */
    public ?array $preview = null;

    public ?string $offerUuid = null;

    #[On('open-package-offer-preview')]
    public function open(string $offerUuid): void
    {
        $this->loading = true;
        $this->showModal = true;
        $this->preview = null;
        $this->offerUuid = $offerUuid;

        $account = $this->resolveAgencyAccount();
        $offer = PackageOffer::query()
            ->where('uuid', $offerUuid)
            ->where('agency_id', $account->id)
            ->whereHas('catalog')
            ->firstOrFail();

        $agencyPrice = null;
        $catalog = $offer->catalog;
        $priceList = $offer->priceList;

        if ($catalog !== null && $priceList !== null) {
            $agencyPrice = app(OperatorPackageAgencyPriceResolver::class)->resolvePackageTotal(
                $catalog,
                $priceList,
                (int) $account->id,
                (int) $offer->operator_id,
            );
        }

        $this->preview = app(PackageOfferPreviewBuilder::class)->build($offer, $agencyPrice);
        $this->loading = false;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->loading = false;
        $this->preview = null;
        $this->offerUuid = null;
    }

    private function resolveAgencyAccount(): Account
    {
        $user = Auth::user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $typeCodes = $account->accountTypes()
            ->where('active', true)
            ->pluck('code');
        abort_unless($typeCodes->contains('agency'), 403);

        return $account;
    }

    public function render(): View
    {
        return view('livewire.account.package-offer-preview-modal');
    }
}
