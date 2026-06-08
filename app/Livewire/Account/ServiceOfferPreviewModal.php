<?php

namespace App\Livewire\Account;

use App\Models\Account;
use App\Models\ServiceOffer;
use App\Services\OperatorVariantPriceResolver;
use App\Services\ServiceOfferPreviewBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ServiceOfferPreviewModal extends Component
{
    public bool $showModal = false;

    public bool $loading = false;

    /** @var array<string, mixed>|null */
    public ?array $preview = null;

    public ?string $offerUuid = null;

    #[On('open-service-offer-preview')]
    public function open(string $offerUuid): void
    {
        $this->loading = true;
        $this->showModal = true;
        $this->preview = null;
        $this->offerUuid = $offerUuid;

        $account = $this->resolveOperatorAccount();
        $offer = ServiceOffer::query()
            ->where('uuid', $offerUuid)
            ->where('operator_id', $account->id)
            ->whereHas('serviceVariant')
            ->firstOrFail();

        $operatorPrice = app(OperatorVariantPriceResolver::class)->resolve(
            $offer->serviceVariant,
            (int) $offer->provider_id,
            (int) $account->id,
        );

        $this->preview = app(ServiceOfferPreviewBuilder::class)->build($offer, $operatorPrice);
        $this->loading = false;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->loading = false;
        $this->preview = null;
        $this->offerUuid = null;
    }

    private function resolveOperatorAccount(): Account
    {
        $user = Auth::user();
        abort_unless($user !== null, 401);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $typeCodes = $account->accountTypes()
            ->where('active', true)
            ->pluck('code');
        abort_unless($typeCodes->contains('operator'), 403);

        return $account;
    }

    public function render(): View
    {
        return view('livewire.account.service-offer-preview-modal');
    }
}
