<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceUserPriceTranslation extends Model
{
    use AuditTrait;

    protected $fillable = [
        'service_user_price_id',
        'language_id',
        'price',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function serviceUserPrice(): BelongsTo
    {
        return $this->belongsTo(ServiceUserPrice::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
