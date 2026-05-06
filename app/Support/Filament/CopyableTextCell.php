<?php

declare(strict_types=1);

namespace App\Support\Filament;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use JsonException;

/**
 * Table cell: visible text + trailing copy icon (HTTP-safe via {@see window.simpleTravelCopyTextToClipboard}).
 *
 * Table shorthand: {@see \Filament\Tables\Columns\TextColumn::httpSafeCopyableUsing()}.
 */
final class CopyableTextCell
{
    /**
     * @param  string  $textToCopy  Raw value for the `data-copy-text` attribute on the copy control.
     * @param  string|null  $displayText  Visible text; defaults to `$textToCopy` (HTML-escaped).
     * @param  string|null  $successMessage  Toast after success (default: `filament.common.code_copied`).
     * @param  string|null  $failMessage  Toast on failure (default: `filament.common.code_copy_failed`).
     * @param  string  $extraWrapperClasses  Extra classes on the outer flex wrapper.
     * @param  string|null  $copyButtonTooltip  Tooltip on the icon button; default uses `filament.common.click_to_copy_code`.
     */
    public static function span(
        string $textToCopy,
        ?string $displayText = null,
        ?string $successMessage = null,
        ?string $failMessage = null,
        string $extraWrapperClasses = '',
        ?string $copyButtonTooltip = null,
    ): HtmlString {
        if ($textToCopy === '') {
            return new HtmlString('—');
        }

        $display = e($displayText ?? $textToCopy);
        $dataAttr = htmlspecialchars($textToCopy, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        try {
            $okJson = json_encode(
                (string) ($successMessage ?? __('filament.common.code_copied')),
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
            );
            $failJson = json_encode(
                (string) ($failMessage ?? __('filament.common.code_copy_failed')),
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
            );
        } catch (JsonException) {
            $okJson = '"Copied"';
            $failJson = '"Copy failed"';
        }

        $handlerRaw = str_replace(
            ['__OK__', '__FAIL__'],
            [$okJson, $failJson],
            <<<'JS'
(async () => {
    try {
        await window.simpleTravelCopyTextToClipboard($el.dataset.copyText);
        $tooltip(__OK__, { theme: $store.theme, timeout: 2000 });
    } catch (e) {
        $tooltip(__FAIL__, { theme: $store.theme, timeout: 2500 });
    }
})()
JS
        );

        $handlerAttr = htmlspecialchars($handlerRaw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $tooltipContent = (string) ($copyButtonTooltip ?? __('filament.common.click_to_copy_code'));
        $tooltipSpec = '{content: '.(string) Js::from($tooltipContent).', theme: $store.theme, allowHTML: false}';
        $tooltipAttr = htmlspecialchars($tooltipSpec, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $aria = htmlspecialchars((string) __('filament.common.copy'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $iconHtml = self::clipboardIconSvg();

        $wrapperClasses = trim('fi-inline '.$extraWrapperClasses);
        $wrapperClassAttr = htmlspecialchars($wrapperClasses, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        // Filament panel CSS does not ship arbitrary Tailwind utilities from PHP strings; use `fi-*` + inline flex.
        // `justify-content: flex-start` + no flex-grow on the label keeps the copy icon flush after the text
        // instead of aligned to the far right of the column.
        $wrapperStyle = 'display:flex;width:100%;min-width:0;align-items:center;justify-content:flex-start;column-gap:0.375rem;';

        $buttonClasses = htmlspecialchars(
            'fi-icon-btn fi-color-gray fi-copy-trigger',
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        return new HtmlString(
            '<span class="'.$wrapperClassAttr.'" style="'.htmlspecialchars($wrapperStyle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'">'
            .'<span class="fi-ta-text-item truncate fi-size-sm text-start" style="min-width:0;flex:0 1 auto;">'.$display.'</span>'
            .'<button type="button" class="'.$buttonClasses.'" style="flex-shrink:0;" '
            .'data-copy-text="'.$dataAttr.'" '
            .'aria-label="'.$aria.'" '
            .'x-tooltip="'.$tooltipAttr.'" '
            .'x-on:click.prevent.stop="'.$handlerAttr.'">'
            .$iconHtml
            .'</button>'
            .'</span>'
        );
    }

    private static function clipboardIconSvg(): string
    {
        return <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20" class="fi-icon" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" /></svg>
SVG;
    }
}
