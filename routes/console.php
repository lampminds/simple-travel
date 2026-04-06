<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use App\Models\Language;
use App\Models\ServiceGastronomyFeature;
use App\Models\ServiceGastronomyFeatureCategory;
use App\Models\ServiceGastronomyMenu;
use App\Models\ServiceGastronomyMenuCategory;
use App\Models\ServiceFeature;
use App\Models\ServiceFeatureCategory;
use App\Models\ServiceType;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('service-gastronomy-menus:import {--language-id= : Id of the Spanish language in the languages table} {--dry-run : Show what would be created/updated without writing}', function (): void {
    $languageId = (int) ($this->option('language-id') ?? 0);
    if ($languageId < 1) {
        $this->error('Option --language-id is required (e.g. --language-id=2 for Spanish).');

        return;
    }

    $language = Language::find($languageId);
    if (! $language) {
        $this->error("Language with id {$languageId} not found.");

        return;
    }

    $dryRun = (bool) $this->option('dry-run');
    if ($dryRun) {
        $this->info('[DRY RUN] No changes will be written.');
    }

    $created = 0;
    $updated = 0;
    $sortOrder = 0;

    $rows = [
        ['code' => 'a_la_carte', 'name' => 'A la carta'],
        ['code' => 'fixed_menu', 'name' => 'Menú fijo'],
        ['code' => 'tasting_menu', 'name' => 'Menú degustación'],
        ['code' => 'set_menu', 'name' => 'Menú del día'],
        ['code' => 'group_menu', 'name' => 'Menú para grupos'],
        ['code' => 'event_menu', 'name' => 'Menú de evento'],
        ['code' => 'kids_menu', 'name' => 'Menú infantil'],
        ['code' => 'beverage_menu', 'name' => 'Carta de bebidas'],
        ['code' => 'wine_list', 'name' => 'Carta de vinos'],
        ['code' => 'brunch_menu', 'name' => 'Menú brunch'],
        ['code' => 'breakfast_menu', 'name' => 'Desayuno'],
        ['code' => 'lunch_menu', 'name' => 'Almuerzo'],
        ['code' => 'dinner_menu', 'name' => 'Cena'],
        ['code' => 'seasonal_menu', 'name' => 'Menú de temporada'],
    ];

    foreach ($rows as $row) {
        $menu = ServiceGastronomyMenu::where('code', $row['code'])->first();

        if ($menu) {
            if (! $dryRun) {
                $trans = $menu->translations()->firstOrNew(['language_id' => $languageId]);
                $trans->name = $row['name'];
                $trans->description = null;
                $trans->save();
            }

            $updated++;
            $this->line("  Update: {$row['code']} → {$row['name']}");
        } else {
            if (! $dryRun) {
                $menu = ServiceGastronomyMenu::create([
                    'code' => $row['code'],
                    'is_default' => false,
                    'sort_order' => $sortOrder,
                    'active' => true,
                ]);

                $menu->translations()->create([
                    'language_id' => $languageId,
                    'name' => $row['name'],
                    'description' => null,
                ]);
            }

            $created++;
            $this->line("  Create: {$row['code']} → {$row['name']}");
        }

        $sortOrder++;
    }

    $this->newLine();
    $this->info("Done. Created: {$created}, Updated: {$updated}.");
});

