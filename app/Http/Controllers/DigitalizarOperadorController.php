<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class DigitalizarOperadorController extends Controller
{
    private const EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Resolve image URL for a given row and suffix (wo = without ST, w = with ST).
     * Accepts exact names like 1-wo-st.jpg or with middle name like 1-cotizaciones-wo-st.jpg.
     * Returns the web path or null if not found.
     */
    private function resolveComparisonImage(int $row, string $suffix): ?string
    {
        $dir = public_path('images/st-comparison');
        if (! File::isDirectory($dir)) {
            return null;
        }
        $baseExact = "{$row}-{$suffix}-st";
        foreach (self::EXTENSIONS as $ext) {
            $path = "{$dir}/{$baseExact}.{$ext}";
            if (File::exists($path)) {
                return "/images/st-comparison/{$baseExact}.{$ext}";
            }
        }
        // Fallback: match N-*-suffix-st.ext (e.g. 1-cotizaciones-wo-st.jpg)
        $files = File::glob("{$dir}/{$row}-*-{$suffix}-st.*");
        if (! empty($files)) {
            $basename = basename($files[0]);

            return "/images/st-comparison/{$basename}";
        }

        return null;
    }

    /**
     * Display the "Digitalizar operador turístico" comparison page.
     */
    public function __invoke(): View
    {
        $rows = [];
        for ($i = 1; $i <= 5; $i++) {
            $rows[] = [
                'title_key' => 'digitalizar.row' . $i . '_title',
                'sin_key' => 'digitalizar.row' . $i . '_sin',
                'con_key' => 'digitalizar.row' . $i . '_con',
                'img_wo' => $this->resolveComparisonImage($i, 'wo'),
                'img_w' => $this->resolveComparisonImage($i, 'w'),
            ];
        }

        return view('pages.digitalizar-operador-turistico', compact('rows'));
    }
}
