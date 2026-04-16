<?php

namespace App\Support;

use Illuminate\Http\Response;

final class ForbiddenResponse
{
    public static function make(?string $title = null, ?string $description = null): Response
    {
        return response()->view('errors.403', [
            'customTitle' => $title,
            'customDescription' => $description,
        ], 403);
    }
}

