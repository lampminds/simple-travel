<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * System-catalog transfer locations for Foz do Iguaçu area (cat_cities.id = 11691), account_id null.
 *
 * Requires {@see ServiceTransferLocationTypesCatalogSeeder} (location type codes).
 *
 * Run: php artisan db:seed --class=ServiceTransferLocationsFozDoIguacuAccount1Seeder
 */
class ServiceTransferLocationsFozDoIguacuAccount1Seeder extends Seeder
{

    private const CITY_ID = 11691;

    /** cat_languages.id for en / es / pt (see LanguagesTableSeeder). */
    private const LANGUAGE_IDS = [1, 2, 3];

    public function run(): void
    {
        $typeIds = DB::table('service_transfer_location_types')
            ->whereIn('code', [
                'international_airport',
                'bus_station',
                'checkpoint',
                'landmark',
                'hotel',
                'national_park',
                'tourist_attraction',
                'meeting_point',
                'downtown',
            ])
            ->pluck('id', 'code')
            ->all();

        $missing = array_diff(
            [
                'international_airport',
                'bus_station',
                'checkpoint',
                'landmark',
                'hotel',
                'national_park',
                'tourist_attraction',
                'meeting_point',
                'downtown',
            ],
            array_keys($typeIds)
        );
        if ($missing !== []) {
            throw new \RuntimeException(
                'Missing service_transfer_location_types codes: '.implode(', ', $missing)
                .'. Run ServiceTransferLocationTypesCatalogSeeder first.'
            );
        }

        $locations = [
            ['type' => 'international_airport', 'name' => 'Foz do Iguaçu International Airport (IGU)', 'airport_code' => 'IGU'],
            ['type' => 'international_airport', 'name' => 'Cataratas del Iguazú International Airport (IGR)', 'airport_code' => 'IGR'],
            ['type' => 'international_airport', 'name' => 'Guaraní International Airport (AGT)', 'airport_code' => 'AGT'],
            ['type' => 'bus_station', 'name' => 'Foz do Iguaçu Bus Terminal', 'airport_code' => null],
            ['type' => 'bus_station', 'name' => 'Puerto Iguazú Bus Terminal', 'airport_code' => null],
            ['type' => 'checkpoint', 'name' => 'Brazil–Argentina Border Crossing', 'airport_code' => null],
            ['type' => 'checkpoint', 'name' => 'Brazil–Paraguay Border Crossing', 'airport_code' => null],
            ['type' => 'landmark', 'name' => 'Friendship Bridge', 'airport_code' => null],
            ['type' => 'landmark', 'name' => 'Tancredo Neves Bridge', 'airport_code' => null],
            ['type' => 'hotel', 'name' => 'Belmond Hotel das Cataratas', 'airport_code' => null],
            ['type' => 'hotel', 'name' => 'Bourbon Cataratas do Iguaçu Resort', 'airport_code' => null],
            ['type' => 'hotel', 'name' => 'JL Hotel by Bourbon', 'airport_code' => null],
            ['type' => 'hotel', 'name' => 'Recanto Cataratas Thermas Resort', 'airport_code' => null],
            ['type' => 'hotel', 'name' => 'Wish Foz do Iguaçu', 'airport_code' => null],
            ['type' => 'hotel', 'name' => 'Mabu Thermas Grand Resort', 'airport_code' => null],
            ['type' => 'national_park', 'name' => 'Iguaçu National Park', 'airport_code' => null],
            ['type' => 'tourist_attraction', 'name' => 'Iguazu Falls', 'airport_code' => null],
            ['type' => 'tourist_attraction', 'name' => 'Parque das Aves', 'airport_code' => null],
            ['type' => 'tourist_attraction', 'name' => 'Itaipu Dam', 'airport_code' => null],
            ['type' => 'tourist_attraction', 'name' => 'Three Borders Landmark', 'airport_code' => null],
            ['type' => 'tourist_attraction', 'name' => 'Dreams Park Show', 'airport_code' => null],
            ['type' => 'meeting_point', 'name' => 'Downtown Foz Pickup Point', 'airport_code' => null],
            ['type' => 'meeting_point', 'name' => 'Cataratas Avenue Meeting Point', 'airport_code' => null],
            ['type' => 'meeting_point', 'name' => 'Puerto Iguazú Downtown Pickup Point', 'airport_code' => null],
            ['type' => 'downtown', 'name' => 'Downtown Foz do Iguaçu', 'airport_code' => null],
            ['type' => 'downtown', 'name' => 'Downtown Puerto Iguazú', 'airport_code' => null],
            ['type' => 'downtown', 'name' => 'Ciudad del Este Downtown', 'airport_code' => null],
        ];

        foreach ($locations as $loc) {
            $slug = $this->slugFromDisplayName($loc['name']);
            $typeId = (int) $typeIds[$loc['type']];

            $now = now();

            $row = DB::table('service_transfer_locations')
                ->whereNull('account_id')
                ->where('slug', $slug)
                ->first();

            $base = [
                'service_transfer_location_type_id' => $typeId,
                'city_id' => self::CITY_ID,
                'address' => null,
                'latitude' => null,
                'longitude' => null,
                'airport_code' => $loc['airport_code'],
                'parent_id' => null,
                'is_active' => true,
                'updated_at' => $now,
            ];

            if ($row) {
                DB::table('service_transfer_locations')->where('id', $row->id)->update($base);
                $locationId = (int) $row->id;
            } else {
                $locationId = (int) DB::table('service_transfer_locations')->insertGetId(array_merge(
                    [
                        'account_id' => null,
                        'slug' => $slug,
                        'created_at' => $now,
                    ],
                    $base
                ));
            }

            DB::table('service_transfer_location_translations')
                ->where('service_transfer_location_id', $locationId)
                ->delete();

            $translationRows = [];
            foreach (self::LANGUAGE_IDS as $languageId) {
                $translationRows[] = [
                    'service_transfer_location_id' => $locationId,
                    'language_id' => $languageId,
                    'name' => $loc['name'],
                ];
            }
            DB::table('service_transfer_location_translations')->insert($translationRows);
        }
    }

    /**
     * ASCII hyphens only; normalizes en/em dashes before slugging.
     */
    private function slugFromDisplayName(string $name): string
    {
        $normalized = str_replace(['–', '—', '‐'], '-', $name);

        return Str::slug($normalized, '-', 'en');
    }
}
