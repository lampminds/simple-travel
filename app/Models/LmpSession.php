<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One successful login. The row stays after Laravel deletes the matching sys_sessions record.
 */
class LmpSession extends Model
{
    protected $table = 'lmp_sessions';

    protected $fillable = [
        'user_id',
        'lmp_ip_id',
        'session_id',
        'user_agent',
        'logged_in_at',
    ];

    protected function casts(): array
    {
        return [
            'logged_in_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ip(): BelongsTo
    {
        return $this->belongsTo(LmpIp::class, 'lmp_ip_id');
    }
}
