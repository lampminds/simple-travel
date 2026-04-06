<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Models\ServiceExcursionType;
use App\Models\ServiceExcursionTypeCategory;
use App\Models\ServiceExcursionTypeTranslation;
use Illuminate\Console\Command;

/**
 * Seeds cat_service_excursion_types and their Spanish translations from a fixed list.
 * Section headers are the "code" of the service_excursion_type_category (e.g. general, culture).
 * Categories must already exist with matching codes. Uses only the languages table.
 *
 * Run from project root (Debian): php artisan service-excursion-types:seed --language-id=2
 * Idempotent: skips types that already exist (by code).
 */
class SeedServiceExcursionTypesCommand extends Command
{
    protected $signature = 'service-excursion-types:seed
                            {--language-id= : Id of the Spanish language in the languages table (required)}
                            {--dry-run : Show what would be created without writing}';

    protected $description = 'Seed service excursion types and Spanish translations from the predefined list';

    /** @var array<string, array<int, array{code: string, name_es: string}>> Key = category code */
    protected array $data = [
        'general' => [
            ['code' => 'city_tour', 'name_es' => 'Tour por la ciudad'],
            ['code' => 'walking_tour', 'name_es' => 'Tour a pie'],
            ['code' => 'guided_tour', 'name_es' => 'Tour guiado'],
            ['code' => 'self_guided_tour', 'name_es' => 'Tour autoguiado'],
            ['code' => 'panoramic_tour', 'name_es' => 'Tour panorámico'],
            ['code' => 'day_trip', 'name_es' => 'Excursión de día completo'],
            ['code' => 'half_day_tour', 'name_es' => 'Excursión de medio día'],
            ['code' => 'multi_day_tour', 'name_es' => 'Tour de varios días'],
        ],
        'culture' => [
            ['code' => 'cultural_tour', 'name_es' => 'Tour cultural'],
            ['code' => 'historical_tour', 'name_es' => 'Tour histórico'],
            ['code' => 'heritage_tour', 'name_es' => 'Tour patrimonial'],
            ['code' => 'museum_tour', 'name_es' => 'Tour de museos'],
            ['code' => 'architecture_tour', 'name_es' => 'Tour de arquitectura'],
            ['code' => 'religious_tour', 'name_es' => 'Tour religioso'],
            ['code' => 'archaeological_tour', 'name_es' => 'Tour arqueológico'],
            ['code' => 'local_experience', 'name_es' => 'Experiencia local'],
        ],
        'nature' => [
            ['code' => 'nature_tour', 'name_es' => 'Tour de naturaleza'],
            ['code' => 'wildlife_tour', 'name_es' => 'Tour de fauna'],
            ['code' => 'birdwatching_tour', 'name_es' => 'Tour de observación de aves'],
            ['code' => 'national_park_tour', 'name_es' => 'Tour de parque nacional'],
            ['code' => 'scenic_tour', 'name_es' => 'Tour escénico'],
            ['code' => 'landscape_tour', 'name_es' => 'Tour de paisajes'],
        ],
        'adventure' => [
            ['code' => 'adventure_tour', 'name_es' => 'Tour de aventura'],
            ['code' => 'hiking_tour', 'name_es' => 'Tour de trekking'],
            ['code' => 'mountain_tour', 'name_es' => 'Tour de montaña'],
            ['code' => 'offroad_tour', 'name_es' => 'Tour off-road'],
            ['code' => 'extreme_adventure', 'name_es' => 'Aventura extrema'],
        ],
        'water' => [
            ['code' => 'boat_tour', 'name_es' => 'Paseo en barco'],
            ['code' => 'lake_tour', 'name_es' => 'Tour por lago'],
            ['code' => 'river_tour', 'name_es' => 'Tour por río'],
            ['code' => 'cruise', 'name_es' => 'Crucero'],
            ['code' => 'catamaran_tour', 'name_es' => 'Tour en catamarán'],
            ['code' => 'kayak_tour', 'name_es' => 'Tour en kayak'],
            ['code' => 'rafting_trip', 'name_es' => 'Excursión de rafting'],
            ['code' => 'fishing_trip', 'name_es' => 'Salida de pesca'],
        ],
        'gastronomy' => [
            ['code' => 'food_tour', 'name_es' => 'Tour gastronómico'],
            ['code' => 'wine_tour', 'name_es' => 'Tour del vino'],
            ['code' => 'brewery_tour', 'name_es' => 'Tour de cervecerías'],
            ['code' => 'distillery_tour', 'name_es' => 'Tour de destilerías'],
            ['code' => 'culinary_tour', 'name_es' => 'Tour culinario'],
            ['code' => 'farm_visit', 'name_es' => 'Visita a granja'],
        ],
        'rural' => [
            ['code' => 'ranch_experience', 'name_es' => 'Experiencia en estancia'],
            ['code' => 'agrotourism', 'name_es' => 'Agroturismo'],
            ['code' => 'farm_experience', 'name_es' => 'Experiencia rural'],
        ],
        'winter' => [
            ['code' => 'ski_tour', 'name_es' => 'Tour de esquí'],
            ['code' => 'winter_tour', 'name_es' => 'Excursión invernal'],
            ['code' => 'snowmobile_tour', 'name_es' => 'Tour en moto de nieve'],
            ['code' => 'dog_sled_tour', 'name_es' => 'Tour en trineo de perros'],
        ],
        'air' => [
            ['code' => 'helicopter_tour', 'name_es' => 'Tour en helicóptero'],
            ['code' => 'scenic_flight', 'name_es' => 'Vuelo panorámico'],
            ['code' => 'hot_air_balloon_ride', 'name_es' => 'Paseo en globo aerostático'],
        ],
        'transport' => [
            ['code' => 'train_tour', 'name_es' => 'Tour en tren'],
            ['code' => 'bike_tour', 'name_es' => 'Tour en bicicleta'],
            ['code' => 'horseback_tour', 'name_es' => 'Tour a caballo'],
            ['code' => 'atv_tour', 'name_es' => 'Tour en cuatriciclo'],
        ],
        'special' => [
            ['code' => 'photography_tour', 'name_es' => 'Tour fotográfico'],
            ['code' => 'sunset_tour', 'name_es' => 'Tour al atardecer'],
            ['code' => 'sunrise_tour', 'name_es' => 'Tour al amanecer'],
            ['code' => 'night_tour', 'name_es' => 'Tour nocturno'],
            ['code' => 'stargazing_tour', 'name_es' => 'Tour de observación de estrellas'],
            ['code' => 'festival_tour', 'name_es' => 'Tour de festivales'],
        ],
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->warn('Dry run: no changes will be written.');
        }

