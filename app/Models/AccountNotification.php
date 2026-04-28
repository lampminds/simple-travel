<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountNotification extends Model
{
    protected $fillable = [
        'account_id',
        'recipient_user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'read_by_user_id',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Parent account that receives the shared notification.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * User that marked notification as read (optional).
     */
    public function readByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'read_by_user_id');
    }

    /**
     * Author from audit columns (provided by lmpTimestamps / audit behavior).
     */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Intended recipient when private; null means broadcast for the whole account.
     */
    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    /**
     * Scope notifications for one account.
     */
    public function scopeForAccount(Builder $query, int $accountId): Builder
    {
        return $query->where('account_id', $accountId);
    }

    /**
     * Scope unread notifications only.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope notifications visible to one account member.
     */
    public function scopeVisibleToUser(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $visibility) use ($user): void {
            $visibility
                ->whereNull('recipient_user_id')
                ->orWhere('recipient_user_id', $user->id);
        });
    }

    /**
     * Mark as read one time (idempotent).
     */
    public function markAsReadBy(User $user): void
    {
        if ($this->read_at !== null) {
            return;
        }

        $this->forceFill([
            'read_at' => now(),
            'read_by_user_id' => $user->id,
        ])->save();
    }

    /**
     * Best-effort author label from audit relation, then payload fallback.
     */
    public function authorName(): ?string
    {
        $fromAudit = $this->createdByUser?->name;
        if (filled($fromAudit)) {
            return $fromAudit;
        }

        $fromPayload = data_get($this->data, 'created_by_user_name');
        if (filled($fromPayload)) {
            return (string) $fromPayload;
        }

        return null;
    }
}
