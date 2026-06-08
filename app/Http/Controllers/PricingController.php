<?php

namespace App\Http\Controllers;

use App\Services\PricingPageService;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function __construct(
        private readonly PricingPageService $pricingPageService,
    ) {}

    /**
     * Display the pricing page with active catalog modules and their prices.
     */
    public function __invoke(): View
    {
        return view('pages.pricing', $this->pricingPageService->build());
    }
}
