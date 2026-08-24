<?php

use App\Models\AccessPermission;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    // Real seeders (not hand-rolled Role::create rows) so these roles carry
    // the role_permissions grants TaskPolicy/SubtaskPolicy now check via
    // hasPermission() — a locally-created Role with no seeded permissions
    // fails every capability check.
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

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

function makeClientOnProject(Organization $org, Project $project): User
{
    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->first()->id,
    ]);
    $project->clients()->attach($client->id);

    return $client;
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
        'subtasks' => [
            ['title' => 'Outline sections', 'assignee_id' => null, 'due_date' => null],
            ['title' => 'Send for review', 'assignee_id' => null, 'due_date' => null],
            ['title' => '', 'assignee_id' => null, 'due_date' => null],
        ],
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

test('a staff user granted create_edit_tasks can create a task only in a department they have access to', function () {
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    Role::where('slug', 'staff')->firstOrFail()->permissions()->attach(
        Permission::where('slug', 'create_edit_tasks')->firstOrFail()->id
    );

    $this->actingAs($staff)->get('/tasks/create')->assertOk();

    $response = $this->actingAs($staff)->post('/tasks', [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Staff-created task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $response->assertRedirect();
    $task = Task::where('title', 'Staff-created task')->firstOrFail();
    $this->assertDatabaseHas('tasks', ['title' => 'Staff-created task', 'department_id' => $this->deptA->id]);

    // A department they DON'T have access to is still rejected, even
    // though the org-level permission is granted.
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Other', 'color' => '#000000']);
    $this->actingAs($staff)->post('/tasks', [
        'project_id' => $this->projectA->id,
        'department_id' => $otherDept->id,
        'title' => 'Should be blocked',
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertForbidden();

    // They can also edit the task they just created.
    $edit = $this->actingAs($staff)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Staff-created task, edited',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $edit->assertRedirect();
    expect($task->fresh()->title)->toBe('Staff-created task, edited');
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

test('assigning a task to a staff member not assigned to the project is rejected', function () {
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);

    $response = $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff->id,
        'title' => 'Unassignable',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response->assertSessionHasErrors('assignee_id');
    $this->assertDatabaseMissing('tasks', ['title' => 'Unassignable']);
});

test('assigning a task to a staff member added to the project succeeds', function () {
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $this->projectA->staff()->attach($staff->id);

    $response = $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff->id,
        'title' => 'Assignable',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('tasks', ['title' => 'Assignable', 'assignee_id' => $staff->id]);
});

test('a management user added to the project can be assigned to a task', function () {
    $this->projectA->staff()->attach($this->management->id);

    $response = $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $this->management->id,
        'title' => 'Assigned to management',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('tasks', ['title' => 'Assigned to management', 'assignee_id' => $this->management->id]);
});

test('the project\'s client can be assigned to a task', function () {
    $client = makeClientOnProject($this->orgA, $this->projectA);

    $response = $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $client->id,
        'title' => 'Assigned to client',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('tasks', ['title' => 'Assigned to client', 'assignee_id' => $client->id]);
});

test('a client not attached to the project cannot be assigned to a task', function () {
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->first()->id]);

    $response = $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $client->id,
        'title' => 'Unattached client',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response->assertSessionHasErrors('assignee_id');
    $this->assertDatabaseMissing('tasks', ['title' => 'Unattached client']);
});

test('assigning a subtask to a staff member not assigned to the project is rejected', function () {
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->post("/tasks/{$task->id}/subtasks", [
        'title' => 'Unassignable subtask',
        'assignee_id' => $staff->id,
    ]);

    $response->assertStatus(422);
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

test('a staff user can view a task assigned to them even outside their granted departments', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Operations', 'color' => '#000000']);
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $otherDept->id,
        'assignee_id' => $staff->id,
        'title' => 'Assigned outside granted department',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $this->actingAs($staff)->get("/tasks/{$task->id}/edit")->assertOk();
});

test('the task list includes a task assigned to the viewer even outside their granted departments', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Operations', 'color' => '#000000']);
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $otherDept->id,
        'assignee_id' => $staff->id,
        'title' => 'Assigned task outside department',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $otherDept->id,
        'title' => 'Unrelated task outside department',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($staff)->get("/tasks/{$this->orgA->id}");

    $response->assertOk()
        ->assertSee('Assigned task outside department')
        ->assertDontSee('Unrelated task outside department');
});

test('a staff user can view a task if they are assigned to one of its subtasks, even outside their granted departments', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Operations', 'color' => '#000000']);
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $otherDept->id,
        'title' => 'Parent task with subtask assignment',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $task->subtasks()->create(['title' => 'Subtask assigned to staff', 'assignee_id' => $staff->id]);

    $this->actingAs($staff)->get("/tasks/{$task->id}/edit")->assertOk();
});

test('the task list includes a task whose subtask is assigned to the viewer, even outside their granted departments', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Operations', 'color' => '#000000']);
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $otherDept->id,
        'title' => 'Task with assigned subtask',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $task->subtasks()->create(['title' => 'Subtask assigned to staff', 'assignee_id' => $staff->id]);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $otherDept->id,
        'title' => 'Unrelated task outside department',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($staff)->get("/tasks/{$this->orgA->id}");

    $response->assertOk()
        ->assertSee('Task with assigned subtask')
        ->assertDontSee('Unrelated task outside department');
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

test('a client user can view but not edit a task on the project they are attached to', function () {
    $client = makeClientOnProject($this->orgA, $this->projectA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Client-visible task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $this->actingAs($client)->get("/tasks/{$task->id}/edit")->assertOk();

    $this->actingAs($client)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Renamed',
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertForbidden();
});

test('a client user cannot view a task on a project they are not attached to', function () {
    $otherProject = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Other Project',
        'description' => 'd',
    ]);
    $client = makeClientOnProject($this->orgA, $this->projectA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $otherProject->id,
        'department_id' => $this->deptA->id,
        'title' => 'Not their task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $this->actingAs($client)->get("/tasks/{$task->id}/edit")->assertForbidden();
});

test('the task list for a client only shows tasks from the project they are attached to', function () {
    $otherProject = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Other Project',
        'description' => 'd',
    ]);
    $client = makeClientOnProject($this->orgA, $this->projectA);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Their task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $otherProject->id,
        'department_id' => $this->deptA->id,
        'title' => 'Not their task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($client)->get("/tasks/{$this->orgA->id}");

    $response->assertOk()->assertSee('Their task')->assertDontSee('Not their task');
});

test('a client user cannot create a task', function () {
    $client = makeClientOnProject($this->orgA, $this->projectA);

    $this->actingAs($client)->get('/tasks/create')->assertForbidden();
    $this->actingAs($client)->post('/tasks', [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Nope',
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
    $this->projectA->staff()->attach($staff->id);
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

test('deactivating a task preserves its subtasks, comments, and document links', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task with relations',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $subtask = $task->subtasks()->create(['title' => 'A subtask']);
    $comment = $task->comments()->create(['user_id' => $this->management->id, 'body' => 'A comment']);
    $document = Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Brief.pdf',
        'link' => 'https://example.com/brief.pdf',
        'access_level' => 'internal',
    ]);
    $task->documents()->attach($document->id);

    $this->actingAs($this->management)->patch("/tasks/{$task->id}/toggle-active");
    $task->refresh();

    expect($task->trashed())->toBeTrue();
    $this->assertDatabaseHas('subtasks', ['id' => $subtask->id, 'task_id' => $task->id]);
    $this->assertDatabaseHas('comments', ['id' => $comment->id, 'task_id' => $task->id]);
    $this->assertDatabaseHas('task_documents', ['task_id' => $task->id, 'document_id' => $document->id]);
    expect($task->subtasks()->count())->toBe(1);
    expect($task->comments()->count())->toBe(1);
    expect($task->documents()->count())->toBe(1);
});

test('the task list only shows a staff member tasks in departments they are granted', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Operations', 'color' => '#000000']);
    $visibleTask = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Visible task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $hiddenTask = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $otherDept->id,
        'title' => 'Hidden task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);

    $response = $this->actingAs($staff)->get('/tasks/'.$this->orgA->id);

    $response->assertOk();
    $response->assertSee('Visible task');
    $response->assertDontSee('Hidden task');
});

test('management sees every task in their company regardless of department', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Operations', 'color' => '#000000']);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task in dept A',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $otherDept->id,
        'title' => 'Task in dept Ops',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->get('/tasks/'.$this->orgA->id);

    $response->assertOk();
    $response->assertSee('Task in dept A');
    $response->assertSee('Task in dept Ops');
});

