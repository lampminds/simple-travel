<?php

namespace App\Livewire\Account;

use App\Models\Account;
use App\Models\OperatorServiceCatalog;
use App\Services\PackageOfferPreviewBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class OperatorPackagePreviewModal extends Component
{
    public bool $showModal = false;

    public bool $loading = false;

    /** @var array<string, mixed>|null */
    public ?array $preview = null;

    public ?string $packageUuid = null;

    #[On('open-operator-package-preview')]
    public function open(string $packageUuid): void
    {
        $this->loading = true;
        $this->showModal = true;
        $this->preview = null;
        $this->packageUuid = $packageUuid;

        $account = $this->resolveOperatorAccount();
        $catalog = OperatorServiceCatalog::query()
            ->where('uuid', $packageUuid)
            ->where('operator_id', $account->id)
            ->firstOrFail();

        $this->preview = app(PackageOfferPreviewBuilder::class)->buildForCatalog($catalog);
        $this->loading = false;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->loading = false;
        $this->preview = null;
        $this->packageUuid = null;
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
        return view('livewire.account.operator-package-preview-modal');
    }
}
