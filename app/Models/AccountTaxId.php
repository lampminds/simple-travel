<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class AccountTaxId extends Model
{
    use HasFactory, AuditTrait;

    protected $table = 'account_tax_ids';

    protected $fillable = [
        'account_id',
        'account_category_id',
        'value',
    ];

    /**
     * Get the account that owns this tax ID.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the tax ID category (from account_categories where group='tax_id').
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AccountCategory::class, 'account_category_id');
    }
}
