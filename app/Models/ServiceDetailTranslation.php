<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceDetailTranslation extends Model
{
    use AuditTrait;

    protected $table = 'cat_service_detail_translations';

    protected $fillable = [
        'service_detail_id',
        'language_id',
        'name',
        'description',
    ];

    public function serviceDetail(): BelongsTo
    {
        return $this->belongsTo(ServiceDetail::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
