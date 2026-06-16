<?php

namespace App\Services;

use App\Models\AiAssistantMessage;
use App\Models\AiKnowledgeItem;
use App\Support\AccountAssistantContext;

/**
 * Restores recent assistant Q&A for the authenticated user and account.
 */
final class AccountAssistantHistoryService
{
    /**
     * @return list<array{role: string, content: string, sources?: list<array{title: string, url: ?string}>}>
     */
    public function recentMessages(AccountAssistantContext $context, int $userId): array
    {
        $pairLimit = (int) config('assistant.history.load_limit', 20);

        $rows = AiAssistantMessage::query()
            ->where('user_id', $userId)
            ->where('usage_type', AiAssistantMessage::USAGE_ASSISTANT)
            ->where('status', AiAssistantMessage::STATUS_SUCCESS)
            ->when(
                $context->accountId !== null,
                fn ($query) => $query->where('account_id', $context->accountId),
                fn ($query) => $query->whereNull('account_id'),
            )
            ->whereNotNull('question')
            ->whereNotNull('answer')
            ->orderByDesc('created_at')
            ->limit($pairLimit)
            ->get(['question', 'answer', 'source_keys'])
            ->reverse()
            ->values();

        $messages = [];

        foreach ($rows as $row) {
            $messages[] = [
                'role' => 'user',
                'content' => (string) $row->question,
            ];

            $messages[] = [
                'role' => 'assistant',
                'content' => (string) $row->answer,
                'sources' => $this->resolveSources($row->source_keys ?? [], $context->languageId),
            ];
        }

        return $messages;
    }

    /**
     * @param  list<string>|null  $keys
     * @return list<array{title: string, url: ?string}>
     */
    private function resolveSources(?array $keys, int $languageId): array
    {
        $keys = array_values(array_filter(array_map(
            static fn ($key): string => trim((string) $key),
            $keys ?? [],
        )));

        if ($keys === []) {
            return [];
        }

        return AiKnowledgeItem::query()
            ->whereIn('key', $keys)
            ->with(['translations' => fn ($query) => $query->where('language_id', $languageId)])
            ->orderBy('id')
            ->get()
            ->map(function (AiKnowledgeItem $item): array {
                $translation = $item->translations->first();
                $title = trim((string) ($translation?->title ?? ''));
                $url = filled($translation?->url) ? (string) $translation->url : null;

                return [
                    'title' => $title !== '' ? $title : (string) $item->key,
                    'url' => $url,
                ];
            })
            ->values()
            ->all();
    }
}