test('inactive tasks are hidden from the list by default and shown when toggled on', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Soon deactivated',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $task->delete();

    $default = $this->actingAs($this->management)->get('/tasks/'.$this->orgA->id);
    $default->assertOk();
    $default->assertDontSee('Soon deactivated');

    $withInactive = $this->actingAs($this->management)->get('/tasks/'.$this->orgA->id.'?show_inactive=1');
    $withInactive->assertOk();
    $withInactive->assertSee('Soon deactivated');
});

test('posting a comment from the drilldown respects CommentPolicy view scoping', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Operations', 'color' => '#000000']);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $otherDept->id,
        'title' => 'Not staff\'s department',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);

    $this->actingAs($staff)->post("/tasks/{$task->id}/comments", ['body' => 'Sneaky comment'])
        ->assertForbidden();
    $this->assertDatabaseMissing('comments', ['body' => 'Sneaky comment']);

    $response = $this->actingAs($this->management)->post("/tasks/{$task->id}/comments", ['body' => 'Legit comment']);
    $response->assertCreated();
    $this->assertDatabaseHas('comments', ['body' => 'Legit comment', 'task_id' => $task->id, 'user_id' => $this->management->id]);
});

test('the task edit page shows the comments section', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $task->comments()->create(['user_id' => $this->management->id, 'body' => 'Visible comment']);

    $response = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit");

    $response->assertOk();
    $response->assertSee('Comments');
    $response->assertSee('Visible comment');
});

