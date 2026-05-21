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

    private const LANG_EN = 1;

    private const LANG_ES = 2;

    private const LANG_PT = 3;

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

        $locations = $this->locationCatalog();

        foreach ($locations as $loc) {
            $slug = $this->slugFromDisplayName($loc['names'][self::LANG_EN]);
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
            foreach ($loc['names'] as $languageId => $name) {
                $translationRows[] = [
                    'service_transfer_location_id' => $locationId,
                    'language_id' => (int) $languageId,
                    'name' => $name,
                ];
            }
            DB::table('service_transfer_location_translations')->insert($translationRows);
        }
    }

    /**
     * @return list<array{type: string, airport_code: string|null, names: array<int, string>}>
     */
    private function locationCatalog(): array
    {
        return [
            [
                'type' => 'international_airport',
                'airport_code' => 'IGU',
                'names' => [
                    self::LANG_EN => 'Foz do Iguaçu International Airport (IGU)',
                    self::LANG_ES => 'Aeropuerto Internacional de Foz do Iguaçu (IGU)',
                    self::LANG_PT => 'Aeroporto Internacional de Foz do Iguaçu (IGU)',
                ],
            ],
            [
                'type' => 'international_airport',
                'airport_code' => 'IGR',
                'names' => [
                    self::LANG_EN => 'Iguazú Falls International Airport (IGR)',
                    self::LANG_ES => 'Aeropuerto Internacional Cataratas del Iguazú (IGR)',
                    self::LANG_PT => 'Aeroporto Internacional Cataratas do Iguaçu (IGR)',
                ],
            ],
            [
                'type' => 'international_airport',
                'airport_code' => 'AGT',
                'names' => [
                    self::LANG_EN => 'Guaraní International Airport (AGT)',
                    self::LANG_ES => 'Aeropuerto Internacional Guaraní (AGT)',
                    self::LANG_PT => 'Aeroporto Internacional Guarani (AGT)',
                ],
            ],
            [
                'type' => 'bus_station',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Foz do Iguaçu Bus Terminal',
                    self::LANG_ES => 'Terminal de ómnibus de Foz do Iguaçu',
                    self::LANG_PT => 'Terminal Rodoviário de Foz do Iguaçu',
                ],
            ],
            [
                'type' => 'bus_station',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Puerto Iguazú Bus Terminal',
                    self::LANG_ES => 'Terminal de ómnibus de Puerto Iguazú',
                    self::LANG_PT => 'Terminal Rodoviário de Puerto Iguazú',
                ],
            ],
            [
                'type' => 'checkpoint',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Brazil–Argentina border crossing',
                    self::LANG_ES => 'Paso fronterizo Brasil–Argentina',
                    self::LANG_PT => 'Posto de fronteira Brasil–Argentina',
                ],
            ],
            [
                'type' => 'checkpoint',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Brazil–Paraguay border crossing',
                    self::LANG_ES => 'Paso fronterizo Brasil–Paraguay',
                    self::LANG_PT => 'Posto de fronteira Brasil–Paraguai',
                ],
            ],
            [
                'type' => 'landmark',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Friendship Bridge',
                    self::LANG_ES => 'Puente de la Amistad',
                    self::LANG_PT => 'Ponte da Amizade',
                ],
            ],
            [
                'type' => 'landmark',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Tancredo Neves Bridge',
                    self::LANG_ES => 'Puente Tancredo Neves',
                    self::LANG_PT => 'Ponte Tancredo Neves',
                ],
            ],
            [
                'type' => 'hotel',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Belmond Hotel das Cataratas',
                    self::LANG_ES => 'Belmond Hotel das Cataratas',
                    self::LANG_PT => 'Belmond Hotel das Cataratas',
                ],
            ],
            [
                'type' => 'hotel',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Bourbon Cataratas do Iguaçu Resort',
                    self::LANG_ES => 'Bourbon Cataratas do Iguaçu Resort',
                    self::LANG_PT => 'Bourbon Cataratas do Iguaçu Resort',
                ],
            ],
            [
                'type' => 'hotel',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'JL Hotel by Bourbon',
                    self::LANG_ES => 'JL Hotel by Bourbon',
                    self::LANG_PT => 'JL Hotel by Bourbon',
                ],
            ],
            [
                'type' => 'hotel',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Recanto Cataratas Thermas Resort',
                    self::LANG_ES => 'Recanto Cataratas Thermas Resort',
                    self::LANG_PT => 'Recanto Cataratas Thermas Resort',
                ],
            ],
            [
                'type' => 'hotel',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Wish Foz do Iguaçu',
                    self::LANG_ES => 'Wish Foz do Iguaçu',
                    self::LANG_PT => 'Wish Foz do Iguaçu',
                ],
            ],
            [
                'type' => 'hotel',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Mabu Thermas Grand Resort',
                    self::LANG_ES => 'Mabu Thermas Grand Resort',
                    self::LANG_PT => 'Mabu Thermas Grand Resort',
                ],
            ],
            [
                'type' => 'national_park',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Iguaçu National Park',
                    self::LANG_ES => 'Parque Nacional del Iguaçu',
                    self::LANG_PT => 'Parque Nacional do Iguaçu',
                ],
            ],
            [
                'type' => 'tourist_attraction',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Iguazu Falls',
                    self::LANG_ES => 'Cataratas del Iguazú',
                    self::LANG_PT => 'Cataratas do Iguaçu',
                ],
            ],
            [
                'type' => 'tourist_attraction',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Parque das Aves (Bird Park)',
                    self::LANG_ES => 'Parque de las Aves',
                    self::LANG_PT => 'Parque das Aves',
                ],
            ],
            [
                'type' => 'tourist_attraction',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Itaipu Dam',
                    self::LANG_ES => 'Represa de Itaipú',
                    self::LANG_PT => 'Usina Hidrelétrica de Itaipu',
                ],
            ],
            [
                'type' => 'tourist_attraction',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Three Borders Landmark',
                    self::LANG_ES => 'Hito Tres Fronteras',
                    self::LANG_PT => 'Marco das Três Fronteiras',
                ],
            ],
            [
                'type' => 'tourist_attraction',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Dreams Park Show',
                    self::LANG_ES => 'Dreams Park Show',
                    self::LANG_PT => 'Dreams Park Show',
                ],
            ],
            [
                'type' => 'meeting_point',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Downtown Foz pickup point',
                    self::LANG_ES => 'Punto de encuentro — centro de Foz do Iguaçu',
                    self::LANG_PT => 'Ponto de encontro — centro de Foz do Iguaçu',
                ],
            ],
            [
                'type' => 'meeting_point',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Cataratas Avenue meeting point',
                    self::LANG_ES => 'Punto de encuentro — Avenida Cataratas',
                    self::LANG_PT => 'Ponto de encontro — Avenida das Cataratas',
                ],
            ],
            [
                'type' => 'meeting_point',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Puerto Iguazú downtown pickup point',
                    self::LANG_ES => 'Punto de encuentro — centro de Puerto Iguazú',
                    self::LANG_PT => 'Ponto de encontro — centro de Puerto Iguazú',
                ],
            ],
            [
                'type' => 'downtown',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Downtown Foz do Iguaçu',
                    self::LANG_ES => 'Centro de Foz do Iguaçu',
                    self::LANG_PT => 'Centro de Foz do Iguaçu',
                ],
            ],
            [
                'type' => 'downtown',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Downtown Puerto Iguazú',
                    self::LANG_ES => 'Centro de Puerto Iguazú',
                    self::LANG_PT => 'Centro de Puerto Iguazú',
                ],
            ],
            [
                'type' => 'downtown',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Ciudad del Este downtown',
                    self::LANG_ES => 'Centro de Ciudad del Este',
                    self::LANG_PT => 'Centro de Ciudad del Este',
                ],
            ],
        ];
    }

    /**
     * ASCII hyphens only; normalizes en/em dashes before slugging. Slugs are derived from the English label.
     */
    private function slugFromDisplayName(string $name): string
    {
        $normalized = str_replace(['–', '—', '‐'], '-', $name);

        return Str::slug($normalized, '-', 'en');
    }
}
