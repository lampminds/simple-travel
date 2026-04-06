<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Row in account_contact_type_assignments: one channel value (email, phone, …) for a contact.
 */
class ContactTypeAssignment extends Model
{
    use AuditTrait;

    protected $table = 'account_contact_type_assignments';

    protected $fillable = [
        'account_id',
        'contact_id',
        'contact_type_id',
        'value',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function contactType(): BelongsTo
    {
        return $this->belongsTo(ContactType::class, 'contact_type_id');
    }
}
