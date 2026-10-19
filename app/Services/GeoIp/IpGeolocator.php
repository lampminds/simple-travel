<?php

namespace App\Services\GeoIp;

use App\Models\LmpIp;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Resolves a public IP once and stores the result on lmp_ips.
 * Later logins from the same address reuse that row.
 */
class IpGeolocator
{
    public function remember(string $ipAddress): LmpIp
    {
        $ipAddress = trim($ipAddress);
        $local = $this->isLocal($ipAddress);

        try {
            $record = LmpIp::query()->firstOrCreate(
                ['ip_address' => $ipAddress],
                ['status' => $local ? LmpIp::STATUS_LOCAL : LmpIp::STATUS_PENDING],
            );
        } catch (UniqueConstraintViolationException) {
            $record = LmpIp::query()->where('ip_address', $ipAddress)->firstOrFail();
        }

        if ($local) {
            if ($record->status !== LmpIp::STATUS_LOCAL) {
                $record->forceFill(['status' => LmpIp::STATUS_LOCAL])->save();
            }

            return $record;
        }

        if (! $this->needsLookup($record)) {
            return $record;
        }

        $this->lookup($record);

        return $record->refresh();
    }

    /**
     * Private, reserved, or malformed addresses are not sent to the external service.
     */
    public function isLocal(string $ipAddress): bool
    {
        if (filter_var($ipAddress, FILTER_VALIDATE_IP) === false) {
            return true;
        }

        return filter_var(
            $ipAddress,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }

    private function needsLookup(LmpIp $record): bool
    {
        if (in_array($record->status, [LmpIp::STATUS_RESOLVED, LmpIp::STATUS_LOCAL], true)) {
            return false;
        }

        if ($record->status === LmpIp::STATUS_FAILED && $record->looked_up_at !== null) {
            $retryAfter = (int) config('services.ip_geolocation.retry_after_minutes', 1440);

            return $record->looked_up_at->lte(now()->subMinutes(max($retryAfter, 0)));
        }

        return true;
    }

    private function lookup(LmpIp $record): void
    {
        $baseUrl = rtrim((string) config('services.ip_geolocation.url', 'https://ipwho.is'), '/');
        $timeout = max(1, (int) config('services.ip_geolocation.timeout', 2));

        try {
            $response = Http::timeout($timeout)
                ->acceptJson()
                ->get($baseUrl.'/'.rawurlencode($record->ip_address));

            $data = $response->json();

            if (! $response->successful() || ! is_array($data) || ($data['success'] ?? false) !== true) {
                $this->markFailed($record);

                return;
            }

            $timezone = $data['timezone']['id'] ?? null;

            $record->forceFill([
                'status' => LmpIp::STATUS_RESOLVED,
                'country_code' => $this->limitedString($data['country_code'] ?? null, 2),
                'country_name' => $this->limitedString($data['country'] ?? null),
                'region_name' => $this->limitedString($data['region'] ?? null),
                'city' => $this->limitedString($data['city'] ?? null),
                'latitude' => $this->coordinate($data['latitude'] ?? null),
                'longitude' => $this->coordinate($data['longitude'] ?? null),
                'timezone' => $this->limitedString(is_string($timezone) ? $timezone : null),
                'looked_up_at' => now(),
            ])->save();
        } catch (Throwable) {
            $this->markFailed($record);
        }
    }

    private function markFailed(LmpIp $record): void
    {
        $record->forceFill([
            'status' => LmpIp::STATUS_FAILED,
            'looked_up_at' => now(),
        ])->save();
    }

    private function limitedString(mixed $value, ?int $max = null): ?string
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return null;
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        if ($max !== null) {
            return mb_substr($text, 0, $max);
        }

        return $text;
    }

    private function coordinate(mixed $value): ?string
    {
        if (! is_numeric($value)) {
            return null;
        }

        return number_format((float) $value, 7, '.', '');
    }
}
