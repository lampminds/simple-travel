<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

/**
 * One component line of a combo variant: parent variant -> child variant.
 *
 * Invariants (enforced by the wizard / sync layer, documented on Service):
 * - a child variant belongs to the same account, or to a supplier provider
 *   that has an accepted active offer of that variant to the buyer;
 * - the parent service is a "combo" service, the child service is not
 *   (combos cannot contain combos in v1);
 * - a supply edge cannot create a circle between providers;
 * - price comes from the parent variant only. The supplier price is an
 *   internal cost and is not added to the parent price.
 */
class ServiceComponent extends Model
{
    use AuditTrait;

    public const INCLUSION_INCLUDED = 'included';

    public const INCLUSION_OPTIONAL = 'optional';

    public const INCLUSION_UPGRADE = 'upgrade';

    protected $fillable = [
        'parent_variant_id',
        'child_variant_id',
        'quantity',
        'day_number',
        'inclusion_mode',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'day_number' => 'integer',
        'sort_order' => 'integer',
    ];

    public function parentVariant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class, 'parent_variant_id');
    }

    public function childVariant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class, 'child_variant_id');
    }

    public function parentService(): HasOneThrough
    {
        return $this->hasOneThrough(
            Service::class,
            ServiceVariant::class,
            'id',
            'id',
            'parent_variant_id',
            'service_id',
        );
    }

    public function childService(): HasOneThrough
    {
        return $this->hasOneThrough(
            Service::class,
            ServiceVariant::class,
            'id',
            'id',
            'child_variant_id',
            'service_id',
        );
    }
}