Artisan::command('service-gastronomy-menu-categories:import {--language-id= : Id of the Spanish language in the languages table} {--dry-run : Show what would be created/updated without writing}', function (): void {
    $languageId = (int) ($this->option('language-id') ?? 0);
    if ($languageId < 1) {
        $this->error('Option --language-id is required (e.g. --language-id=2 for Spanish).');

        return;
    }

    $language = Language::find($languageId);
    if (! $language) {
        $this->error("Language with id {$languageId} not found.");

        return;
    }

    $dryRun = (bool) $this->option('dry-run');
    if ($dryRun) {
        $this->info('[DRY RUN] No changes will be written.');
    }

    $created = 0;
    $updated = 0;
    $sortOrder = 0;

    $rows = [
        ['code' => 'appetizers', 'name' => 'Aperitivos'],
        ['code' => 'soups', 'name' => 'Sopas'],
        ['code' => 'salads', 'name' => 'Ensaladas'],
        ['code' => 'pasta', 'name' => 'Pastas'],
        ['code' => 'rice', 'name' => 'Arroces'],
        ['code' => 'meat', 'name' => 'Carnes'],
        ['code' => 'seafood', 'name' => 'Mariscos'],
        ['code' => 'vegetarian', 'name' => 'Vegetarianos'],
        ['code' => 'vegan', 'name' => 'Veganos'],
        ['code' => 'sides', 'name' => 'Guarniciones'],
        ['code' => 'snacks', 'name' => 'Snacks'],
        ['code' => 'sandwiches', 'name' => 'Sándwiches'],
        ['code' => 'pizza', 'name' => 'Pizza'],
        ['code' => 'grill', 'name' => 'Parrilla'],
        ['code' => 'bakery', 'name' => 'Panadería'],
        ['code' => 'pastries', 'name' => 'Pastelería'],
        ['code' => 'breakfast', 'name' => 'Desayuno'],
        ['code' => 'brunch', 'name' => 'Brunch'],
        ['code' => 'kids', 'name' => 'Infantil'],
        ['code' => 'tasting_courses', 'name' => 'Pasos degustación'],
        ['code' => 'drinks_alcoholic', 'name' => 'Bebidas alcohólicas'],
        ['code' => 'drinks_non_alcoholic', 'name' => 'Bebidas sin alcohol'],
        ['code' => 'wines', 'name' => 'Vinos'],
        ['code' => 'beers', 'name' => 'Cervezas'],
        ['code' => 'cocktails', 'name' => 'Cócteles'],
        ['code' => 'hot_drinks', 'name' => 'Bebidas calientes'],
        ['code' => 'cold_drinks', 'name' => 'Bebidas frías'],
    ];

    foreach ($rows as $row) {
        $category = ServiceGastronomyMenuCategory::where('code', $row['code'])->first();

        if ($category) {
            if (! $dryRun) {
                $trans = $category->translations()->firstOrNew(['language_id' => $languageId]);
                $trans->name = $row['name'];
                $trans->save();
            }

            $updated++;
            $this->line("  Update: {$row['code']} → {$row['name']}");
        } else {
            if (! $dryRun) {
                $category = ServiceGastronomyMenuCategory::create([
                    'code' => $row['code'],
                    'sort_order' => $sortOrder,
                    'active' => true,
                ]);

                $category->translations()->create([
                    'language_id' => $languageId,
                    'name' => $row['name'],
                ]);
            }

            $created++;
            $this->line("  Create: {$row['code']} → {$row['name']}");
        }

        $sortOrder++;
    }

    $this->newLine();
    $this->info("Done. Created: {$created}, Updated: {$updated}.");
});

Artisan::command('service-gastronomy-feature-categories:import {--language-id= : Id of the Spanish language in the languages table} {--dry-run : Show what would be created/updated without writing}', function (): void {
    $languageId = (int) ($this->option('language-id') ?? 0);
    if ($languageId < 1) {
        $this->error('Option --language-id is required (e.g. --language-id=2 for Spanish).');

        return;
    }

    $language = Language::find($languageId);
    if (! $language) {
        $this->error("Language with id {$languageId} not found.");

        return;
    }

    $dryRun = (bool) $this->option('dry-run');
    if ($dryRun) {
        $this->info('[DRY RUN] No changes will be written.');
    }

    $created = 0;
    $updated = 0;
    $sortOrder = 0;

    $rows = [
        ['code' => 'food', 'name' => 'Comida'],
        ['code' => 'drinks', 'name' => 'Bebidas'],
        ['code' => 'dietary', 'name' => 'Dietas'],
        ['code' => 'service', 'name' => 'Servicio'],
        ['code' => 'ambiance', 'name' => 'Ambiente'],
        ['code' => 'facilities', 'name' => 'Instalaciones'],
        ['code' => 'accessibility', 'name' => 'Accesibilidad'],
        ['code' => 'payment', 'name' => 'Métodos de pago'],
        ['code' => 'reservation', 'name' => 'Reservas'],
        ['code' => 'location', 'name' => 'Ubicación'],
        ['code' => 'experience', 'name' => 'Experiencia'],
    ];

    foreach ($rows as $row) {
        $category = ServiceGastronomyFeatureCategory::where('code', $row['code'])->first();

        if ($category) {
            if (! $dryRun) {
                $trans = $category->translations()->firstOrNew(['language_id' => $languageId]);
                $trans->name = $row['name'];
                $trans->save();
            }

            $updated++;
            $this->line("  Update: {$row['code']} → {$row['name']}");
        } else {
            if (! $dryRun) {
                $category = ServiceGastronomyFeatureCategory::create([
                    'code' => $row['code'],
                    'sort_order' => $sortOrder,
                    'active' => true,
                ]);

                $category->translations()->create([
                    'language_id' => $languageId,
                    'name' => $row['name'],
                ]);
            }

            $created++;
            $this->line("  Create: {$row['code']} → {$row['name']}");
        }

        $sortOrder++;
    }

    $this->newLine();
    $this->info("Done. Created: {$created}, Updated: {$updated}.");
});

