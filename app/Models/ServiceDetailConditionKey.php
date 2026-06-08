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

    public const CONSOLIDATION_DEDUPLICATE = 'deduplicate';

    public const CONSOLIDATION_CONFLICT_CHECK = 'conflict_check';

    public const CONSOLIDATION_MOST_RESTRICTIVE = 'most_restrictive';

    public const CONSOLIDATION_SHOW_ALL = 'show_all';

    protected $fillable = [
        'code',
        'category',
        'description',
        'consolidation_mode',
    ];

    public function getFullCodeAttribute(): string
    {
        return $this->category.'.'.$this->code;
    }
}
