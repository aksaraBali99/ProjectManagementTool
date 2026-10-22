<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Concerns\BuildsAssigneeOptions;
use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Models\Organization;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KanbanController extends Controller
{
    use BuildsAssigneeOptions, ResolvesCurrentOrganization;

    /**
     * task #72 phase 5: cards per column on the board, and per Load more.
     * Fixed here rather than taken from the request, so a client can't ask
     * for the whole column at once.
     */
    public const COLUMN_BATCH = 30;

    public function __invoke(?Organization $organization = null): View
    {
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

        $totals = (clone $visible)->toBase()
            ->select('status')
            ->selectRaw('count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $tasksByStatus = collect(TaskStatus::cases())->mapWithKeys(fn (TaskStatus $status) => [
            // An empty column needs no query.
            $status->value => ($totals[$status->value] ?? 0) > 0
                ? $this->columnPage($visible, $status, 0)
                : new EloquentCollection,
        ]);

        // One eager load across every rendered card, not one per column.
        // Loading mutates the models in place, so each column's own
        // collection sees the relations too.
        $rendered = new EloquentCollection($tasksByStatus->flatten(1)->all());
        $rendered->load(['project', 'department', 'assignee']);

        $columns = collect(TaskStatus::cases())->map(function (TaskStatus $status) use ($tasksByStatus, $totals) {
            $tasks = $tasksByStatus[$status->value];
            $total = (int) ($totals[$status->value] ?? 0);

            return [
                'status' => $status,
                'tasks' => $this->cardItems($tasks),
                'total' => $total,
                'remaining' => max(0, $total - $tasks->count()),
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
        $total = (clone $visible)->where('status', $status->value)->count();

        $tasks = $this->columnPage($visible, $status, $offset);
        $tasks->load(['project', 'department', 'assignee']);

        $eligibleAssignees = $this->eligibleAssigneesByTask($tasks);
        $returnTo = route('kanban', $organization);

        $html = $this->cardItems($tasks)
            ->map(fn (array $item) => view('kanban._card', $item + [
                'eligibleAssignees' => $eligibleAssignees,
                'returnTo' => $returnTo,
            ])->render())
            ->implode('');

        return response()->json([
            'html' => $html,
            'remaining' => max(0, $total - $offset - $tasks->count()),
        ]);
    }

    /**
     * One batch of one column, in the column's order: priority (High,
     * Medium, Low), then due date soonest first with no date last, then
     * task id — so consecutive batches never repeat or skip a card.
     */
    private function columnPage(Builder $visible, TaskStatus $status, int $offset): EloquentCollection
    {
        return (clone $visible)
            ->where('status', $status->value)
            ->orderByEnum('priority', Priority::class)
            ->orderByRaw('tasks.due_date IS NULL')
            ->orderBy('tasks.due_date')
            ->orderBy('tasks.id')
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
        ])->toBase();
    }
}
