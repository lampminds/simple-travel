<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class TodoTask extends Model
{
    use AuditTrait;

    public const ACTION_NONE = 'none';

    public const ACTION_ROUTE = 'route';

    public const ACTION_URL = 'url';

    public const ACTION_EXTERNAL = 'external';

    public const VERIFICATION_NONE = 'none';

    public const VERIFICATION_API_CHECK = 'api-check';

    protected $table = 'todo_tasks';

    protected static function booted(): void
    {
        static::deleting(function (TodoTask $task): void {
            $task->translations()->delete();
            $task->userAssignments()->delete();
        });
    }

    protected $fillable = [
        'account_id',
        'code',
        'todo_category_id',
        'original_task_id',
        'action_type',
        'action_url',
        'verification_type',
        'verification_url',
        'sort_order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TodoCategory::class, 'todo_category_id');
    }

    public function originalTask(): BelongsTo
    {
        return $this->belongsTo(self::class, 'original_task_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(TodoTaskTranslation::class, 'todo_task_id');
    }

    public function userAssignments(): HasMany
    {
        return $this->hasMany(TodoTaskUserAssignment::class, 'todo_task_id');
    }

    public function getNameAttribute(): string
    {
        $fromTranslation = $this->firstFilledTranslationName();
        if ($fromTranslation !== '') {
            return $fromTranslation;
        }

        return trim((string) ($this->attributes['code'] ?? ''));
    }

    /**
     * Label for UI lists (matches {@see TodoCategory::displayLabel()} behaviour).
     */
    public function displayLabel(): string
    {
        $label = $this->getNameAttribute();

        return $label !== '' ? $label : '—';
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->description;
    }

    protected function firstFilledTranslationName(): string
    {
        $translation = $this->getTranslationForDisplay();
        if ($translation && filled($translation->name)) {
            return trim((string) $translation->name);
        }

        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        foreach ($this->translations as $row) {
            if (filled($row->name)) {
                return trim((string) $row->name);
            }
        }

        return '';
    }

    protected function getTranslationForDisplay(): ?TodoTaskTranslation
    {
        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
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

    /** @return list<string> */
    public static function actionTypes(): array
    {
        return [self::ACTION_NONE, self::ACTION_ROUTE, self::ACTION_URL, self::ACTION_EXTERNAL];
    }

    /** @return list<string> */
    public static function verificationTypes(): array
    {
        return [self::VERIFICATION_NONE, self::VERIFICATION_API_CHECK];
    }

    public function opensActionInBrowser(): bool
    {
        return in_array($this->action_type, [self::ACTION_URL, self::ACTION_EXTERNAL], true);
    }

    /**
     * URL for executing this task on the welcome checklist when {@see self::ACTION_ROUTE} is set.
     * Returns null if the route is missing or requires parameters that are not provided.
     */
    public function welcomeExecutionUrl(): ?string
    {
        if ($this->action_type !== self::ACTION_ROUTE) {
            return null;
        }

        $routeName = trim((string) ($this->attributes['action_url'] ?? ''));
        if ($routeName === '' || ! Route::has($routeName)) {
            return null;
        }

        try {
            return route($routeName);
        } catch (\Throwable) {
            return null;
        }
    }
}
