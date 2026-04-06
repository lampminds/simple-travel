<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Models\ServiceGastronomyMenu;
use Illuminate\Console\Command;

/**
 * Imports cat_service_gastronomy_menus and their Spanish translations.
 * - Column 1: code
 * - Column 2: name (ES)
 * - Column 3: usage text is ignored (no storage requested for it).
 *
 * Notes:
 * - This command sets `is_default=false` and `active=true` on creation.
 * - On update it only updates the translation name (and keeps description null).
 *
 * Run from project root (Debian):
 * php artisan service-gastronomy-menus:import --language-id=2
 */
class ImportServiceGastronomyMenusCommand extends Command
{
    protected $signature = 'service-gastronomy-menus:import
                            {--language-id= : Id of the Spanish language in the languages table (required)}
                            {--dry-run : Show what would be created/updated without writing}';

    protected $description = 'Import service gastronomy menus and Spanish translations from the predefined list';

    /** @var array<int, array{code: string, name: string}> */
    protected array $rows = [
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
            $menu = ServiceGastronomyMenu::where('code', $row['code'])->first();

            if ($menu) {
                if (! $dryRun) {
                    $trans = $menu->translations()->firstOrNew(['language_id' => $languageId]);
                    $trans->name = $row['name'];
                    $trans->description = null;
                    $trans->save();
                }

                $updated++;
                $this->line(\"  Update: {$row['code']} → {$row['name']}\");
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
                $this->line(\"  Create: {$row['code']} → {$row['name']}\");
            }

            $sortOrder++;
        }

        $this->newLine();
        $this->info(\"Done. Created: {$created}, Updated: {$updated}.\");

        return self::SUCCESS;
    }
}

