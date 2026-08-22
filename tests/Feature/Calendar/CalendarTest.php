<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::where('slug', 'owner')->first()->id);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);

    $this->deptA = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->deptOther = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Operations', 'color' => '#000000']);

    $this->projectA = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Project A',
        'description' => 'd',
    ]);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);
});

function makeStaffOnCalendar(Organization $org, Department $department): User
{
    $staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);
    AccessPermission::create([
        'user_id' => $staff->id,
        'organization_id' => $org->id,
        'department_id' => $department->id,
        'allowed' => true,
    ]);

    return $staff;
}

test('the calendar only shows tasks in a staff user\'s granted departments, same as the Dashboard and Kanban', function () {
    $staff = makeStaffOnCalendar($this->orgA, $this->deptA);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Visible task',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => now()->addDays(3),
    ]);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptOther->id,
        'title' => 'Hidden task',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => now()->addDays(3),
    ]);

    $response = $this->actingAs($staff)->get('/calendar/'.$this->orgA->id);

    $response->assertOk();
    $ganttTasks = $response->viewData('ganttTasks');
    $names = collect($ganttTasks)->pluck('name');

    expect($names->contains(fn ($name) => str_contains($name, 'Visible task')))->toBeTrue()
        ->and($names->contains(fn ($name) => str_contains($name, 'Hidden task')))->toBeFalse();
});

test('tasks with no due date are excluded from the calendar', function () {
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Dated task',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => now()->addDays(3),
    ]);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Undated task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->owner)->get('/calendar/'.$this->orgA->id);

    $response->assertOk();
    $ganttTasks = $response->viewData('ganttTasks');
    $names = collect($ganttTasks)->pluck('name');

    expect($names->contains(fn ($name) => str_contains($name, 'Dated task')))->toBeTrue()
        ->and($names->contains(fn ($name) => str_contains($name, 'Undated task')))->toBeFalse();
});

test('the calendar is scoped to the selected company tab, and only lists active companies as tabs', function () {
    $inactiveOrg = Organization::create(['name' => 'Inactive Co', 'slug' => 'inactive-co', 'accent_color' => '#000000', 'is_active' => false]);

    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Org A task',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => now()->addDays(3),
    ]);

    $projectB = Project::create(['organization_id' => $this->orgB->id, 'name' => 'Project B', 'description' => 'd']);
    $deptB = Department::create(['organization_id' => $this->orgB->id, 'name' => 'Sales', 'color' => '#000000']);
    Task::create([
        'organization_id' => $this->orgB->id,
        'project_id' => $projectB->id,
        'department_id' => $deptB->id,
        'title' => 'Org B task',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => now()->addDays(3),
    ]);

    $response = $this->actingAs($this->owner)->get('/calendar/'.$this->orgA->id);

    $response->assertOk()->assertSee('Org A task')->assertDontSee('Org B task');
    $response->assertDontSee('Inactive Co');

    $organizations = $response->viewData('organizations');
    expect($organizations->pluck('id')->all())->toBe([$this->orgA->id, $this->orgB->id]);
});

test('each gantt task carries the correct Edit Task URL for click-to-navigate', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Click me',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => now()->addDays(3),
    ]);

    $response = $this->actingAs($this->owner)->get('/calendar/'.$this->orgA->id);

    $ganttTask = collect($response->viewData('ganttTasks'))->firstWhere('id', (string) $task->id);
    expect($ganttTask['editUrl'])->toBe(route('tasks.edit', $task));
});

test('a user with no company membership at all sees the generic "no access" calendar message', function () {
    $orphan = User::factory()->create();

    $response = $this->actingAs($orphan)->get('/calendar');

    $response->assertOk();
    $response->assertSee("You don't have access to any companies yet.");
});

