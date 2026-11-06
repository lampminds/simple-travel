<?php

namespace App\Filament\Support;

use App\Enums\PriceAdjustmentType;
use App\Models\PassengerCategory;
use App\Models\ProviderPassengerPolicyRule;
use App\Models\Service;
use App\Models\ServiceVariant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

final class PassengerPolicyForm
{
    /**
     * One category per row: include it, then its inclusive age band.
     *
     * @return list<Section>
     */
    public static function ageBandSections(): array
    {
        return PassengerCategory::query()
            ->ordered()
            ->with('translations.language.locale')
            ->get()
            ->map(function (PassengerCategory $category): Section {
                $prefix = 'rules.'.$category->id;

                return Section::make($category->name)
                    ->schema([
                        Toggle::make($prefix.'.included')
                            ->label(__('filament.resources.passenger_policy_fields.included'))
                            ->live(),
                        TextInput::make($prefix.'.age_min')
                            ->label(__('filament.resources.passenger_policy_fields.age_min'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(255)
                            ->default(0)
                            ->visible(fn (Get $get): bool => (bool) $get($prefix.'.included')),
                        TextInput::make($prefix.'.age_max')
                            ->label(__('filament.resources.passenger_policy_fields.age_max'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(255)
                            ->helperText(__('filament.resources.passenger_policy_fields.age_max_help'))
                            ->visible(fn (Get $get): bool => (bool) $get($prefix.'.included')),
                    ])
                    ->columns(3);
            })
            ->all();
    }

    /**
     * @return list<Select|TextInput>
     */
    public static function variantPriceFields(): array
    {
        return [
            Select::make('service_variant_id')
                ->label(__('filament.resources.passenger_policy_fields.variant'))
                ->options(function (Get $get): array {
                    $serviceId = (int) $get('../../service_id');
                    if ($serviceId < 1) {
                        return [];
                    }

                    return ServiceVariant::query()
                        ->where('service_id', $serviceId)
                        ->orderBy('sku')
                        ->pluck('sku', 'id')
                        ->all();
                })
                ->required(),
            Select::make('passenger_category_id')
                ->label(__('filament.resources.passenger_policy_fields.category'))
                ->options(function (Get $get): array {
                    $rules = $get('../../rules');
                    $ids = [];

                    if (is_array($rules)) {
                        foreach ($rules as $categoryId => $row) {
                            if (is_array($row) && ! empty($row['included'])) {
                                $ids[] = (int) $categoryId;
                            }
                        }
                    }

                    if ($ids === []) {
                        return [];
                    }

                    return PassengerCategory::query()
                        ->whereIn('id', $ids)
                        ->ordered()
                        ->with('translations.language.locale')
                        ->get()
                        ->mapWithKeys(fn (PassengerCategory $category): array => [$category->id => $category->name])
                        ->all();
                })
                ->required(),
            Select::make('adjustment_type')
                ->label(__('filament.resources.passenger_policy_fields.adjustment_type'))
                ->options(PriceAdjustmentType::options())
                ->required()
                ->live(),
            TextInput::make('adjustment_value')
                ->label(__('filament.resources.passenger_policy_fields.adjustment_value'))
                ->numeric()
                ->minValue(0)
                ->step(0.01)
                ->required(),
        ];
    }

    /**
     * Variant fares on the service form. Categories come from the selected provider policy.
     *
     * @return list<Select|TextInput>
     */
    public static function serviceVariantPriceFields(): array
    {
        return [
            Select::make('service_variant_id')
                ->label(__('filament.resources.passenger_policy_fields.variant'))
                ->options(function (?Service $record): array {
                    if (! $record instanceof Service) {
                        return [];
                    }

                    return $record->serviceVariants()
                        ->orderBy('sku')
                        ->pluck('sku', 'id')
                        ->all();
                })
                ->required(),
            Select::make('passenger_category_id')
                ->label(__('filament.resources.passenger_policy_fields.category'))
                ->options(function (Get $get): array {
                    $policyId = (int) $get('../../provider_passenger_policy_id');
                    if ($policyId < 1) {
                        return [];
                    }

                    return ProviderPassengerPolicyRule::query()
                        ->where('provider_passenger_policy_id', $policyId)
                        ->with('category.translations.language.locale')
                        ->get()
                        ->mapWithKeys(fn (ProviderPassengerPolicyRule $rule): array => [
                            $rule->passenger_category_id => $rule->category?->name ?? ('#'.$rule->passenger_category_id),
                        ])
                        ->all();
                })
                ->required(),
            Select::make('adjustment_type')
                ->label(__('filament.resources.passenger_policy_fields.adjustment_type'))
                ->options(PriceAdjustmentType::options())
                ->required()
                ->live(),
            TextInput::make('adjustment_value')
                ->label(__('filament.resources.passenger_policy_fields.adjustment_value'))
                ->numeric()
                ->minValue(0)
                ->step(0.01)
                ->required(),
        ];
    }
}
