<?php

use App\Enums\TaskStatus;
use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

/**
 * The Kanban card's inline assignee select (PATCH /tasks/{task}/assignee).
 * Gated by TaskPolicy::update() - the SAME policy the full Edit Task
 * page's Assignee field uses - not the narrower TaskPolicy::updateStatus()
 * the status select/drag-and-drop use, since handing a task to someone
 * else is a bigger action than moving your own card. Eligibility reuses
 * ValidatesTaskAssignment, the identical check the full form's
 * UpdateTaskRequest applies, so the two surfaces can't drift apart.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->deptA = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->projectA = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);
});

function makeStaffForKanbanAssignee(Organization $org, Department $department): User
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

test('management can reassign a task via the Kanban assignee endpoint, and it is audit logged', function () {
    $newAssignee = makeStaffForKanbanAssignee($this->orgA, $this->deptA);
    $this->projectA->staff()->attach($newAssignee->id);

    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->patchJson("/tasks/{$task->id}/assignee", [
        'assignee_id' => $newAssignee->id,
    ]);

    $response->assertOk()->assertJsonPath('task.assignee_id', $newAssignee->id);
    expect($task->fresh()->assignee_id)->toBe($newAssignee->id);
    $this->assertDatabaseHas('audit_log', [
        'entity_type' => 'task',
        'entity_id' => $task->id,
        'action' => 'task.reassigned',
        'user_id' => $this->management->id,
    ]);
});

test('management can unassign a task via the Kanban assignee endpoint', function () {
    $currentAssignee = makeStaffForKanbanAssignee($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $currentAssignee->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->patchJson("/tasks/{$task->id}/assignee", [
        'assignee_id' => null,
    ]);

    $response->assertOk()->assertJsonPath('task.assignee_id', null);
    expect($task->fresh()->assignee_id)->toBeNull();
});

test('reassigning to a user not attached to the project is rejected, and nothing changes', function () {
    $outsider = makeStaffForKanbanAssignee($this->orgA, $this->deptA);
    // Deliberately never attached to $this->projectA via project_staff.
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->patchJson("/tasks/{$task->id}/assignee", [
        'assignee_id' => $outsider->id,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('assignee_id');
    expect($task->fresh()->assignee_id)->toBeNull();
});

test('a staff user who is neither the assignee nor holds create_edit_tasks cannot reassign via the Kanban endpoint', function () {
    $staff = makeStaffForKanbanAssignee($this->orgA, $this->deptA);
    $otherStaff = makeStaffForKanbanAssignee($this->orgA, $this->deptA);
    $this->projectA->staff()->attach($otherStaff->id);

    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Someone else\'s task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($staff)->patchJson("/tasks/{$task->id}/assignee", [
        'assignee_id' => $otherStaff->id,
    ]);

    $response->assertForbidden();
    expect($task->fresh()->assignee_id)->toBeNull();
});

test('a task\'s own current assignee can reassign it away, even without create_edit_tasks (the same bypass the full Edit form allows)', function () {
    $assignee = makeStaffForKanbanAssignee($this->orgA, $this->deptA);
    $newAssignee = makeStaffForKanbanAssignee($this->orgA, $this->deptA);
    $this->projectA->staff()->attach($newAssignee->id);

    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $assignee->id,
        'title' => 'My task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($assignee)->patchJson("/tasks/{$task->id}/assignee", [
        'assignee_id' => $newAssignee->id,
    ]);

    $response->assertOk();
    expect($task->fresh()->assignee_id)->toBe($newAssignee->id);
});

test('the Kanban card\'s assignee select is enabled for management, pre-selected to the current assignee, with a fixed width regardless of the name', function () {
    $assignee = makeStaffForKanbanAssignee($this->orgA, $this->deptA);
    $this->projectA->staff()->attach($assignee->id);

    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $assignee->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->get('/kanban/'.$this->orgA->id);
    $response->assertOk();

    $column = $response->viewData('columns')->firstWhere('status', TaskStatus::Pending);
    $item = $column['tasks']->firstWhere('task.id', $task->id);
    expect($item['canReassign'])->toBeTrue();

    $html = $response->getContent();
    // task #71: fixed width + truncate — no longer sized to fit whichever
    // name happens to be selected (that was the actual bug this replaced:
    // the dropdown's own width used to vary with the assignee's name).
    expect($html)->toContain('kanban-assignee-select w-20 truncate rounded-md border border-gray-300 px-1.5 py-0.5 text-[10px]');
    // e() first, matching Blade's own {{ }} escaping — a Faker-generated
    // name containing an apostrophe (e.g. "O'Connell") renders as
    // "&#039;" in the actual HTML, which the raw un-escaped name would
    // never match.
    expect($html)->toMatch('/<option value="'.$assignee->id.'"[^>]*selected[^>]*>'.preg_quote(e($assignee->name), '/').'/');
});

test('the Kanban card\'s assignee select is present but disabled for a staff user who cannot reassign', function () {
    $staff = makeStaffForKanbanAssignee($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Someone else\'s task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($staff)->get('/kanban/'.$this->orgA->id);
    $response->assertOk();

    $column = $response->viewData('columns')->firstWhere('status', TaskStatus::Pending);
    $item = $column['tasks']->firstWhere('task.id', $task->id);
    expect($item['canReassign'])->toBeFalse();

    // Rendered (not hidden), just disabled - same "show, don't hide"
    // pattern as the status select.
    expect($response->getContent())->toMatch('/kanban-assignee-select[^>]*data-task-id="'.$task->id.'"[^>]*disabled/');
});

test('the assignee select only offers users actually attached to that task\'s project, not every user in the company', function () {
    $eligible = makeStaffForKanbanAssignee($this->orgA, $this->deptA);
    $this->projectA->staff()->attach($eligible->id);
    $ineligible = makeStaffForKanbanAssignee($this->orgA, $this->deptA);

    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->get('/kanban/'.$this->orgA->id);
    $response->assertOk();

    $ids = collect($response->viewData('staffByProject')[$this->projectA->id])->pluck('id')->all();
    expect($ids)->toContain($eligible->id)->not->toContain($ineligible->id);
});