Artisan::command('service-gastronomy-features:import {--language-id= : Id of the Spanish language in the languages table} {--dry-run : Show what would be created/updated without writing}', function (): void {
    $languageId = (int) ($this->option('language-id') ?? 0);
    if ($languageId < 1) {
        $this->error('Option --language-id is required (e.g. --language-id=2 for Spanish).');

        return;
    }

    $language = Language::find($languageId);
    if (! $language) {
        $this->error("Language with id {$languageId} not found.");

        return;
    }

    $dryRun = (bool) $this->option('dry-run');
    if ($dryRun) {
        $this->info('[DRY RUN] No changes will be written.');
    }

    $created = 0;
    $updated = 0;
    $sortOrder = 0;

    $rows = [
        ['category_code' => 'food', 'code' => 'organic_food', 'name' => 'Comida orgánica'],
        ['category_code' => 'food', 'code' => 'locally_sourced', 'name' => 'Ingredientes locales'],
        ['category_code' => 'food', 'code' => 'homemade', 'name' => 'Comida casera'],
        ['category_code' => 'food', 'code' => 'seasonal_menu', 'name' => 'Menú de temporada'],
        ['category_code' => 'drinks', 'code' => 'wine_selection', 'name' => 'Buena selección de vinos'],
        ['category_code' => 'drinks', 'code' => 'craft_beer', 'name' => 'Cerveza artesanal'],
        ['category_code' => 'drinks', 'code' => 'cocktails', 'name' => 'Cócteles'],
        ['category_code' => 'drinks', 'code' => 'specialty_coffee', 'name' => 'Café de especialidad'],
        ['category_code' => 'dietary', 'code' => 'vegetarian_options', 'name' => 'Opciones vegetarianas'],
        ['category_code' => 'dietary', 'code' => 'vegan_options', 'name' => 'Opciones veganas'],
        ['category_code' => 'dietary', 'code' => 'gluten_free_options', 'name' => 'Opciones sin gluten'],
        ['category_code' => 'dietary', 'code' => 'lactose_free_options', 'name' => 'Sin lactosa'],
        ['category_code' => 'service', 'code' => 'table_service', 'name' => 'Servicio a la mesa'],
        ['category_code' => 'service', 'code' => 'self_service', 'name' => 'Autoservicio'],
        ['category_code' => 'service', 'code' => 'takeaway', 'name' => 'Para llevar'],
        ['category_code' => 'service', 'code' => 'delivery', 'name' => 'Delivery'],
        ['category_code' => 'service', 'code' => 'sommelier', 'name' => 'Sommelier'],
        ['category_code' => 'service', 'code' => 'multilingual_staff', 'name' => 'Personal multilingüe'],
        ['category_code' => 'ambiance', 'code' => 'live_music', 'name' => 'Música en vivo'],
        ['category_code' => 'ambiance', 'code' => 'dj', 'name' => 'DJ'],
        ['category_code' => 'ambiance', 'code' => 'romantic', 'name' => 'Ambiente romántico'],
        ['category_code' => 'ambiance', 'code' => 'family_friendly', 'name' => 'Familiar'],
        ['category_code' => 'ambiance', 'code' => 'adults_only', 'name' => 'Solo adultos'],
        ['category_code' => 'ambiance', 'code' => 'panoramic_view', 'name' => 'Vista panorámica'],
        ['category_code' => 'facilities', 'code' => 'outdoor_seating', 'name' => 'Mesas al aire libre'],
        ['category_code' => 'facilities', 'code' => 'indoor_seating', 'name' => 'Interior'],
        ['category_code' => 'facilities', 'code' => 'terrace', 'name' => 'Terraza'],
        ['category_code' => 'facilities', 'code' => 'parking', 'name' => 'Estacionamiento'],
        ['category_code' => 'facilities', 'code' => 'wifi', 'name' => 'WiFi'],
        ['category_code' => 'facilities', 'code' => 'air_conditioning', 'name' => 'Aire acondicionado'],
        ['category_code' => 'facilities', 'code' => 'heating', 'name' => 'Calefacción'],
        ['category_code' => 'accessibility', 'code' => 'wheelchair_access', 'name' => 'Acceso para sillas de ruedas'],
        ['category_code' => 'accessibility', 'code' => 'accessible_restroom', 'name' => 'Baño accesible'],
        ['category_code' => 'payment', 'code' => 'credit_cards', 'name' => 'Tarjetas de crédito'],
        ['category_code' => 'payment', 'code' => 'debit_cards', 'name' => 'Tarjetas de débito'],
        ['category_code' => 'payment', 'code' => 'cash', 'name' => 'Efectivo'],
        ['category_code' => 'payment', 'code' => 'digital_payments', 'name' => 'Pagos digitales'],
        ['category_code' => 'reservation', 'code' => 'reservation_required', 'name' => 'Requiere reserva'],
        ['category_code' => 'reservation', 'code' => 'reservation_recommended', 'name' => 'Reserva recomendada'],
        ['category_code' => 'reservation', 'code' => 'walk_ins_allowed', 'name' => 'Sin reserva'],
        ['category_code' => 'location', 'code' => 'beachfront', 'name' => 'Frente a la playa'],
        ['category_code' => 'location', 'code' => 'city_center', 'name' => 'Centro'],
        ['category_code' => 'location', 'code' => 'mountain_view', 'name' => 'Vista a la montaña'],
        ['category_code' => 'location', 'code' => 'lake_view', 'name' => 'Vista al lago'],
        ['category_code' => 'experience', 'code' => 'guided_experience', 'name' => 'Experiencia guiada'],
        ['category_code' => 'experience', 'code' => 'interactive', 'name' => 'Interactiva'],
        ['category_code' => 'experience', 'code' => 'show_included', 'name' => 'Incluye espectáculo'],
    ];

    foreach ($rows as $row) {
        $category = ServiceGastronomyFeatureCategory::where('code', $row['category_code'])->first();
        if (! $category) {
            $this->warn("  Skip {$row['code']}: category code \"{$row['category_code']}\" not found. Run service-gastronomy-feature-categories:import first.");

            continue;
        }

        $feature = ServiceGastronomyFeature::where('code', $row['code'])->first();

        if ($feature) {
            if (! $dryRun) {
                $feature->service_feature_category_id = $category->id;
                $feature->save();

                $trans = $feature->translations()->firstOrNew(['language_id' => $languageId]);
                $trans->name = $row['name'];
                $trans->save();
            }

            $updated++;
            $this->line("  Update: {$row['code']} → {$row['name']}");
        } else {
            if (! $dryRun) {
                $feature = ServiceGastronomyFeature::create([
                    'code' => $row['code'],
                    'service_feature_category_id' => $category->id,
                    'sort_order' => $sortOrder,
                    'active' => true,
                ]);

                $feature->translations()->create([
                    'language_id' => $languageId,
                    'name' => $row['name'],
                ]);
            }

            $created++;
            $this->line("  Create: {$row['code']} → {$row['name']}");
        }

        $sortOrder++;
    }

    $this->newLine();
    $this->info("Done. Created: {$created}, Updated: {$updated}.");
});

