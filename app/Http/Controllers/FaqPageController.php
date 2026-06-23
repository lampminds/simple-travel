<?php

namespace App\Http\Controllers;

use App\Services\CatFaqListService;
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
        return view('pages.faq', [
            'faqItems' => $this->faqListService->displayItemsFromRequest($request),
        ]);
    }
}
