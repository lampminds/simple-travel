<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class TodoCategoryTranslation extends Model
{
    use AuditTrait;

    protected $table = 'todo_category_translations';

    protected $fillable = [
        'todo_category_id',
        'language_id',
        'name',
        'description',
    ];

    public function todoCategory(): BelongsTo
    {
        return $this->belongsTo(TodoCategory::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
