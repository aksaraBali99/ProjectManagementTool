<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;

/**
 * task #72 phase 4: paginating and sorting the Tasks list in SQL must not
 * change WHO sees WHICH task. For every user shape, with and without each
 * filter and with "Show inactive", the tasks across all pages must be
 * exactly Task::visibleTo() narrowed by that filter — no more, no fewer,
 * none twice.
 *
 * The filters are re-implemented here in PHP over the visibleTo() result
 * rather than copied from the controller, so a mistake in one can't hide
 * in the other.
 */
beforeEach(function () {
    createOwner();

    $this->org = Organization::create(['name' => 'Visibility Org', 'slug' => 'visibility-org', 'accent_color' => '#1D9E75']);
    $this->otherOrg = Organization::create(['name' => 'Other Org', 'slug' => 'other-org', 'accent_color' => '#534AB7']);

    $this->deptA = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->deptB = Department::create(['organization_id' => $this->org->id, 'name' => 'Operations', 'color' => '#000000']);
    $this->projectA = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);
    $this->projectB = Project::create(['organization_id' => $this->org->id, 'name' => 'Project B', 'description' => 'd']);

    $member = function (string $role) {
        $user = User::factory()->create(['tasks_per_page' => 4]);
        OrgMember::create([
            'organization_id' => $this->org->id,
            'user_id' => $user->id,
            'role_id' => Role::where('slug', $role)->firstOrFail()->id,
        ]);

        return $user;
    };

    $this->management = $member('management');

    $this->staffWithAccess = $member('staff');
    AccessPermission::create(['user_id' => $this->staffWithAccess->id, 'organization_id' => $this->org->id, 'department_id' => $this->deptA->id, 'allowed' => true]);

    // Access to the OTHER department only.
    $this->staffWithOtherAccess = $member('staff');
    AccessPermission::create(['user_id' => $this->staffWithOtherAccess->id, 'organization_id' => $this->org->id, 'department_id' => $this->deptB->id, 'allowed' => true]);

    // No department access and nothing assigned: refused the list outright.
    $this->staffWithoutAccess = $member('staff');

    // No department access, but assigned a task in deptB.
    $this->staffAssignee = $member('staff');

    // No department access, but assigned a subtask of a task in deptB.
    $this->staffSubtaskAssignee = $member('staff');

    $this->client = $member('client');
    $this->projectA->clients()->attach($this->client->id);

    $priorities = ['high', 'medium', 'low'];
    $statuses = ['pending', 'in_progress', 'in_review', 'completed'];
    $assignees = [null, $this->staffWithAccess->id, $this->staffAssignee->id, $this->management->id];

    for ($i = 0; $i < 18; $i++) {
        $task = Task::create([
            'organization_id' => $this->org->id,
            'project_id' => $i % 2 ? $this->projectA->id : $this->projectB->id,
            'department_id' => $i % 3 ? $this->deptA->id : $this->deptB->id,
            'assignee_id' => $assignees[$i % count($assignees)],
            'title' => ($i % 5 ? 'Routine ' : 'Launch ').$i,
            'priority' => $priorities[$i % 3],
            'status' => $statuses[$i % 4],
            'due_date' => $i % 4 ? '2026-11-'.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) : null,
        ]);

        if ($i === 3) {
            Subtask::create(['task_id' => $task->id, 'title' => 'Sub', 'assignee_id' => $this->staffSubtaskAssignee->id]);
        }

        if ($i % 6 === 5) {
            $task->delete();
        }
    }

    // Another company's task must never leak onto this company's list.
    $otherDept = Department::create(['organization_id' => $this->otherOrg->id, 'name' => 'Sales', 'color' => '#000000']);
    $otherProject = Project::create(['organization_id' => $this->otherOrg->id, 'name' => 'Elsewhere', 'description' => 'd']);
    Task::create([
        'organization_id' => $this->otherOrg->id, 'project_id' => $otherProject->id, 'department_id' => $otherDept->id,
        'assignee_id' => $this->staffAssignee->id, 'title' => 'Launch elsewhere', 'priority' => 'high', 'status' => 'pending',
    ]);
});