test('a user can edit and delete their own comment', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $comment = $task->comments()->create(['user_id' => $this->management->id, 'body' => 'Original']);

    $update = $this->actingAs($this->management)->put("/comments/{$comment->id}", ['body' => 'Edited']);
    $update->assertOk();
    expect($comment->fresh()->body)->toBe('Edited');

    $this->actingAs($this->management)->delete("/comments/{$comment->id}")->assertOk();
    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});

test('a user cannot edit or delete another user\'s comment', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $comment = $task->comments()->create(['user_id' => $this->management->id, 'body' => 'Original']);
    $otherManager = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $otherManager->id, 'role_id' => Role::where('slug', 'management')->first()->id]);

    $this->actingAs($otherManager)->put("/comments/{$comment->id}", ['body' => 'Hijacked'])->assertForbidden();
    $this->actingAs($otherManager)->delete("/comments/{$comment->id}")->assertForbidden();
    expect($comment->fresh()->body)->toBe('Original');
    $this->assertDatabaseHas('comments', ['id' => $comment->id]);
});

test('the edit/delete controls are hidden on the task page for another user\'s comment', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $comment = $task->comments()->create(['user_id' => $this->management->id, 'body' => 'Not editable by viewer']);
    $otherManager = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $otherManager->id, 'role_id' => Role::where('slug', 'management')->first()->id]);

    $response = $this->actingAs($otherManager)->get("/tasks/{$task->id}/edit");

    $response->assertOk();
    // data-can-edit is the authoritative, server-rendered signal the JS
    // itself keys off — checking for the literal Edit button markup isn't
    // reliable here, since the same class name also appears inside this
    // partial's own <script> (the template string used to build a newly
    // *posted* comment's controls), which would false-match on any page
    // that includes this partial at all.
    $response->assertSee('data-comment-id="'.$comment->id.'" data-can-edit="0"', false);
});

