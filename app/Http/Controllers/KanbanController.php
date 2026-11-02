<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Concerns\AppliesCompletedWindow;
use App\Http\Controllers\Concerns\BuildsAssigneeOptions;
use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Models\Organization;
use App\Models\Task;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KanbanController extends Controller
{
    use AppliesCompletedWindow, BuildsAssigneeOptions, ResolvesCurrentOrganization;

    /**
     * task #72 phase 5: cards per column on the board, and per Load more.
     * Fixed here rather than taken from the request, so a client can't ask
     * for the whole column at once.
     */
    public const COLUMN_BATCH = 30;

    /**
     * task #72 phase 10: the most changed tasks one poll applies. Past this
     * (a big import, or a tab back after a long time hidden) the board
     * asks for a reload instead of patching hundreds of cards.
     */
    public const CHANGES_LIMIT = 100;

    /**
     * task #72 phase 10: how far back each poll re-reads. updated_at has
     * one-second resolution, so a save in the same second as the previous
     * poll's cursor is read again rather than missed.
     */
    public const CHANGES_OVERLAP_SECONDS = 2;

    public function __invoke(?Organization $organization = null): View
    {
        // task #72 phase 10: taken before anything is read, so a save made
        // while this page renders is picked up by the first poll.
        $cursor = now()->getTimestamp();
        $user = auth()->user();

        $organizations = Organization::whereIn('id', $user->boardOrganizationIds('view_kanban'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($organizations->isEmpty()) {
            return view('kanban', [
                'organizations' => $organizations,
                'organization' => null,
                'columns' => collect(),
                'emptyMessage' => $user->boardAccessDeniedReason('view_kanban')->message(),
                'canCreate' => false,
                'eligibleAssignees' => [],
            ]);
        }

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        // task #72 phase 5: the board used to load every visible task in
        // the company and split it by status in PHP. It now shows the first
        // COLUMN_BATCH cards of each column (Load more fetches the rest from
        // column()), with the column's TOTAL in its header.
        //
        // Built once and cloned per query, so the staff/client visibility
        // lookups inside visibleTo() run once, not once per column.
        $visible = Task::visibleTo($user, $organization->id);

        $since = $this->completedWindowStartFor($organization);
        [$totals, $completedOlder] = $this->columnTotals($visible, $since);

        $tasksByStatus = collect(TaskStatus::cases())->mapWithKeys(fn (TaskStatus $status) => [
            // An empty column needs no query.
            $status->value => ($totals[$status->value] ?? 0) > 0
                ? $this->columnPage($visible, $status, 0, $since)
                : new EloquentCollection,
        ]);

        // One eager load across every rendered card, not one per column.
        // Loading mutates the models in place, so each column's own
        // collection sees the relations too.
        $rendered = new EloquentCollection($tasksByStatus->flatten(1)->all());
        $rendered->load(['project', 'department', 'assignee']);

        $showAllCompleted = $since === null;
        $columns = collect(TaskStatus::cases())->map(function (TaskStatus $status) use ($tasksByStatus, $totals, $completedOlder, $showAllCompleted) {
            $tasks = $tasksByStatus[$status->value];
            $total = (int) ($totals[$status->value] ?? 0);

            return [
                'status' => $status,
                'tasks' => $this->cardItems($tasks),
                'total' => $total,
                'remaining' => max(0, $total - $tasks->count()),
                // Completed column only: hidden by the window, and whether
                // this visit asked to see them.
                'older' => $status === TaskStatus::Completed ? $completedOlder : 0,
                'showAllCompleted' => $status === TaskStatus::Completed && $showAllCompleted,
            ];
        });

        return view('kanban', [
            'organizations' => $organizations,
            'organization' => $organization,
            'columns' => $columns,
            // Same gate the Tasks list page's own "+ Add task" button uses
            // (create_edit_tasks, narrowed to allowed departments for
            // Staff) - viewing the board (view_kanban) and creating a task
            // in it are separate permissions, so this can't be assumed
            // from having reached this far.
            'canCreate' => Gate::allows('create', [Task::class, $organization->id]),
            // Only for the cards actually rendered; Load more builds its
            // own for its batch. Options depend only on (project,
            // department), so a card gets the same list either way.
            'eligibleAssignees' => $this->eligibleAssigneesByTask($rendered),
            'returnTo' => route('kanban', $organization),
            'cursor' => $cursor,
            'showAllCompleted' => $showAllCompleted,
        ]);
    }

    /**
     * task #72 phase 10: what changed on the board since the last poll.
     *
     * - cursor: this server's time at the start of the request (never the
     *   browser's clock); the next poll sends it back as ?since=.
     * - cards: tasks changed since then that belong on this user's board
     *   now (Task::visibleTo(), and inside the completed window unless
     *   ?completed=all), rendered by the same _card partial as the board.
     * - removed: the other changed ids - deactivated, no longer visible to
     *   this user, or completed outside the window. Ids only.
     * - totals / older: every column's total and the Completed "older"
     *   count, recomputed each poll so headers and "N more" stay right.
     * - stale: more than CHANGES_LIMIT changes; the board asks for a
     *   reload.
     *
     * Access is the board's own rule, as in column(). An invalid or
     * missing since is read as "now": nothing changed, fresh totals.
     *
     * The changed ids come from tasks_org_updated_at_index, soft-deleted
     * rows included so a deactivation is seen. The range stops at the
     * cursor, so a row stamped in the future (seeded data) isn't returned
     * on every poll until that date.
     */
    public function changes(Organization $organization): JsonResponse
    {
        $cursor = now()->getTimestamp();
        $user = auth()->user();

        abort_unless(
            $organization->is_active && collect($user->boardOrganizationIds('view_kanban'))->contains($organization->id),
            403,
        );

        $since = request()->query('since');
        $changedIds = is_string($since) && ctype_digit($since)
            ? Task::withTrashed()
                ->where('organization_id', $organization->id)
                ->whereBetween('updated_at', [
                    Carbon::createFromTimestamp((int) $since - self::CHANGES_OVERLAP_SECONDS),
                    Carbon::createFromTimestamp($cursor),
                ])
                ->limit(self::CHANGES_LIMIT + 1)
                ->pluck('id')
            : collect();

        if ($changedIds->count() > self::CHANGES_LIMIT) {
            return response()->json(['cursor' => $cursor, 'stale' => true]);
        }

        $visible = Task::visibleTo($user, $organization->id);
        $window = $this->completedWindowStartFor($organization);
        [$totals, $completedOlder] = $this->columnTotals($visible, $window);

        $cards = [];
        $removed = [];
        if ($changedIds->isNotEmpty()) {
            $tasks = (clone $visible)
                ->whereIn('tasks.id', $changedIds)
                ->when($window, fn (Builder $query) => $query->openOrCompletedWithin($window))
                ->with(['project', 'department', 'assignee'])
                ->get();

            $cards = $this->renderCards($tasks, $organization)
                ->map(fn (array $card) => [
                    'id' => $card['task']->id,
                    'status' => $card['task']->status->value,
                    'sort_key' => $card['task']->kanbanSortKey(),
                    'html' => $card['html'],
                ])
                ->all();
            $removed = $changedIds->diff($tasks->modelKeys())->values()->all();
        }

        return response()->json([
            'cursor' => $cursor,
            'cards' => $cards,
            'removed' => $removed,
            'totals' => collect(TaskStatus::cases())
                ->mapWithKeys(fn (TaskStatus $status) => [$status->value => (int) ($totals[$status->value] ?? 0)])
                ->all(),
            'older' => $completedOlder,
        ]);
    }

    /**
     * task #72 phase 5: Load more — the next COLUMN_BATCH cards of one
     * column, as the same _card partial the board renders, plus how many
     * remain after them.
     *
     * Access is the board's own rule: the company must be one of the
     * caller's view_kanban board companies and active (403 otherwise), and
     * the cards go through the same Task::visibleTo(). An unknown status
     * is a 404 from the TaskStatus route binding.
     *
     * Offset-based: if someone else moves a card out of this column above
     * the offset between loads, the next batch starts one card later and
     * that card is skipped until the board is reloaded. Duplicates can't
     * reach the user — the board skips any card it already shows.
     */
    public function column(Organization $organization, TaskStatus $status): JsonResponse
    {
        $user = auth()->user();

        abort_unless(
            $organization->is_active && collect($user->boardOrganizationIds('view_kanban'))->contains($organization->id),
            403,
        );

        // A non-negative whole number; anything else starts from the top.
        $offset = request()->query('offset');
        $offset = is_string($offset) && ctype_digit($offset) ? (int) $offset : 0;

        $visible = Task::visibleTo($user, $organization->id);
        // Same window as the board, so Load more continues the same list.
        $since = $this->completedWindowStartFor($organization);
        $columnQuery = (clone $visible)->where('status', $status->value);
        if ($since && $status === TaskStatus::Completed) {
            $columnQuery->completedWithin($since);
        }
        $total = $columnQuery->count();

        $tasks = $this->columnPage($visible, $status, $offset, $since);
        $tasks->load(['project', 'department', 'assignee']);

        $html = $this->renderCards($tasks, $organization)->pluck('html')->implode('');

        return response()->json([
            'html' => $html,
            'remaining' => max(0, $total - $offset - $tasks->count()),
        ]);
    }

    /**
     * Every column's total of visible tasks (one grouped count), keyed by
     * status. task #72 phase 7: the Completed column shows only tasks
     * completed within the company's window, unless ?completed=all. Its
     * total counts the window; the rest are "older", by subtraction, so the
     * window costs one extra count. Shared by the board and changes().
     *
     * @return array{0: Collection<string, int>, 1: int}
     */
    private function columnTotals(Builder $visible, ?DateTimeInterface $since): array
    {
        $totals = (clone $visible)->toBase()
            ->select('status')
            ->selectRaw('count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $completedTotal = (int) ($totals[TaskStatus::Completed->value] ?? 0);
        $completedOlder = 0;
        if ($since && $completedTotal > 0) {
            $inWindow = (clone $visible)->completedWithin($since)->count();
            $completedOlder = $completedTotal - $inWindow;
            $totals[TaskStatus::Completed->value] = $inWindow;
        }

        return [$totals, $completedOlder];
    }

    /**
     * Cards rendered outside the board (Load more, changes()) by the
     * board's own _card partial, with the same permissions and assignee
     * options. The tasks must already have project, department and
     * assignee loaded.
     *
     * @return Collection<int, array{task: Task, html: string}>
     */
    private function renderCards(EloquentCollection $tasks, Organization $organization): Collection
    {
        $eligibleAssignees = $this->eligibleAssigneesByTask($tasks);
        $returnTo = route('kanban', $organization);

        return $this->cardItems($tasks)->map(fn (array $item) => [
            'task' => $item['task'],
            'html' => view('kanban._card', $item + [
                'eligibleAssignees' => $eligibleAssignees,
                'returnTo' => $returnTo,
            ])->render(),
        ]);
    }

    /**
     * One batch of one column, in the column's order: priority (High,
     * Medium, Low), then due date soonest first with no date last, then
     * task id — so consecutive batches never repeat or skip a card.
     *
     * The Completed column instead shows the most recently completed
     * first (task #72 phase 6): completed_at DESC, then id DESC, which
     * MySQL reads straight from tasks_org_status_completed_at_index.
     * MySQL and SQLite both sort NULLs last in DESC, so a task with no
     * completed_at goes last without an IS NULL term that would defeat
     * the index.
     *
     * Task::kanbanSortKey() spells this same order as a string, for the
     * board to place a changed card (task #72 phase 10) - change both
     * together.
     */
    private function columnPage(Builder $visible, TaskStatus $status, int $offset, ?DateTimeInterface $completedSince = null): EloquentCollection
    {
        $query = (clone $visible)->where('status', $status->value);

        if ($status === TaskStatus::Completed) {
            if ($completedSince) {
                $query->completedWithin($completedSince);
            }
            $query->orderByDesc('tasks.completed_at')->orderByDesc('tasks.id');
        } else {
            $query->orderByEnum('priority', Priority::class)
                ->orderByRaw('tasks.due_date IS NULL')
                ->orderBy('tasks.due_date')
                ->orderBy('tasks.id');
        }

        return $query
            ->offset($offset)
            ->limit(self::COLUMN_BATCH)
            ->get();
    }

    /**
     * @param  Collection<int, Task>  $tasks
     * @return Collection<int, array{task: Task, canEdit: bool, canReassign: bool}>
     */
    private function cardItems(Collection $tasks): Collection
    {
        return $tasks->values()->map(fn (Task $task) => [
            'task' => $task,
            'canEdit' => Gate::allows('updateStatus', $task),
            // Reassignment is gated by the FULL 'update' policy
            // (create_edit_tasks + department access, or
            // management, or the task's own current assignee) —
            // deliberately not the narrower 'updateStatus' capability
            // 'canEdit' above uses, since handing a task to someone
            // else is a bigger action than moving your own card.
            'canReassign' => Gate::allows('update', $task),
        ]);
    }
}
