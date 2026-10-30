<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\ValidationException;
use Lampminds\Customization\Filament\LmpCustomization\Traits\AuditTrait;

class PassengerCategory extends Model
{
    use AuditTrait;

    protected $fillable = [
        'account_id',
        'name',
        'code',
        'age_min',
        'age_max',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'age_min' => 'integer',
        'age_max' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (PassengerCategory $category): void {
            $category->age_min = self::normalizeAge($category->age_min);
            $category->age_max = self::normalizeAge($category->age_max);

            $message = self::ageRangeConflictMessage(
                (int) $category->account_id,
                $category->age_min,
                $category->age_max,
                $category->exists ? (int) $category->id : null,
            );

            if ($message === null) {
                return;
            }

            throw ValidationException::withMessages([
                'age_min' => $message,
                'age_max' => $message,
            ]);
        });
    }

    public function setAgeMinAttribute(mixed $value): void
    {
        $this->attributes['age_min'] = self::normalizeAge($value);
    }

    public function setAgeMaxAttribute(mixed $value): void
    {
        $this->attributes['age_max'] = self::normalizeAge($value);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function priceRule(): HasOne
    {
        return $this->hasOne(PassengerCategoryPriceRule::class);
    }

    public static function normalizeAge(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    /**
     * Age bands of the same account must not share an age.
     * A category with no minimum and no maximum is not a band.
     * An open end extends to 0 or to the column maximum.
     */
    public static function ageRangeConflictMessage(int $accountId, ?int $ageMin, ?int $ageMax, ?int $ignoreId): ?string
    {
        if ($accountId < 1) {
            return null;
        }

        if ($ageMin !== null && $ageMax !== null && $ageMin > $ageMax) {
            return (string) __('filament.resources.passenger_category_validation.age_min_greater_than_max');
        }

        if ($ageMin === null && $ageMax === null) {
            return null;
        }

        $start = $ageMin ?? 0;
        $end = $ageMax ?? 65535;

        $others = static::query()
            ->where('account_id', $accountId)
            ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where(function ($query): void {
                $query->whereNotNull('age_min')->orWhereNotNull('age_max');
            })
            ->get(['id', 'name', 'age_min', 'age_max']);

        foreach ($others as $other) {
            $otherStart = $other->age_min ?? 0;
            $otherEnd = $other->age_max ?? 65535;

            if ($start <= $otherEnd && $otherStart <= $end) {
                return (string) __('filament.resources.passenger_category_validation.age_overlap', [
                    'category' => $other->name,
                    'range' => $other->ageRangeLabel(),
                ]);
            }
        }

        return null;
    }

    public function ageRangeLabel(): string
    {
        if ($this->age_min === null && $this->age_max === null) {
            return '—';
        }

        if ($this->age_min !== null && $this->age_max !== null) {
            return $this->age_min.'–'.$this->age_max;
        }

        if ($this->age_min !== null) {
            return $this->age_min.'+';
        }

        return '0–'.$this->age_max;
    }
}
