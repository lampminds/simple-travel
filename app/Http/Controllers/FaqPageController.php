<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\CatFaqListService;
use App\Support\AccountDashboardLane;
use App\Support\CurrentAccountSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class FaqPageController extends Controller
{
    public function __construct(
        private readonly CatFaqListService $faqListService,
    ) {}

    /**
     * Public FAQ page (footer). Logged-in users also see FAQs scoped to their current lane / account type.
     */
    public function __invoke(Request $request): View
    {
        $accountTypeId = null;
        $user = $request->user();
        if ($user !== null) {
            $account = $user->currentAccount();
            if ($account instanceof Account) {
                $accountTypeId = AccountDashboardLane::resolvedLaneTypeId($request, $account);
                if ($accountTypeId === null) {
                    $typeIds = CurrentAccountSession::typeIds($request);
                    $accountTypeId = $typeIds[0] ?? null;
                }
            }
        }

        return view('pages.faq', [
            'faqItems' => $this->faqListService->displayItems($accountTypeId),
        ]);
    }
}
