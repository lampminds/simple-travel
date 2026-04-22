<?php

namespace App\Services;

use App\Models\TodoCategory;
use App\Models\TodoTask;
use Illuminate\Support\Facades\DB;

final class TodoCategoryCopyTasksToAccountService
{
    /**
     * Duplicates every task in the category onto {@see $destinationAccountId},
     * including `todo_task_translations` and remapping `original_task_id` when both endpoints are in this batch.
     * Optionally restricts the source to one account id.
     *
     * If the destination already has a row with the same `code` (unique per account), that row is reused for
     * dependency mapping instead of inserting again.
     *
     * @return array{created: int, skipped: int}
     */
    public function copy(TodoCategory $category, int $destinationAccountId, ?int $sourceAccountId = null): array
    {
        if ($destinationAccountId < 1) {
            throw new \InvalidArgumentException('Invalid destination account.');
        }

        $tasksQuery = TodoTask::query()
            ->where('todo_category_id', (int) $category->getKey())
            // Never use the destination account as a source row (would "copy" a task onto itself).
            ->where('account_id', '!=', $destinationAccountId)
            ->with('translations')
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($sourceAccountId !== null) {
            $tasksQuery->where('account_id', $sourceAccountId);
        }

        $tasks = $tasksQuery->get();

        if ($tasks->isEmpty()) {
            return ['created' => 0, 'skipped' => 0];
        }

        return DB::transaction(function () use ($tasks, $category, $destinationAccountId): array {
            /** @var array<int, TodoTask> $idMap old task id => new task model */
            $idMap = [];
            $created = 0;
            $skipped = 0;

            foreach ($tasks as $task) {
                $existingOnDestination = TodoTask::query()
                    ->where('account_id', $destinationAccountId)
                    ->where('code', $task->code)
                    ->first();

                if ($existingOnDestination !== null) {
                    // Row already on destination (e.g. startup copy). Merge template fields from this source row
                    // so action_type / action_url match the catalogue account, not an older empty copy.
                    $existingOnDestination->forceFill(
                        self::templateAttributesFromSource($task, (int) $category->getKey())
                    );
                    $existingOnDestination->save();

                    $idMap[(int) $task->getKey()] = $existingOnDestination->fresh();
                    $skipped++;

                    continue;
                }

                // Copy every persisted column from the source row (action_type, action_url, etc.)
                // so we do not miss attributes if the schema grows; timestamps are excluded by replicate().
                $new = $task->replicate();
                $new->account_id = $destinationAccountId;
                $new->todo_category_id = (int) $category->getKey();
                $new->original_task_id = null;
                $new->forceFill(self::templateAttributesFromSource($task, (int) $category->getKey()));
                $new->unsetRelations();
                $new->save();

                $idMap[(int) $task->getKey()] = $new;
                $created++;

                foreach ($task->translations as $tr) {
                    $new->translations()->create([
                        'language_id' => $tr->language_id,
                        'name' => $tr->name,
                        'description' => $tr->description,
                    ]);
                }
            }

            foreach ($tasks as $task) {
                $oldOriginalId = $task->original_task_id;
                if ($oldOriginalId === null || ! isset($idMap[(int) $oldOriginalId])) {
                    continue;
                }
                $idMap[(int) $task->getKey()]->update([
                    'original_task_id' => $idMap[(int) $oldOriginalId]->getKey(),
                ]);
            }

            return ['created' => $created, 'skipped' => $skipped];
        });
    }

    /**
     * Task fields that define behaviour and ordering for checklist templates (explicit DB values).
     *
     * @return array<string, mixed>
     */
    private static function templateAttributesFromSource(TodoTask $source, int $categoryId): array
    {
        return [
            'todo_category_id' => $categoryId,
            'action_type' => $source->getRawOriginal('action_type') ?? $source->action_type,
            'action_url' => $source->getRawOriginal('action_url'),
            'verification_type' => $source->getRawOriginal('verification_type') ?? $source->verification_type,
            'verification_url' => $source->getRawOriginal('verification_url'),
            'sort_order' => $source->getRawOriginal('sort_order') ?? $source->sort_order,
            'active' => $source->getRawOriginal('active') ?? $source->active,
        ];
    }
}
