<?php

namespace App\Services;

use App\Models\Account;
use App\Models\CatFaq;
use App\Support\AccountDashboardLane;
use App\Support\CurrentAccountSession;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Resolves active FAQ rows for public pages, with locale-aware question/answer text.
 *
 * When {@see $accountTypeId} is set, rows scoped to that type win over generic rows (null account_type_id)
 * for the same {@see CatFaq::$code}.
 */
final class CatFaqListService
{
    /**
     * @return list<array{id: int, question: string, answer: string}>
     */
    public function displayItems(?int $accountTypeId = null): array
    {
        $rows = $this->resolvedFaqs($accountTypeId);

        $items = [];
        foreach ($rows as $faq) {
            $question = trim($faq->question);
            $answer = trim((string) ($faq->answer ?? ''));
            if ($question === '' || $answer === '') {
                continue;
            }

            $items[] = [
                'id' => (int) $faq->id,
                'question' => $question,
                'answer' => $answer,
            ];
        }

        return $items;
    }

    /**
     * @return list<array{id: int, question: string, answer: string}>
     */
    public function displayItemsFromRequest(Request $request, ?int $limit = null): array
    {
        $items = $this->displayItems($this->resolveAccountTypeId($request));

        if ($limit === null) {
            return $items;
        }

        return array_slice($items, 0, max(0, $limit));
    }

    private function resolveAccountTypeId(Request $request): ?int
    {
        $user = $request->user();
        if ($user === null) {
            return null;
        }

        $account = $user->currentAccount();
        if (! $account instanceof Account) {
            return null;
        }

        $accountTypeId = AccountDashboardLane::resolvedLaneTypeId($request, $account);
        if ($accountTypeId !== null) {
            return $accountTypeId;
        }

        $typeIds = CurrentAccountSession::typeIds($request);

        return $typeIds[0] ?? null;
    }

    /**
     * @return Collection<int, CatFaq>
     */
    private function resolvedFaqs(?int $accountTypeId): Collection
    {
        $builder = CatFaq::query()
            ->where('active', true)
            ->with(['translations.language.locale']);

        if ($accountTypeId !== null) {
            $builder->where(function ($query) use ($accountTypeId): void {
                $query->whereNull('account_type_id')
                    ->orWhere('account_type_id', $accountTypeId);
            });
        } else {
            $builder->whereNull('account_type_id');
        }

        /** @var Collection<int, CatFaq> $candidates */
        $candidates = $builder->get();
        if ($candidates->isEmpty()) {
            return collect();
        }

        $byCode = [];
        foreach ($candidates as $faq) {
            $code = (string) $faq->code;
            if (! isset($byCode[$code])) {
                $byCode[$code] = $faq;

                continue;
            }

            $existing = $byCode[$code];
            if ($this->scopeRank($faq, $accountTypeId) > $this->scopeRank($existing, $accountTypeId)) {
                $byCode[$code] = $faq;
            }
        }

        return collect($byCode)
            ->values()
            ->sortBy([
                ['sort_order', 'asc'],
                ['id', 'asc'],
            ])
            ->values();
    }

    private function scopeRank(CatFaq $faq, ?int $accountTypeId): int
    {
        if ($accountTypeId !== null && $faq->account_type_id === $accountTypeId) {
            return 1;
        }

        return 0;
    }
}
