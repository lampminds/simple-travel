<?php

namespace Database\Seeders;

use Database\Seeders\Support\SeederUuid;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds global transfer vehicle type categories (with translations) and system catalog vehicle types (account_id null).
 *
 * Category labels use the translation table with English, Spanish, and Portuguese names.
 */
class ServiceTransferVehicleTypesCatalogSeeder extends Seeder
{

    /** cat_languages.id for en-US, es-AR, pt-BR (see CatLanguagesTableSeeder + CatLocalesTableSeeder). */
    private const LANGUAGE_IDS = [1, 2, 3];

    /**
     * @return array<int, string>
     */
    private function labelsForLanguage(array $row, int $languageId): string
    {
        return match ($languageId) {
            1 => (string) $row['label_en'],
            2 => (string) $row['label_es'],
            3 => (string) $row['label_pt'],
            default => (string) $row['label_en'],
        };
    }

    public function run(): void
    {
        $categories = [
            [
                'code' => 'small_vehicles',
                'sort_order' => 10,
                'label_en' => 'Small vehicles',
                'label_es' => 'Vehículos pequeños',
                'label_pt' => 'Veículos pequenos',
            ],
            [
                'code' => 'suvs_4x4',
                'sort_order' => 20,
                'label_en' => 'SUVs / 4x4',
                'label_es' => 'SUVs / 4x4',
                'label_pt' => 'SUVs / 4x4',
            ],
            [
                'code' => 'vans',
                'sort_order' => 30,
                'label_en' => 'Vans',
                'label_es' => 'Vans',
                'label_pt' => 'Vans',
            ],
            [
                'code' => 'buses',
                'sort_order' => 40,
                'label_en' => 'Buses',
                'label_es' => 'Autobuses',
                'label_pt' => 'Ônibus',
            ],
            [
                'code' => 'premium_special',
                'sort_order' => 50,
                'label_en' => 'Premium & special transport',
                'label_es' => 'Transporte premium y especial',
                'label_pt' => 'Transporte premium e especial',
            ],
            [
                'code' => 'outdoor_touristic',
                'sort_order' => 60,
                'label_en' => 'Outdoor & touristic transport',
                'label_es' => 'Transporte outdoor y turístico',
                'label_pt' => 'Transporte outdoor e turístico',
            ],
            [
                'code' => 'watercraft',
                'sort_order' => 70,
                'label_en' => 'Watercraft',
                'label_es' => 'Embarcaciones',
                'label_pt' => 'Embarcações',
            ],
            [
                'code' => 'aircraft',
                'sort_order' => 80,
                'label_en' => 'Aircraft',
                'label_es' => 'Aeronaves',
                'label_pt' => 'Aeronaves',
            ],
            [
                'code' => 'rail',
                'sort_order' => 90,
                'label_en' => 'Rail',
                'label_es' => 'Ferrocarril',
                'label_pt' => 'Ferrovia',
            ],
            [
                'code' => 'special',
                'sort_order' => 100,
                'label_en' => 'Special',
                'label_es' => 'Especial',
                'label_pt' => 'Especial',
            ],
        ];

        foreach ($categories as $row) {
            DB::table('cat_service_transfer_vehicle_type_categories')->updateOrInsert(
                ['code' => $row['code']],
                [
                    'sort_order' => $row['sort_order'],
                    'active' => true,
                ]
            );
        }

        $categoryIds = DB::table('cat_service_transfer_vehicle_type_categories')
            ->whereIn('code', array_column($categories, 'code'))
            ->pluck('id', 'code')
            ->all();

        DB::table('cat_service_transfer_vehicle_type_category_translations')
            ->whereIn('service_transfer_vehicle_type_category_id', array_values($categoryIds))
            ->delete();

        $translationRows = [];
        foreach ($categories as $row) {
            $cid = $categoryIds[$row['code']] ?? null;
            if ($cid === null) {
                continue;
            }
            foreach (self::LANGUAGE_IDS as $languageId) {
                $translationRows[] = [
                    'service_transfer_vehicle_type_category_id' => $cid,
                    'language_id' => $languageId,
                    'name' => $this->labelsForLanguage($row, $languageId),
                ];
            }
        }
        DB::table('cat_service_transfer_vehicle_type_category_translations')->insert($translationRows);

        $types = $this->vehicleTypeDefinitions($categoryIds);

        foreach ($types as $t) {
            $where = [
                'account_id' => null,
                'code' => $t['code'],
            ];
            $values = SeederUuid::forUpdateOrInsert('service_transfer_vehicle_types', $where, [
                'name' => $t['name'],
                'service_transfer_vehicle_type_category_id' => $t['category_id'],
                'sort_order' => $t['sort_order'],
                'max_passengers' => $t['max_passengers'],
                'max_luggage' => $t['max_luggage'],
                'active' => true,
            ]);

            DB::table('service_transfer_vehicle_types')->updateOrInsert($where, $values);
        }
    }

