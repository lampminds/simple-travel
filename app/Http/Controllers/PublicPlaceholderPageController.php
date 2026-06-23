<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Renders public marketing pages that are not filled with final content yet.
 */
final class PublicPlaceholderPageController extends Controller
{
    /** @var list<string> */
    private const PAGES = [
        'demo',
        'integrations',
        'help-center',
        'api',
        'contact',
    ];

    public function __invoke(Request $request, string $page): View
    {
        abort_unless(in_array($page, self::PAGES, true), 404);

        return view('pages.placeholder', [
            'page' => $page,
            'showDemoCta' => $page === 'demo',
        ]);
    }
}
