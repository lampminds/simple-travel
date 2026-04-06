<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Models\ServiceGastronomyCuisine;
use Illuminate\Console\Command;

/**
 * Imports cat_service_gastronomy_cuisines and their Spanish name translation from a fixed list.
 * Column 1 = code, Column 2 = name (ES).
 *
 * Run from project root (Debian): php artisan service-gastronomy-cuisines:import --language-id=2
 * Idempotent: updates existing cuisines by code.
 */
class ImportServiceGastronomyCuisinesCommand extends Command
{
    protected $signature = 'service-gastronomy-cuisines:import
                            {--language-id= : Id of the Spanish language in the languages table (required)}
                            {--dry-run : Show what would be created/updated without writing}';

    protected $description = 'Import service gastronomy cuisines and Spanish name translation from the predefined list';

    /** @var array<int, array{code: string, name: string}> */
    protected array $rows = [
        ['code' => 'argentinian', 'name' => 'Argentina'],
        ['code' => 'patagonian', 'name' => 'Patagónica'],
        ['code' => 'latin_american', 'name' => 'Latinoamericana'],
        ['code' => 'mediterranean', 'name' => 'Mediterránea'],
        ['code' => 'italian', 'name' => 'Italiana'],
        ['code' => 'spanish', 'name' => 'Española'],
        ['code' => 'french', 'name' => 'Francesa'],
        ['code' => 'american', 'name' => 'Estadounidense'],
        ['code' => 'mexican', 'name' => 'Mexicana'],
        ['code' => 'peruvian', 'name' => 'Peruana'],
        ['code' => 'brazilian', 'name' => 'Brasileña'],
        ['code' => 'asian', 'name' => 'Asiática'],
        ['code' => 'japanese', 'name' => 'Japonesa'],
        ['code' => 'chinese', 'name' => 'China'],
        ['code' => 'thai', 'name' => 'Tailandesa'],
        ['code' => 'indian', 'name' => 'India'],
        ['code' => 'middle_eastern', 'name' => 'Medio Oriente'],
        ['code' => 'international', 'name' => 'Internacional'],
        ['code' => 'fusion', 'name' => 'Fusión'],
        ['code' => 'grill', 'name' => 'Parrilla'],
        ['code' => 'bbq', 'name' => 'Barbacoa'],
        ['code' => 'steakhouse', 'name' => 'Steakhouse'],
        ['code' => 'seafood', 'name' => 'Mariscos'],
        ['code' => 'pizza', 'name' => 'Pizza'],
        ['code' => 'burgers', 'name' => 'Hamburguesas'],
        ['code' => 'sushi', 'name' => 'Sushi'],
        ['code' => 'tapas', 'name' => 'Tapas'],
        ['code' => 'sandwiches', 'name' => 'Sándwiches'],
        ['code' => 'fast_food', 'name' => 'Comida rápida'],
        ['code' => 'street_food', 'name' => 'Comida callejera'],
        ['code' => 'vegetarian', 'name' => 'Vegetariana'],
        ['code' => 'vegan', 'name' => 'Vegana'],
        ['code' => 'gluten_free', 'name' => 'Sin gluten'],
        ['code' => 'healthy', 'name' => 'Saludable'],
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
            $cuisine = ServiceGastronomyCuisine::where('code', $row['code'])->first();

            if ($cuisine) {
                if (! $dryRun) {
                    $trans = $cuisine->translations()->firstOrNew(['language_id' => $languageId]);
                    $trans->name = $row['name'];
                    $trans->save();
                }
                $updated++;
                $this->line("  Update: {$row['code']} → {$row['name']}");
            } else {
                if (! $dryRun) {
                    $cuisine = ServiceGastronomyCuisine::create([
                        'code' => $row['code'],
                        'sort_order' => $sortOrder,
                        'active' => true,
                    ]);
                    $cuisine->translations()->create([
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
