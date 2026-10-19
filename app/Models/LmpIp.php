<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Cached geolocation for one IP address. One row is shared by every login from that IP.
 */
class LmpIp extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_FAILED = 'failed';

    public const STATUS_LOCAL = 'local';

    protected $table = 'lmp_ips';

    protected $fillable = [
        'ip_address',
        'status',
        'country_code',
        'country_name',
        'region_name',
        'city',
        'latitude',
        'longitude',
        'timezone',
        'looked_up_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'looked_up_at' => 'datetime',
        ];
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(LmpSession::class);
    }

    /**
     * City, region and country, or a fallback when the lookup did not produce a place.
     */
    public function originLabel(): string
    {
        if ($this->status === self::STATUS_LOCAL) {
            return (string) __('filament.resources.user_sessions.local_network');
        }

        $parts = array_values(array_filter([
            $this->city,
            $this->region_name,
            $this->country_name,
        ], fn (mixed $part): bool => is_string($part) && $part !== ''));

        if ($parts !== []) {
            return implode(', ', $parts);
        }

        return (string) __('filament.resources.user_sessions.unavailable');
    }
}
