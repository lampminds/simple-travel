<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Pivot-style row: one person’s link to an account (staff member or managed client).
 */
class AccountPerson extends Model
{
    use AuditTrait, HasUuid;

    public const LINK_MEMBER = 'member';

    public const LINK_CLIENT = 'client';

    protected $table = 'account_person';

    protected $fillable = [
        'account_id',
        'person_id',
        'link_type',
        'contact_department_id',
        'contact_position_id',
        'is_primary',
        'is_active',
        'is_public_contact',
        'is_preferred_contact_mode',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'is_public_contact' => 'boolean',
        'is_preferred_contact_mode' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(ContactDepartment::class, 'contact_department_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(ContactPosition::class, 'contact_position_id');
    }

    public function scopeMembers($query)
    {
        return $query->where('link_type', self::LINK_MEMBER);
    }

    public function scopeClients($query)
    {
        return $query->where('link_type', self::LINK_CLIENT);
    }

    public function isClientLink(): bool
    {
        return $this->link_type === self::LINK_CLIENT;
    }

    public function isMemberLink(): bool
    {
        return $this->link_type === self::LINK_MEMBER;
    }
}
