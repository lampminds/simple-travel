<?php

namespace App\Http\Controllers\TenantSite;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountPublicProfile;
use App\Models\Language;
use App\Services\CatFaqListService;
use App\Services\OperatorPublicProductCategories;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly CatFaqListService $faqListService,
        private readonly OperatorPublicProductCategories $productCategories,
    ) {}

    /**
     * SaaS landing on the main host; minimal welcome on tenant website hosts.
     */
    public function __invoke(Request $request): View
    {
        $portalAccount = $request->attributes->get('operator_portal_account');
        if ($portalAccount instanceof Account) {
            return view('portal.home', [
                'account' => $portalAccount,
                'title' => $portalAccount->commercial_name ?? $portalAccount->name ?? $portalAccount->nick,
                'languages' => Language::query()->with('locale')->get(),
            ]);
        }

        $account = $request->attributes->get('tenant_website_account');

        if ($account instanceof Account) {
            $account->load([
                'publicProfile.translations.language.locale',
            ]);
            $profile = $account->publicProfile;
            $translation = $profile instanceof AccountPublicProfile
                ? $profile->translationForDisplay()
                : null;
            $title = trim((string) ($translation?->title ?? ''));
            $subtitle = trim((string) ($translation?->subtitle ?? ''));
            $about = trim((string) ($translation?->about ?? ''));

            $data = [
                'account' => $account,
                'title' => $title !== '' ? $title : ($account->commercial_name ?? $account->name ?? $account->nick),
                'subtitle' => $subtitle !== '' ? $subtitle : null,
                'about' => $about !== '' ? $about : null,
                'videoEmbed' => $profile?->videoEmbed(),
                'productCategories' => $this->productCategories->forAccount($account),
            ];

            if (site_assets_published()) {
                return view('site::home', $data);
            }

            return view('tenant.welcome', $data);
        }

        return view('landings.saas', [
            'faqItems' => $this->faqListService->displayItemsFromRequest($request, 3),
        ]);
    }
}