        $languageId = $this->option('language-id');
        if ($languageId === null || $languageId === '') {
            $this->error('Option --language-id is required. Pass the id of the Spanish language from the languages table (e.g. --language-id=2).');

            return self::FAILURE;
        }

        $spanishLanguage = Language::query()->find($languageId);
        if (! $spanishLanguage) {
            $this->error("Language with id {$languageId} not found in the languages table.");

            return self::FAILURE;
        }

        $this->info("Using language id: {$spanishLanguage->id} (from languages table).");

        $created = 0;
        $skipped = 0;
        $missingCategories = [];

        foreach ($this->data as $categoryCode => $types) {
            $category = ServiceExcursionTypeCategory::query()
                ->where('code', $categoryCode)
                ->first();

            if (! $category) {
                $missingCategories[] = $categoryCode;

                continue;
            }

            foreach ($types as $index => $item) {
                $code = $item['code'];
                $nameEs = $item['name_es'];

                $exists = ServiceExcursionType::query()->where('code', $code)->exists();
                if ($exists) {
                    $skipped++;

                    continue;
                }

                if (! $dryRun) {
                    $type = ServiceExcursionType::create([
                        'code' => $code,
                        'service_excursion_type_category_id' => $category->id,
                        'sort_order' => $index + 1,
                        'active' => true,
                    ]);

                    ServiceExcursionTypeTranslation::create([
                        'service_excursion_type_id' => $type->id,
                        'language_id' => $spanishLanguage->id,
                        'name' => $nameEs,
                    ]);
                }

                $created++;
            }
        }

        if ($missingCategories !== []) {
            $this->warn('Categories not found (code must match exactly):');
            foreach ($missingCategories as $code) {
                $this->line("  - {$code}");
            }
        }

        $this->info($dryRun
            ? "Would create {$created} excursion types (skip {$skipped} existing)."
            : "Created {$created} excursion types, skipped {$skipped} existing.");

        return self::SUCCESS;
    }
}
