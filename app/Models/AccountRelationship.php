<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class AccountRelationship extends Model
{
    use AuditTrait;

    public const STATUS_APPROVED = 'approved';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_TERMINATED = 'terminated';

    public const CREATED_VIA_INVITATION = 'invitation';
    public const CREATED_VIA_MANUAL = 'manual';
    public const CREATED_VIA_SYSTEM = 'system';

    protected $table = 'account_relationships';

    protected $fillable = [
        'operator_account_id',
        'provider_account_id',
        'status',
        'created_via',
        'source_invitation_id',
        'approved_by_user_id',
        'approved_at',
        'suspended_at',
        'terminated_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'suspended_at' => 'datetime',
        'terminated_at' => 'datetime',
    ];

    public function operatorAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'operator_account_id');
    }

    public function providerAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'provider_account_id');
    }

    public function sourceInvitation(): BelongsTo
    {
        return $this->belongsTo(UserInvitation::class, 'source_invitation_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}

