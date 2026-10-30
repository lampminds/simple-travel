<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class PassengerCategoryPriceRule extends Model
{
    use AuditTrait;

    public const TYPE_REGULAR = 'regular';

    public const TYPE_PERCENTAGE_DISCOUNT = 'percentage_discount';

    public const TYPE_PERCENTAGE_SURCHARGE = 'percentage_surcharge';

    public const TYPE_FIXED_DISCOUNT = 'fixed_discount';

    public const TYPE_FIXED_SURCHARGE = 'fixed_surcharge';

    public const TYPE_FIXED_PRICE = 'fixed_price';

    /**
     * @var list<string>
     */
    public const FIXED_AMOUNT_TYPES = [
        self::TYPE_FIXED_DISCOUNT,
        self::TYPE_FIXED_SURCHARGE,
        self::TYPE_FIXED_PRICE,
    ];

    /**
     * @var list<string>
     */
    public const PERCENTAGE_TYPES = [
        self::TYPE_PERCENTAGE_DISCOUNT,
        self::TYPE_PERCENTAGE_SURCHARGE,
    ];

    protected $fillable = [
        'passenger_category_id',
        'adjustment_type',
        'adjustment_value',
        'currency_id',
        'is_active',
    ];

    protected $casts = [
        'adjustment_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (PassengerCategoryPriceRule $rule): void {
            if ($rule->adjustment_type === self::TYPE_REGULAR) {
                $rule->adjustment_value = 0;
                $rule->currency_id = null;

                return;
            }

            if (! $rule->usesFixedAmount()) {
                $rule->currency_id = null;

                return;
            }

            if ($rule->currency_id === null || $rule->currency_id === '') {
                throw ValidationException::withMessages([
                    'currency_id' => (string) __('filament.resources.passenger_category_price_rule_validation.currency_required'),
                ]);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PassengerCategory::class, 'passenger_category_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function usesFixedAmount(): bool
    {
        return in_array($this->adjustment_type, self::FIXED_AMOUNT_TYPES, true);
    }

    /**
     * @return array<string, string>
     */
    public static function adjustmentTypeOptions(): array
    {
        $options = [];

        foreach ([
            self::TYPE_REGULAR,
            self::TYPE_PERCENTAGE_DISCOUNT,
            self::TYPE_PERCENTAGE_SURCHARGE,
            self::TYPE_FIXED_DISCOUNT,
            self::TYPE_FIXED_SURCHARGE,
            self::TYPE_FIXED_PRICE,
        ] as $type) {
            $options[$type] = (string) __('filament.resources.passenger_category_price_rule_adjustment_type.'.$type);
        }

        return $options;
    }
}