Artisan::command('service-feature-categories:import {--language-id= : Id of the Spanish language in the languages table} {--dry-run : Show what would be created/updated without writing}', function (): void {
    $languageId = (int) ($this->option('language-id') ?? 0);
    if ($languageId < 1) {
        $this->error('Option --language-id is required (e.g. --language-id=2 for Spanish).');

        return;
    }

    $language = Language::find($languageId);
    if (! $language) {
        $this->error("Language with id {$languageId} not found.");

        return;
    }

    $dryRun = (bool) $this->option('dry-run');
    if ($dryRun) {
        $this->info('[DRY RUN] No changes will be written.');
    }

    $created = 0;
    $sortOrder = 0;

    $rows = [
        ['code' => 'connectivity', 'name' => 'Conectividad'],
        ['code' => 'facilities', 'name' => 'Instalaciones'],
        ['code' => 'services', 'name' => 'Servicios'],
        ['code' => 'food_drink', 'name' => 'Comida y bebida'],
        ['code' => 'experience', 'name' => 'Experiencia'],
        ['code' => 'accessibility', 'name' => 'Accesibilidad'],
        ['code' => 'ambiance', 'name' => 'Ambiente'],
        ['code' => 'location', 'name' => 'Ubicación'],
        ['code' => 'policies', 'name' => 'Políticas'],
    ];

    if (! $dryRun) {
        // Reset the full feature catalog so old values are discarded.
        DB::table('cat_service_feature_scopes')->delete();
        DB::table('cat_service_feature_translations')->delete();
        DB::table('cat_service_features')->delete();
        DB::table('cat_service_feature_category_translations')->delete();
        DB::table('cat_service_feature_categories')->delete();
    }

    foreach ($rows as $row) {
        if (! $dryRun) {
            $category = ServiceFeatureCategory::create([
                'code' => $row['code'],
                'sort_order' => $sortOrder,
                'active' => true,
            ]);

            $category->translations()->create([
                'language_id' => $languageId,
                'name' => $row['name'],
            ]);
        }

        $created++;
        $this->line("  Create: {$row['code']} → {$row['name']}");
        $sortOrder++;
    }

    $this->newLine();
    $this->info("Done. Created: {$created} categories.");
});

