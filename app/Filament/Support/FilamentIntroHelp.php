<?php

namespace App\Filament\Support;

use Filament\Actions\Action;
use Filament\Support\Enums\Size;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

/**
 * Header "?" action + HTML tooltip for Filament list pages and similar screens.
 * Copy lives under lang/{locale}/filament_help.php (see {@see filamentListTranslationPrefix}).
 */
final class FilamentIntroHelp
{
    public static function filamentListTranslationPrefix(string $slug): string
    {
        return 'filament_help.' . $slug . '.list';
    }

    public static function resolveListTitle(string $slug, string $defaultTitle): string
    {
        $key = self::filamentListTranslationPrefix($slug) . '.intro_help_aria_label';

        if (! Lang::has($key)) {
            return $defaultTitle;
        }

        $value = trim((string) __($key));

        return $value !== '' ? $value : $defaultTitle;
    }

    public static function resolveListBodyHtml(string $slug): string
    {
        $key = self::filamentListTranslationPrefix($slug) . '.intro_tooltip_html';

        if (! Lang::has($key)) {
            return '';
        }

        return (string) __($key);
    }

    public static function listTooltipHtml(string $slug, string $defaultTitle): HtmlString
    {
        $title = e(self::resolveListTitle($slug, $defaultTitle));
        $bodyHtml = self::resolveListBodyHtml($slug);

        $html = '<div class="lmp-filament-help-tooltip">'
            . '<p class="lmp-filament-help-tooltip__title">' . $title . '</p>';

        if (trim($bodyHtml) !== '') {
            $html .= '<div class="lmp-filament-help-tooltip__body">' . $bodyHtml . '</div>';
        }

        $html .= '</div>';

        return new HtmlString($html);
    }

    public static function makeListHeaderAction(string $slug, string $defaultTitle): Action
    {
        $resolvedTitle = self::resolveListTitle($slug, $defaultTitle);

        return Action::make('filamentListIntroHelp_' . $slug)
            ->hiddenLabel()
            ->icon('heroicon-o-information-circle')
            ->iconButton()
            ->size(Size::Large)
            ->color('primary')
            ->extraAttributes([
                'aria-label' => (string) __('filament_help.common.icon_help_aria', [
                    'title' => strip_tags($resolvedTitle),
                ]),
            ])
            ->tooltip(fn (): HtmlString => self::listTooltipHtml($slug, $defaultTitle))
            ->action(fn () => null);
    }
}
