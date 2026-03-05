<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class Account extends Model
{
    use HasFactory, AuditTrait;

    protected $fillable = [
        'nick',
        'code',
        'name',
        'commercial_name',
        'email',
        'phone',
        'address_line1',
        'address_line2',
        'city_id',
        'postal_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the city that belongs to the account.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(LmpCity::class);
    }

    /**
     * Get the categories that belong to the account.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(AccountCategory::class, 'account_category');
    }

    /**
     * Get the tax IDs that belong to the account.
     */
    public function taxIds(): HasMany
    {
        return $this->hasMany(AccountTaxId::class);
    }

    /**
     * Get the clients that belong to the account.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(\App\Models\Client::class);
    }

    /**
     * Get the users that belong to the account.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Generate a unique code based on nick and random characters.
     */
    public static function generateCode(string $nick): string
    {
        $randomChars = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5));
        return $nick . $randomChars;
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            if (empty($account->code)) {
                $account->code = static::generateCode($account->nick);
            }
        });
    }
}
