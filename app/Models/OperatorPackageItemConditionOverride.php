<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class OperatorPackageItemConditionOverride extends Model
{
    use AuditTrait;

    public const ACTION_APPEND_TOP = 'append_top';

    public const ACTION_APPEND_BOTTOM = 'append_bottom';

    public const ACTION_REPLACE = 'replace';

    public const ACTION_SUPPRESS = 'suppress';

    /** @var list<string> */
    public const ACTIONS = [
        self::ACTION_APPEND_TOP,
        self::ACTION_APPEND_BOTTOM,
        self::ACTION_REPLACE,
        self::ACTION_SUPPRESS,
    ];

    protected $fillable = [
        'operator_package_item_id',
        'service_detail_topic_id',
        'action',
    ];

    public function packageItem(): BelongsTo
    {
        return $this->belongsTo(OperatorPackageItem::class, 'operator_package_item_id');
    }

    public function serviceDetailTopic(): BelongsTo
    {
        return $this->belongsTo(ServiceDetailTopic::class, 'service_detail_topic_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(OperatorPackageItemConditionOverrideTranslation::class, 'operator_package_item_condition_override_id');
    }
}
