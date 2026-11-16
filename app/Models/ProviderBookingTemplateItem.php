<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ProviderBookingTemplateItem extends Model
{
    use AuditTrait;

    protected $fillable = [
        'provider_booking_template_id',
        'scope',
        'input_type',
        'is_required',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(ProviderBookingTemplate::class, 'provider_booking_template_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProviderBookingTemplateItemTranslation::class);
    }

    public function replicatedField(): HasOne
    {
        return $this->hasOne(ProviderBookingField::class, 'source_template_item_id');
    }

    public function scopeLabel(): string
    {
        return (string) __('account.booking_fields.scope_'.$this->scope);
    }

    public function inputTypeLabel(): string
    {
        return (string) __('account.booking_fields.type_'.$this->input_type);
    }
}
