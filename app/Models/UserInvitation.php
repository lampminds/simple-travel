<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Services\PendingInvitationUserCleanup;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class UserInvitation extends Model
{
    use AuditTrait;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_DECLINED = 'declined';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_REVOKED = 'revoked';

    public const TYPE_INTERNAL = 'internal';

    public const TYPE_EXTERNAL = 'external';

    protected $fillable = [
        'account_id',
        'account_inviting',
        'email',
        'name',
        'company_name',
        'role_id',
        'token',
        'send_attempts',
        'expires_at',
        'accepted_at',
        'declined_at',
        'invited_by',
        'type',
        'status',
        'invited_user_id',
        'invited_person_id',
        'invited_account_id',
    ];

    protected $casts = [
        'send_attempts' => 'integer',
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * The account that created/sent the invitation (e.g. operator when inviting a provider).
     */
    public function accountInviting(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_inviting');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function invitedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }

    public function invitedPerson(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'invited_person_id');
    }

    /**
     * Target company when the invitee is an existing platform user (external company invite).
     */
    public function invitedAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'invited_account_id');
    }

    /**
     * Commercial relationship created when this external invitation was accepted.
     */
    public function establishedRelationship(): HasOne
    {
        return $this->hasOne(AccountRelationship::class, 'source_invitation_id');
    }

    /**
     * Whether the invited company is an agency or provider (external invitations only).
     */
    public function resolveInviteeCompanyKind(): ?string
    {
        if ($this->type !== self::TYPE_EXTERNAL) {
            return null;
        }

        if ($this->invitedAccount instanceof Account) {
            return $this->companyKindFromAccount($this->invitedAccount);
        }

        $providerAccount = $this->establishedRelationship?->providerAccount;
        if ($providerAccount instanceof Account) {
            return $this->companyKindFromAccount($providerAccount);
        }

        return null;
    }

    /**
     * Pending and not past expires_at (status may still say pending until sync runs).
     */
    public function isUsable(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isFuture();
    }

    public function isExpiringSoon(int $withinHours = 48): bool
    {
        if (! $this->isUsable()) {
            return false;
        }

        return $this->expires_at->lte(now()->addHours($withinHours));
    }

    /**
     * Mark pending invitations as expired when past expires_at.
     */
    public static function syncExpiredForAccount(?int $accountId = null): void
    {
        $base = static::query()
            ->where('status', self::STATUS_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());

        if ($accountId !== null) {
            $base->where('account_id', $accountId);
        }

        $expired = (clone $base)->get();

        foreach ($expired as $invitation) {
            app(PendingInvitationUserCleanup::class)->deleteStubForInvitation($invitation);
        }

        $base->update(['status' => self::STATUS_EXPIRED]);
    }

    public function markRevoked(): void
    {
        $this->forceFill(['status' => self::STATUS_REVOKED])->save();
    }

    private function companyKindFromAccount(Account $account): ?string
    {
        $codes = $account->accountTypes
            ->where('active', true)
            ->pluck('code');

        if ($codes->contains('agency')) {
            return 'agency';
        }

        if ($codes->contains('provider')) {
            return 'provider';
        }

        return null;
    }
}