Artisan::command('service-features:import {--language-id= : Id of the Spanish language in the languages table} {--dry-run : Show what would be created/updated without writing}', function (): void {
    $languageId = (int) ($this->option('language-id') ?? 0);
    if ($languageId < 1) {
        $this->error('Option --language-id is required (e.g. --language-id=2 for Spanish).');

        return;
    }

    $language = Language::find($languageId);
    if (! $language) {
        $this->error("Language with id {$languageId} not found.");

        return;
    }

    $dryRun = (bool) $this->option('dry-run');
    if ($dryRun) {
        $this->info('[DRY RUN] No changes will be written.');
    }

    $created = 0;
    $sortOrder = 0;

    $rows = [
        // CONNECTIVITY
        ['category_code' => 'connectivity', 'code' => 'wifi', 'name' => 'WiFi'],
        ['category_code' => 'connectivity', 'code' => 'charging_station', 'name' => 'Estación de carga'],

        // FACILITIES
        ['category_code' => 'facilities', 'code' => 'parking', 'name' => 'Estacionamiento'],
        ['category_code' => 'facilities', 'code' => 'indoor', 'name' => 'Interior'],
        ['category_code' => 'facilities', 'code' => 'outdoor', 'name' => 'Exterior'],
        ['category_code' => 'facilities', 'code' => 'terrace', 'name' => 'Terraza'],
        ['category_code' => 'facilities', 'code' => 'garden', 'name' => 'Jardín'],
        ['category_code' => 'facilities', 'code' => 'pool', 'name' => 'Piscina'],
        ['category_code' => 'facilities', 'code' => 'spa', 'name' => 'Spa'],
        ['category_code' => 'facilities', 'code' => 'gym', 'name' => 'Gimnasio'],
        ['category_code' => 'facilities', 'code' => 'restroom', 'name' => 'Baños'],

        // SERVICES
        ['category_code' => 'services', 'code' => 'reception_24h', 'name' => 'Recepción 24h'],
        ['category_code' => 'services', 'code' => 'housekeeping', 'name' => 'Limpieza'],
        ['category_code' => 'services', 'code' => 'room_service', 'name' => 'Room service'],
        ['category_code' => 'services', 'code' => 'car_rental', 'name' => 'Alquiler de autos'],
        ['category_code' => 'services', 'code' => 'shuttle_service', 'name' => 'Shuttle / traslado'],
        ['category_code' => 'services', 'code' => 'takeaway', 'name' => 'Para llevar'],
        ['category_code' => 'services', 'code' => 'delivery', 'name' => 'Delivery'],
        ['category_code' => 'services', 'code' => 'table_service', 'name' => 'Servicio a la mesa'],
        ['category_code' => 'services', 'code' => 'self_service', 'name' => 'Autoservicio'],
        ['category_code' => 'services', 'code' => 'multilingual_staff', 'name' => 'Personal multilingüe'],

        // FOOD & DRINK
        ['category_code' => 'food_drink', 'code' => 'breakfast_included', 'name' => 'Desayuno incluido'],
        ['category_code' => 'food_drink', 'code' => 'restaurant_on_site', 'name' => 'Restaurante en el lugar'],
        ['category_code' => 'food_drink', 'code' => 'bar_on_site', 'name' => 'Bar en el lugar'],
        ['category_code' => 'food_drink', 'code' => 'drinks_included', 'name' => 'Bebidas incluidas'],
        ['category_code' => 'food_drink', 'code' => 'alcohol_available', 'name' => 'Alcohol disponible'],
        ['category_code' => 'food_drink', 'code' => 'vegetarian_options', 'name' => 'Opciones vegetarianas'],
        ['category_code' => 'food_drink', 'code' => 'vegan_options', 'name' => 'Opciones veganas'],
        ['category_code' => 'food_drink', 'code' => 'gluten_free_options', 'name' => 'Opciones sin gluten'],

        // EXPERIENCE
        ['category_code' => 'experience', 'code' => 'guided', 'name' => 'Guiado'],
        ['category_code' => 'experience', 'code' => 'private', 'name' => 'Experiencia privada'],
        ['category_code' => 'experience', 'code' => 'group_friendly', 'name' => 'Apto grupos'],
        ['category_code' => 'experience', 'code' => 'interactive', 'name' => 'Interactivo'],
        ['category_code' => 'experience', 'code' => 'educational', 'name' => 'Educativo'],
        ['category_code' => 'experience', 'code' => 'adventure', 'name' => 'Aventura'],
        ['category_code' => 'experience', 'code' => 'cultural', 'name' => 'Cultural'],
        ['category_code' => 'experience', 'code' => 'relaxing', 'name' => 'Relax'],
        ['category_code' => 'experience', 'code' => 'live_music', 'name' => 'Música en vivo'],
        ['category_code' => 'experience', 'code' => 'show_included', 'name' => 'Incluye espectáculo'],

        // ACCESSIBILITY
        ['category_code' => 'accessibility', 'code' => 'wheelchair_access', 'name' => 'Acceso silla de ruedas'],
        ['category_code' => 'accessibility', 'code' => 'accessible_restroom', 'name' => 'Baño accesible'],
        ['category_code' => 'accessibility', 'code' => 'elevator', 'name' => 'Ascensor'],

        // AMBIANCE
        ['category_code' => 'ambiance', 'code' => 'romantic', 'name' => 'Romántico'],
        ['category_code' => 'ambiance', 'code' => 'family_friendly', 'name' => 'Familiar'],
        ['category_code' => 'ambiance', 'code' => 'adults_only', 'name' => 'Solo adultos'],
        ['category_code' => 'ambiance', 'code' => 'quiet', 'name' => 'Tranquilo'],
        ['category_code' => 'ambiance', 'code' => 'lively', 'name' => 'Animado'],

        // LOCATION
        ['category_code' => 'location', 'code' => 'city_center', 'name' => 'Centro'],
        ['category_code' => 'location', 'code' => 'beachfront', 'name' => 'Frente a la playa'],
        ['category_code' => 'location', 'code' => 'lakeside', 'name' => 'Frente al lago'],
        ['category_code' => 'location', 'code' => 'mountain_area', 'name' => 'Zona de montaña'],
        ['category_code' => 'location', 'code' => 'rural_area', 'name' => 'Zona rural'],
        ['category_code' => 'location', 'code' => 'panoramic_view', 'name' => 'Vista panorámica'],

        // POLICIES
        ['category_code' => 'policies', 'code' => 'reservation_required', 'name' => 'Requiere reserva'],
        ['category_code' => 'policies', 'code' => 'free_cancellation', 'name' => 'Cancelación gratuita'],
        ['category_code' => 'policies', 'code' => 'instant_confirmation', 'name' => 'Confirmación inmediata'],
    ];

    if (! $dryRun) {
        DB::table('cat_service_feature_scopes')->delete();
        DB::table('cat_service_feature_translations')->delete();
        DB::table('cat_service_features')->delete();
    }

    foreach ($rows as $row) {
        $category = ServiceFeatureCategory::where('code', $row['category_code'])->first();
        if (! $category) {
            $this->warn("  Skip {$row['code']}: category code \"{$row['category_code']}\" not found. Run service-feature-categories:import first.");

            continue;
        }

        if (! $dryRun) {
            $feature = ServiceFeature::create([
                'code' => $row['code'],
                'service_feature_category_id' => $category->id,
                'sort_order' => $sortOrder,
                'active' => true,
            ]);

            $feature->translations()->create([
                'language_id' => $languageId,
                'name' => $row['name'],
                'description' => null,
            ]);
        }

        $created++;
        $this->line("  Create: {$row['code']} → {$row['name']}");
        $sortOrder++;
    }

    $this->newLine();
    $this->info("Done. Created: {$created} features.");
});