test('a newly created subtask defaults to the parent task\'s current assignee and due date when neither is explicitly overridden', function () {
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $this->projectA->staff()->attach($staff->id);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff->id,
        'due_date' => '2026-09-15',
        'title' => 'Parent task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    // Simulates the form's pre-fill: the request carries the parent's own
    // current values unchanged, exactly as the client-side default would.
    $response = $this->actingAs($this->management)->post("/tasks/{$task->id}/subtasks", [
        'title' => 'Inherits parent defaults',
        'assignee_id' => $staff->id,
        'due_date' => '2026-09-15',
    ]);

    $response->assertCreated();
    $subtask = Subtask::where('title', 'Inherits parent defaults')->firstOrFail();
    expect($subtask->assignee_id)->toBe($staff->id);
    expect($subtask->due_date->toDateString())->toBe('2026-09-15');
});

test('a subtask created with an explicitly different assignee and due date saves those values, not the parent\'s', function () {
    $parentAssignee = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $subtaskAssignee = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $this->projectA->staff()->attach($subtaskAssignee->id);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $parentAssignee->id,
        'due_date' => '2026-09-15',
        'title' => 'Parent task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->post("/tasks/{$task->id}/subtasks", [
        'title' => 'Overridden values',
        'assignee_id' => $subtaskAssignee->id,
        'due_date' => '2026-10-01',
    ]);

    $response->assertCreated();
    $subtask = Subtask::where('title', 'Overridden values')->firstOrFail();
    expect($subtask->assignee_id)->toBe($subtaskAssignee->id);
    expect($subtask->due_date->toDateString())->toBe('2026-10-01');
});

test('changing the parent task\'s assignee or due date after a subtask already exists does not change the existing subtask', function () {
    $originalAssignee = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $newAssignee = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $this->projectA->staff()->attach($newAssignee->id);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $originalAssignee->id,
        'due_date' => '2026-09-15',
        'title' => 'Parent task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $subtask = $task->subtasks()->create([
        'title' => 'Existing subtask',
        'assignee_id' => $originalAssignee->id,
        'due_date' => '2026-09-15',
    ]);

    $this->actingAs($this->management)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $newAssignee->id,
        'title' => 'Parent task',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => '2026-11-01',
    ]);

    expect($task->fresh()->assignee_id)->toBe($newAssignee->id);
    expect($task->fresh()->due_date->toDateString())->toBe('2026-11-01');

    expect($subtask->fresh()->assignee_id)->toBe($originalAssignee->id);
    expect($subtask->fresh()->due_date->toDateString())->toBe('2026-09-15');
});

test('a staff user who can toggle a subtask done state but is not its assignee or management cannot change its assignee or due date', function () {
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Not assigned to staff',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $subtask = $task->subtasks()->create(['title' => 'A subtask']);

    $this->actingAs($staff)->patch("/subtasks/{$subtask->id}/toggle")->assertOk();

    $this->actingAs($staff)->put("/subtasks/{$subtask->id}", ['assignee_id' => $staff->id])->assertForbidden();
    $this->actingAs($staff)->put("/subtasks/{$subtask->id}", ['due_date' => '2026-12-01'])->assertForbidden();

    expect($subtask->fresh()->assignee_id)->toBeNull();
    expect($subtask->fresh()->due_date)->toBeNull();
});

test('attaching a document to a task creates a task_documents row, and attaching it again does not duplicate it', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $document = Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Brief.pdf',
        'link' => 'https://example.com/brief.pdf',
        'access_level' => 'internal',
    ]);

    $this->actingAs($this->management)->post("/tasks/{$task->id}/documents", ['document_id' => $document->id])->assertOk();
    expect($task->documents()->count())->toBe(1);

    $this->actingAs($this->management)->post("/tasks/{$task->id}/documents", ['document_id' => $document->id])->assertOk();
    expect($task->fresh()->documents()->count())->toBe(1);
});

