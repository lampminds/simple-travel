<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

/**
 * Project currencies in cat_currencies (references lmp_currencies.id on the addons connection).
 */
class CatCurrenciesTableSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        Currency::query()->delete();

        foreach ([5, 9, 27] as $lmpCurrencyId) {
            Currency::create([
                'currency_id' => $lmpCurrencyId,
            ]);
        }
    }
}
