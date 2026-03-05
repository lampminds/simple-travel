<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Models\User as BaseUser;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Application User model. Extends the package User to add project-specific
 * fields (account_id) and the account() relationship.
 */
class User extends BaseUser
{
    use HasFactory, LogsActivity;

    /** No created_by/updated_by columns on users table; skip audit section in LmpResource form. */
    protected $dont_use_audit = true;

    /**
     * Mass assignable attributes (parent fillable + account_id). Set before parent::__construct so mass assignment works.
     *
     * @var list<string>
     */
    protected $fillable = [];

    public function __construct(array $attributes = [])
    {
        $this->fillable = array_merge(BaseUser::getFillableAttributes(), ['account_id', 'password']);
        parent::__construct($attributes);
    }

    /**
     * Get the account this user belongs to.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
