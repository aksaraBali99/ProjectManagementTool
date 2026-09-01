<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

beforeEach(function () {
    $this->owner = createOwner();

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

/**
 * Flattens the controller's week-rows into a single list of day-cells, so a
 * test can find "the cell for this date" without caring whether it's Month
 * view's multiple rows or Week view's single row.
 */
function flattenCalendarCells(array $weeks): Collection
{
    return collect($weeks)->flatten(1);
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
    $response->assertSee('Visible task');
    $response->assertDontSee('Hidden task');
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
    $response->assertSee('Dated task');
    $response->assertDontSee('Undated task');

    $cells = flattenCalendarCells($response->viewData('weeks'));
    $allTaskTitles = $cells->flatMap(fn ($cell) => $cell['tasks']->pluck('title'));
    expect($allTaskTitles)->not->toContain('Undated task');
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

test('a task with a due date appears in the correct date cell in Month view', function () {
    $anchor = now()->startOfMonth()->addDays(14); // the 15th — clear of both month-start and month-end boundaries
    $dueDate = $anchor->copy()->addDays(3);

    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Placed task',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => $dueDate,
    ]);

    $response = $this->actingAs($this->owner)->get('/calendar/'.$this->orgA->id.'?date='.$anchor->toDateString());

    $response->assertOk();
    $cells = flattenCalendarCells($response->viewData('weeks'));

    $matchingCell = $cells->firstWhere(fn ($cell) => $cell['date']->isSameDay($dueDate));
    expect($matchingCell['tasks']->pluck('id')->all())->toBe([$task->id]);

    $otherCellsWithTask = $cells->reject(fn ($cell) => $cell['date']->isSameDay($dueDate))
        ->filter(fn ($cell) => $cell['tasks']->contains('id', $task->id));
    expect($otherCellsWithTask)->toHaveCount(0);
});

test('a task with a due date appears in the correct date cell in Week view', function () {
    $anchor = now()->startOfMonth()->addDays(14);
    $dueDate = $anchor->copy()->addDays(2); // still inside the same displayed week

    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Placed weekly task',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => $dueDate,
    ]);

    $response = $this->actingAs($this->owner)->get('/calendar/'.$this->orgA->id.'?view=week&date='.$anchor->toDateString());

    $response->assertOk();
    $weeks = $response->viewData('weeks');
    expect($weeks)->toHaveCount(1); // Week view is always exactly one row

    $cells = flattenCalendarCells($weeks);
    $matchingCell = $cells->firstWhere(fn ($cell) => $cell['date']->isSameDay($dueDate));
    expect($matchingCell['tasks']->pluck('id')->all())->toBe([$task->id]);
});

test('a task with no due date does not appear anywhere on the calendar grid', function () {
    $anchor = now()->startOfMonth()->addDays(14);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'No due date task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->owner)->get('/calendar/'.$this->orgA->id.'?date='.$anchor->toDateString());

    $cells = flattenCalendarCells($response->viewData('weeks'));
    $allTaskTitles = $cells->flatMap(fn ($cell) => $cell['tasks']->pluck('title'));
    expect($allTaskTitles)->not->toContain('No due date task');
});

test('clicking a task on the calendar links to its Edit Task page', function () {
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

    $response->assertOk();
    $response->assertSee(route('tasks.edit', $task), false);
});

test('a user with no company membership at all sees the generic "no access" calendar message', function () {
    $orphan = User::factory()->create();

    $response = $this->actingAs($orphan)->get('/calendar');

    $response->assertOk();
    $response->assertSee("You don't have access to any companies yet.");
});

test('the "+ Add task" affordance is hidden from a staff user who cannot create tasks', function () {
    $staff = makeStaffOnCalendar($this->orgA, $this->deptA);

    $response = $this->actingAs($staff)->get('/calendar/'.$this->orgA->id);

    $response->assertOk();
    $response->assertDontSee('+ Add task');
});

test('the "+ Add task" affordance is shown to management, pre-filling the due date of the specific cell clicked', function () {
    $anchor = now()->startOfMonth()->addDays(14);
    $cellDate = $anchor->copy()->addDays(3);

    $response = $this->actingAs($this->management)->get('/calendar/'.$this->orgA->id.'?date='.$anchor->toDateString());

    $response->assertOk();
    $response->assertSee('+ Add task');

    $expectedUrl = route('tasks.create', $this->projectA).'?due_date='.$cellDate->toDateString();
    $response->assertSee($expectedUrl, false);
});

test('visiting the pre-filled Add Task link from the calendar sets the due date field', function () {
    $dueDate = now()->addDays(5)->toDateString();

    $response = $this->actingAs($this->management)->get('/tasks/create/'.$this->projectA->id.'?due_date='.$dueDate);

    $response->assertOk();
    $response->assertSee('value="'.$dueDate.'"', false);
});
