<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class AccountTypeTranslation extends Model
{
    use AuditTrait;

    protected $table = 'cat_account_type_translations';

    protected $fillable = [
        'account_type_id',
        'language_id',
        'name',
        'description',
    ];

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
