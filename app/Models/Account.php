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
        return $this->belongsToMany(AccountCategory::class, 'account_category_assignments');
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
     * Users that belong to this account (account_user pivot).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Generate a unique code in the form [Alias]-NNN,
     * where NNN is 3 random digits from 2-9 (no zeros nor ones).
     */
    public static function generateCode(string $nick): string
    {
        $digits = '23456789';

        do {
            $nnn = '';
            for ($i = 0; $i < 3; $i++) {
                $nnn .= $digits[random_int(0, 7)];
            }
            $code = $nick . '-' . $nnn;
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            if (empty($account->code) && ! empty($account->nick)) {
                $account->code = static::generateCode($account->nick);
            }
        });
    }
}
