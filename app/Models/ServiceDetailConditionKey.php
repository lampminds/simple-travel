<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceDetailConditionKey extends Model
{
    use AuditTrait;

    public const CATEGORIES = [
        'payment',
        'operation',
        'transport',
        'accommodation',
        'safety',
        'legal',
        'inclusions',
        'traveler',
        'service',
    ];

    protected $table = 'cat_service_detail_condition_keys';

    protected $fillable = [
        'code',
        'category',
        'description',
    ];

    public function getFullCodeAttribute(): string
    {
        return $this->category.'.'.$this->code;
    }
}
