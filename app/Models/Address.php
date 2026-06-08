<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class Address extends Model
{
    use AuditTrait, HasUuid;

    protected $fillable = [
        'address_line_1',
        'address_line_2',
        'city_id',
        'city',
        'state_id',
        'state',
        'postal_code',
        'country_id',
    ];

    protected $casts = [
        'city_id' => 'integer',
        'state_id' => 'integer',
        'country_id' => 'integer',
    ];

    public function cityRelation(): BelongsTo
    {
        return $this->belongsTo(LmpCity::class, 'city_id');
    }
}
