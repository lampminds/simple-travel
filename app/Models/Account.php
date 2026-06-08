<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class Account extends Model
{
    use HasFactory, AuditTrait, HasUuid;

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
     * Business types assigned to this account ({@see AccountType}, pivot account_type_assignments).
     */
    public function accountTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            AccountType::class,
            'account_type_assignments',
            'account_id',
            'account_type_id'
        );
    }

    /**
     * Documents stored for this account (e.g. tax IDs, group tax_id in cat_documents).
     */
    public function documents(): HasMany
    {
        return $this->hasMany(AccountDocument::class);
    }

    /**
     * Company clients (organizations) owned by this agency account.
     */
    public function clientOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'agency_id');
    }

    /**
     * Individual person clients managed by this account (account_person with link_type client).
     */
    public function clientPersons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'account_person')
            ->wherePivot('link_type', AccountPerson::LINK_CLIENT)
            ->withPivot([
                'link_type',
                'is_active',
            ])
            ->withTimestamps();
    }

    /**
     * Users that belong to this account (account_user pivot).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Notifications shared at account level (read once for all account members).
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(AccountNotification::class);
    }

    /**
     * Commercial SaaS subscriptions purchased by this account.
     */
    public function commercialSubscriptions(): HasMany
    {
        return $this->hasMany(CommercialSubscription::class);
    }

    /**
     * Persons linked to this account with department/position (account_person).
     */
    public function accountPersons(): HasMany
    {
        return $this->hasMany(AccountPerson::class);
    }

    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'account_person')
            ->withPivot([
                'link_type',
                'contact_department_id',
                'contact_position_id',
                'is_primary',
                'is_active',
                'is_public_contact',
                'is_preferred_contact_mode',
            ])
            ->withTimestamps();
    }

    /**
     * Staff / member persons linked to this account (excludes client links).
     */
    public function memberPersons(): BelongsToMany
    {
        return $this->persons()->wherePivot('link_type', AccountPerson::LINK_MEMBER);
    }

    /**
     * Contact links this account stores (person from another account).
     */
    public function accountContactLinks(): HasMany
    {
        return $this->hasMany(AccountContactLink::class);
    }

    /**
     * How many of provider, operator, and agency (active business types) are assigned to this account.
     */
    public function businessDashboardLaneCount(): int
    {
        return $this->businessDashboardLaneInfo()['count'];
    }

    /**
     * @return array{count: int, hasProvider: bool, hasOperator: bool, hasAgency: bool}
     */
    protected function businessDashboardLaneInfo(): array
    {
        $typeCodes = $this->accountTypes()
            ->where((new AccountType)->getTable().'.active', true)
            ->pluck((new AccountType)->getTable().'.code');

        $hasProvider = $typeCodes->contains('provider');
        $hasOperator = $typeCodes->contains('operator');
        $hasAgency = $typeCodes->contains('agency');

        return [
            'count' => (int) $hasProvider + (int) $hasOperator + (int) $hasAgency,
            'hasProvider' => $hasProvider,
            'hasOperator' => $hasOperator,
            'hasAgency' => $hasAgency,
        ];
    }

    /**
     * When this account maps to exactly one frontend dashboard lane (provider, operator, or agency), return that route name.
     * Operator accounts map to the operator dashboard.
     */
    public function soleDashboardRouteName(): ?string
    {
        $i = $this->businessDashboardLaneInfo();

        if ($i['count'] !== 1) {
            return null;
        }

        if ($i['hasProvider']) {
            return 'provider.dashboard';
        }

        if ($i['hasOperator']) {
            return 'operator.dashboard';
        }

        return 'agency.dashboard';
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
