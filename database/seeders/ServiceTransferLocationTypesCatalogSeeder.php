<?php

namespace Database\Seeders;

use Database\Seeders\Support\DisablesForeignKeyChecks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds global transfer location type categories (with translations) and location types.
 *
 * When a city location seeder needs a type that does not exist yet, add it here first (with EN/ES/PT
 * labels), re-run this seeder, then use the new `code` in the location seeder.
 *
 * Category and type names use the translation table with English, Spanish, and Portuguese labels.
 */
class ServiceTransferLocationTypesCatalogSeeder extends Seeder
{
    use DisablesForeignKeyChecks;

    /** cat_languages.id for en-US, es-AR, pt-BR (see CatLanguagesTableSeeder + CatLocalesTableSeeder). */
    private const LANGUAGE_IDS = [1, 2, 3];

    /**
     * @param  array<string, mixed>  $row
     */
    private function pickLocalized(array $row, string $fieldPrefix, int $languageId): string
    {
        $suffix = match ($languageId) {
            1 => '_en',
            2 => '_es',
            3 => '_pt',
            default => '_en',
        };

        $key = $fieldPrefix.$suffix;

        return (string) ($row[$key] ?? $row[$fieldPrefix.'_en'] ?? '');
    }

    public function run(): void
    {
        $this->withoutForeignKeyChecks(function (): void {
            $this->seedCatalog();
        });
    }

    private function seedCatalog(): void
    {
        $categories = [
            [
                'code' => 'public_transport',
                'sort_order' => 10,
                'label_en' => 'Public Transport',
                'label_es' => 'Transporte público',
                'label_pt' => 'Transporte público',
            ],
            [
                'code' => 'hospitality',
                'sort_order' => 20,
                'label_en' => 'Hospitality',
                'label_es' => 'Alojamiento',
                'label_pt' => 'Hospedagem',
            ],
            [
                'code' => 'tourism_attractions',
                'sort_order' => 30,
                'label_en' => 'Tourism / Attractions',
                'label_es' => 'Turismo y atracciones',
                'label_pt' => 'Turismo e atrações',
            ],
            [
                'code' => 'urban',
                'sort_order' => 40,
                'label_en' => 'Urban',
                'label_es' => 'Urbano',
                'label_pt' => 'Urbano',
            ],
            [
                'code' => 'meeting_points',
                'sort_order' => 50,
                'label_en' => 'Meeting Points',
                'label_es' => 'Puntos de encuentro',
                'label_pt' => 'Pontos de encontro',
            ],
            [
                'code' => 'services',
                'sort_order' => 60,
                'label_en' => 'Services',
                'label_es' => 'Servicios',
                'label_pt' => 'Serviços',
            ],
            [
                'code' => 'outdoor_adventure',
                'sort_order' => 70,
                'label_en' => 'Outdoor / Adventure',
                'label_es' => 'Aire libre y aventura',
                'label_pt' => 'Ar livre e aventura',
            ],
            [
                'code' => 'private_generic',
                'sort_order' => 80,
                'label_en' => 'Private / Generic',
                'label_es' => 'Privado / genérico',
                'label_pt' => 'Privado / genérico',
            ],
        ];

        foreach ($categories as $row) {
            DB::table('cat_service_transfer_location_type_categories')->updateOrInsert(
                ['code' => $row['code']],
                [
                    'sort_order' => $row['sort_order'],
                    'active' => true,
                ]
            );
        }

        $categoryIds = DB::table('cat_service_transfer_location_type_categories')
            ->whereIn('code', array_column($categories, 'code'))
            ->pluck('id', 'code')
            ->all();

        DB::table('cat_service_transfer_location_type_category_translations')
            ->whereIn('service_transfer_location_type_category_id', array_values($categoryIds))
            ->delete();

        $translationRows = [];
        foreach ($categories as $row) {
            $cid = $categoryIds[$row['code']] ?? null;
            if ($cid === null) {
                continue;
            }
            foreach (self::LANGUAGE_IDS as $languageId) {
                $translationRows[] = [
                    'service_transfer_location_type_category_id' => $cid,
                    'language_id' => $languageId,
                    'name' => $this->pickLocalized($row, 'label', $languageId),
                ];
            }
        }
        DB::table('cat_service_transfer_location_type_category_translations')->insert($translationRows);

        $types = $this->locationTypeDefinitions($categoryIds);

        foreach ($types as $t) {
            DB::table('service_transfer_location_types')->updateOrInsert(
                ['code' => $t['code']],
                [
                    'service_transfer_location_type_category_id' => $t['category_id'],
                    'sort_order' => $t['sort_order'],
                    'active' => true,
                ]
            );
        }

        $typeIds = DB::table('service_transfer_location_types')
            ->whereIn('code', array_column($types, 'code'))
            ->pluck('id', 'code')
            ->all();

        DB::table('service_transfer_location_type_translations')
            ->whereIn('service_transfer_location_type_id', array_values($typeIds))
            ->delete();

        $typeTranslationRows = [];
        foreach ($types as $t) {
            $tid = $typeIds[$t['code']] ?? null;
            if ($tid === null) {
                continue;
            }
            foreach (self::LANGUAGE_IDS as $languageId) {
                $typeTranslationRows[] = [
                    'service_transfer_location_type_id' => $tid,
                    'language_id' => $languageId,
                    'name' => $this->pickLocalized($t, 'name', $languageId),
                ];
            }
        }
        DB::table('service_transfer_location_type_translations')->insert($typeTranslationRows);
    }

