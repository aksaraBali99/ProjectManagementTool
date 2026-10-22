<?php

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * task #72 phase 4: the Tasks list is now sorted in SQL, one page at a
 * time. Its order must be exactly the one the old PHP sortTasks() gave,
 * plus tasks.id as the final tie-break — so walking every page with a
 * small page size must give back the reference order with no task
 * repeated, skipped or out of place.
 *
 * Titles and names are ASCII and never all digits on purpose: those are
 * the two accepted differences (PHP compared "10" and "9" as numbers, and
 * MySQL's collation ignores accents), see applyListOrder().
 */

/**
 * The old TaskManagementController::sortTasks(), copied as it was before
 * phase 4, with tasks.id added as the final tie-break.
 */
function referenceTaskListOrder(Collection $tasks, string $sort, string $direction): array
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

        if ($result !== 0) {
            return $result;
        }

        return (strtolower($a->title) <=> strtolower($b->title)) ?: $a->id <=> $b->id;
    })->pluck('id')->all();
}

beforeEach(function () {
    createOwner();

    $this->org = Organization::create(['name' => 'Sort Org', 'slug' => 'sort-org', 'accent_color' => '#1D9E75']);

    $this->management = User::factory()->create(['name' => 'Manager Person', 'tasks_per_page' => 3]);
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);

    // Names that differ only in case, and an empty one, so the
    // case-insensitive compare and the empty-last rule both get exercised.
    $projects = collect(['alpha project', 'Beta project', 'beta project', ''])
        ->map(fn ($name) => Project::create(['organization_id' => $this->org->id, 'name' => $name, 'description' => 'd']));
    $departments = collect(['Marketing', 'marketing', 'Operations', ''])
        ->map(fn ($name) => Department::create(['organization_id' => $this->org->id, 'name' => $name, 'color' => '#000000']));
    // null = unassigned.
    $assignees = collect(['anna', 'Bob', 'bob', 'Carla', null])
        ->map(fn ($name) => $name === null ? null : User::factory()->create(['name' => $name]));

    $titles = ['Same title', 'same title', 'Same title', 'apple', 'Apple', 'banana', 'Cherry pie', 'cherry Pie',
        'delta', 'Echo', 'echo', 'fox trot', 'Golf', 'hotel', 'India', 'juliet', 'Kilo', 'kilo', 'Lima', 'mike'];
    $dueDates = ['2026-11-01', '2026-11-01', null, '2026-10-15', null, '2026-12-31', '2026-10-15', '2026-11-01'];

    foreach ($titles as $i => $title) {
        $task = Task::create([
            'organization_id' => $this->org->id,
            'project_id' => $projects[$i % $projects->count()]->id,
            'department_id' => $departments[($i + 1) % $departments->count()]->id,
            'assignee_id' => $assignees[$i % $assignees->count()]?->id,
            'title' => $title,
            'priority' => Priority::cases()[$i % count(Priority::cases())]->value,
            'status' => TaskStatus::cases()[($i * 3) % count(TaskStatus::cases())]->value,
            'due_date' => $dueDates[$i % count($dueDates)],
        ]);

        if ($i % 4 === 1) {
            $task->delete();
        }
    }
});

dataset('sort columns', ['title', 'project', 'department', 'assignee', 'priority', 'status', 'due_date', 'active']);
dataset('directions', ['asc', 'desc']);

test('walking every page gives exactly the old sortTasks() order', function (string $sort, string $direction) {
    $reference = referenceTaskListOrder(
        Task::withTrashed()->where('organization_id', $this->org->id)->with(['project', 'department', 'assignee'])->get(),
        $sort,
        $direction,
    );

    $shown = taskListIdsAcrossPages($this->management, $this->org->id, [
        'show_inactive' => 1,
        'sort' => $sort,
        'direction' => $direction,
    ]);

    expect($reference)->toHaveCount(20);
    expect($shown)->toBe($reference);
})->with('sort columns', 'directions');

test('with no sort chosen, the active tasks come back in due date order, across pages', function () {
    $reference = referenceTaskListOrder(
        Task::where('organization_id', $this->org->id)->with(['project', 'department', 'assignee'])->get(),
        'due_date',
        'asc',
    );

    expect(taskListIdsAcrossPages($this->management, $this->org->id))->toBe($reference);
});

test('an unknown sort column or direction falls back to due date ascending', function () {
    $default = taskListIdsAcrossPages($this->management, $this->org->id);

    expect(taskListIdsAcrossPages($this->management, $this->org->id, ['sort' => 'id; drop table tasks', 'direction' => 'sideways']))
        ->toBe($default);
});
