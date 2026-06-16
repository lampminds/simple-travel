<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceAvailabilityOverride extends Model
{
    use AuditTrait;

    protected $table = 'service_availability_overrides';

    protected $fillable = [
        'service_id',
        'date',
        'end_date',
        'closed',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
        'end_date' => 'date',
        'closed' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function rangeStartDateString(): string
    {
        return $this->date->format('Y-m-d');
    }

    public function rangeEndDateString(): string
    {
        return ($this->end_date ?? $this->date)->format('Y-m-d');
    }

    public function coversDate(Carbon $date): bool
    {
        $day = $date->toDateString();

        return $day >= $this->rangeStartDateString() && $day <= $this->rangeEndDateString();
    }
}
