<?php

namespace App\Http\Controllers;

use App\Models\AiAssistantMessage;
use App\Services\AccountAssistantChatService;
use App\Services\AccountAssistantHistoryService;
use App\Services\AiUsageLogger;
use App\Support\AccountAssistantContext;
use App\Support\AiUsageContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use RuntimeException;
use Throwable;

final class AccountAssistantController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        abort_unless(config('assistant.enabled', true), 404);

        abort_unless($request->user() !== null, 401);

        return redirect()->route('account.dashboard');
    }

    public function history(Request $request, AccountAssistantHistoryService $historyService): JsonResponse
    {
        abort_unless(config('assistant.enabled', true), 404);

        $user = $request->user();
        abort_unless($user !== null, 401);

        $context = AccountAssistantContext::fromRequest($request, $user);

        return response()->json([
            'messages' => $historyService->recentMessages($context, (int) $user->id),
        ]);
    }

    public function message(
        Request $request,
        AccountAssistantChatService $chatService,
        AiUsageLogger $usageLogger,
    ): JsonResponse
    {
        abort_unless(config('assistant.enabled', true), 404);

        $user = $request->user();
        abort_unless($user !== null, 401);

        $maxQuestion = (int) config('assistant.max_question_length', 2000);
        $maxHistory = (int) config('assistant.max_history_messages', 6);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:' . $maxQuestion],
            'history' => ['sometimes', 'array', 'max:' . $maxHistory],
            'history.*.role' => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:' . $maxQuestion],
        ]);

        $context = AccountAssistantContext::fromRequest($request, $user);
        $question = trim((string) $validated['message']);

        if (! $this->allowRequest($request, (int) $user->id)) {
            $this->logMessage(
                usageLogger: $usageLogger,
                userId: (int) $user->id,
                context: $context,
                question: $question,
                status: AiAssistantMessage::STATUS_RATE_LIMITED,
                errorMessage: 'Rate limit exceeded.',
            );

            return response()->json([
                'message' => __('account.assistant.rate_limited'),
            ], 429);
        }

        $history = $this->normalizeHistory($validated['history'] ?? []);

        try {
            $result = $chatService->ask($question, $context, $history);

            $log = $this->logMessage(
                usageLogger: $usageLogger,
                userId: (int) $user->id,
                context: $context,
                question: $question,
                status: AiAssistantMessage::STATUS_SUCCESS,
                answer: $result['answer'],
                chatModel: $result['chat_model'],
                embeddingModel: $result['embedding_model'],
                embeddingPromptTokens: $result['embedding_prompt_tokens'],
                chatPromptTokens: $result['chat_prompt_tokens'],
                chatCompletionTokens: $result['chat_completion_tokens'],
                chatTotalTokens: $result['chat_total_tokens'],
                totalTokens: $result['total_tokens'],
                sourceKeys: $result['source_keys'],
            );

            return response()->json([
                'answer' => $result['answer'],
                'sources' => $result['sources'],
                'log_id' => $log->id,
            ]);
        } catch (RuntimeException $e) {
            $this->logMessage(
                usageLogger: $usageLogger,
                userId: (int) $user->id,
                context: $context,
                question: $question,
                status: AiAssistantMessage::STATUS_FAILED,
                errorMessage: mb_substr($e->getMessage(), 0, 500),
            );

            return response()->json([
                'message' => $e->getMessage(),
            ], 502);
        } catch (Throwable $e) {
            report($e);

            $this->logMessage(
                usageLogger: $usageLogger,
                userId: (int) $user->id,
                context: $context,
                question: $question,
                status: AiAssistantMessage::STATUS_FAILED,
                errorMessage: mb_substr($e->getMessage(), 0, 500),
            );

            return response()->json([
                'message' => __('account.assistant.error_generic'),
            ], 500);
        }
    }

    private function allowRequest(Request $request, int $userId): bool
    {
        $perMinute = (int) config('assistant.rate_limit.per_minute', 20);
        $key = 'assistant:user:' . $userId;

        return RateLimiter::attempt(
            $key,
            $perMinute,
            fn () => true,
            60,
        );
    }

    /**
     * @param  list<array{role?: string, content?: string}>  $history
     * @return list<array{role: string, content: string}>
     */
    private function normalizeHistory(array $history): array
    {
        $out = [];
        foreach ($history as $turn) {
            $role = ($turn['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
            $content = trim((string) ($turn['content'] ?? ''));
            if ($content === '') {
                continue;
            }
            $out[] = ['role' => $role, 'content' => $content];
        }

        return $out;
    }

    /**
     * @param  list<string>  $sourceKeys
     */
    private function logMessage(
        AiUsageLogger $usageLogger,
        int $userId,
        AccountAssistantContext $context,
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
        return $usageLogger->logAssistant(
            context: new AiUsageContext(
                userId: $userId,
                accountId: $context->accountId,
                accountTypeId: $context->accountTypeId,
                languageId: $context->languageId,
                source: 'account.assistant',
            ),
            question: $question,
            status: $status,
            answer: $answer,
            chatModel: $chatModel,
            embeddingModel: $embeddingModel,
            embeddingPromptTokens: $embeddingPromptTokens,
            chatPromptTokens: $chatPromptTokens,
            chatCompletionTokens: $chatCompletionTokens,
            chatTotalTokens: $chatTotalTokens,
            totalTokens: $totalTokens,
            errorMessage: $errorMessage,
            sourceKeys: $sourceKeys,
        );
    }
}
