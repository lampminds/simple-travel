<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceActivity;
use App\Models\ServiceDetail;
use App\Models\ServiceGastronomy;
use App\Models\ServiceGastronomyExperience;
use App\Models\ServiceHotel;
use App\Models\ServiceTransfer;
use App\Models\ServiceTransferPrice;
use App\Models\ServiceTransferRoute;
use App\Models\ServiceTransferVehicle;
use App\Models\ServiceVariant;
use App\Support\ServiceCopyOptions;
use App\Support\ServiceCopySections;
use App\Support\ServiceWizardStepEight;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class ServiceCopyService
{
    /** @var array<int, int> Old variant id => new variant id */
    private array $variantIdMap = [];

    public function copy(Service $source, ServiceCopyOptions $options): Service
    {
        $source->loadMissing([
            'translations',
            'features',
            'experiences',
            'serviceDetails',
            'serviceVariants.translations',
            'serviceVariants.media',
            'media',
            'serviceType',
            'activity.activityTypes',
            'hotel.hotelTypes',
            'gastronomy.gastronomyTypes',
            'gastronomy.cuisines',
            'gastronomy.venues',
            'gastronomy.menus',
            'gastronomy.experience',
            'serviceVariants.transfer.routes',
            'serviceVariants.transfer.vehicles',
            'serviceVariants.transfer.prices',
        ]);

        return DB::transaction(function () use ($source, $options): Service {
            $this->variantIdMap = [];

            $target = $this->createTargetService($source, $options);

            if ($options->includes(ServiceCopySections::FEATURES)) {
                $target->features()->sync($source->features->pluck('id')->all());
            }

            if ($options->includes(ServiceCopySections::EXPERIENCES)) {
                $target->experiences()->sync($source->experiences->pluck('id')->all());
            }

            if ($options->includes(ServiceCopySections::DETAILS)) {
                $this->copyDetails($source, $target);
            }

            if ($options->includes(ServiceCopySections::VARIANTS)) {
                $this->copyVariants($source, $target);
            }

            if ($options->includes(ServiceCopySections::IMAGES)) {
                $this->copyServiceMedia($source, $target);
                if ($options->includes(ServiceCopySections::VARIANTS)) {
                    $this->copyVariantMedia($source);
                }
            }

            if ($options->includes(ServiceCopySections::ADVANCED)) {
                $this->copyAdvancedProfile($source, $target);
            }

            return $target->fresh([
                'translations.language.locale',
                'serviceType',
                'media',
            ]);
        });
    }

    protected function createTargetService(Service $source, ServiceCopyOptions $options): Service
    {
        $attributes = [
            'account_id' => $source->account_id,
            'service_type_id' => $source->service_type_id,
            'city_id' => $source->city_id,
            'status' => 'onhold',
            'is_featured' => false,
            'is_public' => false,
            'booking_mode' => 'instant',
            'confirmation_time_hours' => null,
        ];

        if ($options->includes(ServiceCopySections::STATUS)) {
            $attributes['status'] = $source->status;
            $attributes['is_featured'] = $source->is_featured;
            $attributes['is_public'] = $source->is_public;
            $attributes['booking_mode'] = $source->booking_mode;
            $attributes['confirmation_time_hours'] = $source->confirmation_time_hours;
        }

        $target = Service::query()->create($attributes);

        $suffix = (string) __('wizard.service_copy_name_suffix');

        if ($options->includes(ServiceCopySections::BASE)) {
            foreach ($source->translations as $translation) {
                $name = trim((string) ($translation->name ?? ''));
                if ($name !== '' && ! str_ends_with($name, $suffix)) {
                    $name .= $suffix;
                } elseif ($name === '') {
                    $name = (string) __('wizard.service_copy_default_name', ['id' => $source->id]);
                }

                $target->translations()->create([
                    'language_id' => $translation->language_id,
                    'name' => $name,
                    'slug' => null,
                    'description' => $translation->description,
                ]);
            }
        } else {
            $displayName = trim($source->name) !== ''
                ? trim($source->name).$suffix
                : (string) __('wizard.service_copy_default_name', ['id' => $source->id]);

            if ($source->translations->isEmpty()) {
                $target->translations()->create([
                    'language_id' => (int) (\App\Models\Language::query()->orderBy('id')->value('id') ?? 1),
                    'name' => $displayName,
                    'slug' => null,
                    'description' => null,
                ]);
            } else {
                foreach ($source->translations as $translation) {
                    $target->translations()->create([
                        'language_id' => $translation->language_id,
                        'name' => $displayName,
                        'slug' => null,
                        'description' => null,
                    ]);
                }
            }
        }

        return $target;
    }

    protected function copyDetails(Service $source, Service $target): void
    {
        foreach ($source->serviceDetails as $detail) {
            $row = $detail->replicate(['id', 'service_id']);
            $row->service_id = $target->id;
            $row->save();
        }
    }

    protected function copyVariants(Service $source, Service $target): void
    {
        foreach ($source->serviceVariants as $variant) {
            $payload = $variant->replicate(['id', 'service_id'])->getAttributes();
            unset($payload['id']);
            $payload['service_id'] = $target->id;
            $payload['sku'] = $this->uniqueSkuForService($target->id, (string) $variant->sku);

            $newVariant = ServiceVariant::query()->create($payload);

            $this->variantIdMap[(int) $variant->id] = (int) $newVariant->id;

            foreach ($variant->translations as $translation) {
                $newVariant->translations()->create([
                    'language_id' => $translation->language_id,
                    'name' => $translation->name,
                    'description' => $translation->description,
                ]);
            }
        }
    }

    protected function uniqueSkuForService(int $serviceId, string $originalSku): string
    {
        $originalSku = trim($originalSku);
        if ($originalSku === '') {
            $originalSku = 'variant';
        }

        $counter = 0;
        while ($counter < 1000) {
            $suffix = $counter === 0 ? '-copy' : '-copy-'.$counter;
            $candidate = $originalSku.$suffix;
            if (mb_strlen($candidate) > 255) {
                $candidate = mb_substr($originalSku, 0, max(0, 255 - mb_strlen($suffix))).$suffix;
            }

            if (! ServiceVariant::query()->where('service_id', $serviceId)->where('sku', $candidate)->exists()) {
                return $candidate;
            }

            $counter++;
        }

        return mb_substr($originalSku, 0, 200).'-copy-'.uniqid();
    }

    protected function copyServiceMedia(Service $source, Service $target): void
    {
        $this->replicateSingleMedia($source, $target, Service::MEDIA_COLLECTION_MAIN);
        $this->replicateCollectionMedia($source, $target, Service::MEDIA_COLLECTION_GALLERY);
    }

    protected function copyVariantMedia(Service $source): void
    {
        foreach ($source->serviceVariants as $variant) {
            $newVariantId = $this->variantIdMap[(int) $variant->id] ?? null;
            if ($newVariantId === null) {
                continue;
            }

            $newVariant = ServiceVariant::query()->find($newVariantId);
            if ($newVariant === null) {
                continue;
            }

            $this->replicateSingleMedia($variant, $newVariant, ServiceVariant::MEDIA_COLLECTION_MAIN);
            $this->replicateCollectionMedia($variant, $newVariant, ServiceVariant::MEDIA_COLLECTION_GALLERY);
        }
    }

    protected function replicateSingleMedia(HasMedia $source, HasMedia $target, string $collection): void
    {
        $media = $source->getFirstMedia($collection);
        if ($media instanceof Media) {
            $media->copy($target, $collection);
        }
    }

    protected function replicateCollectionMedia(HasMedia $source, HasMedia $target, string $collection): void
    {
        foreach ($source->getMedia($collection) as $media) {
            $media->copy($target, $collection);
        }
    }

    protected function copyAdvancedProfile(Service $source, Service $target): void
    {
        $code = strtolower((string) ($source->serviceType?->code ?? ''));

        if (! ServiceWizardStepEight::isEnabledForServiceTypeCode($code)) {
            return;
        }

        match ($code) {
            'activity', 'event' => $this->copyActivityProfile($source, $target),
            'accomodation' => $this->copyHotelProfile($source, $target),
            'gastronomy' => $this->copyGastronomyProfile($source, $target),
            'transfer' => $this->copyTransferProfile($source, $target),
            default => null,
        };
    }

    protected function copyActivityProfile(Service $source, Service $target): void
    {
        $row = $source->activity;
        if ($row === null) {
            return;
        }

        $profile = ServiceActivity::query()->create([
            'service_id' => $target->id,
            'guide_included' => $row->guide_included,
            'transport_included' => $row->transport_included,
            'outdoor_activity' => $row->outdoor_activity,
            'active' => $row->active,
        ]);

        $profile->activityTypes()->sync($row->activityTypes->pluck('id')->all());
    }

    protected function copyHotelProfile(Service $source, Service $target): void
    {
        $row = $source->hotel;
        if ($row === null) {
            return;
        }

        $hotel = ServiceHotel::query()->create([
            'service_id' => $target->id,
            'stars' => $row->stars,
            'check_in_time' => $row->check_in_time,
            'check_out_time' => $row->check_out_time,
        ]);

        $hotel->hotelTypes()->sync($row->hotelTypes->pluck('id')->all());
    }

    protected function copyGastronomyProfile(Service $source, Service $target): void
    {
        $row = $source->gastronomy;
        if ($row === null) {
            return;
        }

        $gastro = ServiceGastronomy::query()->create([
            'service_id' => $target->id,
            'city_id' => $row->city_id,
            'address' => $row->address,
            'latitude' => $row->latitude,
            'longitude' => $row->longitude,
            'is_indoor' => $row->is_indoor,
            'is_outdoor' => $row->is_outdoor,
            'has_takeaway' => $row->has_takeaway,
            'has_delivery' => $row->has_delivery,
        ]);

        $gastro->gastronomyTypes()->sync($row->gastronomyTypes->pluck('id')->all());
        $gastro->cuisines()->sync($row->cuisines->pluck('id')->all());
        $gastro->venues()->sync($row->venues->pluck('id')->all());
        $gastro->menus()->sync($row->menus->pluck('id')->all());

        $experience = $row->experience;
        if ($experience !== null) {
            ServiceGastronomyExperience::query()->create([
                'service_gastronomy_id' => $gastro->id,
                'duration_minutes' => $experience->duration_minutes,
                'includes_food' => $experience->includes_food,
                'includes_drinks' => $experience->includes_drinks,
                'is_guided' => $experience->is_guided,
            ]);
        }
    }

    protected function copyTransferProfile(Service $source, Service $target): void
    {
        foreach ($source->serviceVariants as $variant) {
            $row = $variant->transfer;
            if ($row === null) {
                continue;
            }

            $newVariantId = $this->variantIdMap[(int) $variant->id] ?? null;
            if ($newVariantId === null) {
                continue;
            }

            $transfer = ServiceTransfer::query()->create([
                'service_variant_id' => $newVariantId,
                'transfer_type' => $row->transfer_type,
                'modality' => $row->modality,
                'allows_multiple_stops' => $row->allows_multiple_stops,
                'operation_mode' => $row->operation_mode,
                'duration_minutes' => $row->duration_minutes,
                'default_duration_minutes' => $row->default_duration_minutes,
                'requires_flight_info' => $row->requires_flight_info,
                'requires_pickup_time' => $row->requires_pickup_time,
                'requires_dropoff_time' => $row->requires_dropoff_time,
            ]);

            /** @var array<int, int> $routeIdMap */
            $routeIdMap = [];
            foreach ($row->routes as $route) {
                $newRoute = ServiceTransferRoute::query()->create([
                    'service_transfer_id' => $transfer->id,
                    'origin_location_id' => $route->origin_location_id,
                    'destination_location_id' => $route->destination_location_id,
                    'is_active' => $route->is_active,
                    'distance_km' => $route->distance_km,
                    'duration_minutes' => $route->duration_minutes,
                ]);
                $routeIdMap[(int) $route->id] = (int) $newRoute->id;
            }

            foreach ($row->vehicles as $vehicle) {
                ServiceTransferVehicle::query()->create([
                    'service_transfer_id' => $transfer->id,
                    'service_transfer_vehicle_type_id' => $vehicle->service_transfer_vehicle_type_id,
                    'max_passengers' => $vehicle->max_passengers,
                    'max_luggage' => $vehicle->max_luggage,
                    'notes' => $vehicle->notes,
                    'is_default' => $vehicle->is_default,
                ]);
            }

            foreach ($row->prices as $price) {
                $newRouteId = null;
                if ($price->route_id !== null) {
                    $newRouteId = $routeIdMap[(int) $price->route_id] ?? null;
                }

                ServiceTransferPrice::query()->create([
                    'service_transfer_id' => $transfer->id,
                    'route_id' => $newRouteId,
                    'service_transfer_vehicle_type_id' => $price->service_transfer_vehicle_type_id,
                    'pricing_type' => $price->pricing_type,
                    'currency_id' => $price->currency_id,
                    'base_price' => $price->base_price,
                    'price_per_person' => $price->price_per_person,
                    'price_per_extra_passenger' => $price->price_per_extra_passenger,
                    'min_passengers' => $price->min_passengers,
                    'max_passengers' => $price->max_passengers,
                    'valid_from' => $price->valid_from,
                    'valid_to' => $price->valid_to,
                ]);
            }
        }
    }
}
