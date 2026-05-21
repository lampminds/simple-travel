<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * System-catalog transfer locations for San Martín de los Andes area (cat_cities.id = 1425), account_id null.
 *
 * Requires {@see ServiceTransferLocationTypesCatalogSeeder}. New type codes: docs/transfer-location-types.md.
 *
 * Run: php artisan db:seed --class=ServiceTransferLocationsSanMartinDeLosAndesSeeder
 */
class ServiceTransferLocationsSanMartinDeLosAndesSeeder extends Seeder
{
    private const CITY_ID = 1425;

    private const LANG_EN = 1;

    private const LANG_ES = 2;

    private const LANG_PT = 3;

    public function run(): void
    {
        $requiredTypeCodes = [
            'airport',
            'bus_station',
            'downtown',
            'lake',
            'national_park',
            'volcano',
            'ski_resort',
            'scenic_route',
            'viewpoint',
            'beach',
            'waterfall',
            'geological_site',
            'park',
            'hotel',
            'meeting_point',
        ];

        $typeIds = DB::table('service_transfer_location_types')
            ->whereIn('code', $requiredTypeCodes)
            ->pluck('id', 'code')
            ->all();

        $missing = array_diff($requiredTypeCodes, array_keys($typeIds));
        if ($missing !== []) {
            throw new \RuntimeException(
                'Missing service_transfer_location_types codes: '.implode(', ', $missing)
                .'. Run ServiceTransferLocationTypesCatalogSeeder first.'
            );
        }

        foreach ($this->locationCatalog() as $loc) {
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
                'type' => 'airport',
                'airport_code' => 'CPC',
                'names' => [
                    self::LANG_EN => 'Aviador Carlos Campos Airport (CPC)',
                    self::LANG_ES => 'Aeropuerto Aviador Carlos Campos (CPC)',
                    self::LANG_PT => 'Aeroporto Aviador Carlos Campos (CPC)',
                ],
            ],
            [
                'type' => 'bus_station',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'San Martín de los Andes Bus Terminal',
                    self::LANG_ES => 'Terminal de ómnibus de San Martín de los Andes',
                    self::LANG_PT => 'Terminal Rodoviário de San Martín de los Andes',
                ],
            ],
            [
                'type' => 'downtown',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Downtown San Martín de los Andes',
                    self::LANG_ES => 'Centro de San Martín de los Andes',
                    self::LANG_PT => 'Centro de San Martín de los Andes',
                ],
            ],
            [
                'type' => 'downtown',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Junín de los Andes Downtown',
                    self::LANG_ES => 'Centro de Junín de los Andes',
                    self::LANG_PT => 'Centro de Junín de los Andes',
                ],
            ],
            [
                'type' => 'lake',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Lácar Lake',
                    self::LANG_ES => 'Lago Lácar',
                    self::LANG_PT => 'Lago Lácar',
                ],
            ],
            [
                'type' => 'lake',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Huechulafquen Lake',
                    self::LANG_ES => 'Lago Huechulafquén',
                    self::LANG_PT => 'Lago Huechulafquen',
                ],
            ],
            [
                'type' => 'lake',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Lolog Lake',
                    self::LANG_ES => 'Lago Lolog',
                    self::LANG_PT => 'Lago Lolog',
                ],
            ],
            [
                'type' => 'lake',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Meliquina Lake',
                    self::LANG_ES => 'Lago Meliquina',
                    self::LANG_PT => 'Lago Meliquina',
                ],
            ],
            [
                'type' => 'lake',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Falkner Lake',
                    self::LANG_ES => 'Lago Falkner',
                    self::LANG_PT => 'Lago Falkner',
                ],
            ],
            [
                'type' => 'lake',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Machónico Lake',
                    self::LANG_ES => 'Lago Machónico',
                    self::LANG_PT => 'Lago Machónico',
                ],
            ],
            [
                'type' => 'national_park',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Lanín National Park',
                    self::LANG_ES => 'Parque Nacional Lanín',
                    self::LANG_PT => 'Parque Nacional Lanín',
                ],
            ],
            [
                'type' => 'volcano',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Lanín Volcano',
                    self::LANG_ES => 'Volcán Lanín',
                    self::LANG_PT => 'Vulcão Lanín',
                ],
            ],
            [
                'type' => 'ski_resort',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Chapelco Ski Resort',
                    self::LANG_ES => 'Centro de esquí Chapelco',
                    self::LANG_PT => 'Estação de esqui Chapelco',
                ],
            ],
            [
                'type' => 'scenic_route',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Route of the Seven Lakes',
                    self::LANG_ES => 'Ruta de los Siete Lagos',
                    self::LANG_PT => 'Rota dos Sete Lagos',
                ],
            ],
            [
                'type' => 'viewpoint',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Arrayanes Viewpoint',
                    self::LANG_ES => 'Mirador Arrayanes',
                    self::LANG_PT => 'Mirante dos Arrayanes',
                ],
            ],
            [
                'type' => 'viewpoint',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Bandurrias Viewpoint',
                    self::LANG_ES => 'Mirador Bandurrias',
                    self::LANG_PT => 'Mirante Bandurrias',
                ],
            ],
            [
                'type' => 'beach',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Quila Quina Beach',
                    self::LANG_ES => 'Playa Quila Quina',
                    self::LANG_PT => 'Praia Quila Quina',
                ],
            ],
            [
                'type' => 'beach',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Yuco Beach',
                    self::LANG_ES => 'Playa Yuco',
                    self::LANG_PT => 'Praia Yuco',
                ],
            ],
            [
                'type' => 'beach',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Catritre Beach',
                    self::LANG_ES => 'Playa Catritre',
                    self::LANG_PT => 'Praia Catritre',
                ],
            ],
            [
                'type' => 'waterfall',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Cascada Chachín',
                    self::LANG_ES => 'Cascada Chachín',
                    self::LANG_PT => 'Cachoeira Chachín',
                ],
            ],
            [
                'type' => 'geological_site',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Escorial Lava Field',
                    self::LANG_ES => 'Campo de lava Escorial',
                    self::LANG_PT => 'Campo de lava Escorial',
                ],
            ],
            [
                'type' => 'park',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Via Christi Park',
                    self::LANG_ES => 'Parque Via Christi',
                    self::LANG_PT => 'Parque Via Christi',
                ],
            ],
            [
                'type' => 'hotel',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Loi Suites Chapelco Hotel',
                    self::LANG_ES => 'Loi Suites Chapelco Hotel',
                    self::LANG_PT => 'Loi Suites Chapelco Hotel',
                ],
            ],
            [
                'type' => 'hotel',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Rio Hermoso Hotel',
                    self::LANG_ES => 'Rio Hermoso Hotel',
                    self::LANG_PT => 'Rio Hermoso Hotel',
                ],
            ],
            [
                'type' => 'hotel',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Patagonia Plaza Hotel',
                    self::LANG_ES => 'Patagonia Plaza Hotel',
                    self::LANG_PT => 'Patagonia Plaza Hotel',
                ],
            ],
            [
                'type' => 'hotel',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Le Chatelet Hotel',
                    self::LANG_ES => 'Le Chatelet Hotel',
                    self::LANG_PT => 'Le Chatelet Hotel',
                ],
            ],
            [
                'type' => 'hotel',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Hosteria La Posta del Cazador',
                    self::LANG_ES => 'Hostería La Posta del Cazador',
                    self::LANG_PT => 'Hosteria La Posta del Cazador',
                ],
            ],
            [
                'type' => 'meeting_point',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Downtown San Martín pickup point',
                    self::LANG_ES => 'Punto de encuentro — centro de San Martín de los Andes',
                    self::LANG_PT => 'Ponto de encontro — centro de San Martín de los Andes',
                ],
            ],
            [
                'type' => 'meeting_point',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Lácar Lake pickup point',
                    self::LANG_ES => 'Punto de encuentro — Lago Lácar',
                    self::LANG_PT => 'Ponto de encontro — Lago Lácar',
                ],
            ],
            [
                'type' => 'meeting_point',
                'airport_code' => null,
                'names' => [
                    self::LANG_EN => 'Chapelco base pickup point',
                    self::LANG_ES => 'Punto de encuentro — base de Chapelco',
                    self::LANG_PT => 'Ponto de encontro — base de Chapelco',
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
