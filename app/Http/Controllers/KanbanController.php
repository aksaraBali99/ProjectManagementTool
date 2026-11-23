<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Enums\TaskStatus;
use App\Http\Controllers\Concerns\AppliesCompletedWindow;
use App\Http\Controllers\Concerns\BuildsAssigneeOptions;
use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Models\Organization;
use App\Models\Project;
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
     * task #72 phase 5: cards per status in an opened lane (per column
     * before SOL-0025), and per Load more.
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
     * task #72 phase 10: how far back before the previous cursor each poll
     * re-reads. updated_at is stamped when a save starts but only seen once
     * its transaction commits, and has one-second resolution — so a save
     * that was stamped before the last poll but committed after it (or
     * landed in the same second) is read again rather than missed. A card
     * read twice is simply replaced with itself.
     */
    public const CHANGES_OVERLAP_SECONDS = 10;

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
                'lanes' => collect(),
                'totals' => [],
                'emptyMessage' => $user->boardAccessDeniedReason('view_kanban')->message(),
                'canCreate' => false,
            ]);
        }

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        // SOL-0025: the board is one collapsible lane per project, and
        // renders no cards: a lane's cards come from project() when it is
        // first opened. The page costs the same however many tasks or
        // projects the company has.
        //
        // Built once and cloned per query, so the staff/client visibility
        // lookups inside visibleTo() run once.
        $visible = Task::visibleTo($user, $organization->id);
        $since = $this->completedWindowStartFor($organization);
        $summary = $this->laneSummaries($visible, $since);

        return view('kanban', [
            'organizations' => $organizations,
            'organization' => $organization,
            'lanes' => $summary['lanes'],
            // The column headers: the company's total per status.
            'totals' => $summary['totals'],
            // Same gate the Tasks list page's own "+ Add task" button uses
            // (create_edit_tasks, narrowed to allowed departments for
            // Staff) - viewing the board (view_kanban) and creating a task
            // in it are separate permissions, so this can't be assumed
            // from having reached this far.
            'canCreate' => Gate::allows('create', [Task::class, $organization->id]),
            'returnTo' => route('kanban', $organization),
            'cursor' => $cursor,
            'showAllCompleted' => $since === null,
        ]);
    }

    /**
     * SOL-0025: one lane's cards, fetched when the lane is first opened:
     * the first COLUMN_BATCH cards of each status (same order and
     * completed window as the board), each status's total and what remains
     * after the batch, and the lane's "older completed" count.
     *
     * Access is the board's rule (403); a project of another company is a
     * 404. A project with no visible tasks returns empty columns, since a
     * lane can empty out between page load and open.
     */
    public function project(Organization $organization, string $project): JsonResponse
    {
        $project = $this->boardProject($organization, $project);

        $visible = Task::visibleTo(auth()->user(), $organization->id)
            ->where('tasks.project_id', $project->id);
        $since = $this->completedWindowStartFor($organization);
        $summary = $this->laneSummaries($visible, $since);
        $totals = $summary['totals'];

        $pages = collect(TaskStatus::cases())->mapWithKeys(fn (TaskStatus $status) => [
            // An empty status needs no query.
            $status->value => $totals[$status->value] > 0
                ? $this->columnPage($visible, $status, 0, $since)
                : new EloquentCollection,
        ]);

        // One eager load and one set of assignee options across every
        // card of the lane, not one per status.
        $rendered = new EloquentCollection($pages->flatten(1)->all());
        $rendered->load(['project', 'department', 'assignee']);
        $html = $this->renderCards($rendered, $organization)
            ->groupBy(fn (array $card) => $card['task']->status->value)
            ->map(fn (Collection $cards) => $cards->pluck('html')->implode(''));

        return response()->json([
            'columns' => $pages->map(fn (EloquentCollection $tasks, string $status) => [
                'html' => $html[$status] ?? '',
                'total' => $totals[$status],
                'remaining' => max(0, $totals[$status] - $tasks->count()),
            ])->all(),
            'older' => $summary['older'],
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
     * - lanes (SOL-0025): every project lane in the board's order, with
     *   its totals and older count; the board adds, updates and removes
     *   lanes from it. A card carries its project_id.
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

        $this->authorizeBoard($organization);

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
        $summary = $this->laneSummaries($visible, $window);

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
                    // SOL-0025: the lane it goes in. A task moved to
                    // another project leaves its old lane for this one.
                    'project_id' => $card['task']->project_id,
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
            'totals' => $summary['totals'],
            // Company-wide, for board tabs opened before SOL-0025; the
            // lanes carry their own.
            'older' => $summary['older'],
            'lanes' => $summary['lanes']->map(fn (array $lane) => [
                'id' => $lane['id'],
                'name' => $lane['name'],
                'closed' => $lane['closed'],
                'totals' => $lane['totals'],
                'older' => $lane['older'],
            ])->all(),
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
     * SOL-0025: with ?project=<id>, only that project's tasks (404 for a
     * project of another company).
     *
     * Offset-based: if someone else moves a card out of this column above
     * the offset between loads, the next batch starts one card later and
     * that card is skipped until the board is reloaded. Duplicates can't
     * reach the user — the board skips any card it already shows.
     */
    public function column(Organization $organization, TaskStatus $status): JsonResponse
    {
        $user = auth()->user();

        $this->authorizeBoard($organization);

        // A non-negative whole number; anything else starts from the top.
        $offset = request()->query('offset');
        $offset = is_string($offset) && ctype_digit($offset) ? (int) $offset : 0;

        $visible = Task::visibleTo($user, $organization->id);
        // SOL-0025: the grouped board continues one lane's cell. Without
        // ?project= (a tab opened before the deploy) the whole company's
        // column, as before.
        $project = request()->query('project');
        if ($project !== null) {
            $visible->where('tasks.project_id', $this->boardProject($organization, $project)->id);
        }
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
     * SOL-0025: the board's lanes, one per project with visible tasks,
     * sorted by name (case-insensitive) then id, plus the company totals
     * the column headers show. At most three queries whatever the number
     * of tasks or projects: a count grouped by project and status, the
     * in-window completed count grouped by project (only when a window
     * applies and something is completed), and the lanes' projects.
     *
     * task #72 phase 7: Completed counts only tasks completed within the
     * company's window, unless ?completed=all ($since null); the rest are
     * the lane's "older", by subtraction. A project whose only visible
     * tasks are older completed ones gets no lane, but those tasks still
     * count in the company-wide older.
     *
     * Each lane: id, name, closed, totals (every status), total, older.
     *
     * @return array{lanes: Collection<int, array<string, mixed>>, totals: array<string, int>, older: int}
     */
    private function laneSummaries(Builder $visible, ?DateTimeInterface $since): array
    {
        $rows = (clone $visible)->toBase()
            ->select('tasks.project_id', 'tasks.status')
            ->selectRaw('count(*) as aggregate')
            ->groupBy('tasks.project_id', 'tasks.status')
            ->get();

        $completed = TaskStatus::Completed->value;
        $inWindow = null;
        if ($since && $rows->contains(fn ($row) => $row->status === $completed)) {
            $inWindow = (clone $visible)->completedWithin($since)->toBase()
                ->select('tasks.project_id')
                ->selectRaw('count(*) as aggregate')
                ->groupBy('tasks.project_id')
                ->pluck('aggregate', 'project_id');
        }

        $emptyTotals = collect(TaskStatus::cases())->mapWithKeys(fn (TaskStatus $status) => [$status->value => 0])->all();
        $companyTotals = $emptyTotals;
        $companyOlder = 0;
        $counts = [];
        foreach ($rows->groupBy('project_id') as $projectId => $projectRows) {
            $totals = $emptyTotals;
            foreach ($projectRows as $row) {
                $totals[$row->status] = (int) $row->aggregate;
            }
            $older = 0;
            if ($inWindow !== null) {
                $within = (int) ($inWindow[$projectId] ?? 0);
                $older = $totals[$completed] - $within;
                $totals[$completed] = $within;
            }

            foreach ($totals as $status => $count) {
                $companyTotals[$status] += $count;
            }
            $companyOlder += $older;

            if (array_sum($totals) > 0) {
                $counts[(int) $projectId] = ['totals' => $totals, 'older' => $older];
            }
        }

        // The tasks are already this company's, so the projects need no
        // organization scope.
        $projects = $counts === []
            ? collect()
            : Project::withoutGlobalScope('organization')->whereIn('id', array_keys($counts))->get(['id', 'name', 'status']);

        $lanes = $projects
            ->map(fn (Project $project) => [
                'id' => $project->id,
                'name' => $project->name,
                'closed' => $project->status === ProjectStatus::Closed,
                'totals' => $counts[$project->id]['totals'],
                'total' => array_sum($counts[$project->id]['totals']),
                'older' => $counts[$project->id]['older'],
            ])
            ->sort(fn (array $a, array $b) => [mb_strtolower($a['name']), $a['id']] <=> [mb_strtolower($b['name']), $b['id']])
            ->values()
            ->toBase();

        return ['lanes' => $lanes, 'totals' => $companyTotals, 'older' => $companyOlder];
    }

    /**
     * The board's own access rule: the company must be one of the
     * caller's view_kanban board companies, and active (403 otherwise).
     */
    private function authorizeBoard(Organization $organization): void
    {
        abort_unless(
            $organization->is_active && collect(auth()->user()->boardOrganizationIds('view_kanban'))->contains($organization->id),
            403,
        );
    }

    /**
     * SOL-0025: the project a lane request names, after the board's access
     * rule (403). A project of another company, or no such project, is a
     * 404. Looked up without the organization scope, so which of the two
     * a caller gets doesn't depend on the companies they belong to.
     */
    private function boardProject(Organization $organization, mixed $projectId): Project
    {
        $this->authorizeBoard($organization);

        abort_unless(is_string($projectId) && ctype_digit($projectId), 404);

        return Project::withoutGlobalScope('organization')
            ->where('organization_id', $organization->id)
            ->findOrFail((int) $projectId);
    }

    /**
     * Cards rendered by the board's own _card partial (a lane, Load more,
     * changes()), with the same permissions and assignee
     * options. The tasks must already have project, department and
     * assignee loaded.
     *
     * @return Collection<int, array{task: Task, html: string}>
     */
    private function renderCards(EloquentCollection $tasks, Organization $organization): Collection
    {
        // task #118: the cards' codes from the board's own company, no query.
        Task::useOrganization($tasks, $organization);
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