test('detaching a document removes the task_documents link without deleting the document itself', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $document = Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Brief.pdf',
        'link' => 'https://example.com/brief.pdf',
        'access_level' => 'internal',
    ]);
    $task->documents()->attach($document->id);

    $this->actingAs($this->management)->delete("/tasks/{$task->id}/documents/{$document->id}")->assertOk();

    expect($task->fresh()->documents()->count())->toBe(0);
    $this->assertDatabaseHas('documents', ['id' => $document->id]);
});

test('a staff user cannot attach a private document they do not own and are not management for', function () {
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
    $privateDocument = Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Confidential.pdf',
        'link' => 'https://example.com/confidential.pdf',
        'access_level' => 'private',
    ]);

    $this->actingAs($staff)->post("/tasks/{$task->id}/documents", ['document_id' => $privateDocument->id])
        ->assertForbidden();
    expect($task->documents()->count())->toBe(0);
});

test('a user without task edit permission cannot attach or detach documents', function () {
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Not assigned to staff',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $document = Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Brief.pdf',
        'link' => 'https://example.com/brief.pdf',
        'access_level' => 'internal',
    ]);
    $task->documents()->attach($document->id);

    $this->actingAs($staff)->post("/tasks/{$task->id}/documents", ['document_id' => $document->id])->assertForbidden();
    $this->actingAs($staff)->delete("/tasks/{$task->id}/documents/{$document->id}")->assertForbidden();
});

test('a private document attached to a task stays hidden from a task viewer who cannot otherwise see it', function () {
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff->id,
        'title' => 'Task with a hidden document',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $privateDocument = Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Confidential attachment',
        'link' => 'https://example.com/confidential-attachment.pdf',
        'access_level' => 'private',
    ]);
    // Management (who can view it) is the one who attaches it — the leak
    // this test guards against is a *viewer* seeing it via the task
    // drilldown despite being unable to view it directly.
    $task->documents()->attach($privateDocument->id);

    $response = $this->actingAs($staff)->get("/tasks/{$task->id}/edit");

    $response->assertOk();
    $response->assertDontSee('Confidential attachment');
    expect($response->viewData('attachedDocuments')->pluck('id')->all())->not->toContain($privateDocument->id);

    $managementResponse = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit");
    $managementResponse->assertSee('Confidential attachment');
});

test('a client on the task\'s project sees a public attached document but not internal or private ones', function () {
    $client = makeClientOnProject($this->orgA, $this->projectA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task on client\'s project',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $publicDocument = Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Public brief',
        'link' => 'https://example.com/public-brief.pdf',
        'access_level' => 'public',
    ]);
    $internalDocument = Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Internal notes',
        'link' => 'https://example.com/internal-notes.pdf',
        'access_level' => 'internal',
    ]);
    $privateDocument = Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Private memo',
        'link' => 'https://example.com/private-memo.pdf',
        'access_level' => 'private',
    ]);
    $task->documents()->attach([$publicDocument->id, $internalDocument->id, $privateDocument->id]);

    $response = $this->actingAs($client)->get("/tasks/{$task->id}/edit");

    $response->assertOk();
    $response->assertSee('Public brief');
    $response->assertDontSee('Internal notes');
    $response->assertDontSee('Private memo');
    expect($response->viewData('attachedDocuments')->pluck('id')->all())->toBe([$publicDocument->id]);
});

test('creating a new document from the task edit page attaches it to that task in the same request', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    // postJson (not post) so expectsJson() is true, matching the real
    // inline "create & attach" fetch() call's Accept header — plain post()
    // would take DocumentController's redirect-back branch instead.
    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'New brief',
        'link' => 'https://example.com/new-brief.pdf',
        'access_level' => 'internal',
        'task_id' => $task->id,
    ]);

    $response->assertCreated();
    $document = Document::where('name', 'New brief')->firstOrFail();
    expect($task->fresh()->documents()->pluck('documents.id')->all())->toBe([$document->id]);
});

