<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class TodoTaskTranslation extends Model
{
    use AuditTrait;

    protected $table = 'todo_task_translations';

    protected $fillable = [
        'todo_task_id',
        'language_id',
        'name',
        'description',
    ];

    public function todoTask(): BelongsTo
    {
        return $this->belongsTo(TodoTask::class, 'todo_task_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
