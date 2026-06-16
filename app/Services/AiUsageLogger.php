<?php

namespace App\Services;

use App\Models\AiAssistantMessage;
use App\Support\AiUsageContext;

final class AiUsageLogger
{
    public function logAssistant(
        AiUsageContext $context,
        string $question,
        string $status,
        ?string $answer = null,
        ?string $chatModel = null,
        ?string $embeddingModel = null,
        ?int $embeddingPromptTokens = null,
        ?int $chatPromptTokens = null,
        ?int $chatCompletionTokens = null,
        ?int $chatTotalTokens = null,
        ?int $totalTokens = null,
        ?string $errorMessage = null,
        array $sourceKeys = [],
    ): AiAssistantMessage {
        $estimatedUsd = AiUsageCostCalculator::assistantUsd(
            $embeddingPromptTokens,
            $chatPromptTokens,
            $chatCompletionTokens,
        );

        return AiAssistantMessage::query()->create([
            'usage_type' => AiAssistantMessage::USAGE_ASSISTANT,
            'source' => $context->source,
            'user_id' => $context->resolvedUserId(),
            'account_id' => $context->resolvedAccountId(),
            'account_type_id' => $context->accountTypeId,
            'language_id' => $context->languageId,
            'question' => $question,
            'answer' => $answer,
            'status' => $status,
            'error_message' => $errorMessage,
            'chat_model' => $chatModel,
            'embedding_model' => $embeddingModel,
            'embedding_prompt_tokens' => $embeddingPromptTokens,
            'chat_prompt_tokens' => $chatPromptTokens,
            'chat_completion_tokens' => $chatCompletionTokens,
            'chat_total_tokens' => $chatTotalTokens,
            'total_tokens' => $totalTokens,
            'estimated_usd' => $estimatedUsd,
            'source_keys' => $sourceKeys === [] ? null : $sourceKeys,
        ]);
    }

    /**
     * Free translation APIs (MyMemory / Google fallback). Tokens are a character-based proxy; USD is zero.
     */
    public function logFreeTranslation(
        AiUsageContext $context,
        int $totalTokens,
        string $summary,
        string $status = AiAssistantMessage::STATUS_SUCCESS,
        ?string $errorMessage = null,
    ): AiAssistantMessage {
        return AiAssistantMessage::query()->create([
            'usage_type' => AiAssistantMessage::USAGE_TRANSLATION,
            'source' => $context->source,
            'user_id' => $context->resolvedUserId(),
            'account_id' => $context->resolvedAccountId(),
            'account_type_id' => $context->accountTypeId,
            'language_id' => $context->languageId,
            'question' => $summary,
            'status' => $status,
            'error_message' => $errorMessage,
            'total_tokens' => $totalTokens,
            'estimated_usd' => 0,
        ]);
    }

    public function logOpenAiTranslation(
        AiUsageContext $context,
        string $summary,
        ?string $chatModel,
        ?int $chatPromptTokens,
        ?int $chatCompletionTokens,
        ?int $chatTotalTokens,
        ?int $totalTokens,
        string $status = AiAssistantMessage::STATUS_SUCCESS,
        ?string $errorMessage = null,
    ): AiAssistantMessage {
        $estimatedUsd = AiUsageCostCalculator::chatUsd($chatPromptTokens, $chatCompletionTokens);

        return AiAssistantMessage::query()->create([
            'usage_type' => AiAssistantMessage::USAGE_OPENAI_TRANSLATION,
            'source' => $context->source,
            'user_id' => $context->resolvedUserId(),
            'account_id' => $context->resolvedAccountId(),
            'account_type_id' => $context->accountTypeId,
            'language_id' => $context->languageId,
            'question' => $summary,
            'status' => $status,
            'error_message' => $errorMessage,
            'chat_model' => $chatModel,
            'chat_prompt_tokens' => $chatPromptTokens,
            'chat_completion_tokens' => $chatCompletionTokens,
            'chat_total_tokens' => $chatTotalTokens,
            'total_tokens' => $totalTokens,
            'estimated_usd' => $estimatedUsd,
        ]);
    }
}
