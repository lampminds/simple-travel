<?php

namespace App\Services;

use App\Models\Media;
use App\Models\Service;
use App\Models\ServiceVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

/**
 * Counts and deletes a {@see Service} and all dependent rows that are not covered by DB ON DELETE CASCADE.
 */
final class ServiceCascadeDeletion
{
    /**
     * @return array{lines: list<array{label: string, count: int}>, related_total: int, grand_total: int}
     */
    public static function impactSummary(Service $service): array
    {
        $serviceId = (int) $service->id;
        $variantIds = self::variantIds($serviceId);

        $lines = [];
        $add = static function (string $labelKey, int $count) use (&$lines): void {
            if ($count > 0) {
                $lines[] = [
                    'label' => __($labelKey),
                    'count' => $count,
                ];
            }
        };

        $add('filament.resources.service_delete_cascade.labels.translations', (int) DB::table('service_translations')->where('service_id', $serviceId)->count());
        $add('filament.resources.service_delete_cascade.labels.experience_assignments', (int) DB::table('service_experience_assignments')->where('service_id', $serviceId)->count());
        $add('filament.resources.service_delete_cascade.labels.details', (int) DB::table('cat_service_details')->where('service_id', $serviceId)->count());
        $add('filament.resources.service_delete_cascade.labels.feature_links', (int) DB::table('service_featurables')
            ->where('service_featurable_type', Service::class)
            ->where('service_featurable_id', $serviceId)
            ->count());

        $variantCount = $variantIds->count();
        $add('filament.resources.service_delete_cascade.labels.variants', $variantCount);

        if ($variantIds->isNotEmpty()) {
            $add('filament.resources.service_delete_cascade.labels.variant_translations', (int) DB::table('service_variant_translations')->whereIn('service_variant_id', $variantIds)->count());
            $add('filament.resources.service_delete_cascade.labels.variant_availability_rules', (int) DB::table('service_variant_availability_rules')->whereIn('service_variant_id', $variantIds)->count());
            $add('filament.resources.service_delete_cascade.labels.variant_availability_overrides', (int) DB::table('service_variant_availability_overrides')->whereIn('service_variant_id', $variantIds)->count());
            $add('filament.resources.service_delete_cascade.labels.allocations', (int) DB::table('allocations')->whereIn('service_variant_id', $variantIds)->count());
        }

        $add('filament.resources.service_delete_cascade.labels.price_list_items', (int) DB::table('provider_price_list_items')
            ->where(function ($q) use ($serviceId, $variantIds): void {
                $q->where('service_id', $serviceId);
                if ($variantIds->isNotEmpty()) {
                    $q->orWhereIn('service_variant_id', $variantIds);
                }
            })
            ->count());

        $add('filament.resources.service_delete_cascade.labels.service_offers', self::countServiceOffers($serviceId, $variantIds));
        $add('filament.resources.service_delete_cascade.labels.operator_catalog_items', self::countOperatorCatalogItems($serviceId, $variantIds));

        $serviceMorph = $service->getMorphClass();
        $variantMorph = (new ServiceVariant)->getMorphClass();
        $mediaCount = (int) Media::query()->where('model_type', $serviceMorph)->where('model_id', $serviceId)->count();
        if ($variantIds->isNotEmpty()) {
            $mediaCount += (int) Media::query()->where('model_type', $variantMorph)->whereIn('model_id', $variantIds)->count();
        }
        $add('filament.resources.service_delete_cascade.labels.media_files', $mediaCount);

        $hotelIds = DB::table('service_hotels')->where('service_id', $serviceId)->pluck('id');
        $add('filament.resources.service_delete_cascade.labels.hotel_type_assignments', $hotelIds->isEmpty() ? 0 : (int) DB::table('service_hotel_type_assignments')->whereIn('service_hotel_id', $hotelIds)->count());
        $add('filament.resources.service_delete_cascade.labels.service_hotels', (int) DB::table('service_hotels')->where('service_id', $serviceId)->count());
        $add('filament.resources.service_delete_cascade.labels.service_activity', (int) DB::table('service_activities')->where('service_id', $serviceId)->count());

        foreach (self::gastronomyCounts($serviceId) as $labelKey => $count) {
            $add($labelKey, $count);
        }

        foreach (self::transferCounts($serviceId) as $labelKey => $count) {
            $add($labelKey, $count);
        }

        $relatedTotal = 0;
        foreach ($lines as $row) {
            $relatedTotal += $row['count'];
        }

        return [
            'lines' => $lines,
            'related_total' => $relatedTotal,
            'grand_total' => $relatedTotal + 1,
        ];
    }

