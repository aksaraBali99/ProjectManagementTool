<?php

use App\Enums\Priority;
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

function makeStaffOnDashboard(Organization $org, Department $department): User
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

function makeTaskOnDashboard(Organization $org, Project $project, Department $department, string $title, Priority $priority): Task
{
    return Task::create([
        'organization_id' => $org->id,
        'project_id' => $project->id,
        'department_id' => $department->id,
        'title' => $title,
        'priority' => $priority,
        'status' => 'pending',
    ]);
}

test('a staff user\'s Dashboard only shows tasks in their granted departments, correctly grouped by Priority enum value', function () {
    $staff = makeStaffOnDashboard($this->orgA, $this->deptA);

    $visibleHigh = makeTaskOnDashboard($this->orgA, $this->projectA, $this->deptA, 'Visible high task', Priority::High);
    $visibleLow = makeTaskOnDashboard($this->orgA, $this->projectA, $this->deptA, 'Visible low task', Priority::Low);
    $hiddenHigh = makeTaskOnDashboard($this->orgA, $this->projectA, $this->deptOther, 'Hidden high task', Priority::High);

    $response = $this->actingAs($staff)->get('/dashboard/'.$this->orgA->id);

    $response->assertOk();
    $response->assertSee('Visible high task');
    $response->assertSee('Visible low task');
    $response->assertDontSee('Hidden high task');

    $priorityGroups = $response->viewData('priorityGroups');
    expect($priorityGroups[Priority::High->value]->pluck('id')->all())->toBe([$visibleHigh->id])
        ->and($priorityGroups[Priority::Low->value]->pluck('id')->all())->toBe([$visibleLow->id])
        ->and($priorityGroups[Priority::Medium->value])->toHaveCount(0);
});

test('a task assigned to a staff user outside their granted departments still appears on their Dashboard', function () {
    $staff = makeStaffOnDashboard($this->orgA, $this->deptA);
    $assignedElsewhere = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptOther->id,
        'assignee_id' => $staff->id,
        'title' => 'Assigned outside department',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($staff)->get('/dashboard/'.$this->orgA->id);

    $response->assertOk();
    $priorityGroups = $response->viewData('priorityGroups');
    expect($priorityGroups[Priority::Medium->value]->pluck('id')->all())->toBe([$assignedElsewhere->id]);
});

test('the Active list only includes High-priority tasks that are In progress or In review', function () {
    $activeInProgress = makeTaskOnDashboard($this->orgA, $this->projectA, $this->deptA, 'High + in progress', Priority::High);
    $activeInProgress->update(['status' => 'in_progress']);
    $activeInReview = makeTaskOnDashboard($this->orgA, $this->projectA, $this->deptA, 'High + in review', Priority::High);
    $activeInReview->update(['status' => 'in_review']);

    // Each excluded for a different reason: wrong status, wrong priority,
    // or both — pins down that it's an AND of both conditions, not an OR.
    $wrongStatusPending = makeTaskOnDashboard($this->orgA, $this->projectA, $this->deptA, 'High + pending', Priority::High);
    $wrongStatusCompleted = makeTaskOnDashboard($this->orgA, $this->projectA, $this->deptA, 'High + completed', Priority::High);
    $wrongStatusCompleted->update(['status' => 'completed']);
    $wrongPriority = makeTaskOnDashboard($this->orgA, $this->projectA, $this->deptA, 'Medium + in progress', Priority::Medium);
    $wrongPriority->update(['status' => 'in_progress']);

    $response = $this->actingAs($this->management)->get('/dashboard/'.$this->orgA->id);

    $response->assertOk();
    // Priority-group membership is by priority alone (any status), so
    // "High + pending" legitimately still appears in the High column —
    // the Active list's own scoped viewData is the precise assertion here,
    // not a page-wide assertSee/assertDontSee.
    $activeTasks = $response->viewData('activeTasks');
    expect($activeTasks->pluck('id')->sort()->values()->all())
        ->toBe(collect([$activeInProgress->id, $activeInReview->id])->sort()->values()->all());
});

test('a deactivated (soft-deleted) task is excluded from the Dashboard', function () {
    $task = makeTaskOnDashboard($this->orgA, $this->projectA, $this->deptA, 'Deactivated task', Priority::High);
    $task->delete();

    $response = $this->actingAs($this->management)->get('/dashboard/'.$this->orgA->id);

    $response->assertOk();
    $response->assertDontSee('Deactivated task');
});

test('a client-role user gets a Dashboard tab for their project\'s company, scoped to only their attached project\'s tasks', function () {
    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->first()->id,
    ]);
    $this->projectA->clients()->attach($client->id);

    $myProjectTask = makeTaskOnDashboard($this->orgA, $this->projectA, $this->deptA, 'My project task', Priority::High);

    $otherProject = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Other project',
        'description' => 'd',
    ]);
    makeTaskOnDashboard($this->orgA, $otherProject, $this->deptA, 'Other project task', Priority::High);

    $response = $this->actingAs($client)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Org A');
    $response->assertSee('My project task');
    $response->assertDontSee('Other project task');
    // No MyTask box for a client — their full (already project-scoped)
    // task list is already shown in full above, so a MyTask section would
    // just repeat it.
    expect($response->viewData('myTaskMode'))->toBe('none');
    $response->assertDontSee('My Task');
});

