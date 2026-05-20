<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class DigitalizarOperadorController extends Controller
{
    /** Display order (row 2 = Tarifas y prestadores is penultimate). */
    private const COMPARISON_ROW_ORDER = [1, 3, 4, 2, 5];

    /** @var array<int, string> Content row => filename in public/images/comparison */
    private const COMPARISON_IMAGES = [
        1 => '1-quoting.png',
        2 => '2-pricing-providers.png',
        3 => '3-profit.png',
        4 => '4-payments.png',
        5 => '5-teamwork.png',
    ];

    /**
     * Resolve the illustration URL for a comparison row.
     */
    private function resolveComparisonImage(int $row): ?string
    {
        $filename = self::COMPARISON_IMAGES[$row] ?? null;
        if ($filename === null) {
            return null;
        }

        $path = public_path('images/comparison/'.$filename);
        if (! File::exists($path)) {
            return null;
        }

        return '/images/comparison/'.$filename;
    }

    /**
     * Display the "Digitalizar operador turístico" comparison page.
     */
    public function __invoke(): View
    {
        $rows = [];
        foreach (self::COMPARISON_ROW_ORDER as $i) {
            $rows[] = [
                'title_key' => 'digitalizar.row'.$i.'_title',
                'sin_key' => 'digitalizar.row'.$i.'_sin',
                'con_key' => 'digitalizar.row'.$i.'_con',
                'img' => $this->resolveComparisonImage($i),
            ];
        }

        return view('pages.digitalizar-operador-turistico', compact('rows'));
    }
}