test('adding a document from the task list does not attach it to any task', function () {
    $response = $this->actingAs($this->management)->post('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Library doc',
        'link' => 'https://example.com/library-doc.pdf',
        'access_level' => 'internal',
    ]);

    $response->assertRedirect();
    $document = Document::where('name', 'Library doc')->firstOrFail();
    expect($document->tasks()->count())->toBe(0);
});

test('creating a task writes an audit_log entry', function () {
    $response = $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Audited task',
        'priority' => 'high',
        'status' => 'pending',
    ]);

    $task = Task::where('title', 'Audited task')->firstOrFail();
    $response->assertRedirect('/tasks/'.$task->id.'/edit');

    $log = AuditLog::where('entity_type', 'task')->where('entity_id', $task->id)->firstOrFail();
    expect($log->action)->toBe('task.created')
        ->and($log->organization_id)->toBe($this->orgA->id)
        ->and($log->user_id)->toBe($this->management->id)
        ->and($log->changes['title'])->toBe('Audited task');
});

test('updating a task writes an audit_log entry recording the changed fields', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Original title',
        'priority' => 'low',
        'status' => 'pending',
    ]);

    $this->actingAs($this->management)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Updated title',
        'priority' => 'high',
        'status' => 'pending',
    ]);

    // Priority is the only "named" changed field here (title isn't), so
    // that's the action the observer picks, but the changes payload still
    // captures every field that actually changed in this one combined row.
    $log = AuditLog::where('entity_type', 'task')->where('entity_id', $task->id)->where('action', 'task.priority_changed')->firstOrFail();
    expect($log->user_id)->toBe($this->management->id)
        ->and($log->changes['title'])->toBe(['old' => 'Original title', 'new' => 'Updated title'])
        ->and($log->changes['priority'])->toBe(['old' => 'low', 'new' => 'high'])
        ->and($log->changes)->not->toHaveKey('status');
});

test('updating a task with no actual field changes does not write an audit_log entry', function () {
    // assignee_id/due_date are explicitly set to null here (rather than
    // omitted) to match what TaskManagementController::store() always
    // writes — Eloquent's dirty-check treats a key entirely absent from
    // the original attributes as "changed" the first time it's set, even
    // to the same null value, which would otherwise make this update look
    // dirty for reasons unrelated to what's actually being tested.
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => null,
        'due_date' => null,
        'title' => 'Unchanged',
        'description' => 'Same description',
        'priority' => 'low',
        'status' => 'pending',
    ]);

    $this->actingAs($this->management)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Unchanged',
        'description' => 'Same description',
        'priority' => 'low',
        'status' => 'pending',
    ]);

    expect(AuditLog::where('entity_type', 'task')->where('entity_id', $task->id)->exists())->toBeFalse();
});

test('deactivating and reactivating a task writes audit_log entries', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Toggle me',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $this->actingAs($this->management)->patch("/tasks/{$task->id}/toggle-active");
    $this->actingAs($this->management)->patch("/tasks/{$task->id}/toggle-active");

    $actions = AuditLog::where('entity_type', 'task')->where('entity_id', $task->id)
        ->whereIn('action', ['task.deactivated', 'task.reactivated'])
        ->orderBy('id')
        ->pluck('action');

    expect($actions->all())->toBe(['task.deactivated', 'task.reactivated']);
});

test('the comments JSON endpoint returns the task comments with a per-viewer can_edit flag', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task with comments',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $ownComment = $task->comments()->create(['user_id' => $this->management->id, 'body' => 'Mine']);
    $othersComment = $task->comments()->create(['user_id' => $this->owner->id, 'body' => 'Not mine']);

    $response = $this->actingAs($this->management)->getJson("/tasks/{$task->id}/comments");

    $response->assertOk();
    $comments = collect($response->json('comments'))->keyBy('id');
    expect($comments[$ownComment->id]['body'])->toBe('Mine')
        ->and($comments[$ownComment->id]['can_edit'])->toBeTrue()
        ->and($comments[$othersComment->id]['can_edit'])->toBeFalse();
});

