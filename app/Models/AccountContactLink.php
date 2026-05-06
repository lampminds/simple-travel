<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * Cross-account contact reference: an account stores a link to a person coming from another account.
 */
class AccountContactLink extends Model
{
    use AuditTrait;

    protected $table = 'account_contact_links';

    protected $fillable = [
        'account_id',
        'person_id',
        'source_account_id',
        'is_favorite',
        'visibility',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'source_account_id');
    }
}