Artisan::command('service-feature-translations:fill-missing
    {--language-id=2 : Target language id in the languages table}
    {--source-language-id= : Preferred source language id (optional)}
    {--use-code-fallback : Allow fallback from feature code when no source translation exists}
    {--fill-empty : Also fill empty name/description on existing target rows}
    {--overwrite : Overwrite existing target name/description with resolved values}
    {--dry-run : Show what would be created/updated without writing}', function (): void {
    $languageId = (int) ($this->option('language-id') ?? 0);
    if ($languageId < 1) {
        $this->error('Option --language-id must be a valid id (e.g. --language-id=2 for Spanish).');

        return;
    }

    $targetLanguage = Language::find($languageId);
    if (! $targetLanguage) {
        $this->error("Language with id {$languageId} not found.");

        return;
    }

    $sourceLanguageId = $this->option('source-language-id');
    $sourceLanguageId = $sourceLanguageId !== null && $sourceLanguageId !== ''
        ? (int) $sourceLanguageId
        : null;

    if ($sourceLanguageId !== null && ! Language::find($sourceLanguageId)) {
        $this->error("Source language with id {$sourceLanguageId} not found.");

        return;
    }

    $fillEmpty = (bool) $this->option('fill-empty');
    $overwrite = (bool) $this->option('overwrite');
    $useCodeFallback = (bool) $this->option('use-code-fallback');
    $dryRun = (bool) $this->option('dry-run');
    if ($dryRun) {
        $this->info('[DRY RUN] No changes will be written.');
    }

    $created = 0;
    $updated = 0;
    $skippedNoSource = 0;

    $features = ServiceFeature::query()
        ->with(['translations' => fn ($q) => $q->orderBy('language_id')])
        ->orderBy('id')
        ->get();

    // Canonical ES labels from the current service-features import dataset.
    $spanishNamesByCode = [
        'wifi' => 'WiFi',
        'charging_station' => 'Estación de carga',
        'parking' => 'Estacionamiento',
        'indoor' => 'Interior',
        'outdoor' => 'Exterior',
        'terrace' => 'Terraza',
        'garden' => 'Jardín',
        'pool' => 'Piscina',
        'spa' => 'Spa',
        'gym' => 'Gimnasio',
        'restroom' => 'Baños',
        'reception_24h' => 'Recepción 24h',
        'housekeeping' => 'Limpieza',
        'room_service' => 'Room service',
        'car_rental' => 'Alquiler de autos',
        'shuttle_service' => 'Shuttle / traslado',
        'takeaway' => 'Para llevar',
        'delivery' => 'Delivery',
        'table_service' => 'Servicio a la mesa',
        'self_service' => 'Autoservicio',
        'multilingual_staff' => 'Personal multilingüe',
        'breakfast_included' => 'Desayuno incluido',
        'restaurant_on_site' => 'Restaurante en el lugar',
        'bar_on_site' => 'Bar en el lugar',
        'drinks_included' => 'Bebidas incluidas',
        'alcohol_available' => 'Alcohol disponible',
        'vegetarian_options' => 'Opciones vegetarianas',
        'vegan_options' => 'Opciones veganas',
        'gluten_free_options' => 'Opciones sin gluten',
        'guided' => 'Guiado',
        'private' => 'Experiencia privada',
        'group_friendly' => 'Apto grupos',
        'interactive' => 'Interactivo',
        'educational' => 'Educativo',
        'adventure' => 'Aventura',
        'cultural' => 'Cultural',
        'relaxing' => 'Relax',
        'live_music' => 'Música en vivo',
        'show_included' => 'Incluye espectáculo',
        'wheelchair_access' => 'Acceso silla de ruedas',
        'accessible_restroom' => 'Baño accesible',
        'elevator' => 'Ascensor',
        'romantic' => 'Romántico',
        'family_friendly' => 'Familiar',
        'adults_only' => 'Solo adultos',
        'quiet' => 'Tranquilo',
        'lively' => 'Animado',
        'city_center' => 'Centro',
        'beachfront' => 'Frente a la playa',
        'lakeside' => 'Frente al lago',
        'mountain_area' => 'Zona de montaña',
        'rural_area' => 'Zona rural',
        'panoramic_view' => 'Vista panorámica',
        'reservation_required' => 'Requiere reserva',
        'free_cancellation' => 'Cancelación gratuita',
        'instant_confirmation' => 'Confirmación inmediata',
    ];

    foreach ($features as $feature) {
        $translations = $feature->translations;
        $target = $translations->firstWhere('language_id', $languageId);

        if ($target && ! $fillEmpty && ! $overwrite) {
            continue;
        }

        $source = null;
        $name = null;
        $description = null;

        if ($languageId === 2 && isset($spanishNamesByCode[$feature->code])) {
            $name = $spanishNamesByCode[$feature->code];
        }

        if ($sourceLanguageId !== null) {
            $source = $translations->firstWhere('language_id', $sourceLanguageId);
        }

        if (! $source && $name === null) {
            $source = $translations->first(fn ($t) => (int) $t->language_id !== $languageId);
        }

        if ($name === null) {
            $fromSource = trim((string) ($source->name ?? ''));
            $name = $fromSource !== '' ? $fromSource : null;
            $description = $source->description ?? null;
        }

        if (($name === null || $name === '') && $useCodeFallback) {
            $fallbackName = trim(str_replace('_', ' ', (string) $feature->code));
            $name = $fallbackName !== '' ? mb_convert_case($fallbackName, MB_CASE_TITLE, 'UTF-8') : null;
        }

        if ($name === null || $name === '') {
            $skippedNoSource++;
            $this->warn("  Skip feature {$feature->code}: no translation source for language_id={$languageId}. Use --source-language-id or --use-code-fallback.");

            continue;
        }

        if (! $target) {
            if (! $dryRun) {
                $feature->translations()->create([
                    'language_id' => $languageId,
                    'name' => $name,
                    'description' => $description,
                ]);
            }

            $created++;
            $this->line("  Create translation: {$feature->code} -> {$name}");

            continue;
        }

        if ($overwrite) {
            $newName = $name;
            $newDescription = $description ?? $target->description;
        } else {
            $newName = trim((string) ($target->name ?? '')) !== '' ? $target->name : $name;
            $newDescription = $target->description ?? $description;
        }

        if ($newName === $target->name && $newDescription === $target->description) {
            continue;
        }

        if (! $dryRun) {
            $target->name = $newName;
            $target->description = $newDescription;
            $target->save();
        }

        $updated++;
        $this->line("  Update translation: {$feature->code}");
    }

    $this->newLine();
    $this->info("Done. Created: {$created}, Updated: {$updated}, Skipped (no source): {$skippedNoSource}.");
});

