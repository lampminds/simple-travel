<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Catalog row for a BCP 47 locale tag (RFC 5646).
 */
class Locale extends Model
{
    use AuditTrait;

    protected $table = 'cat_locales';

    protected $fillable = [
        'tag',
        'name_en',
        'active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * ISO 639-1 primary language subtag (e.g. en from en-US).
     */
    public function primaryLanguageTag(): string
    {
        $tag = trim((string) ($this->tag ?? ''));
        if ($tag === '') {
            return '';
        }

        return strtolower(explode('-', str_replace('_', '-', $tag))[0]);
    }

    /**
     * Match this row's primary language to the Laravel app locale string (e.g. es, es_ES, en-US).
     */
    public static function primaryTagMatches(?self $row, string $appLocale): bool
    {
        if (! $row) {
            return false;
        }

        $primary = $row->primaryLanguageTag();
        if ($primary === '') {
            return false;
        }

        $normalized = strtolower(explode('-', str_replace('_', '-', $appLocale))[0]);

        return $primary === $normalized;
    }

    /**
     * Emoji flag for the locale primary language (best-effort mapping).
     */
    public function flagEmoji(): string
    {
        return self::flagEmojiForPrimaryTag($this->primaryLanguageTag());
    }

    public static function flagEmojiForPrimaryTag(string $primaryTag): string
    {
        return match (strtolower(trim($primaryTag))) {
            'es' => '🇪🇸',
            'en' => '🇺🇸',
            'pt' => '🇵🇹',
            'fr' => '🇫🇷',
            'it' => '🇮🇹',
            'de' => '🇩🇪',
            default => '🌐',
        };
    }
}
