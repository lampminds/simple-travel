<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class AccountPublicProfileTranslation extends Model
{
    use AuditTrait;

    protected $table = 'account_public_profile_translations';

    protected $fillable = [
        'account_public_profile_id',
        'language_id',
        'title',
        'subtitle',
        'about',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(AccountPublicProfile::class, 'account_public_profile_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