Artisan::command('service-feature-scopes:import {--dry-run : Show what would be created/updated without writing}', function (): void {
    $dryRun = (bool) $this->option('dry-run');
    if ($dryRun) {
        $this->info('[DRY RUN] No changes will be written.');
    }

    $created = 0;
    $missing = 0;
    $skippedTypes = 0;

    /** @var list<string> Scope codes: must match `cat_service_types.code`. */
    $allScopeCodes = ['hotel', 'gastronomy', 'excursion', 'transport'];

    $typeIdsByCode = ServiceType::query()->pluck('id', 'code')->all();

    $rows = [
        ['code' => 'wifi', 'scopes' => $allScopeCodes],
        ['code' => 'charging_station', 'scopes' => ['hotel', 'gastronomy', 'transport']],
        ['code' => 'parking', 'scopes' => ['hotel', 'gastronomy', 'transport']],
        ['code' => 'indoor', 'scopes' => ['hotel', 'gastronomy']],
        ['code' => 'outdoor', 'scopes' => ['gastronomy', 'excursion']],
        ['code' => 'terrace', 'scopes' => ['gastronomy', 'hotel']],
        ['code' => 'garden', 'scopes' => ['hotel', 'gastronomy']],
        ['code' => 'pool', 'scopes' => ['hotel']],
        ['code' => 'spa', 'scopes' => ['hotel']],
        ['code' => 'gym', 'scopes' => ['hotel']],
        ['code' => 'restroom', 'scopes' => ['gastronomy', 'excursion', 'transport']],
        ['code' => 'reception_24h', 'scopes' => ['hotel']],
        ['code' => 'housekeeping', 'scopes' => ['hotel']],
        ['code' => 'room_service', 'scopes' => ['hotel']],
        ['code' => 'car_rental', 'scopes' => ['hotel']],
        ['code' => 'shuttle_service', 'scopes' => ['hotel', 'excursion']],
        ['code' => 'takeaway', 'scopes' => ['gastronomy']],
        ['code' => 'delivery', 'scopes' => ['gastronomy']],
        ['code' => 'table_service', 'scopes' => ['gastronomy']],
        ['code' => 'self_service', 'scopes' => ['gastronomy']],
        ['code' => 'multilingual_staff', 'scopes' => $allScopeCodes],
        ['code' => 'breakfast_included', 'scopes' => ['hotel']],
        ['code' => 'restaurant_on_site', 'scopes' => ['hotel']],
        ['code' => 'bar_on_site', 'scopes' => ['hotel']],
        ['code' => 'drinks_included', 'scopes' => ['excursion', 'gastronomy']],
        ['code' => 'alcohol_available', 'scopes' => ['gastronomy', 'excursion']],
        ['code' => 'vegetarian_options', 'scopes' => ['gastronomy']],
        ['code' => 'vegan_options', 'scopes' => ['gastronomy']],
        ['code' => 'gluten_free_options', 'scopes' => ['gastronomy']],
        ['code' => 'guided', 'scopes' => ['excursion', 'gastronomy']],
        ['code' => 'private', 'scopes' => $allScopeCodes],
        ['code' => 'group_friendly', 'scopes' => $allScopeCodes],
        ['code' => 'interactive', 'scopes' => ['excursion', 'gastronomy']],
        ['code' => 'educational', 'scopes' => ['excursion']],
        ['code' => 'adventure', 'scopes' => ['excursion']],
        ['code' => 'cultural', 'scopes' => ['excursion', 'gastronomy']],
        ['code' => 'relaxing', 'scopes' => $allScopeCodes],
        ['code' => 'live_music', 'scopes' => ['gastronomy']],
        ['code' => 'show_included', 'scopes' => ['gastronomy']],
        ['code' => 'wheelchair_access', 'scopes' => $allScopeCodes],
        ['code' => 'accessible_restroom', 'scopes' => ['hotel', 'gastronomy']],
        ['code' => 'elevator', 'scopes' => ['hotel']],
        ['code' => 'romantic', 'scopes' => ['hotel', 'gastronomy']],
        ['code' => 'family_friendly', 'scopes' => $allScopeCodes],
        ['code' => 'adults_only', 'scopes' => ['hotel', 'gastronomy']],
        ['code' => 'quiet', 'scopes' => ['hotel']],
        ['code' => 'lively', 'scopes' => ['gastronomy']],
        ['code' => 'city_center', 'scopes' => $allScopeCodes],
        ['code' => 'beachfront', 'scopes' => ['hotel', 'gastronomy']],
        ['code' => 'lakeside', 'scopes' => ['hotel', 'gastronomy']],
        ['code' => 'mountain_area', 'scopes' => ['hotel', 'excursion']],
        ['code' => 'rural_area', 'scopes' => $allScopeCodes],
        ['code' => 'panoramic_view', 'scopes' => ['hotel', 'gastronomy']],
        ['code' => 'reservation_required', 'scopes' => ['gastronomy', 'excursion']],
        ['code' => 'free_cancellation', 'scopes' => $allScopeCodes],
        ['code' => 'instant_confirmation', 'scopes' => $allScopeCodes],
    ];

    if (! $dryRun) {
        DB::table('cat_service_feature_scopes')->delete();
    }

    foreach ($rows as $row) {
        $feature = ServiceFeature::where('code', $row['code'])->first();
        if (! $feature) {
            $missing++;
            $this->warn("  Skip {$row['code']}: feature not found. Run service-features:import first.");

            continue;
        }

        foreach ($row['scopes'] as $serviceTypeCode) {
            $serviceTypeId = isset($typeIdsByCode[$serviceTypeCode])
                ? (int) $typeIdsByCode[$serviceTypeCode]
                : null;
            if ($serviceTypeId === null) {
                $skippedTypes++;
                $this->warn("  Skip scope for {$row['code']}: no cat_service_types row with code \"{$serviceTypeCode}\".");

                continue;
            }

            if (! $dryRun) {
                DB::table('cat_service_feature_scopes')->insert([
                    'service_type_id' => $serviceTypeId,
                    'service_feature_id' => $feature->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $created++;
            $this->line("  Create scope: {$row['code']} → service_type_id={$serviceTypeId}");
        }
    }

    $this->newLine();
    $this->info("Done. Scope rows: {$created}, Missing features: {$missing}, Skipped unresolved types: {$skippedTypes}.");
});

// Daily at 7:00: fetch USD exchange rates and save one row per project currency in currency_rates.
Schedule::command('currency:fetch-rates')->dailyAt('7:00');
