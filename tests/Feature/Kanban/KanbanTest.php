<?php

use App\Enums\Priority;
use App\Enums\TaskStatus;
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

function makeStaffOnKanban(Organization $org, Department $department): User
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

test('Kanban renders one column per TaskStatus case, in the enum\'s declared order', function () {
    $response = $this->actingAs($this->management)->get('/kanban/'.$this->orgA->id);
    $response->assertOk();

    $content = $response->getContent();
    $expectedLabelsInOrder = array_map(fn (TaskStatus $status) => $status->label(), TaskStatus::cases());

    // Computed from the live enum, not a hardcoded literal list — if
    // TaskStatus::cases() is ever reordered, this recomputes automatically
    // and still passes for a correct (enum-driven) implementation, while a
    // controller/view with a separate hardcoded column list would keep the
    // old order and fail here.
    $positions = array_map(fn (string $label) => strpos($content, $label), $expectedLabelsInOrder);

    expect($positions)->not->toContain(false);
    $sorted = $positions;
    sort($sorted);
    expect($positions)->toBe($sorted);

    $columnCount = substr_count($content, 'kanban-column rounded');
    expect($columnCount)->toBe(count(TaskStatus::cases()));
});

test('Kanban columns sort tasks by Priority\'s natural ordering, High before Medium before Low', function () {
    $low = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Low priority task',
        'priority' => Priority::Low,
        'status' => 'pending',
    ]);
    $high = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'High priority task',
        'priority' => Priority::High,
        'status' => 'pending',
    ]);
    $medium = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Medium priority task',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->get('/kanban/'.$this->orgA->id);
    $response->assertOk();

    $pendingColumn = $response->viewData('columns')->firstWhere('status', TaskStatus::Pending);
    expect($pendingColumn['tasks']->pluck('task.id')->all())->toBe([$high->id, $medium->id, $low->id]);
});

test('a staff user who is not the assignee cannot change another person\'s task status via Kanban', function () {
    $staff = makeStaffOnKanban($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Someone else\'s task',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);

    $this->actingAs($staff)->patchJson("/tasks/{$task->id}/status", ['status' => 'in_progress'])
        ->assertForbidden();
    expect($task->fresh()->status)->toBe(TaskStatus::Pending);
});

test('the assignee of a task can change its status via Kanban, and it is audit logged', function () {
    $staff = makeStaffOnKanban($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff->id,
        'title' => 'My task',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($staff)->patchJson("/tasks/{$task->id}/status", ['status' => 'in_progress']);

    $response->assertOk();
    expect($task->fresh()->status)->toBe(TaskStatus::InProgress);
    $this->assertDatabaseHas('audit_log', [
        'entity_type' => 'task',
        'entity_id' => $task->id,
        'action' => 'task.status_changed',
        'user_id' => $staff->id,
    ]);
});

test('management can change the status of any task in their company via Kanban', function () {
    $staff = makeStaffOnKanban($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff->id,
        'title' => 'Staff task',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);

    $this->actingAs($this->management)->patchJson("/tasks/{$task->id}/status", ['status' => 'in_review'])
        ->assertOk();
    expect($task->fresh()->status)->toBe(TaskStatus::InReview);
});

test('revoking update_kanban_cards from management blocks moving another person\'s card, but not editing the task via the full form', function () {
    $staff = makeStaffOnKanban($this->orgA, $this->deptA);
    $this->projectA->staff()->attach($staff->id);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff->id,
        'title' => 'Staff task',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);

    $managementRole = Role::where('slug', 'management')->firstOrFail();
    $remainingPermissionIds = $managementRole->permissions()
        ->where('slug', '!=', 'update_kanban_cards')
        ->pluck('permissions.id')
        ->all();
    $managementRole->permissions()->sync($remainingPermissionIds);

    $this->actingAs($this->management)->patchJson("/tasks/{$task->id}/status", ['status' => 'in_review'])
        ->assertForbidden();
    expect($task->fresh()->status)->toBe(TaskStatus::Pending);

    // update_kanban_cards is independent of create_edit_tasks — the full
    // task-edit form still works for management with that permission
    // still intact.
    $this->actingAs($this->management)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff->id,
        'title' => 'Staff task, renamed',
        'priority' => 'medium',
        'status' => 'in_review',
    ])->assertRedirect("/tasks/{$task->id}/edit");
    expect($task->fresh()->title)->toBe('Staff task, renamed')
        ->and($task->fresh()->status)->toBe(TaskStatus::InReview);
});

test('the assignee of a task can still move its Kanban card even without the update_kanban_cards permission', function () {
    $staff = makeStaffOnKanban($this->orgA, $this->deptA);
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff->id,
        'title' => 'My task',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);

    // Staff never holds update_kanban_cards, per the seeder — the
    // assignee bypass in TaskPolicy::updateStatus() is what's under test.
    $this->actingAs($staff)->patchJson("/tasks/{$task->id}/status", ['status' => 'in_progress'])
        ->assertOk();
    expect($task->fresh()->status)->toBe(TaskStatus::InProgress);
});

test('Kanban only shows tasks in a staff user\'s granted departments, same as the Dashboard', function () {
    $staff = makeStaffOnKanban($this->orgA, $this->deptA);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Visible task',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptOther->id,
        'title' => 'Hidden task',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($staff)->get('/kanban/'.$this->orgA->id);

    $response->assertOk();
    $response->assertSee('Visible task');
    $response->assertDontSee('Hidden task');
});

test('a client-role user gets a Kanban tab for their project\'s company, scoped to only their attached project\'s tasks', function () {
    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->first()->id,
    ]);
    $this->projectA->clients()->attach($client->id);

    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'My project task',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);

    $otherProject = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Other project',
        'description' => 'd',
    ]);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $otherProject->id,
        'department_id' => $this->deptA->id,
        'title' => 'Other project task',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($client)->get('/kanban');

    $response->assertOk();
    $response->assertSee('Org A');
    $response->assertSee('My project task');
    $response->assertDontSee('Other project task');
});

test('a client with the Client role but no attached project sees a "no active project" Kanban message', function () {
    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->first()->id,
    ]);

    $response = $this->actingAs($client)->get('/kanban');

    $response->assertOk();
    $response->assertSee("You don't have any active project yet.");
});

test('a user with no company membership at all sees the generic "no access" Kanban message', function () {
    $orphan = User::factory()->create();

    $response = $this->actingAs($orphan)->get('/kanban');

    $response->assertOk();
    $response->assertSee("You don't have access to any companies yet.");
});

test('revoking the view_kanban permission from Client removes their Kanban access, even with an attached project', function () {
    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->first()->id,
    ]);
    $this->projectA->clients()->attach($client->id);

    // Sanity check: client can see Kanban before the change.
    $this->actingAs($client)->get('/kanban')->assertSee('Org A');

    $clientRole = Role::where('slug', 'client')->firstOrFail();
    $remainingPermissionIds = $clientRole->permissions()
        ->where('slug', '!=', 'view_kanban')
        ->pluck('permissions.id')
        ->all();
    $clientRole->permissions()->sync($remainingPermissionIds);

    $response = $this->actingAs($client)->get('/kanban');

    $response->assertOk();
    // Their project attachment is untouched — only the board-level
    // capability was revoked.
    expect($client->projectsAsClient()->pluck('projects.id')->all())->toBe([$this->projectA->id]);
    $response->assertSee('You have no access for this page.');
});
