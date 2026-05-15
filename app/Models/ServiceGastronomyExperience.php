<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceGastronomyExperience extends Model
{
    use AuditTrait;

    protected $table = 'service_gastronomy_experiences';

    protected $fillable = [
        'service_gastronomy_id',
        'duration_minutes',
        'includes_food',
        'includes_drinks',
        'is_guided',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'includes_food' => 'boolean',
        'includes_drinks' => 'boolean',
        'is_guided' => 'boolean',
    ];

    public function serviceGastronomy(): BelongsTo
    {
        return $this->belongsTo(ServiceGastronomy::class, 'service_gastronomy_id');
    }
}