/**
 * Every filter the list offers, one at a time, plus none.
 *
 * @return array<string, array{0: array<string, mixed>, 1: callable(Task): bool}>
 */
function taskListVisibilityFilterCases(object $context): array
{
    return [
        'no filter' => [[], fn (Task $t) => true],
        'title' => [['q' => 'launch'], fn (Task $t) => stripos($t->title, 'launch') !== false],
        'project' => [['project_id' => $context->projectA->id], fn (Task $t) => $t->project_id === $context->projectA->id],
        'department' => [['department_id' => $context->deptB->id], fn (Task $t) => $t->department_id === $context->deptB->id],
        'assignee' => [['assignee_id' => $context->staffAssignee->id], fn (Task $t) => $t->assignee_id === $context->staffAssignee->id],
        'unassigned' => [['assignee_id' => 'unassigned'], fn (Task $t) => $t->assignee_id === null],
        'priority' => [['priority' => 'high'], fn (Task $t) => $t->priority->value === 'high'],
        'status' => [['status' => 'in_review'], fn (Task $t) => $t->status->value === 'in_review'],
        'due from' => [['due_from' => '2026-11-08'], fn (Task $t) => $t->due_date && $t->due_date->toDateString() >= '2026-11-08'],
        'due to' => [['due_to' => '2026-11-08'], fn (Task $t) => $t->due_date && $t->due_date->toDateString() <= '2026-11-08'],
    ];
}

dataset('task list viewers', [
    'management' => ['management'],
    'staff with department access' => ['staffWithAccess'],
    'staff with access to the other department only' => ['staffWithOtherAccess'],
    'staff who is only a task assignee' => ['staffAssignee'],
    'staff who is only a subtask assignee' => ['staffSubtaskAssignee'],
    'client' => ['client'],
]);

test('the tasks across all pages are exactly what visibleTo() allows, for every filter', function (string $viewer) {
    $user = $this->{$viewer};

    foreach ([false, true] as $showInactive) {
        $visible = Task::visibleTo($user, $this->org->id)
            ->when($showInactive, fn ($query) => $query->withTrashed())
            ->get();

        foreach (taskListVisibilityFilterCases($this) as $label => [$query, $matches]) {
            $expected = $visible->filter($matches)->pluck('id')->sort()->values()->all();

            $shown = taskListIdsAcrossPages($user, $this->org->id, array_merge($query, $showInactive ? ['show_inactive' => 1] : []));

            expect($shown)->toHaveCount(count(array_unique($shown)), "{$viewer} / {$label}: a task appeared twice");
            sort($shown);
            expect($shown)->toBe($expected, "{$viewer} / {$label}".($showInactive ? ' / show inactive' : ''));
        }
    }
})->with('task list viewers');

test('the dataset gives each viewer a different, non-trivial slice', function () {
    // Guards the test above against passing vacuously: if every viewer saw
    // nothing, or everything, it would prove very little.
    $count = fn (User $user) => Task::visibleTo($user, $this->org->id)->count();

    expect($count($this->management))->toBe(15)
        ->and($count($this->staffWithOtherAccess))->toBeGreaterThan(0)->toBeLessThan(15)
        ->and($count($this->staffAssignee))->toBeGreaterThan(0)->toBeLessThan(15)
        ->and($count($this->staffSubtaskAssignee))->toBe(1)
        ->and($count($this->client))->toBeGreaterThan(0)->toBeLessThan(15)
        ->and($count($this->staffWithAccess))->toBeGreaterThan($count($this->staffAssignee));
});

test('a staff member with no department access and nothing assigned is still refused the list', function () {
    // Unchanged by phase 4: TaskPolicy::viewAny() turns them away before
    // any paging or sorting happens.
    $this->actingAs($this->staffWithoutAccess)->get(route('tasks.index', $this->org))->assertForbidden();
});
