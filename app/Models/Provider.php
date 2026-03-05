<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class Provider extends Model
{
    use HasFactory, AuditTrait;

    protected $fillable = [
        'name',
        'commercial_name',
        'email',
        'phone',
        'address_line1',
        'address_line2',
        'city_id',
        'postal_code',
        'status',
        'inviting_id',
    ];

    /**
     * Get the account that invited this provider (if any).
     */
    public function inviting(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'inviting_id');
    }

    /**
     * Get the city that belongs to the provider.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(LmpCity::class);
    }

    /**
     * Get the categories that belong to the provider.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProviderCategory::class, 'category_provider');
    }
}