    public static function modalDescriptionHtml(Service $service): HtmlString
    {
        $summary = self::impactSummary($service);
        $parts = ['<p class="text-sm text-gray-600 dark:text-gray-400">'.e(__('filament.resources.service_delete_cascade.modal_intro')).'</p>'];

        if ($summary['lines'] !== []) {
            $parts[] = '<ul class="mt-2 list-disc space-y-1 ps-5 text-sm">';
            foreach ($summary['lines'] as $row) {
                $parts[] = '<li>'.e($row['label']).': <strong>'.number_format($row['count']).'</strong></li>';
            }
            $parts[] = '</ul>';
        }

        $parts[] = '<p class="mt-3 text-sm font-semibold">'.e(__('filament.resources.service_delete_cascade.grand_total', ['count' => number_format($summary['grand_total'])])).'</p>';

        return new HtmlString(implode('', $parts));
    }

    public static function delete(Service $service): void
    {
        $serviceId = (int) $service->id;

        DB::transaction(function () use ($service, $serviceId): void {
            $variantIds = self::variantIds($serviceId);

            DB::table('provider_price_list_items')
                ->where(function ($q) use ($serviceId, $variantIds): void {
                    $q->where('service_id', $serviceId);
                    if ($variantIds->isNotEmpty()) {
                        $q->orWhereIn('service_variant_id', $variantIds);
                    }
                })
                ->delete();

            if ($variantIds->isNotEmpty()) {
                DB::table('allocations')->whereIn('service_variant_id', $variantIds)->delete();
                DB::table('service_variant_availability_overrides')->whereIn('service_variant_id', $variantIds)->delete();
                DB::table('service_variant_availability_rules')->whereIn('service_variant_id', $variantIds)->delete();
            }

            self::deleteOperatorCatalogItems($serviceId, $variantIds);
            self::deleteServiceOffers($serviceId, $variantIds);

            if ($variantIds->isNotEmpty()) {
                DB::table('service_variant_translations')->whereIn('service_variant_id', $variantIds)->delete();
            }

            $service->loadMissing('serviceVariants');
            $service->clearMediaCollection(Service::MEDIA_COLLECTION_MAIN);
            $service->clearMediaCollection(Service::MEDIA_COLLECTION_GALLERY);
            foreach ($service->serviceVariants as $variant) {
                $variant->clearMediaCollection(ServiceVariant::MEDIA_COLLECTION_MAIN);
                $variant->clearMediaCollection(ServiceVariant::MEDIA_COLLECTION_GALLERY);
            }

            if ($variantIds->isNotEmpty()) {
                DB::table('service_variants')->where('service_id', $serviceId)->delete();
            }

            self::deleteTransferSubtree($serviceId);
            self::deleteGastronomySubtree($serviceId);

            $saIds = DB::table('service_activities')->where('service_id', $serviceId)->pluck('id');
            if ($saIds->isNotEmpty()) {
                DB::table('service_activity_type_assignments')->whereIn('service_activity_id', $saIds)->delete();
            }
            $hotelIds = DB::table('service_hotels')->where('service_id', $serviceId)->pluck('id');
            if ($hotelIds->isNotEmpty()) {
                DB::table('service_hotel_type_assignments')->whereIn('service_hotel_id', $hotelIds)->delete();
            }
            DB::table('service_hotels')->where('service_id', $serviceId)->delete();
            DB::table('service_activities')->where('service_id', $serviceId)->delete();

            DB::table('cat_service_details')->where('service_id', $serviceId)->delete();
            DB::table('service_experience_assignments')->where('service_id', $serviceId)->delete();
            DB::table('service_featurables')
                ->where('service_featurable_type', Service::class)
                ->where('service_featurable_id', $serviceId)
                ->delete();
            DB::table('service_translations')->where('service_id', $serviceId)->delete();

            $service->delete();
        });
    }

    /**
     * @return Collection<int, int>
     */
    private static function variantIds(int $serviceId): Collection
    {
        return DB::table('service_variants')->where('service_id', $serviceId)->pluck('id');
    }

    /**
     * @param  Collection<int, int>  $variantIds
     */
    private static function countServiceOffers(int $serviceId, Collection $variantIds): int
    {
        return (int) DB::table('service_offers')
            ->where(function ($q) use ($serviceId, $variantIds): void {
                $q->where('service_id', $serviceId);
                if ($variantIds->isNotEmpty()) {
                    $q->orWhereIn('service_variant_id', $variantIds);
                }
            })
            ->count();
    }

    /**
     * @param  Collection<int, int>  $variantIds
     */
    private static function countOperatorCatalogItems(int $serviceId, Collection $variantIds): int
    {
        return (int) DB::table('operator_catalog_items')
            ->where(function ($q) use ($serviceId, $variantIds): void {
                $q->where('service_id', $serviceId);
                if ($variantIds->isNotEmpty()) {
                    $q->orWhereIn('service_variant_id', $variantIds);
                }
            })
            ->count();
    }

    /**
     * @param  Collection<int, int>  $variantIds
     */
    private static function deleteServiceOffers(int $serviceId, Collection $variantIds): void
    {
        DB::table('service_offers')
            ->where(function ($q) use ($serviceId, $variantIds): void {
                $q->where('service_id', $serviceId);
                if ($variantIds->isNotEmpty()) {
                    $q->orWhereIn('service_variant_id', $variantIds);
                }
            })
            ->delete();
    }

