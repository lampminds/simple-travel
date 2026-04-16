<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class TodoTaskUserAssignment extends Model
{
    use AuditTrait;

    protected $table = 'todo_task_user_assignments';

    protected $fillable = [
        'todo_task_id',
        'user_id',
        'status',
        'notes',
        'completed_at',
        'ignored_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'ignored_at' => 'datetime',
    ];

    public function todoTask(): BelongsTo
    {
        return $this->belongsTo(TodoTask::class, 'todo_task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
