<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'email',
        'token',
        'expires_at',
        'accepted_at',
        'declined_at',
        'invited_by',
        'type',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
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
        $q = static::query()
            ->where('status', self::STATUS_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());

        if ($accountId !== null) {
            $q->where('account_id', $accountId);
        }

        $q->update(['status' => self::STATUS_EXPIRED]);
    }

    public function markRevoked(): void
    {
        $this->forceFill(['status' => self::STATUS_REVOKED])->save();
    }
}
