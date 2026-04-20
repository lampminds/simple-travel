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
     * @return int Number of tasks created
     */
    public function copy(TodoCategory $category, int $destinationAccountId, ?int $sourceAccountId = null): int
    {
        if ($destinationAccountId < 1) {
            throw new \InvalidArgumentException('Invalid destination account.');
        }

        $tasksQuery = TodoTask::query()
            ->where('todo_category_id', (int) $category->getKey())
            ->with('translations')
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($sourceAccountId !== null) {
            $tasksQuery->where('account_id', $sourceAccountId);
        }

        $tasks = $tasksQuery->get();

        if ($tasks->isEmpty()) {
            return 0;
        }

        return (int) DB::transaction(function () use ($tasks, $category, $destinationAccountId): int {
            /** @var array<int, TodoTask> $idMap old task id => new task model */
            $idMap = [];

            foreach ($tasks as $task) {
                $new = TodoTask::query()->create([
                    'account_id' => $destinationAccountId,
                    'code' => (string) $task->code,
                    'todo_category_id' => (int) $category->getKey(),
                    'original_task_id' => null,
                    'action_type' => $task->action_type,
                    'action_url' => $task->action_url,
                    'verification_type' => $task->verification_type,
                    'verification_url' => $task->verification_url,
                    'sort_order' => $task->sort_order,
                    'active' => $task->active,
                ]);

                $idMap[(int) $task->getKey()] = $new;

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

            return count($idMap);
        });
    }
}
