<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Services\DolarApiCurrencyRateImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Fetches official buy/sell quotes from dolarapi.com and stores system currency_rates rows.
 *
 * Quotes are in ARS per 1 unit of foreign currency. Rows use source "dolarapi".
 * Config: DOLARAPI_URL, DOLARAPI_CASA (default oficial).
 *
 * Scheduled daily via routes/console.php at the `daily_update_time` system parameter.
 */
class FetchDolarApiCurrencyRatesCommand extends Command
{
    protected $signature = 'currency:fetch-dolarapi-rates
                            {--casa= : Exchange house filter (default from config, usually oficial)}';

    protected $description = 'Fetch buy/sell rates from dolarapi.com and save currency_rates per project currency';

    public function handle(DolarApiCurrencyRateImporter $importer): int
    {
        $url = (string) config('services.dolarapi.url');
        $casa = (string) ($this->option('casa') ?: config('services.dolarapi.casa', 'oficial'));

        if ($url === '') {
            $this->error('DOLARAPI_URL must be set in .env or config/services.php');

            return self::FAILURE;
        }

        $this->info("Fetching rates from {$url} (casa: {$casa})...");

        $response = Http::timeout(30)->acceptJson()->get($url);
        if (! $response->successful()) {
            $this->error('HTTP '.$response->status().': failed to fetch cotizaciones.');

            return self::FAILURE;
        }

        $quotes = $response->json();
        if (! is_array($quotes)) {
            $this->error('Invalid API response: expected a JSON array.');

            return self::FAILURE;
        }

        $currencies = Currency::query()->with('lmpCurrency')->orderBy('id')->get();
        if ($currencies->isEmpty()) {
            $this->warn('No project currencies found in cat_currencies.');

            return self::SUCCESS;
        }

        $result = $importer->import($quotes, $currencies, $casa);

        foreach ($result['messages'] as $message) {
            $this->warn($message);
        }

        $this->info("Saved {$result['saved']} rate(s), skipped {$result['skipped']}.");

        return $result['saved'] > 0 ? self::SUCCESS : self::FAILURE;
    }
}
