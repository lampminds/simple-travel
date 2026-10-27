<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class AccountPublicProfile extends Model
{
    use AuditTrait;

    protected $table = 'account_public_profiles';

    protected $fillable = [
        'account_id',
        'video_url',
        'business_hours',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(AccountPublicProfileTranslation::class, 'account_public_profile_id');
    }

    /**
     * Sanitized HTML safe to print with {!! !!}. Plain text keeps its line breaks.
     */
    public static function renderAbout(?string $html): string
    {
        $clean = self::storedAbout($html);
        if ($clean === null) {
            return '';
        }

        if (! str_contains($clean, '<')) {
            return nl2br(e($clean));
        }

        return $clean;
    }

    public function translationForDisplay(): ?AccountPublicProfileTranslation
    {
        $translations = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->with('language.locale')->get();

        if ($translations->isEmpty()) {
            return null;
        }

        $locale = app()->getLocale();
        foreach ($translations as $translation) {
            $language = $translation->language;
            if (! $language instanceof Language) {
                continue;
            }

            $language->loadMissing('locale');
            if (Locale::primaryTagMatches($language->locale, $locale)) {
                return $translation;
            }
        }

        return $translations->first();
    }

    /**
     * YouTube and Vimeo links become an embed id. Any other http(s) URL stays a plain link.
     *
     * @return array{provider: string, id: string, url: string}|null
     */
    public function videoEmbed(): ?array
    {
        $url = trim((string) $this->video_url);
        if ($url === '') {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = (string) parse_url($url, PHP_URL_PATH);
        $query = [];
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        if (str_contains($host, 'youtu.be')) {
            $id = strtok(trim($path, '/'), '?') ?: '';
            if ($id !== '') {
                return ['provider' => 'youtube', 'id' => $id, 'url' => $url];
            }
        }

        if (str_contains($host, 'youtube.com') || str_contains($host, 'youtube-nocookie.com')) {
            $watchId = trim((string) ($query['v'] ?? ''));
            if ($watchId !== '') {
                return ['provider' => 'youtube', 'id' => $watchId, 'url' => $url];
            }

            if (preg_match('#/(?:embed|shorts|live)/([A-Za-z0-9_-]+)#', $path, $matches) === 1) {
                return ['provider' => 'youtube', 'id' => $matches[1], 'url' => $url];
            }
        }

        if (str_contains($host, 'vimeo.com') && preg_match('#/(?:video/)?(\d+)#', $path, $matches) === 1) {
            return ['provider' => 'vimeo', 'id' => $matches[1], 'url' => $url];
        }

        return ['provider' => 'link', 'id' => '', 'url' => $url];
    }

    /**
     * Create, update, or remove the profile from a form payload.
     * An empty video and empty translations remove the row.
     *
     * @param  array{video_url?: string|null, business_hours?: string|null, translations?: array<int|string, array<string, mixed>>}  $input
     */
    public static function syncForAccount(Account $account, array $input): void
    {
        $videoUrl = trim((string) ($input['video_url'] ?? ''));
        $videoUrl = $videoUrl !== '' ? $videoUrl : null;
        $businessHours = self::nullableString($input['business_hours'] ?? null);
        $translations = is_array($input['translations'] ?? null) ? $input['translations'] : [];

        $hasCopy = $videoUrl !== null || $businessHours !== null;
        foreach ($translations as $row) {
            if (is_array($row) && self::rowHasCopy($row)) {
                $hasCopy = true;
                break;
            }
        }

        $profile = $account->publicProfile()->first();

        if (! $hasCopy) {
            $profile?->delete();

            return;
        }

        if (! $profile instanceof self) {
            $profile = $account->publicProfile()->create([
                'video_url' => $videoUrl,
                'business_hours' => $businessHours,
            ]);
        } else {
            $profile->update([
                'video_url' => $videoUrl,
                'business_hours' => $businessHours,
            ]);
        }

        foreach ($translations as $languageId => $row) {
            if (! is_numeric($languageId) || ! is_array($row)) {
                continue;
            }

            $languageId = (int) $languageId;
            if ($languageId < 1) {
                continue;
            }

            if (! self::rowHasCopy($row)) {
                $profile->translations()->where('language_id', $languageId)->delete();

                continue;
            }

            $profile->translations()->updateOrCreate(
                ['language_id' => $languageId],
                [
                    'title' => self::nullableString($row['title'] ?? null),
                    'subtitle' => self::nullableString($row['subtitle'] ?? null),
                    'about' => self::storedAbout($row['about'] ?? null),
                ],
            );
        }
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private static function rowHasCopy(array $row): bool
    {
        foreach (['title', 'subtitle'] as $key) {
            if (trim((string) ($row[$key] ?? '')) !== '') {
                return true;
            }
        }

        if (self::storedAbout($row['about'] ?? null) !== null) {
            return true;
        }

        return false;
    }

    private static function nullableString(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text !== '' ? $text : null;
    }

    private static function storedAbout(mixed $value): ?string
    {
        $html = trim((string) $value);
        if ($html === '') {
            return null;
        }

        $clean = trim((string) Str::sanitizeHtml($html));
        $text = trim(html_entity_decode(strip_tags(str_replace(
            ['<br>', '<br/>', '<br />'],
            ' ',
            $clean,
        )), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = str_replace("\xc2\xa0", '', $text);

        return $text === '' ? null : $clean;
    }
}
