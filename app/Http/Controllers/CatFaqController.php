<?php

namespace App\Http\Controllers;

use App\Services\CatFaqListService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatFaqController extends Controller
{
    public function __construct(
        private readonly CatFaqListService $faqListService,
    ) {}

    /**
     * Active FAQs for the current app locale (JSON for front-end or integrations).
     *
     * Query: account_type_id (optional) — when omitted, only FAQs with no account type scope are returned.
     */
    public function index(Request $request): JsonResponse
    {
        $accountTypeId = $request->filled('account_type_id')
            ? (int) $request->query('account_type_id')
            : null;

        if ($accountTypeId !== null && $accountTypeId < 1) {
            $accountTypeId = null;
        }

        return response()->json([
            'data' => $this->faqListService->displayItems($accountTypeId),
        ]);
    }
}
