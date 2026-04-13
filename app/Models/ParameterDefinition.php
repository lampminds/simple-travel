<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ParameterDefinition extends Model
{
    use AuditTrait;

    protected $table = 'cat_parameter_definitions';

    /** @var list<string> */
    protected $fillable = [
        'category',
        'subcategory',
        'code',
        'type',
        'scope',
        'has_default',
        'ui_component',
        'ui_options',
        'sort_order',
        'default_value',
        'validation_rules',
        'comments',
    ];

    protected $casts = [
        'has_default' => 'boolean',
        'ui_options' => 'array',
        'validation_rules' => 'array',
        'sort_order' => 'integer',
    ];

    /** @var list<string> */
    public const TYPES = [
        'string',
        'integer',
        'decimal',
        'boolean',
        'json',
        'array',
        'object',
        'date',
        'datetime',
        'time',
    ];

    /** @var list<string> */
    public const SCOPES = [
        'system',
        'tenant',
    ];

    /** Must match `cat_parameter_definitions.ui_component` enum in migrations. */
    public const UI_COMPONENTS = [
        'input',
        'select',
        'checkbox',
        'radio',
        'switch',
        'textarea',
        'editor',
        'date',
        'datetime',
        'time',
    ];

    /** UI modes that require explicit options (e.g. two rows for boolean/switch). */
    public const UI_COMPONENTS_REQUIRING_OPTIONS = [
        'select',
        'checkbox',
        'radio',
        'switch',
    ];

    public static function uiComponentRequiresOptions(?string $uiComponent): bool
    {
        return in_array($uiComponent, self::UI_COMPONENTS_REQUIRING_OPTIONS, true);
    }

    /**
     * Whether stored values for this definition should be chosen from predefined options (DB), for standalone value forms.
     */
    public static function queryUsesOptionBackedValue(?int $definitionId): bool
    {
        if (! $definitionId) {
            return false;
        }

        $def = static::withCount('parameterOptions')->find($definitionId);

        if (! $def) {
            return false;
        }

        return static::uiComponentRequiresOptions($def->ui_component)
            && $def->parameter_options_count >= 2;
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ParameterDefinitionTranslation::class, 'parameter_definition_id');
    }

    public function parameterOptions(): HasMany
    {
        return $this->hasMany(ParameterOption::class, 'parameter_definition_id')->orderBy('sort_order');
    }

    public function parameterValues(): HasMany
    {
        return $this->hasMany(ParameterValue::class, 'parameter_definition_id');
    }

    /**
     * Stored option value => label for selects (by option translations / current locale).
     *
     * @return array<string, string>
     */
    public function optionValueToLabelMap(): array
    {
        $this->loadMissing(['parameterOptions.translations.language.locale']);

        $out = [];
        foreach ($this->parameterOptions as $option) {
            $out[(string) $option->value] = $option->labelForDisplay();
        }

        return $out;
    }

    public function getNameAttribute(): string
    {
        return $this->getTranslationForDisplay()?->name ?? '';
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->description;
    }

    public function getHelpAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->help;
    }

    protected function getTranslationForDisplay(): ?ParameterDefinitionTranslation
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
            $lang = $translation->language;
            if (! $lang) {
                continue;
            }
            $lang->loadMissing('locale');
            if (Locale::primaryTagMatches($lang->locale, $locale)) {
                return $translation;
            }
        }

        return $translations->first();
    }
}
