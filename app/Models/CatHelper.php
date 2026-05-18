<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class CatHelper extends Model
{
    use AuditTrait;

    protected $table = 'cat_helpers';

    protected $fillable = [
        'screen_code',
        'code',
        'account_type_id',
        'service_type_id',
        'active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'account_type_id' => 'integer',
            'service_type_id' => 'integer',
        ];
    }

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'account_type_id');
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CatHelperTranslation::class, 'helper_id');
    }
}
