<?php

namespace App\Http\Controllers;

use App\Enums\DocumentAccessLevel;
use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Concerns\BuildsAssigneeOptions;
use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\FileStorageService;
use App\Services\LinkPreviewService;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TaskManagementController extends Controller
{
    use BuildsAssigneeOptions, ResolvesCurrentOrganization, ValidatesTaskAssignment;

    public function index(?Organization $organization = null): View
    {
        Gate::authorize('viewAny', Task::class);

        $user = auth()->user();
        $organizations = Organization::whereIn('id', $user->visibleOrganizationIds())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($organizations->isEmpty()) {
            return view('tasks.index', [
                'organizations' => $organizations,
                'organization' => null,
                'tasks' => collect(),
                'showInactive' => false,
                'canCreate' => false,
            ]);
        }

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        $showInactive = request()->boolean('show_inactive');

        $visibleScope = fn () => Task::visibleTo($user, $organization->id);

        $query = $visibleScope()->with(['project', 'department', 'assignee', 'subtasks', 'comments.user', 'comments.mentionedUsers']);

        if ($showInactive) {
            $query->withTrashed();
        }

        $filters = request()->only(['q', 'project_id', 'department_id', 'assignee_id', 'priority', 'status', 'due_from', 'due_to']);

        if ($search = trim((string) ($filters['q'] ?? ''))) {
            $query->where('title', 'like', '%'.$search.'%');
        }

        if ($projectId = request()->integer('project_id')) {
            $query->where('project_id', $projectId);
        }

        if ($departmentId = request()->integer('department_id')) {
            $query->where('department_id', $departmentId);
        }

        if ($assigneeId = request()->string('assignee_id')->toString()) {
            if ($assigneeId === 'unassigned') {
                $query->whereNull('assignee_id');
            } elseif (ctype_digit($assigneeId)) {
                $query->where('assignee_id', (int) $assigneeId);
            }
        }

        if ($priority = request()->string('priority')->toString()) {
            $query->where('priority', $priority);
        }

        if ($status = request()->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($dueFrom = request()->string('due_from')->toString()) {
            $query->whereDate('due_date', '>=', $dueFrom);
        }

        if ($dueTo = request()->string('due_to')->toString()) {
            $query->whereDate('due_date', '<=', $dueTo);
        }

        $sortable = ['title', 'project', 'department', 'assignee', 'priority', 'status', 'due_date', 'active'];
        $sort = request()->string('sort')->toString();
        $sort = in_array($sort, $sortable, true) ? $sort : 'due_date';
        $direction = request()->string('direction')->toString() === 'desc' ? 'desc' : 'asc';

        $tasks = $this->sortTasks($query->get(), $sort, $direction);

        $projectsInList = $tasks->pluck('project')->filter()->unique('id')->values();

        // Filter option lists reflect what this user can actually see (same
        // visibility scope as the list itself, ignoring the other filters so
        // switching one filter doesn't prune the others' choices) rather
        // than every project/department/user in the company, so a staff
        // member never sees a department they don't have access to sitting
        // in the dropdown.
        $optionsScope = $visibleScope();
        if ($showInactive) {
            $optionsScope->withTrashed();
        }
        $optionProjectIds = (clone $optionsScope)->distinct()->pluck('project_id');
        $optionDepartmentIds = (clone $optionsScope)->distinct()->pluck('department_id');
        $optionAssigneeIds = (clone $optionsScope)->whereNotNull('assignee_id')->distinct()->pluck('assignee_id');

        return view('tasks.index', [
            'organizations' => $organizations,
            'organization' => $organization,
            'tasks' => $tasks,
            'showInactive' => $showInactive,
            'canCreate' => Gate::allows('create', [Task::class, $organization->id]),
            'staffByProject' => $this->staffOptionsByProject($projectsInList),
            'filters' => $filters,
            'sort' => $sort,
            'direction' => $direction,
            'filterProjects' => Project::whereIn('id', $optionProjectIds)->orderBy('name')->get(['id', 'name']),
            'filterDepartments' => Department::whereIn('id', $optionDepartmentIds)->orderBy('name')->get(['id', 'name']),
            'filterAssignees' => User::whereIn('id', $optionAssigneeIds)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Sorted at the collection level, not via the query builder, since
     * project/department/assignee live on related models already eager-
     * loaded for the drilldown rows — joining for those would duplicate
     * what's already in memory. A missing value (no due date, no assignee)
     * always sorts last regardless of direction; ties break on title so the
     * list order stays stable and predictable.
     */
    private function sortTasks(Collection $tasks, string $sort, string $direction): Collection
    {
        $priorityRank = array_flip(array_map(fn ($case) => $case->value, Priority::cases()));
        $statusRank = array_flip(array_map(fn ($case) => $case->value, TaskStatus::cases()));

        $valueFor = function (Task $task) use ($sort, $priorityRank, $statusRank) {
            return match ($sort) {
                'project' => strtolower($task->project->name ?? ''),
                'department' => strtolower($task->department->name ?? ''),
                'assignee' => strtolower($task->assignee->name ?? ''),
                'priority' => $priorityRank[$task->priority->value] ?? PHP_INT_MAX,
                'status' => $statusRank[$task->status->value] ?? PHP_INT_MAX,
                'active' => $task->trashed() ? 0 : 1,
                'due_date' => $task->due_date?->timestamp,
                default => strtolower($task->title),
            };
        };

        return $tasks->sort(function (Task $a, Task $b) use ($valueFor, $direction) {
            $valueA = $valueFor($a);
            $valueB = $valueFor($b);

            $missingA = $valueA === null || $valueA === '';
            $missingB = $valueB === null || $valueB === '';
            if ($missingA !== $missingB) {
                return $missingA ? 1 : -1;
            }

            $result = $valueA <=> $valueB;
            if ($direction === 'desc') {
                $result = -$result;
            }

            return $result !== 0 ? $result : strtolower($a->title) <=> strtolower($b->title);
        })->values();
    }

    /**
     * Where the Add/Edit Task page's "← Back" and "Cancel" links should
     * go — the page that actually linked here (Tasks list, Kanban,
     * Calendar, Dashboard, or a project's own task list), never a
     * hardcoded default like Projects, which has no Add Task button and
     * so can never legitimately be where a Create flow started.
     *
     * Every entry-point link passes return_to (its own url()->full(), an
     * ABSOLUTE url() carrying whatever filters/scroll-state it had) and
     * return_label (a name from the fixed list below) as plain route()
     * query params - see tasks/index.blade.php, kanban.blade.php,
     * calendar.blade.php, dashboard.blade.php, projects/index.blade.php.
     * return_to is validated same-origin (isSafeReturnUrl()) before use,
     * since it arrives via the query string and this is otherwise a
     * textbook open-redirect: a crafted link could point return_to at an
     * external site and ride a click of "Cancel"/"← Back" there.
     * return_label is checked against a fixed allow-list for the same
     * reason - a stray or tampered value falls back to $defaultLabel
     * instead of rendering whatever was passed.
     *
     * @return array{url: string, label: string}
     */
    private function resolveReturnTo(string $defaultUrl, string $defaultLabel = 'Tasks'): array
    {
        $allowedLabels = ['Tasks', 'Kanban', 'Calendar', 'Dashboard', 'Projects'];

        $returnTo = request()->query('return_to');
        $returnLabel = request()->query('return_label');

        return [
            'url' => is_string($returnTo) && $this->isSafeReturnUrl($returnTo) ? $returnTo : $defaultUrl,
            'label' => in_array($returnLabel, $allowedLabels, true) ? $returnLabel : $defaultLabel,
        ];
    }

    /**
     * Accepts either a plain relative path ("/kanban/1") or an absolute
     * url() pointing back at THIS app's own host (what url()->full()
     * actually produces) - rejects anything pointing at a different host,
     * plus the protocol-relative ("//host/...") and backslash-variant
     * ("/\host/...", which some browsers also treat as protocol-relative)
     * open-redirect tricks that a naive "starts with /" check alone
     * wouldn't catch.
     */
    private function isSafeReturnUrl(string $url): bool
    {
        if (str_starts_with($url, '//') || str_starts_with($url, '/\\')) {
            return false;
        }

        $parsed = parse_url($url);
        if ($parsed === false) {
            return false;
        }

        if (! isset($parsed['host'])) {
            return str_starts_with($url, '/');
        }

        return $parsed['host'] === request()->getHost();
    }

    public function create(?Project $project = null): View
    {
        $manageableOrgIds = auth()->user()->manageableOrganizationIds();
        abort_if(empty($manageableOrgIds), 403);

        $projects = Project::whereIn('organization_id', $manageableOrgIds)->orderBy('name')->get();

        if ($projects->isEmpty()) {
            $returnTo = $this->resolveReturnTo(route('tasks.index'));

            return view('tasks.create', [
                'projects' => $projects,
                'project' => null,
                'returnToUrl' => $returnTo['url'],
                'returnToLabel' => $returnTo['label'],
            ]);
        }

        if (! $project || ! $projects->contains('id', $project->id)) {
            $project = $projects->first();
        }

        Gate::authorize('create', [Task::class, $project->organization_id]);

        $returnTo = $this->resolveReturnTo(route('tasks.index', $project->organization_id));

        return view('tasks.create', array_merge([
            'projects' => $projects,
            'project' => $project,
            'returnToUrl' => $returnTo['url'],
            'returnToLabel' => $returnTo['label'],
            // Lets a calendar cell's "+ Add task" link pre-fill the Due
            // Date field with that cell's date, so the user doesn't have
            // to re-pick it after clicking through.
            'dueDate' => request()->query('due_date'),
            'canCreateDepartments' => Gate::allows('create', Department::class),
            // The task doesn't exist yet, so a file (image, audio, ...)
            // uploaded from this page's Description editor can't be keyed
            // by a real task id — it's stored under tasks/pending/{this}/...
            // instead and moved to its real tasks/{id}/... path once the
            // task is actually saved (see store() and
            // FileStorageService::reconcilePendingFiles()). One id shared
            // across every media category the editor uploads.
            'pendingMediaId' => (string) Str::uuid(),
        ], $this->cascadingOptions($projects)));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $project = Project::findOrFail($request->integer('project_id'));

        Gate::authorize('create', [Task::class, $project->organization_id, $request->integer('department_id')]);

        $task = DB::transaction(function () use ($request, $project) {
            $task = Task::create([
                'organization_id' => $project->organization_id,
                'project_id' => $project->id,
                'department_id' => $request->integer('department_id'),
                'assignee_id' => $request->input('assignee_id') ?: null,
                'title' => $request->string('title'),
                'description' => $request->string('description'),
                'priority' => $request->input('priority'),
                'status' => $request->input('status'),
                'due_date' => $request->input('due_date') ?: null,
            ]);

            foreach ($request->input('subtasks', []) as $subtask) {
                $task->subtasks()->create([
                    'title' => $subtask['title'],
                    'description' => $subtask['description'] ?? null,
                    'assignee_id' => $subtask['assignee_id'] ?? null,
                    'due_date' => $subtask['due_date'] ?? null,
                ]);
            }

            // Any file (image, audio, ...) the Description editor uploaded
            // while this task was still being drafted landed under a
            // pending/ path (no task id existed yet) — now that one does,
            // move those files to their permanent tasks/{id}/... home and
            // repoint the saved HTML at the new URLs. A no-op (one string
            // comparison, no disk calls) when the description has no
            // pending reference in it.
            if ($task->description !== null) {
                $reconciled = app(FileStorageService::class)->reconcilePendingFiles($task->description, $task->id);

                if ($reconciled !== $task->description) {
                    $task->update(['description' => $reconciled]);
                }

                $this->attachDocumentChips($task, $reconciled);
            }

            return $task;
        });

        return redirect()->route('tasks.edit', $task)->with('status', 'Task created.');
    }

    /**
     * A <file-chip href="...">Name</file-chip> or
     * <link-preview href="..." domain="...">Title</link-preview> in a
     * just-created task's Description can only ever have gotten there via
     * the Add Task page's document button or a pasted/toolbar-inserted
     * link — RichTextDocumentController::storePending() and
     * LinkPreviewController::resolvePending() both deliberately never
     * create a Document row at that point, since there's no task_id to
     * attach one to yet (see each controller's own docblock). This is the
     * other half of that deferral, once a real task_id finally exists.
     *
     * file-chip: no "does this one already have a Document row" check
     * needed — a real (non-pending) document upload on an EXISTING task
     * never goes through store() at all, so every file-chip found here,
     * by construction, is one this exact save is seeing for the first
     * time.
     *
     * link-preview: DOES need that check, via
     * LinkPreviewService::attachAsDocument()'s own per-task dedup — the
     * same URL could appear in more than one link-preview chip within a
     * single draft (pasted twice while still on the Add Task page), and
     * each one after the first should reuse the same Document row, not
     * duplicate it.
     *
     * Called with the already-reconciled description, so a file-chip's
     * href is already the permanent tasks/{id}/documents/... URL, not a
     * pending one (link-preview hrefs are external URLs, untouched by
     * reconciliation either way).
     */
    private function attachDocumentChips(Task $task, string $description): void
    {
        if (str_contains($description, '<file-chip')) {
            preg_match_all('/<file-chip href="([^"]*)">([^<]*)<\/file-chip>/', $description, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $href = html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $name = html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');

                $document = Document::create([
                    'organization_id' => $task->organization_id,
                    'uploaded_by' => auth()->id(),
                    'name' => $name,
                    'link' => $href,
                    'access_level' => DocumentAccessLevel::Internal,
                ]);

                $task->documents()->attach($document->id);
            }
        }

        if (str_contains($description, '<link-preview')) {
            $dom = new DOMDocument;
            libxml_use_internal_errors(true);
            $dom->loadHTML('<?xml encoding="utf-8" ?>'.$description);
            libxml_clear_errors();

            $service = app(LinkPreviewService::class);

            foreach ((new DOMXPath($dom))->query('//link-preview[@href]') as $node) {
                $href = $node->getAttribute('href');
                $title = trim($node->textContent);
                $service->attachAsDocument($task, $href, $title !== '' ? $title : null);
            }
        }
    }

    public function edit(Task $task): View
    {
        Gate::authorize('view', $task);

        $project = $task->project;
        $manageableOrgIds = auth()->user()->manageableOrganizationIds();
        $projects = Project::whereIn('organization_id', $manageableOrgIds)->orderBy('name')->get();

        if (! $projects->contains('id', $project->id)) {
            $projects->push($project);
        }

        $allAttachedDocuments = $task->documents()->orderBy('name')->get();

        // A document attached to this task isn't automatically visible to
        // everyone who can see the task — e.g. a private document
        // management attached stays hidden from a staff assignee who
        // isn't its uploader, same as it would be on the Documents page
        // itself.
        $attachedDocuments = $allAttachedDocuments
            ->filter(fn (Document $document) => Gate::allows('view', $document))
            ->values();

        $availableDocuments = Document::where('organization_id', $task->organization_id)
            ->whereNotIn('id', $allAttachedDocuments->pluck('id'))
            ->get()
            ->filter(fn (Document $document) => Gate::allows('view', $document))
            ->sortBy('name')
            ->values();

        $returnTo = $this->resolveReturnTo(route('tasks.index', $task->organization_id));

        return view('tasks.edit', array_merge([
            'task' => $task->load('subtasks', 'comments.user', 'comments.mentionedUsers'),
            'project' => $project,
            'projects' => $projects,
            'returnToUrl' => $returnTo['url'],
            'returnToLabel' => $returnTo['label'],
            'canEdit' => auth()->user()->can('update', $task),
            'canDeactivate' => auth()->user()->can('delete', $task),
            // Deliberately separate from canEdit: creating a new document
            // (DocumentPolicy::create, gated by manage_documents) is a
            // different capability from editing this task, even though the
            // two happen to overlap for most roles today. Attaching an
            // EXISTING document, and detaching one, are task-editing
            // actions and stay under canEdit.
            'canManageDocuments' => Gate::allows('create', [Document::class, $task->organization_id]),
            'attachedDocuments' => $attachedDocuments,
            'availableDocuments' => $availableDocuments,
        ], $this->cascadingOptions($projects)));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        Gate::authorize('update', $task);

        $project = Project::findOrFail($request->integer('project_id'));

        $task->update([
            'organization_id' => $project->organization_id,
            'project_id' => $project->id,
            'department_id' => $request->integer('department_id'),
            'assignee_id' => $request->input('assignee_id') ?: null,
            'title' => (string) $request->string('title'),
            'description' => (string) $request->string('description'),
            'priority' => $request->input('priority'),
            'status' => $request->input('status'),
            'due_date' => $request->input('due_date') ?: null,
            'start_date' => $request->input('start_date') ?: null,
        ]);

        return redirect()->route('tasks.edit', $task)->with('status', 'Task updated.');
    }

    public function toggleActive(Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        if ($task->trashed()) {
            $task->restore();
            $status = 'Task activated.';
        } else {
            $task->delete();
            $status = 'Task deactivated.';
        }

        return redirect()->route('tasks.edit', $task)->with('status', $status);
    }

    /**
     * Kanban's drag-and-drop (and its dropdown fallback) both hit this
     * endpoint — authorized via TaskPolicy::updateStatus() (its own
     * update_kanban_cards permission, OR being the task's own assignee),
     * not the general task-edit ability, so a drag isn't a side door
     * around a rule a form submit already enforces, and "who can move
     * cards on Kanban" is independently configurable from "who can edit
     * the full task form".
     */
    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('updateStatus', $task);

        $data = $request->validate([
            'status' => ['required', Rule::enum(TaskStatus::class)],
        ]);

        $task->update(['status' => $data['status']]);

        return response()->json([
            'task' => ['id' => $task->id, 'status' => $task->status->value, 'status_label' => $task->status->label()],
        ]);
    }

    /**
     * Reassignment via the Kanban card's compact assignee select. Gated by
     * the same 'update' policy the full Edit Task page's Assignee field
     * uses (create_edit_tasks + department access, or management, or the
     * task's own current assignee) - not the narrower 'updateStatus'
     * capability the status select/drag-and-drop use, since handing a task
     * to someone else is a bigger action than moving your own card.
     * assignee_id eligibility reuses ValidatesTaskAssignment, the same
     * check UpdateTaskRequest applies to the full form, so the two can't
     * drift out of sync. TaskObserver::updated() logs the audit_log entry
     * (task.reassigned) automatically off this update() call.
     */
    public function updateAssignee(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('update', $task);

        $data = $request->validate([
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        if (! empty($data['assignee_id']) && ! $this->isAssignableStaffForProject($task->project, $data['assignee_id'])) {
            throw ValidationException::withMessages(['assignee_id' => 'Select a user assigned to this project.']);
        }

        $task->update(['assignee_id' => $data['assignee_id'] ?? null]);
        $task->load('assignee');

        return response()->json([
            'task' => [
                'id' => $task->id,
                'assignee_id' => $task->assignee_id,
                'assignee_name' => $task->assignee?->name,
            ],
        ]);
    }

    /**
     * @param  Collection<int, Project>  $projects
     * @return array{projectOrganizations: array<int, int>, departmentsByOrganization: array<int, array<int, array{id: int, name: string}>>, staffByProject: array<int, array<int, array{id: int, name: string}>>}
     */
    private function cascadingOptions(Collection $projects): array
    {
        $user = auth()->user();
        $organizationIds = $projects->pluck('organization_id')->unique()->values();

        // Management/global roles see every active department; a staff
        // member only sees the departments they've actually been granted,
        // so the dropdown can't offer a department store()'s Gate check
        // would then reject.
        $departmentsByOrganization = Department::whereIn('organization_id', $organizationIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->filter(fn (Department $department) => $user->isSuperAdmin() || $user->isOwner()
                || $user->isManagementInOrg($department->organization_id)
                || $user->hasDepartmentAccess($department->organization_id, $department->id))
            ->groupBy('organization_id')
            ->map(fn ($departments) => $departments->map(fn ($d) => ['id' => $d->id, 'name' => $d->name])->values())
            ->all();

        return [
            'projectOrganizations' => $projects->pluck('organization_id', 'id')->all(),
            'departmentsByOrganization' => $departmentsByOrganization,
            'staffByProject' => $this->staffOptionsByProject($projects),
        ];
    }
}
