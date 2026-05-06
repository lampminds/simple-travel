<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Pivot user ↔ person (table user_person). Uses AuditTrait so attach/create fill audit columns.
 */
class UserPerson extends Pivot
{
    use AuditTrait;

    protected $table = 'user_person';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'person_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