test('the comments JSON endpoint respects CommentPolicy view scoping like the page', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Operations', 'color' => '#000000']);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $otherDept->id,
        'title' => 'Not staff\'s department',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);

    $this->actingAs($staff)->getJson("/tasks/{$task->id}/comments")->assertForbidden();
});

test('creating a subtask writes an audit_log entry', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Parent task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->post("/tasks/{$task->id}/subtasks", [
        'title' => 'Audited subtask',
    ]);
    $response->assertCreated();

    $subtask = Subtask::where('title', 'Audited subtask')->firstOrFail();
    $log = AuditLog::where('entity_type', 'subtask')->where('entity_id', $subtask->id)->firstOrFail();
    expect($log->action)->toBe('subtask.created')
        ->and($log->organization_id)->toBe($this->orgA->id)
        ->and($log->user_id)->toBe($this->management->id)
        ->and($log->changes['title'])->toBe('Audited subtask');
});

test('toggling a subtask done state writes an audit_log entry', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Parent task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $subtask = $task->subtasks()->create(['title' => 'Toggle me']);

    $this->actingAs($this->management)->patch("/subtasks/{$subtask->id}/toggle")->assertOk();
    $this->actingAs($this->management)->patch("/subtasks/{$subtask->id}/toggle")->assertOk();

    // Both directions share the same action name — the toggle direction
    // is distinguished by the changes payload, not the action string.
    $logs = AuditLog::where('entity_type', 'subtask')->where('entity_id', $subtask->id)
        ->orderBy('id')
        ->get();

    expect($logs->pluck('action')->all())->toBe(['subtask.status_changed', 'subtask.status_changed'])
        ->and($logs[0]->changes['is_done'])->toBe(['old' => false, 'new' => true])
        ->and($logs[1]->changes['is_done'])->toBe(['old' => true, 'new' => false]);
});

test('updating a subtask writes an audit_log entry recording the changed fields', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Parent task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $subtask = $task->subtasks()->create(['title' => 'Original title']);

    $this->actingAs($this->management)->put("/subtasks/{$subtask->id}", ['title' => 'Renamed'])->assertOk();

    $log = AuditLog::where('entity_type', 'subtask')->where('entity_id', $subtask->id)->where('action', 'subtask.updated')->firstOrFail();
    expect($log->user_id)->toBe($this->management->id)
        ->and($log->changes)->toBe(['title' => ['old' => 'Original title', 'new' => 'Renamed']]);
});

test('updating a subtask with no actual field changes does not write an audit_log entry', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Parent task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $subtask = $task->subtasks()->create(['title' => 'Unchanged']);

    $this->actingAs($this->management)->put("/subtasks/{$subtask->id}", ['title' => 'Unchanged'])->assertOk();

    expect(AuditLog::where('entity_type', 'subtask')->where('entity_id', $subtask->id)->exists())->toBeFalse();
});

test('deleting a subtask writes an audit_log entry', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Parent task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $subtask = $task->subtasks()->create(['title' => 'Doomed subtask']);
    $subtaskId = $subtask->id;

    $this->actingAs($this->management)->delete("/subtasks/{$subtask->id}")->assertOk();

    $log = AuditLog::where('entity_type', 'subtask')->where('entity_id', $subtaskId)->where('action', 'subtask.deleted')->firstOrFail();
    expect($log->organization_id)->toBe($this->orgA->id)
        ->and($log->changes['title'])->toBe('Doomed subtask');
});

test('the task list renders with mobile card-stacking markup alongside the desktop table', function () {
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Responsive markup task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->get("/tasks/{$this->orgA->id}");

    $response->assertOk();
    // Header hidden below md (labels move inline into each cell instead).
    $response->assertSee('hidden bg-gray-50 md:table-header-group', false);
    // Rows/cells switch between block (mobile) and table display (desktop).
    $response->assertSee('block divide-y divide-gray-100 bg-white md:table-row-group', false);
    $response->assertSee('md:table-cell', false);
});