test('a gantt bar spans from the task\'s start_date to its due_date when start_date is set', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Spanning task',
        'priority' => 'medium',
        'status' => 'in_progress',
        'due_date' => now()->addDays(10),
        'start_date' => now()->subDays(3),
    ]);

    $response = $this->actingAs($this->owner)->get('/calendar/'.$this->orgA->id);

    $ganttTask = collect($response->viewData('ganttTasks'))->firstWhere('id', (string) $task->id);
    expect($ganttTask['start'])->toBe(now()->subDays(3)->toDateString())
        ->and($ganttTask['end'])->toBe(now()->addDays(10)->toDateString());
});

test('a task with no start_date renders as a single-day bar at its due date, not a multi-day span', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Not started yet',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => now()->addDays(10),
    ]);

    $response = $this->actingAs($this->owner)->get('/calendar/'.$this->orgA->id);

    $ganttTask = collect($response->viewData('ganttTasks'))->firstWhere('id', (string) $task->id);
    expect($ganttTask['start'])->toBe(now()->addDays(10)->toDateString())
        ->and($ganttTask['end'])->toBe(now()->addDays(10)->toDateString());
});

test('a task whose start_date falls after its due_date renders as a single-day bar instead of being dropped', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Due date pulled earlier',
        'priority' => 'medium',
        'status' => 'in_progress',
        'due_date' => now()->subDays(5),
        'start_date' => now(),
    ]);

    $response = $this->actingAs($this->owner)->get('/calendar/'.$this->orgA->id);

    $ganttTask = collect($response->viewData('ganttTasks'))->firstWhere('id', (string) $task->id);
    expect($ganttTask['start'])->toBe(now()->subDays(5)->toDateString())
        ->and($ganttTask['end'])->toBe(now()->subDays(5)->toDateString());
});

test('the gantt-container data-tasks attribute is valid, parseable JSON even when a task title contains a double quote', function () {
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Say "hi" & <test>',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => now()->addDays(3),
    ]);

    $response = $this->actingAs($this->owner)->get('/calendar/'.$this->orgA->id);

    $response->assertOk();
    preg_match("/data-tasks='(.*?)'/s", $response->getContent(), $matches);
    expect($matches)->toHaveCount(2);

    $decoded = json_decode($matches[1], true);
    expect(json_last_error())->toBe(JSON_ERROR_NONE)
        ->and($decoded[0]['name'])->toContain('Say "hi" & <test>');
});

test('a gantt task carries its plottable subtask count for the drill-down affordance', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task with subtasks',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => now()->addDays(3),
    ]);
    $task->subtasks()->create(['title' => 'Dated subtask', 'due_date' => now()->addDays(2)]);
    $task->subtasks()->create(['title' => 'Undated subtask']);

    $response = $this->actingAs($this->owner)->get('/calendar/'.$this->orgA->id);

    $ganttTask = collect($response->viewData('ganttTasks'))->firstWhere('id', (string) $task->id);
    expect($ganttTask['subtaskCount'])->toBe(1);
});

test('subtasksByTask carries a gantt bar per plottable subtask, keyed by parent task id', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Parent task',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => now()->addDays(10),
    ]);
    $subtask = $task->subtasks()->create([
        'title' => 'Drill-down subtask',
        'start_date' => now()->subDays(1),
        'due_date' => now()->addDays(5),
        'is_done' => true,
    ]);
    $task->subtasks()->create(['title' => 'Undated subtask, excluded']);

    $response = $this->actingAs($this->owner)->get('/calendar/'.$this->orgA->id);

    $subtasksByTask = $response->viewData('subtasksByTask');
    expect($subtasksByTask)->toHaveKey($task->id)
        ->and($subtasksByTask[$task->id])->toHaveCount(1);

    $bar = $subtasksByTask[$task->id][0];
    expect($bar['id'])->toBe('subtask-'.$subtask->id)
        ->and($bar['name'])->toContain('Drill-down subtask')
        ->and($bar['start'])->toBe(now()->subDays(1)->toDateString())
        ->and($bar['end'])->toBe(now()->addDays(5)->toDateString())
        ->and($bar['progress'])->toBe(100)
        ->and($bar['editUrl'])->toBe(route('tasks.edit', $task));
});
