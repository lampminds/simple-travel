<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components\Actions;

use Filament\Forms\Components\TextInput\Actions\CopyAction;
use Illuminate\Support\Js;

/**
 * Filament {@see CopyAction} uses `navigator.clipboard.writeText` only (requires HTTPS).
 * This action uses `window.simpleTravelCopyTextToClipboard` so the same suffix UX works over HTTP.
 *
 * Use the `TextInput::copyableWithHttpFallback()` macro from {@see \App\Providers\AppServiceProvider},
 * or attach this class manually as a suffix action.
 */
final class HttpSafeCopyAction extends CopyAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->alpineClickHandler(function (mixed $state): string {
            $text = match (true) {
                $state === null => '',
                is_bool($state) => $state ? '1' : '0',
                is_scalar($state) => (string) $state,
                default => json_encode($state, JSON_UNESCAPED_UNICODE) ?: '',
            };

            $copyableState = Js::from($text);
            $copyMessageJs = Js::from($this->getCopyMessage($state));
            $copyMessageDurationJs = Js::from($this->getCopyMessageDuration($state));

            return <<<JS
(async () => {
    try {
        await window.simpleTravelCopyTextToClipboard({$copyableState});
    } catch (e) {
        return;
    }
    \$tooltip({$copyMessageJs}, {
        theme: \$store.theme,
        timeout: {$copyMessageDurationJs},
    });
})()
JS;
        });
    }
}
