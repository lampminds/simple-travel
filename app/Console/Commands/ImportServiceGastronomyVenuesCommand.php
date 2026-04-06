<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Models\ServiceGastronomyVenue;
use Illuminate\Console\Command;

/**
 * Imports cat_service_gastronomy_venues and their Spanish name translation from a fixed list.
 * Column 1 = code, Column 2 = name (ES). Description column is ignored (no storage).
 *
 * Run from project root (Debian): php artisan service-gastronomy-venues:import --language-id=2
 * Idempotent: updates existing venues by code.
 */
class ImportServiceGastronomyVenuesCommand extends Command
{
    protected $signature = 'service-gastronomy-venues:import
                            {--language-id= : Id of the Spanish language in the languages table (required)}
                            {--dry-run : Show what would be created/updated without writing}';

    protected $description = 'Import service gastronomy venues and Spanish name translation from the predefined list';

    /** @var array<int, array{code: string, name: string}> */
    protected array $rows = [
        ['code' => 'restaurant', 'name' => 'Restaurante'],
        ['code' => 'bar', 'name' => 'Bar'],
        ['code' => 'cafe', 'name' => 'Café'],
        ['code' => 'bistro', 'name' => 'Bistró'],
        ['code' => 'fine_dining', 'name' => 'Alta cocina'],
        ['code' => 'casual_dining', 'name' => 'Restaurante informal'],
        ['code' => 'fast_food', 'name' => 'Comida rápida'],
        ['code' => 'buffet', 'name' => 'Buffet'],
        ['code' => 'food_truck', 'name' => 'Food truck'],
        ['code' => 'street_food_stall', 'name' => 'Puesto callejero'],
        ['code' => 'food_market', 'name' => 'Mercado gastronómico'],
        ['code' => 'food_hall', 'name' => 'Patio de comidas'],
        ['code' => 'winery', 'name' => 'Bodega'],
        ['code' => 'brewery', 'name' => 'Cervecería'],
        ['code' => 'distillery', 'name' => 'Destilería'],
        ['code' => 'bakery', 'name' => 'Panadería'],
        ['code' => 'pastry_shop', 'name' => 'Pastelería'],
        ['code' => 'deli', 'name' => 'Delicatessen'],
        ['code' => 'ice_cream_shop', 'name' => 'Heladería'],
        ['code' => 'chocolate_shop', 'name' => 'Chocolatería'],
        ['code' => 'tea_house', 'name' => 'Casa de té'],
        ['code' => 'juice_bar', 'name' => 'Juguería'],
        ['code' => 'rooftop', 'name' => 'Rooftop'],
        ['code' => 'beach_club', 'name' => 'Club de playa'],
        ['code' => 'mountain_lodge', 'name' => 'Refugio de montaña'],
        ['code' => 'farm', 'name' => 'Granja / estancia'],
        ['code' => 'vineyard', 'name' => 'Viñedo'],
        ['code' => 'private_home', 'name' => 'Casa privada'],
        ['code' => 'event_venue', 'name' => 'Salón de eventos'],
        ['code' => 'pop_up_location', 'name' => 'Ubicación temporal'],
        ['code' => 'hotel_restaurant', 'name' => 'Restaurante de hotel'],
        ['code' => 'resort_restaurant', 'name' => 'Restaurante de resort'],
        ['code' => 'casino_restaurant', 'name' => 'Restaurante de casino'],
        ['code' => 'mall_restaurant', 'name' => 'Restaurante en shopping'],
        ['code' => 'airport_restaurant', 'name' => 'Restaurante en aeropuerto'],
        ['code' => 'train_station_restaurant', 'name' => 'Restaurante en estación'],
        ['code' => 'cruise_ship', 'name' => 'Crucero'],
        ['code' => 'outdoor_camp', 'name' => 'Campamento'],
    ];

    public function handle(): int
    {
        $languageId = (int) $this->option('language-id');
        if ($languageId < 1) {
            $this->error('Option --language-id is required (e.g. --language-id=2 for Spanish).');

            return self::FAILURE;
        }

        $language = Language::find($languageId);
        if (! $language) {
            $this->error("Language with id {$languageId} not found.");

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            $this->info('[DRY RUN] No changes will be written.');
        }

        $created = 0;
        $updated = 0;
        $sortOrder = 0;

        foreach ($this->rows as $row) {
            $venue = ServiceGastronomyVenue::where('code', $row['code'])->first();

            if ($venue) {
                if (! $dryRun) {
                    $trans = $venue->translations()->firstOrNew(['language_id' => $languageId]);
                    $trans->name = $row['name'];
                    $trans->save();
                }
                $updated++;
                $this->line("  Update: {$row['code']} → {$row['name']}");
            } else {
                if (! $dryRun) {
                    $venue = ServiceGastronomyVenue::create([
                        'code' => $row['code'],
                        'sort_order' => $sortOrder,
                        'active' => true,
                    ]);
                    $venue->translations()->create([
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

        return self::SUCCESS;
    }
}
