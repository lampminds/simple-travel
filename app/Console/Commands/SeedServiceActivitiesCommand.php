<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Models\ServiceActivity;
use App\Models\ServiceActivityCategory;
use App\Models\ServiceActivityTranslation;
use Illuminate\Console\Command;

/**
 * Seeds cat_service_activities and their Spanish translations from a fixed list.
 * Categories must already exist and their Spanish translation name must match
 * the section headers below (e.g. "Naturaleza", "Actividades acuáticas").
 * Uses only the languages table (no lmp_languages). Pass the id of the Spanish language.
 *
 * Run from project root (Debian): php artisan service-activities:seed --language-id=2
 * Idempotent: skips activities that already exist (by code).
 */
class SeedServiceActivitiesCommand extends Command
{
    protected $signature = 'service-activities:seed
                            {--language-id= : Id of the Spanish language in the languages table (required)}
                            {--dry-run : Show what would be created without writing}';

    protected $description = 'Seed service activities and Spanish translations from the predefined list';

    /** @var array<string, array<int, array{code: string, name_es: string}>> */
    protected array $data = [
        'Naturaleza' => [
            ['code' => 'hiking', 'name_es' => 'Senderismo'],
            ['code' => 'trekking', 'name_es' => 'Trekking'],
            ['code' => 'nature_walk', 'name_es' => 'Caminata en la naturaleza'],
            ['code' => 'birdwatching', 'name_es' => 'Observación de aves'],
            ['code' => 'wildlife_observation', 'name_es' => 'Observación de fauna'],
            ['code' => 'flora_observation', 'name_es' => 'Observación de flora'],
            ['code' => 'geology_exploration', 'name_es' => 'Exploración geológica'],
            ['code' => 'stargazing', 'name_es' => 'Observación de estrellas'],
            ['code' => 'photography', 'name_es' => 'Fotografía'],
            ['code' => 'nature_photography', 'name_es' => 'Fotografía de naturaleza'],
        ],
        'Actividades acuáticas' => [
            ['code' => 'kayaking', 'name_es' => 'Kayak'],
            ['code' => 'canoeing', 'name_es' => 'Canotaje'],
            ['code' => 'rafting', 'name_es' => 'Rafting'],
            ['code' => 'stand_up_paddle', 'name_es' => 'Paddle surf'],
            ['code' => 'sailing', 'name_es' => 'Navegación a vela'],
            ['code' => 'boating', 'name_es' => 'Paseo en barco'],
            ['code' => 'motorboat', 'name_es' => 'Lancha'],
            ['code' => 'jet_ski', 'name_es' => 'Moto de agua'],
            ['code' => 'snorkeling', 'name_es' => 'Snorkel'],
            ['code' => 'scuba_diving', 'name_es' => 'Buceo'],
            ['code' => 'swimming', 'name_es' => 'Natación'],
            ['code' => 'fishing', 'name_es' => 'Pesca'],
            ['code' => 'fly_fishing', 'name_es' => 'Pesca con mosca'],
            ['code' => 'whale_watching', 'name_es' => 'Avistaje de ballenas'],
            ['code' => 'seal_watching', 'name_es' => 'Avistaje de lobos marinos'],
        ],
        'Actividades de montaña' => [
            ['code' => 'mountaineering', 'name_es' => 'Montañismo'],
            ['code' => 'rock_climbing', 'name_es' => 'Escalada en roca'],
            ['code' => 'ice_climbing', 'name_es' => 'Escalada en hielo'],
            ['code' => 'via_ferrata', 'name_es' => 'Vía ferrata'],
            ['code' => 'canyoning', 'name_es' => 'Barranquismo'],
            ['code' => 'zipline', 'name_es' => 'Tirolesa'],
            ['code' => 'paragliding', 'name_es' => 'Parapente'],
            ['code' => 'hang_gliding', 'name_es' => 'Ala delta'],
            ['code' => 'bungee_jumping', 'name_es' => 'Puenting'],
            ['code' => 'mountain_biking', 'name_es' => 'Ciclismo de montaña'],
            ['code' => 'downhill_biking', 'name_es' => 'Descenso en bicicleta'],
            ['code' => 'trail_running', 'name_es' => 'Trail running'],
        ],
        'Actividades de nieve' => [
            ['code' => 'skiing', 'name_es' => 'Esquí'],
            ['code' => 'snowboarding', 'name_es' => 'Snowboard'],
            ['code' => 'cross_country_skiing', 'name_es' => 'Esquí de fondo'],
            ['code' => 'snowshoeing', 'name_es' => 'Caminata con raquetas'],
            ['code' => 'sledding', 'name_es' => 'Trineo'],
            ['code' => 'dog_sledding', 'name_es' => 'Trineo con perros'],
            ['code' => 'snowmobiling', 'name_es' => 'Moto de nieve'],
            ['code' => 'ice_skating', 'name_es' => 'Patinaje sobre hielo'],
            ['code' => 'winter_hiking', 'name_es' => 'Caminata invernal'],
        ],
        'Actividades ecuestres' => [
            ['code' => 'horseback_riding', 'name_es' => 'Cabalgata'],
            ['code' => 'horse_trekking', 'name_es' => 'Trekking a caballo'],
            ['code' => 'carriage_rides', 'name_es' => 'Paseo en carruaje'],
        ],
        'Actividades culturales' => [
            ['code' => 'city_tour', 'name_es' => 'Tour por la ciudad'],
            ['code' => 'museum_visit', 'name_es' => 'Visita a museo'],
            ['code' => 'historical_tour', 'name_es' => 'Tour histórico'],
            ['code' => 'cultural_experience', 'name_es' => 'Experiencia cultural'],
            ['code' => 'indigenous_culture', 'name_es' => 'Cultura indígena'],
            ['code' => 'architecture_tour', 'name_es' => 'Tour de arquitectura'],
            ['code' => 'religious_sites', 'name_es' => 'Visita a sitios religiosos'],
            ['code' => 'local_lifestyle', 'name_es' => 'Experiencia de vida local'],
        ],
        'Gastronomía' => [
            ['code' => 'food_tasting', 'name_es' => 'Degustación gastronómica'],
            ['code' => 'wine_tasting', 'name_es' => 'Degustación de vinos'],
            ['code' => 'beer_tasting', 'name_es' => 'Degustación de cervezas'],
            ['code' => 'spirits_tasting', 'name_es' => 'Degustación de destilados'],
            ['code' => 'cooking_class', 'name_es' => 'Clase de cocina'],
            ['code' => 'culinary_experience', 'name_es' => 'Experiencia culinaria'],
            ['code' => 'farm_to_table', 'name_es' => 'Experiencia de campo a mesa'],
            ['code' => 'vineyard_visit', 'name_es' => 'Visita a viñedo'],
            ['code' => 'brewery_visit', 'name_es' => 'Visita a cervecería'],
            ['code' => 'distillery_visit', 'name_es' => 'Visita a destilería'],
        ],
        'Bienestar' => [
            ['code' => 'spa', 'name_es' => 'Spa'],
            ['code' => 'massage', 'name_es' => 'Masajes'],
            ['code' => 'thermal_baths', 'name_es' => 'Termas'],
            ['code' => 'yoga', 'name_es' => 'Yoga'],
            ['code' => 'meditation', 'name_es' => 'Meditación'],
            ['code' => 'wellness_program', 'name_es' => 'Programa de bienestar'],
            ['code' => 'fitness', 'name_es' => 'Fitness'],
            ['code' => 'pilates', 'name_es' => 'Pilates'],
        ],
        'Actividades recreativas' => [
            ['code' => 'cycling', 'name_es' => 'Ciclismo'],
            ['code' => 'bike_rental', 'name_es' => 'Alquiler de bicicletas'],
            ['code' => 'golf', 'name_es' => 'Golf'],
            ['code' => 'mini_golf', 'name_es' => 'Mini golf'],
            ['code' => 'tennis', 'name_es' => 'Tenis'],
            ['code' => 'paddle_tennis', 'name_es' => 'Pádel'],
            ['code' => 'table_tennis', 'name_es' => 'Tenis de mesa'],
            ['code' => 'bowling', 'name_es' => 'Bowling'],
            ['code' => 'billiards', 'name_es' => 'Billar'],
            ['code' => 'archery', 'name_es' => 'Tiro con arco'],
            ['code' => 'shooting_range', 'name_es' => 'Tiro deportivo'],
        ],
        'Actividades familiares' => [
            ['code' => 'kids_activities', 'name_es' => 'Actividades para niños'],
            ['code' => 'playground', 'name_es' => 'Área de juegos'],
            ['code' => 'petting_zoo', 'name_es' => 'Granja educativa'],
            ['code' => 'educational_workshop', 'name_es' => 'Taller educativo'],
            ['code' => 'treasure_hunt', 'name_es' => 'Búsqueda del tesoro'],
            ['code' => 'interactive_experience', 'name_es' => 'Experiencia interactiva'],
        ],
        'Experiencias rurales' => [
            ['code' => 'farm_experience', 'name_es' => 'Experiencia de granja'],
            ['code' => 'animal_feeding', 'name_es' => 'Alimentación de animales'],
            ['code' => 'sheep_shearing', 'name_es' => 'Esquila de ovejas'],
            ['code' => 'cheese_making', 'name_es' => 'Elaboración de quesos'],
            ['code' => 'horse_training', 'name_es' => 'Entrenamiento de caballos'],
            ['code' => 'gaucho_experience', 'name_es' => 'Experiencia gaucha'],
        ],
        'Transporte recreativo' => [
            ['code' => 'scenic_flight', 'name_es' => 'Vuelo panorámico'],
            ['code' => 'helicopter_tour', 'name_es' => 'Tour en helicóptero'],
            ['code' => 'hot_air_balloon', 'name_es' => 'Globo aerostático'],
            ['code' => 'train_ride', 'name_es' => 'Paseo en tren'],
            ['code' => 'boat_cruise', 'name_es' => 'Paseo en barco'],
            ['code' => 'catamaran_cruise', 'name_es' => 'Paseo en catamarán'],
            ['code' => 'sunset_cruise', 'name_es' => 'Crucero al atardecer'],
        ],
        'Aventura' => [
            ['code' => 'offroad', 'name_es' => 'Off-road'],
            ['code' => 'atv_riding', 'name_es' => 'Paseo en cuatriciclo'],
            ['code' => 'buggy_riding', 'name_es' => 'Paseo en buggy'],
            ['code' => 'sandboarding', 'name_es' => 'Sandboard'],
            ['code' => 'dune_bashing', 'name_es' => 'Recorrido por dunas'],
            ['code' => 'survival_experience', 'name_es' => 'Experiencia de supervivencia'],
            ['code' => 'adventure_course', 'name_es' => 'Circuito de aventura'],
        ],
        'Educativas' => [
            ['code' => 'workshop', 'name_es' => 'Taller'],
            ['code' => 'craft_workshop', 'name_es' => 'Taller de artesanía'],
            ['code' => 'photography_workshop', 'name_es' => 'Taller de fotografía'],
            ['code' => 'language_class', 'name_es' => 'Clase de idioma'],
            ['code' => 'art_class', 'name_es' => 'Clase de arte'],
        ],
        'Observación' => [
            ['code' => 'aurora_viewing', 'name_es' => 'Observación de auroras'],
            ['code' => 'eclipse_viewing', 'name_es' => 'Observación de eclipses'],
            ['code' => 'sunrise_viewing', 'name_es' => 'Observación del amanecer'],
            ['code' => 'sunset_viewing', 'name_es' => 'Observación del atardecer'],
            ['code' => 'panoramic_viewpoint', 'name_es' => 'Mirador panorámico'],
        ],
        'Relax' => [
            ['code' => 'beach_relaxation', 'name_es' => 'Relax en la playa'],
            ['code' => 'picnic', 'name_es' => 'Picnic'],
            ['code' => 'campfire', 'name_es' => 'Fogón'],
            ['code' => 'camping', 'name_es' => 'Camping'],
            ['code' => 'glamping', 'name_es' => 'Glamping'],
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

        foreach ($this->data as $categoryNameEs => $activities) {
            $category = ServiceActivityCategory::query()
                ->whereHas('translations', function ($q) use ($spanishLanguage, $categoryNameEs): void {
                    $q->where('language_id', $spanishLanguage->id)
                        ->where('name', $categoryNameEs);
                })
                ->first();

            if (! $category) {
                $missingCategories[] = $categoryNameEs;

                continue;
            }

            foreach ($activities as $index => $item) {
                $code = $item['code'];
                $nameEs = $item['name_es'];

                $exists = ServiceActivity::query()->where('code', $code)->exists();
                if ($exists) {
                    $skipped++;

                    continue;
                }

                if (! $dryRun) {
                    $activity = ServiceActivity::create([
                        'code' => $code,
                        'service_activity_category_id' => $category->id,
                        'sort_order' => $index + 1,
                        'active' => true,
                    ]);

                    ServiceActivityTranslation::create([
                        'service_activity_id' => $activity->id,
                        'language_id' => $spanishLanguage->id,
                        'name' => $nameEs,
                    ]);
                }

                $created++;
            }
        }

        if ($missingCategories !== []) {
            $this->warn('Categories not found (Spanish translation name must match exactly):');
            foreach ($missingCategories as $name) {
                $this->line("  - {$name}");
            }
        }

        $this->info($dryRun
            ? "Would create {$created} activities (skip {$skipped} existing)."
            : "Created {$created} activities, skipped {$skipped} existing.");

        return self::SUCCESS;
    }
}
