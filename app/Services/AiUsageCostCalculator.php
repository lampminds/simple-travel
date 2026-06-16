<?php

namespace App\Services;

use App\Models\AiAssistantMessage;

/**
 * Estimates OpenAI usage cost in USD (gpt-4o-mini chat + text-embedding-3-small defaults).
 */
final class AiUsageCostCalculator
{
    public static function assistantUsd(
        ?int $embeddingPromptTokens,
        ?int $chatPromptTokens,
        ?int $chatCompletionTokens,
    ): float {
        $embeddingRate = (float) config('services.openai.pricing.embedding_per_million', 0.02);
        $promptRate = (float) config('services.openai.pricing.chat_prompt_per_million', 0.15);
        $completionRate = (float) config('services.openai.pricing.chat_completion_per_million', 0.60);

        $usd = 0.0;

        if ($embeddingPromptTokens !== null && $embeddingPromptTokens > 0) {
            $usd += ($embeddingPromptTokens * $embeddingRate) / 1_000_000;
        }

        if ($chatPromptTokens !== null && $chatPromptTokens > 0) {
            $usd += ($chatPromptTokens * $promptRate) / 1_000_000;
        }

        if ($chatCompletionTokens !== null && $chatCompletionTokens > 0) {
            $usd += ($chatCompletionTokens * $completionRate) / 1_000_000;
        }

        return round($usd, 6);
    }

    public static function chatUsd(?int $promptTokens, ?int $completionTokens): float
    {
        return self::assistantUsd(null, $promptTokens, $completionTokens);
    }

    public static function forRecord(AiAssistantMessage $record): float
    {
        if ($record->estimated_usd !== null) {
            return (float) $record->estimated_usd;
        }

        if ($record->usage_type === AiAssistantMessage::USAGE_ASSISTANT) {
            return self::assistantUsd(
                $record->embedding_prompt_tokens,
                $record->chat_prompt_tokens,
                $record->chat_completion_tokens,
            );
        }

        if ($record->usage_type === AiAssistantMessage::USAGE_OPENAI_TRANSLATION) {
            return self::chatUsd($record->chat_prompt_tokens, $record->chat_completion_tokens);
        }

        return 0.0;
    }
}
