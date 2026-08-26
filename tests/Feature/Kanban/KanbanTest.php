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

beforeEach(function () {
    $this->owner = createOwner();

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

    $columnCount = substr_count($content, 'class="kanban-column ');
    expect($columnCount)->toBe(count(TaskStatus::cases()));
});

test('Kanban board uses a horizontally scroll-snapping container below md, and equal-width columns at md and up', function () {
    $response = $this->actingAs($this->management)->get('/kanban/'.$this->orgA->id);

    $response->assertOk();
    $response->assertSee('snap-x snap-mandatory', false);
    $response->assertSee('md:flex-1', false);
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

test('a column\'s status color applies only to its header, not the whole column, which stays white', function () {
    $response = $this->actingAs($this->owner)->get("/kanban/{$this->orgA->id}");
    $html = $response->getContent();

    $response->assertOk();

    // The column wrapper itself carries bg-white and no inline
    // background-color — only its header div does.
    preg_match('/class="kanban-column [^"]*"[^>]*data-status="pending"/', $html, $columnMatch);
    expect($columnMatch)->not->toBeEmpty();
    expect($columnMatch[0])->toContain('bg-white')->not->toContain('style=');

    preg_match('/<div class="flex items-center justify-between rounded-t-lg border-b border-gray-200 px-3 py-2"\s+style="background-color: ([^;"]+)/', $html, $headerMatch);
    expect($headerMatch)->not->toBeEmpty();
    expect($headerMatch[1])->toBe(TaskStatus::Pending->badgeBackground());
});

test('the whole board sits inside a pale wash of the active company\'s own color', function () {
    $response = $this->actingAs($this->owner)->get("/kanban/{$this->orgA->id}");
    $html = $response->getContent();

    $response->assertOk();

    preg_match('/<div class="rounded-b-lg p-3" style="background-color: ([^;"]+);">/', $html, $wrapperMatch);
    expect($wrapperMatch)->not->toBeEmpty();
    expect($wrapperMatch[1])->toBe($this->orgA->badgeBackground());

    // The wrapper's tint sits behind #kanban-board, not the other way
    // around — confirms the board is nested inside it, not a sibling.
    $wrapperPos = strpos($html, $wrapperMatch[0]);
    $boardPos = strpos($html, 'id="kanban-board"');
    expect($boardPos)->toBeGreaterThan($wrapperPos);
});
