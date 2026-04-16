<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class TodoTask extends Model
{
    use AuditTrait;

    public const ACTION_LINK = 'link';

    public const ACTION_API_CHECK = 'api_check';

    public const ACTION_MANUAL = 'manual';

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
        return $this->getTranslationForDisplay()?->name ?? '';
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->getTranslationForDisplay()?->description;
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
        return [self::ACTION_LINK, self::ACTION_API_CHECK, self::ACTION_MANUAL];
    }
}
