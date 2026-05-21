<?php

namespace App\Support;

use Throwable;

/**
 * Turns OpenAI client / HTTP errors into short operator-facing text (Filament notifications).
 */
final class OpenAiUserFacingMessage
{
    public static function from(Throwable $e): string
    {
        $message = strtolower($e->getMessage());

        if (
            str_contains($message, 'request limit')
            || str_contains($message, 'rate limit')
            || str_contains($message, '429')
            || str_contains($message, 'too many requests')
        ) {
            return (string) __('filament.resources.lmp_city_actions.openai_rate_limit');
        }

        if (
            str_contains($message, 'insufficient_quota')
            || str_contains($message, 'exceeded your current quota')
            || str_contains($message, 'billing')
        ) {
            return (string) __('filament.resources.lmp_city_actions.openai_quota');
        }

        if (
            str_contains($message, 'invalid_api_key')
            || str_contains($message, 'incorrect api key')
            || str_contains($message, 'authentication')
        ) {
            return (string) __('filament.resources.lmp_city_actions.openai_invalid_key');
        }

        if (str_contains($message, 'model') && (str_contains($message, 'not found') || str_contains($message, 'does not exist'))) {
            return (string) __('filament.resources.lmp_city_actions.openai_model', [
                'model' => (string) config('services.openai.chat_model', 'gpt-4o-mini'),
            ]);
        }

        return (string) __('filament.resources.lmp_city_actions.openai_generic', [
            'detail' => self::truncate($e->getMessage()),
        ]);
    }

    private static function truncate(string $text, int $max = 280): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? $text);

        if (strlen($text) <= $max) {
            return $text;
        }

        return substr($text, 0, $max - 3).'...';
    }
}
