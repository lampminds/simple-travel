<?php

namespace App\Livewire;

use App\Models\LmpSession;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Login history for the user view page. Pagination stays inside this component
 * so it is not blocked by the disabled Filament view form.
 */
class UserLoginSessions extends Component
{
    use WithPagination;

    #[Locked]
    public ?int $userId = null;

    public function mount(mixed $record = null): void
    {
        $this->userId = $record instanceof User ? (int) $record->getKey() : null;
    }

    public function render(): View
    {
        $sessions = $this->sessions();

        return view('livewire.user-login-sessions', [
            'sessions' => $sessions,
            'activeSessionIds' => $this->activeSessionIds(
                $sessions->getCollection()->pluck('session_id')->filter()->map(fn (mixed $id): string => (string) $id)->all()
            ),
        ]);
    }

    private function sessions(): LengthAwarePaginator
    {
        if ($this->userId === null || ! Schema::hasTable('lmp_sessions')) {
            return new Paginator([], 0, 15);
        }

        return LmpSession::query()
            ->with('ip')
            ->where('user_id', $this->userId)
            ->orderByDesc('logged_in_at')
            ->orderByDesc('id')
            ->paginate(15, pageName: 'loginSessionsPage');
    }

    /**
     * @param  list<string>  $sessionIds
     * @return array<string, true>
     */
    private function activeSessionIds(array $sessionIds): array
    {
        if ($sessionIds === [] || $this->userId === null || config('session.driver') !== 'database') {
            return [];
        }

        $table = (string) config('session.table', 'sys_sessions');
        if (! Schema::hasTable($table)) {
            return [];
        }

        return DB::table($table)
            ->where('user_id', $this->userId)
            ->whereIn('id', $sessionIds)
            ->pluck('id')
            ->mapWithKeys(fn (mixed $id): array => [(string) $id => true])
            ->all();
    }
}
