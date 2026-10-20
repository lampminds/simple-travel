<?php

namespace App\Models\Concerns;

use App\Models\ServiceFeature;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Catalogue features attached through service_featurables.
 * Pivot column inclusion is included or excluded. No row means unspecified.
 */
trait HasCatalogueFeatures
{
    public function features(): MorphToMany
    {
        return $this->morphToMany(
            ServiceFeature::class,
            'service_featurable',
            'service_featurables',
        )->withPivot('inclusion')->withTimestamps();
    }
}
