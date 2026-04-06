<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class Contact extends Model
{
    use HasFactory, AuditTrait;

    protected $table = 'account_contacts';

    protected $fillable = [
        'account_id',
        'name',
        'contact_department_id',
        'contact_position_id',
    ];

    /**
     * Get the account that owns the contact.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the department of the contact.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(ContactDepartment::class, 'contact_department_id');
    }

    /**
     * Get the position of the contact.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(ContactPosition::class, 'contact_position_id');
    }

    /**
     * Channel values (email, phone, etc.) per catalog contact type.
     */
    public function contactTypeAssignments(): HasMany
    {
        return $this->hasMany(ContactTypeAssignment::class);
    }
}
