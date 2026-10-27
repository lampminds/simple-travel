<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * One channel value (email, phone, WhatsApp, …) for an account, typed by cat_contact_types.
 */
class AccountContactMethod extends Model
{
    use AuditTrait;

    protected $table = 'account_contact_methods';

    protected $fillable = [
        'account_id',
        'contact_type_id',
        'value',
        'is_primary',
        'is_verified',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function contactType(): BelongsTo
    {
        return $this->belongsTo(ContactType::class, 'contact_type_id');
    }
}
