<?php

namespace App\Http\Controllers\TenantSite;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ServiceType;
use App\Services\OperatorPublicProductCategories;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceCategoryController extends Controller
{
    public function __construct(
        private readonly OperatorPublicProductCategories $productCategories,
    ) {}

    /**
     * Public page for one service category the operator actually offers.
     */
    public function __invoke(Request $request, string $code): View
    {
        $account = $request->attributes->get('tenant_website_account');
        abort_unless($account instanceof Account, 404);
        abort_unless(site_assets_published(), 404);

        $category = $this->productCategories
            ->forAccount($account)
            ->first(fn (ServiceType $type): bool => $type->code === $code);

        abort_if($category === null, 404);

        $title = $category->name !== '' ? $category->name : $category->code;

        return view('site::category', [
            'account' => $account,
            'category' => $category,
            'title' => $title,
        ]);
    }
}
