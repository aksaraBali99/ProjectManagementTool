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

beforeEach(function () {
    Role::create(['name' => 'Super Admin', 'slug' => 'super_admin', 'is_system' => true]);
    Role::create(['name' => 'Owner', 'slug' => 'owner', 'is_system' => true]);
    Role::create(['name' => 'Management', 'slug' => 'management', 'is_system' => true]);
    Role::create(['name' => 'Staff', 'slug' => 'staff', 'is_system' => true]);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::where('slug', 'owner')->first()->id);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);

    $this->deptA = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->deptB = Department::create(['organization_id' => $this->orgB->id, 'name' => 'Sales', 'color' => '#000000']);

    $this->projectA = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Project A',
        'description' => 'd',
        'client_name' => 'internal',
    ]);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);
});

function makeStaffWithDepartmentAccess(Organization $org, Department $department): User
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

test('a management user can create a task with staged subtasks bulk-created against the new task only after saving', function () {
    expect(Subtask::count())->toBe(0);

    $response = $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Write the brief',
        'description' => 'Draft the campaign brief',
        'priority' => 'high',
        'status' => 'pending',
        'subtasks' => ['Outline sections', 'Send for review', ''],
    ]);

    $task = Task::where('title', 'Write the brief')->firstOrFail();
    $response->assertRedirect('/tasks/'.$task->id.'/edit');

    expect($task->subtasks()->count())->toBe(2);
    expect($task->subtasks()->pluck('title')->all())->toBe(['Outline sections', 'Send for review']);
});

test('a staff user cannot create a task', function () {
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);

    $this->actingAs($staff)->get('/tasks/create')->assertForbidden();
    $this->actingAs($staff)->post('/tasks', [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Nope',
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertForbidden();
});

test('submitting a department that does not belong to the selected project company is rejected', function () {
    $response = $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptB->id,
        'title' => 'Mismatched department',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response->assertSessionHasErrors('department_id');
    $this->assertDatabaseMissing('tasks', ['title' => 'Mismatched department']);
});

test('a staff user cannot see a task outside their granted departments', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Operations', 'color' => '#000000']);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $otherDept->id,
        'title' => 'Hidden task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);

    $this->actingAs($staff)->get("/tasks/{$task->id}/edit")->assertForbidden();
});

test('a staff user can view but not edit a task they are not the assignee of', function () {
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Someone else\'s task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $this->actingAs($staff)->get("/tasks/{$task->id}/edit")->assertOk();

    $this->actingAs($staff)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Renamed',
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertForbidden();
});

test('a staff user who can view but not edit a task can toggle a subtask done state, but cannot edit its title, delete it, or edit the parent task', function () {
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Assigned to someone else',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $subtask = $task->subtasks()->create(['title' => 'Do the thing']);

    $this->actingAs($staff)->patch("/subtasks/{$subtask->id}/toggle")->assertOk();
    expect($subtask->fresh()->is_done)->toBeTrue();

    $this->actingAs($staff)->put("/subtasks/{$subtask->id}", ['title' => 'Renamed'])->assertForbidden();
    expect($subtask->fresh()->title)->toBe('Do the thing');

    $this->actingAs($staff)->delete("/subtasks/{$subtask->id}")->assertForbidden();
    $this->assertDatabaseHas('subtasks', ['id' => $subtask->id]);

    $this->actingAs($staff)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Renamed task',
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertForbidden();
});

test('the assignee of a task can edit it and its subtasks even though they are not management', function () {
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff->id,
        'title' => 'My task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $subtask = $task->subtasks()->create(['title' => 'Step one']);

    $this->actingAs($staff)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff->id,
        'title' => 'My task, renamed',
        'priority' => 'medium',
        'status' => 'in_progress',
    ])->assertRedirect("/tasks/{$task->id}/edit");
    expect($task->fresh()->title)->toBe('My task, renamed');

    $this->actingAs($staff)->put("/subtasks/{$subtask->id}", ['title' => 'Step one, revised'])->assertOk();
    expect($subtask->fresh()->title)->toBe('Step one, revised');
});

test('deactivating a task is restricted to management and above', function () {
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $this->actingAs($staff)->patch("/tasks/{$task->id}/toggle-active")->assertForbidden();

    $this->actingAs($this->management)->patch("/tasks/{$task->id}/toggle-active");
    expect($task->fresh()->trashed())->toBeTrue();
});
