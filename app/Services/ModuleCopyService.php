<?php

namespace App\Services;

use App\Models\Module;
use Illuminate\Support\Facades\DB;

final class ModuleCopyService
{
    /**
     * Duplicates a catalog module and its configuration (translations, account types,
     * features, feature translations, prices, and price tiers).
     *
     * Does not copy plan assignments or subscription links.
     */
    public function copy(Module $source, string $code): Module
    {
        $code = trim($code);

        if ($code === '') {
            throw new \InvalidArgumentException(__('filament.resources.module_actions.copy_code_required'));
        }

        if (Module::query()->where('code', $code)->exists()) {
            throw new \InvalidArgumentException(__('filament.resources.module_actions.copy_code_exists'));
        }

        $source->load([
            'translations',
            'accountTypes',
            'features.translations',
            'commercialModulePrices.tiers',
        ]);

        return DB::transaction(function () use ($source, $code): Module {
            $newModule = Module::query()->create([
                'code' => $code,
                'active' => $source->active,
                'sort_order' => $source->sort_order,
            ]);

            foreach ($source->translations as $translation) {
                $newModule->translations()->create([
                    'language_id' => $translation->language_id,
                    'name' => $translation->name,
                    'description' => $translation->description,
                ]);
            }

            $newModule->accountTypes()->sync(
                $source->accountTypes->pluck('id')->all(),
            );

            foreach ($source->features as $feature) {
                $newFeature = $newModule->features()->create([
                    'sort_order' => $feature->sort_order,
                    'active' => $feature->active,
                ]);

                foreach ($feature->translations as $featureTranslation) {
                    $newFeature->translations()->create([
                        'language_id' => $featureTranslation->language_id,
                        'text' => $featureTranslation->text,
                    ]);
                }
            }

            foreach ($source->commercialModulePrices as $price) {
                $newPrice = $newModule->commercialModulePrices()->create([
                    'billing_type' => $price->billing_type,
                    'base_price' => $price->base_price,
                    'included_users' => $price->included_users,
                    'price_per_user' => $price->price_per_user,
                    'active' => $price->active,
                ]);

                foreach ($price->tiers as $tier) {
                    $newPrice->tiers()->create([
                        'from_users' => $tier->from_users,
                        'to_users' => $tier->to_users,
                        'price_per_user' => $tier->price_per_user,
                    ]);
                }
            }

            return $newModule;
        });
    }
}
