<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceFeatureScope extends Model
{
    use HasFactory, AuditTrait;

    protected $table = 'cat_service_feature_scopes';

    protected $fillable = [
        'service_type_id',
        'service_feature_id',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    public function serviceFeature(): BelongsTo
    {
        return $this->belongsTo(ServiceFeature::class, 'service_feature_id');
    }
}

