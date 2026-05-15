<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class ServiceTransferVehicleType extends Model
{
    use AuditTrait;

    protected $table = 'service_transfer_vehicle_types';

    protected $fillable = [
        'account_id',
        'code',
        'service_transfer_vehicle_type_category_id',
        'sort_order',
        'name',
        'max_passengers',
        'max_luggage',
        'active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'max_passengers' => 'integer',
        'max_luggage' => 'integer',
        'active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceTransferVehicleTypeCategory::class, 'service_transfer_vehicle_type_category_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transferVehicles(): HasMany
    {
        return $this->hasMany(ServiceTransferVehicle::class, 'service_transfer_vehicle_type_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ServiceTransferPrice::class, 'service_transfer_vehicle_type_id');
    }

    /**
     * Human-readable label for transfer pricing UIs (dropdown, price tables).
     */
    public function wizardPricingVehicleLabel(): string
    {
        $name = $this->name !== '' && $this->name !== null
            ? (string) $this->name
            : (trim((string) ($this->code ?? '')) !== '' ? (string) $this->code : '#'.(string) $this->id);

        $paxPart = $this->max_passengers !== null
            ? (string) (int) $this->max_passengers.(string) __('wizard.step7_transfer_capacity_pax_suffix')
            : (string) __('wizard.step7_transfer_capacity_not_set');
        $bagPart = $this->max_luggage !== null
            ? (string) (int) $this->max_luggage.(string) __('wizard.step7_transfer_capacity_bag_suffix')
            : (string) __('wizard.step7_transfer_capacity_not_set');

        return (string) __('wizard.step7_transfer_vehicle_price_option_label', [
            'name' => $name,
            'pax' => $paxPart,
            'bag' => $bagPart,
        ]);
    }
}
