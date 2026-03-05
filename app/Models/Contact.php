<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class Contact extends Model
{
    use HasFactory, AuditTrait;

    protected $fillable = [
        'account_id',
        'name',
        'contact_department_id',
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
}
