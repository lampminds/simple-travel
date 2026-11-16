<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ProviderBookingField extends Model
{
    use AuditTrait;

    public const SCOPE_PASSENGER = 'passenger';

    public const SCOPE_BOOKING = 'booking';

    public const TYPE_TEXT = 'text';

    public const TYPE_TEXTAREA = 'textarea';

    public const TYPE_DATE = 'date';

    public const TYPE_TIME = 'time';

    public const TYPE_PHONE = 'phone';

    public const TYPE_COUNTRY = 'country';

    protected $fillable = [
        'account_id',
        'source_template_item_id',
        'scope',
        'input_type',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'source_template_item_id' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * @return list<string>
     */
    public static function scopes(): array
    {
        return [
            self::SCOPE_PASSENGER,
            self::SCOPE_BOOKING,
        ];
    }

    /**
     * @return list<string>
     */
    public static function inputTypes(): array
    {
        return [
            self::TYPE_TEXT,
            self::TYPE_TEXTAREA,
            self::TYPE_DATE,
            self::TYPE_TIME,
            self::TYPE_PHONE,
            self::TYPE_COUNTRY,
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function sourceTemplateItem(): BelongsTo
    {
        return $this->belongsTo(ProviderBookingTemplateItem::class, 'source_template_item_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProviderBookingFieldTranslation::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ServiceBookingFieldAssignment::class);
    }

    public function scopeLabel(): string
    {
        return (string) __('account.booking_fields.scope_'.$this->scope);
    }

    public function inputTypeLabel(): string
    {
        return (string) __('account.booking_fields.type_'.$this->input_type);
    }

    public function labelForDisplay(): string
    {
        return (string) ($this->translationForDisplay()?->label ?? '');
    }

    public function helpForDisplay(): ?string
    {
        $help = $this->translationForDisplay()?->help_text;

        if ($help === null || trim($help) === '') {
            return null;
        }

        return $help;
    }

    private function translationForDisplay(): ?ProviderBookingFieldTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations.language.locale');
        }

        $translations = $this->translations;

        if ($translations->isEmpty()) {
            return null;
        }

        $locale = app()->getLocale();
        foreach ($translations as $translation) {
            $language = $translation->language;
            if (! $language) {
                continue;
            }
            $language->loadMissing('locale');
            if (Locale::primaryTagMatches($language->locale, $locale)) {
                return $translation;
            }
        }

        return $translations->first();
    }
}