test('a client with no attached project and no other role sees the empty-state Dashboard', function () {
    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->first()->id,
    ]);
    // Deliberately NOT attached to any project via project_clients.

    $response = $this->actingAs($client)->get('/dashboard');

    $response->assertOk();
    $response->assertSee("You don't have access to any companies yet.", false);
});

test('a staff user\'s MyTask only shows tasks assigned to them AND within their granted departments', function () {
    $staff = makeStaffOnDashboard($this->orgA, $this->deptA);

    $mine = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff->id,
        'title' => 'Mine, in my department',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);
    // Visible on the general Dashboard via the assignee-anywhere bypass,
    // but MyTask further constrains to granted departments, so this one
    // must NOT appear there.
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptOther->id,
        'assignee_id' => $staff->id,
        'title' => 'Mine, outside my department',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);
    // Visible generally via department access, but not assigned to me.
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Not mine, in my department',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($staff)->get('/dashboard/'.$this->orgA->id);

    $response->assertOk();
    expect($response->viewData('myTasks')->pluck('id')->all())->toBe([$mine->id])
        ->and($response->viewData('myTaskMode'))->toBe('staff');
});

test('a management user\'s MyTask shows tasks assigned to Staff-role members of their company, not other management', function () {
    $staffMember = makeStaffOnDashboard($this->orgA, $this->deptA);
    $otherManagement = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $otherManagement->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);

    $assignedToStaff = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staffMember->id,
        'title' => 'Assigned to staff',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $otherManagement->id,
        'title' => 'Assigned to another management user',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Unassigned',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->get('/dashboard/'.$this->orgA->id);

    $response->assertOk();
    expect($response->viewData('myTasks')->pluck('id')->all())->toBe([$assignedToStaff->id])
        ->and($response->viewData('myTaskMode'))->toBe('management');
});

test('super_admin MyTask defaults to every task in the company, narrowed by a staff checkbox filter', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->roles()->attach(Role::where('slug', 'super_admin')->first()->id);

    $staff1 = makeStaffOnDashboard($this->orgA, $this->deptA);
    $staff2 = makeStaffOnDashboard($this->orgA, $this->deptA);

    $task1 = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff1->id,
        'title' => 'Staff 1 task',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);
    $task2 = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $staff2->id,
        'title' => 'Staff 2 task',
        'priority' => Priority::Medium,
        'status' => 'pending',
    ]);

    $defaultResponse = $this->actingAs($superAdmin)->get('/dashboard/'.$this->orgA->id);
    $defaultResponse->assertOk();
    expect($defaultResponse->viewData('myTasks')->pluck('id')->sort()->values()->all())
        ->toBe(collect([$task1->id, $task2->id])->sort()->values()->all())
        ->and($defaultResponse->viewData('myTaskMode'))->toBe('admin')
        ->and($defaultResponse->viewData('staffOptions')->pluck('id')->sort()->values()->all())
        ->toBe(collect([$staff1->id, $staff2->id])->sort()->values()->all());

    $filteredResponse = $this->actingAs($superAdmin)->get('/dashboard/'.$this->orgA->id.'?staff[]='.$staff1->id);
    $filteredResponse->assertOk();
    expect($filteredResponse->viewData('myTasks')->pluck('id')->all())->toBe([$task1->id]);
});

test('management only sees their own company as a Dashboard tab, and super_admin sees every active company', function () {
    $orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);

    $managementResponse = $this->actingAs($this->management)->get('/dashboard');
    $managementResponse->assertSee('Org A');
    $managementResponse->assertDontSee('Org B');

    $superAdmin = User::factory()->create();
    $superAdmin->roles()->attach(Role::where('slug', 'super_admin')->first()->id);

    $superAdminResponse = $this->actingAs($superAdmin)->get('/dashboard');
    $superAdminResponse->assertSee('Org A');
    $superAdminResponse->assertSee('Org B');
});

test('revoking the view_dashboard permission from Staff removes their Dashboard access, even with department access intact', function () {
    $staff = makeStaffOnDashboard($this->orgA, $this->deptA);

    // Sanity check: staff can see the Dashboard before the change.
    $this->actingAs($staff)->get('/dashboard/'.$this->orgA->id)->assertSee('Org A');

    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $remainingPermissionIds = $staffRole->permissions()
        ->where('slug', '!=', 'view_dashboard')
        ->pluck('permissions.id')
        ->all();
    $staffRole->permissions()->sync($remainingPermissionIds);

    $response = $this->actingAs($staff)->get('/dashboard');

    $response->assertOk();
    // Department access (access_permissions) is untouched — only the
    // board-level capability was revoked, confirming the two stay
    // independent as required.
    expect($staff->allowedDepartmentIds($this->orgA->id)->all())->toBe([$this->deptA->id]);
    $response->assertSee("You don't have access to any companies yet.", false);
});
