<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

/**
 * Stub endpoint for the bundled theme contact forms (XHR to /contactus).
 * Does not persist data; avoids RouteNotFoundException during static browsing.
 */
class DemoContactFormController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'message' => '<div class="text-green-600 dark:text-green-400">Thanks — this is a demo; your message was not stored.</div>',
        ]);
    }
}
