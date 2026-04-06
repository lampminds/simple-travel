<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\CurrencyRate;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Fetches USD exchange rates from the configured API and stores one row per
 * project currency in currency_rates.
 *
 * Config: EXCHANGERATE_URL and EXCHANGERATE_API_KEY in .env. The API key is
 * sent as query param "api_key". Expected JSON: { "success": true, "quotes": { "USDEUR": 0.87, ... } }.
 *
 * Crontab options:
 *   - Run this command daily at 7:00:
 *     0 7 * * * cd /path/to/project && php artisan currency:fetch-rates
 *   - Or use Laravel scheduler (ensure one crontab line runs schedule:run every minute);
 *     this command is already scheduled at 7:00 in routes/console.php.
 */
class FetchCurrencyRatesCommand extends Command
{
    protected $signature = 'currency:fetch-rates';

    protected $description = 'Fetch exchange rates from API and save one currency_rates row per project currency';

    public function handle(): int
    {
        $url = config('services.exchangerate.url');
        $apiKey = config('services.exchangerate.api_key');

        if (empty($url) || empty($apiKey)) {
            $this->error('EXCHANGERATE_URL and EXCHANGERATE_API_KEY must be set in .env');

            return self::FAILURE;
        }

        $requestUrl = $this->buildUrl($url, $apiKey);

        $this->info('Fetching rates from API...');

        $json = @file_get_contents($requestUrl);
        if ($json === false) {
            $this->error('Failed to fetch URL: '.$url);

            return self::FAILURE;
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($data['quotes']) || empty($data['success'])) {
            $this->error('Invalid or unsuccessful API response');

            return self::FAILURE;
        }

        $quotes = $data['quotes'];
        $currencies = Currency::with('lmpCurrency')->get();

        if ($currencies->isEmpty()) {
            $this->warn('No project currencies found. Add currencies in the admin panel.');

            return self::SUCCESS;
        }

        $startingAt = Carbon::now()->startOfDay();
        $saved = 0;
        $skipped = 0;

        foreach ($currencies as $currency) {
            $code = $currency->lmpCurrency?->code;
            if (empty($code)) {
                $this->warn("Currency id {$currency->id} has no lmp_currency code; skipped.");
                $skipped++;

                continue;
            }

            $rate = $this->getRateForCode($quotes, $code);
            if ($rate === null) {
                $this->warn("No quote for code {$code} (currency id {$currency->id}); skipped.");
                $skipped++;

                continue;
            }

            CurrencyRate::create([
                'currency_id' => $currency->id,
                'rate' => $rate,
                'starting_at' => $startingAt,
            ]);
            $saved++;
        }

        $this->info("Saved {$saved} rate(s), skipped {$skipped}.");

        return self::SUCCESS;
    }

    /**
     * Append api_key to URL (handles existing query string).
     */
    private function buildUrl(string $baseUrl, string $apiKey): string
    {
        $separator = str_contains($baseUrl, '?') ? '&' : '?';

        return $baseUrl.$separator.'access_key='.urlencode($apiKey);
    }

    /**
     * Get rate from quotes. Keys are "USD{CODE}" (e.g. USDEUR). USD itself is 1.0.
     */
    private function getRateForCode(array $quotes, string $code): ?float
    {
        if (strtoupper($code) === 'USD') {
            return 1.0;
        }

        $key = 'USD'.strtoupper($code);
        $value = $quotes[$key] ?? null;

        if ($value === null) {
            return null;
        }

        return (float) $value;
    }
}
