<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * One-time token stored in cache; redeem opens a fresh browser session as the target user (support flow).
 */
final class WebsiteImpersonationTokenService
{
    private const CACHE_PREFIX = 'website_impersonation_token:';

    private const TTL_MINUTES = 15;

    /**
     * @throws InvalidArgumentException
     */
    public function createToken(User $target, User $createdBy): string
    {
        if ($createdBy->id === $target->id) {
            throw new InvalidArgumentException('Cannot create token for the same user.');
        }
        if (! $createdBy->belongsToPlatformAccount()) {
            throw new InvalidArgumentException('Only platform staff can create this link.');
        }
        if ($target->belongsToPlatformAccount()) {
            throw new InvalidArgumentException('Cannot target platform-linked users.');
        }

        $plain = Str::random(64);
        $key = self::CACHE_PREFIX.hash('sha256', $plain);

        Cache::put($key, [
            'user_id' => $target->getKey(),
            'created_by_user_id' => $createdBy->getKey(),
        ], now()->addMinutes(self::TTL_MINUTES));

        return $plain;
    }

    public function urlForPlainToken(string $plainToken): string
    {
        return URL::route('impersonate.website.enter', ['token' => $plainToken]);
    }

    /**
     * @return array{user_id: int, created_by_user_id: int}|null
     */
    public function pullPayload(string $plainToken): ?array
    {
        if ($plainToken === '' || strlen($plainToken) < 32) {
            return null;
        }

        $key = self::CACHE_PREFIX.hash('sha256', $plainToken);

        $data = Cache::pull($key);
        if (! is_array($data)) {
            return null;
        }

        $userId = isset($data['user_id']) ? (int) $data['user_id'] : 0;
        $createdBy = isset($data['created_by_user_id']) ? (int) $data['created_by_user_id'] : 0;
        if ($userId < 1 || $createdBy < 1) {
            return null;
        }

        return [
            'user_id' => $userId,
            'created_by_user_id' => $createdBy,
        ];
    }
}