    /**
     * @param  array<string, int>  $categoryIds  code => id
     * @return array<int, array{code: string, category_id: int, sort_order: int, name_en: string, name_es: string, name_pt: string}>
     */
    private function locationTypeDefinitions(array $categoryIds): array
    {
        $c = static fn (string $code): int => $categoryIds[$code]
            ?? throw new \InvalidArgumentException("Missing category id for code: {$code}");

        $rows = [];
        $order = 0;

        /** @param  array<string, array{en: string, es: string, pt: string}>  $items */
        $add = function (string $categoryCode, array $items) use (&$rows, &$order, $c): void {
            foreach ($items as $code => $labels) {
                $order++;
                $rows[] = [
                    'code' => $code,
                    'category_id' => $c($categoryCode),
                    'sort_order' => $order,
                    'name_en' => $labels['en'],
                    'name_es' => $labels['es'],
                    'name_pt' => $labels['pt'],
                ];
            }
        };

        $add('public_transport', [
            'airport' => ['en' => 'Airport', 'es' => 'Aeropuerto', 'pt' => 'Aeroporto'],
            'domestic_airport' => ['en' => 'Domestic Airport', 'es' => 'Aeropuerto nacional', 'pt' => 'Aeroporto doméstico'],
            'international_airport' => ['en' => 'International Airport', 'es' => 'Aeropuerto internacional', 'pt' => 'Aeroporto internacional'],
            'private_airport' => ['en' => 'Private Airport', 'es' => 'Aeropuerto privado', 'pt' => 'Aeroporto privado'],
            'heliport' => ['en' => 'Heliport', 'es' => 'Helipuerto', 'pt' => 'Heliporto'],
            'bus_station' => ['en' => 'Bus Station', 'es' => 'Terminal de ómnibus', 'pt' => 'Rodoviária'],
            'train_station' => ['en' => 'Train Station', 'es' => 'Estación de tren', 'pt' => 'Estação ferroviária'],
            'subway_station' => ['en' => 'Subway Station', 'es' => 'Estación de subte', 'pt' => 'Estação de metrô'],
            'ferry_terminal' => ['en' => 'Ferry Terminal', 'es' => 'Terminal de ferry', 'pt' => 'Terminal de balsa'],
            'cruise_terminal' => ['en' => 'Cruise Terminal', 'es' => 'Terminal de cruceros', 'pt' => 'Terminal de cruzeiros'],
            'port' => ['en' => 'Port', 'es' => 'Puerto', 'pt' => 'Porto'],
            'marina' => ['en' => 'Marina', 'es' => 'Marina', 'pt' => 'Marina'],
        ]);

        $add('hospitality', [
            'hotel' => ['en' => 'Accommodation', 'es' => 'Alojamiento', 'pt' => 'Alojamento'],
            'hostel' => ['en' => 'Hostel', 'es' => 'Hostal', 'pt' => 'Hostel'],
            'resort' => ['en' => 'Resort', 'es' => 'Resort', 'pt' => 'Resort'],
            'lodge' => ['en' => 'Lodge', 'es' => 'Lodge', 'pt' => 'Lodge'],
            'cabin' => ['en' => 'Cabin', 'es' => 'Cabaña', 'pt' => 'Cabana'],
            'apartment' => ['en' => 'Apartment', 'es' => 'Apartamento', 'pt' => 'Apartamento'],
            'vacation_rental' => ['en' => 'Vacation Rental', 'es' => 'Alquiler vacacional', 'pt' => 'Aluguel por temporada'],
            'campground' => ['en' => 'Campground', 'es' => 'Camping', 'pt' => 'Camping'],
            'glamping' => ['en' => 'Glamping Site', 'es' => 'Glamping', 'pt' => 'Glamping'],
        ]);

        $add('tourism_attractions', [
            'tourist_attraction' => ['en' => 'Tourist Attraction', 'es' => 'Atracción turística', 'pt' => 'Atração turística'],
            'viewpoint' => ['en' => 'Viewpoint', 'es' => 'Mirador', 'pt' => 'Mirante'],
            'landmark' => ['en' => 'Landmark', 'es' => 'Monumento / punto emblemático', 'pt' => 'Marco / ponto turístico'],
            'monument' => ['en' => 'Monument', 'es' => 'Monumento', 'pt' => 'Monumento'],
            'museum' => ['en' => 'Museum', 'es' => 'Museo', 'pt' => 'Museu'],
            'national_park' => ['en' => 'National Park', 'es' => 'Parque nacional', 'pt' => 'Parque nacional'],
            'ski_resort' => ['en' => 'Ski Resort', 'es' => 'Estación de esquí', 'pt' => 'Estação de esqui'],
            'beach' => ['en' => 'Beach', 'es' => 'Playa', 'pt' => 'Praia'],
            'lake' => ['en' => 'Lake', 'es' => 'Lago', 'pt' => 'Lago'],
            'waterfall' => ['en' => 'Waterfall', 'es' => 'Cascada', 'pt' => 'Cachoeira'],
            'volcano' => ['en' => 'Volcano', 'es' => 'Volcán', 'pt' => 'Vulcão'],
            'trailhead' => ['en' => 'Trailhead', 'es' => 'Inicio de sendero', 'pt' => 'Início da trilha'],
            'park' => ['en' => 'Park', 'es' => 'Parque', 'pt' => 'Parque'],
            'scenic_route' => ['en' => 'Scenic Route', 'es' => 'Ruta panorámica', 'pt' => 'Rota cênica'],
            'geological_site' => ['en' => 'Geological Site', 'es' => 'Sitio geológico', 'pt' => 'Sítio geológico'],
        ]);

        $add('urban', [
            'downtown' => ['en' => 'Downtown', 'es' => 'Centro', 'pt' => 'Centro'],
            'neighborhood' => ['en' => 'Neighborhood', 'es' => 'Barrio', 'pt' => 'Bairro'],
            'shopping_center' => ['en' => 'Shopping Center', 'es' => 'Centro comercial', 'pt' => 'Shopping'],
            'convention_center' => ['en' => 'Convention Center', 'es' => 'Centro de convenciones', 'pt' => 'Centro de convenções'],
            'stadium' => ['en' => 'Stadium', 'es' => 'Estadio', 'pt' => 'Estádio'],
            'arena' => ['en' => 'Arena', 'es' => 'Arena', 'pt' => 'Arena'],
            'theater' => ['en' => 'Theater', 'es' => 'Teatro', 'pt' => 'Teatro'],
            'casino' => ['en' => 'Casino', 'es' => 'Casino', 'pt' => 'Cassino'],
        ]);

        $add('meeting_points', [
            'meeting_point' => ['en' => 'Meeting Point', 'es' => 'Punto de encuentro', 'pt' => 'Ponto de encontro'],
            'pickup_point' => ['en' => 'Pickup Point', 'es' => 'Punto de recogida', 'pt' => 'Ponto de embarque'],
            'dropoff_point' => ['en' => 'Drop-off Point', 'es' => 'Punto de entrega', 'pt' => 'Ponto de desembarque'],
            'checkpoint' => ['en' => 'Checkpoint', 'es' => 'Punto de control', 'pt' => 'Posto de controle'],
        ]);

        $add('services', [
            'hospital' => ['en' => 'Hospital', 'es' => 'Hospital', 'pt' => 'Hospital'],
            'clinic' => ['en' => 'Clinic', 'es' => 'Clínica', 'pt' => 'Clínica'],
            'university' => ['en' => 'University', 'es' => 'Universidad', 'pt' => 'Universidade'],
            'school' => ['en' => 'School', 'es' => 'Escuela', 'pt' => 'Escola'],
            'embassy' => ['en' => 'Embassy', 'es' => 'Embajada', 'pt' => 'Embaixada'],
        ]);

        $add('outdoor_adventure', [
            'mountain' => ['en' => 'Mountain', 'es' => 'Montaña', 'pt' => 'Montanha'],
            'basecamp' => ['en' => 'Base Camp', 'es' => 'Campamento base', 'pt' => 'Acampamento base'],
            'campsite' => ['en' => 'Campsite', 'es' => 'Campamento', 'pt' => 'Acampamento'],
            'refuge' => ['en' => 'Mountain Refuge', 'es' => 'Refugio de montaña', 'pt' => 'Abrigo de montanha'],
            'dock' => ['en' => 'Dock', 'es' => 'Muelle', 'pt' => 'Doca'],
            'pier' => ['en' => 'Pier', 'es' => 'Embarcadero', 'pt' => 'Cais'],
        ]);

        $add('private_generic', [
            'private_address' => ['en' => 'Private Address', 'es' => 'Domicilio privado', 'pt' => 'Endereço residencial'],
            'custom_location' => ['en' => 'Custom Location', 'es' => 'Ubicación personalizada', 'pt' => 'Local personalizado'],
            'warehouse' => ['en' => 'Warehouse', 'es' => 'Depósito', 'pt' => 'Armazém'],
            'office' => ['en' => 'Office', 'es' => 'Oficina', 'pt' => 'Escritório'],
        ]);

        return $rows;
    }
}