    /**
     * @param  Collection<int, int>  $variantIds
     */
    private static function deleteOperatorCatalogItems(int $serviceId, Collection $variantIds): void
    {
        DB::table('operator_catalog_items')
            ->where(function ($q) use ($serviceId, $variantIds): void {
                $q->where('service_id', $serviceId);
                if ($variantIds->isNotEmpty()) {
                    $q->orWhereIn('service_variant_id', $variantIds);
                }
            })
            ->delete();
    }

    /**
     * @return array<string, int> translation key => count
     */
    private static function gastronomyCounts(int $serviceId): array
    {
        $gastroIds = DB::table('service_gastronomies')->where('service_id', $serviceId)->pluck('id');
        if ($gastroIds->isEmpty()) {
            return [];
        }

        return [
            'filament.resources.service_delete_cascade.labels.gastronomy_menu_assignments' => (int) DB::table('service_gastronomy_menu_assignments')->whereIn('service_gastronomy_id', $gastroIds)->count(),
            'filament.resources.service_delete_cascade.labels.gastronomy_venue_assignments' => (int) DB::table('service_gastronomy_venue_assignments')->whereIn('service_gastronomy_id', $gastroIds)->count(),
            'filament.resources.service_delete_cascade.labels.gastronomy_experiences' => (int) DB::table('service_gastronomy_experiences')->whereIn('service_gastronomy_id', $gastroIds)->count(),
            'filament.resources.service_delete_cascade.labels.gastronomy_schedules' => (int) DB::table('service_gastronomy_schedules')->whereIn('service_gastronomy_id', $gastroIds)->count(),
            'filament.resources.service_delete_cascade.labels.gastronomy_capacities' => (int) DB::table('service_gastronomy_capacities')->whereIn('service_gastronomy_id', $gastroIds)->count(),
            'filament.resources.service_delete_cascade.labels.cuisine_gastronomy_assignments' => (int) DB::table('service_cuisine_gastronomy_assignments')->whereIn('service_gastronomy_id', $gastroIds)->count(),
            'filament.resources.service_delete_cascade.labels.gastronomy_type_assignments' => (int) DB::table('service_gastronomy_type_assignments')->whereIn('service_gastronomy_id', $gastroIds)->count(),
            'filament.resources.service_delete_cascade.labels.service_gastronomies' => (int) $gastroIds->count(),
        ];
    }

    private static function deleteGastronomySubtree(int $serviceId): void
    {
        $gastroIds = DB::table('service_gastronomies')->where('service_id', $serviceId)->pluck('id');
        if ($gastroIds->isEmpty()) {
            return;
        }

        DB::table('service_gastronomy_menu_assignments')->whereIn('service_gastronomy_id', $gastroIds)->delete();
        DB::table('service_gastronomy_venue_assignments')->whereIn('service_gastronomy_id', $gastroIds)->delete();
        DB::table('service_gastronomy_experiences')->whereIn('service_gastronomy_id', $gastroIds)->delete();
        DB::table('service_gastronomy_schedules')->whereIn('service_gastronomy_id', $gastroIds)->delete();
        DB::table('service_gastronomy_capacities')->whereIn('service_gastronomy_id', $gastroIds)->delete();
        DB::table('service_cuisine_gastronomy_assignments')->whereIn('service_gastronomy_id', $gastroIds)->delete();
        DB::table('service_gastronomy_type_assignments')->whereIn('service_gastronomy_id', $gastroIds)->delete();
        DB::table('service_gastronomies')->where('service_id', $serviceId)->delete();
    }

    /**
     * @return array<string, int>
     */
    private static function transferCounts(int $serviceId): array
    {
        $transferIds = DB::table('service_transfers')->where('service_id', $serviceId)->pluck('id');
        if ($transferIds->isEmpty()) {
            return [];
        }

        return [
            'filament.resources.service_delete_cascade.labels.transfer_routes' => (int) DB::table('service_transfer_routes')->whereIn('service_transfer_id', $transferIds)->count(),
            'filament.resources.service_delete_cascade.labels.transfer_vehicles' => (int) DB::table('service_transfer_vehicles')->whereIn('service_transfer_id', $transferIds)->count(),
            'filament.resources.service_delete_cascade.labels.transfer_prices' => (int) DB::table('service_transfer_prices')->whereIn('service_transfer_id', $transferIds)->count(),
            'filament.resources.service_delete_cascade.labels.service_transfers' => (int) $transferIds->count(),
        ];
    }

    private static function deleteTransferSubtree(int $serviceId): void
    {
        $transferIds = DB::table('service_transfers')->where('service_id', $serviceId)->pluck('id');
        if ($transferIds->isEmpty()) {
            return;
        }

        DB::table('service_transfer_routes')->whereIn('service_transfer_id', $transferIds)->delete();
        DB::table('service_transfer_vehicles')->whereIn('service_transfer_id', $transferIds)->delete();
        DB::table('service_transfer_prices')->whereIn('service_transfer_id', $transferIds)->delete();
        DB::table('service_transfers')->where('service_id', $serviceId)->delete();
    }
}