    /**
     * @param  array<string, int>  $categoryIds  code => id
     * @return array<int, array{code: string, name: string, category_id: int, sort_order: int, max_passengers: int, max_luggage: null}>
     */
    private function vehicleTypeDefinitions(array $categoryIds): array
    {
        $c = static fn (string $code): int => $categoryIds[$code]
            ?? throw new \InvalidArgumentException("Missing category id for code: {$code}");

        $rows = [];
        $order = 0;

        $add = function (string $categoryCode, array $items) use (&$rows, &$order, $c): void {
            foreach ($items as $code => $meta) {
                $order++;
                $rows[] = [
                    'code' => $code,
                    'name' => $meta['name'],
                    'category_id' => $c($categoryCode),
                    'sort_order' => $order,
                    'max_passengers' => $meta['max_passengers'],
                    'max_luggage' => null,
                ];
            }
        };

        $add('small_vehicles', [
            'motorcycle' => ['name' => 'Motorcycle', 'max_passengers' => 2],
            'scooter' => ['name' => 'Scooter', 'max_passengers' => 2],
            'compact' => ['name' => 'Compact Car', 'max_passengers' => 4],
            'sedan' => ['name' => 'Sedan', 'max_passengers' => 4],
            'executive_sedan' => ['name' => 'Executive Sedan', 'max_passengers' => 4],
            'luxury_sedan' => ['name' => 'Luxury Sedan', 'max_passengers' => 4],
        ]);

        $add('suvs_4x4', [
            'suv' => ['name' => 'SUV', 'max_passengers' => 7],
            'luxury_suv' => ['name' => 'Luxury SUV', 'max_passengers' => 6],
            'offroad_4x4' => ['name' => '4x4 Offroad', 'max_passengers' => 5],
        ]);

        $add('vans', [
            'mini_van' => ['name' => 'Mini Van', 'max_passengers' => 8],
            'passenger_van' => ['name' => 'Passenger Van', 'max_passengers' => 12],
            'luxury_van' => ['name' => 'Luxury Van', 'max_passengers' => 10],
        ]);

        $add('buses', [
            'minibus' => ['name' => 'Minibus', 'max_passengers' => 20],
            'midibus' => ['name' => 'Midibus', 'max_passengers' => 35],
            'coach_bus' => ['name' => 'Coach Bus', 'max_passengers' => 55],
            'double_decker_bus' => ['name' => 'Double Decker Bus', 'max_passengers' => 75],
        ]);

        $add('premium_special', [
            'limousine' => ['name' => 'Limousine', 'max_passengers' => 8],
            'party_bus' => ['name' => 'Party Bus', 'max_passengers' => 24],
            'executive_shuttle' => ['name' => 'Executive Shuttle', 'max_passengers' => 12],
        ]);

        $add('outdoor_touristic', [
            'atv' => ['name' => 'ATV', 'max_passengers' => 2],
            'buggy' => ['name' => 'Buggy', 'max_passengers' => 2],
            'snowmobile' => ['name' => 'Snowmobile', 'max_passengers' => 2],
            'golf_cart' => ['name' => 'Golf Cart', 'max_passengers' => 4],
            'safari_vehicle' => ['name' => 'Safari Vehicle', 'max_passengers' => 9],
        ]);

        $add('watercraft', [
            'speedboat' => ['name' => 'Speedboat', 'max_passengers' => 8],
            'sailboat' => ['name' => 'Sailboat', 'max_passengers' => 12],
            'catamaran' => ['name' => 'Catamaran', 'max_passengers' => 20],
            'ferry' => ['name' => 'Ferry', 'max_passengers' => 200],
            'yacht' => ['name' => 'Yacht', 'max_passengers' => 12],
        ]);

        $add('aircraft', [
            'helicopter' => ['name' => 'Helicopter', 'max_passengers' => 6],
            'private_plane' => ['name' => 'Private Plane', 'max_passengers' => 8],
            'seaplane' => ['name' => 'Seaplane', 'max_passengers' => 6],
        ]);

        $add('rail', [
            'train' => ['name' => 'Train', 'max_passengers' => 200],
            'tram' => ['name' => 'Tram', 'max_passengers' => 40],
        ]);

        $add('special', [
            'wheelchair_vehicle' => ['name' => 'Wheelchair Accessible Vehicle', 'max_passengers' => 4],
            'cargo_vehicle' => ['name' => 'Cargo Vehicle', 'max_passengers' => 2],
            'bicycle_transfer' => ['name' => 'Bicycle Transfer Vehicle', 'max_passengers' => 1],
        ]);

        return $rows;
    }
}
