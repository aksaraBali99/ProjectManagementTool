<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Document;
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

test('a newly created subtask defaults to the parent task\'s current assignee and due date when neither is explicitly overridden', function () {
    $staff = makeStaffWithDepartmentAccess($this->orgA, $this->deptA);
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
