<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ProviderCategory extends Model
{
    use HasFactory, AuditTrait;

    protected $fillable = [
        'group',
        'code',
        'name',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get the providers that belong to this category.
     */
    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(Provider::class, 'category_provider');
    }

    /**
     * Scope to order by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope to filter by group.
     */
    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
