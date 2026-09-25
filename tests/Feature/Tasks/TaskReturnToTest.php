<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

/**
 * The Add/Edit Task page's "← Back" and "Cancel" links must go to wherever
 * the user actually came from (Tasks list, Kanban, Calendar, Dashboard, a
 * project's own task list) - never a hardcoded default like Projects,
 * which has no Add Task button and so can never legitimately be where a
 * Create flow started. Entry-point views pass return_to/return_label as
 * plain query params; TaskManagementController::resolveReturnTo() reads
 * and validates them.
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

test('the Add Task page defaults its back/cancel links to the Tasks list, never Projects', function () {
    $response = $this->actingAs($this->management)->get('/tasks/create/'.$this->projectA->id);

    $response->assertOk();
    $tasksIndexUrl = route('tasks.index', $this->orgA->id);
    // Two occurrences (breadcrumb + Cancel) — not assertDontSee(Projects),
    // since the persistent sidebar nav always links to Projects too.
    expect(substr_count($response->getContent(), 'href="'.e($tasksIndexUrl).'"'))->toBeGreaterThanOrEqual(2);
    $response->assertSee('← Tasks');
});

test('the Add Task page honors return_to/return_label from Kanban for both the back link and Cancel', function () {
    $kanbanUrl = url('/kanban/'.$this->orgA->id);

    $response = $this->actingAs($this->management)->get('/tasks/create/'.$this->projectA->id.'?'.http_build_query([
        'return_to' => $kanbanUrl,
        'return_label' => 'Kanban',
    ]));

    $response->assertOk();
    $response->assertSee('← Kanban');
    // Both the top breadcrumb and the Cancel button use the same
    // $returnToUrl - two occurrences of the exact same href.
    expect(substr_count($response->getContent(), 'href="'.e($kanbanUrl).'"'))->toBeGreaterThanOrEqual(2);
});

test('the Edit Task page honors return_to/return_label from Dashboard', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $dashboardUrl = url('/dashboard');

    $response = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit?".http_build_query([
        'return_to' => $dashboardUrl,
        'return_label' => 'Dashboard',
    ]));

    $response->assertOk();
    $response->assertSee('← Dashboard');
    expect(substr_count($response->getContent(), 'href="'.e($dashboardUrl).'"'))->toBeGreaterThanOrEqual(2);
});

test('the Edit Task page defaults to the Tasks list when no return_to is given, not Projects', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit");

    $response->assertOk();
    $response->assertSee('← Tasks');
    $tasksIndexUrl = route('tasks.index', $this->orgA->id);
    expect(substr_count($response->getContent(), 'href="'.e($tasksIndexUrl).'"'))->toBeGreaterThanOrEqual(2);
});

test('a return_to pointing at an external URL is rejected and falls back to the default, not followed', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit?".http_build_query([
        'return_to' => 'https://evil.example.com/phish',
        'return_label' => 'Dashboard',
    ]));

    $response->assertOk();
    $response->assertDontSee('evil.example.com');
    $response->assertSee('href="'.e(route('tasks.index', $this->orgA->id)).'"', false);
});

test('a protocol-relative return_to ("//host/path") is also rejected', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit?".http_build_query([
        'return_to' => '//evil.example.com/phish',
        'return_label' => 'Dashboard',
    ]));

    $response->assertOk();
    $response->assertDontSee('evil.example.com');
});

test('an unrecognized return_label falls back to the default label instead of rendering an arbitrary value', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit?".http_build_query([
        'return_to' => url('/dashboard'),
        'return_label' => 'SomeRandomLabel',
    ]));

    $response->assertOk();
    $response->assertDontSee('SomeRandomLabel');
    $response->assertSee('← Tasks');
});

test('the Add Task page\'s empty-projects state also avoids defaulting to Projects', function () {
    // A brand new org with no projects at all, so $this->projectA doesn't
    // count against "empty" for this manager.
    $emptyOrg = Organization::create(['name' => 'Empty Org', 'slug' => 'empty-org', 'accent_color' => '#000000']);
    $manager = User::factory()->create();
    OrgMember::create([
        'organization_id' => $emptyOrg->id,
        'user_id' => $manager->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);

    $response = $this->actingAs($manager)->get('/tasks/create');

    $response->assertOk();
    $response->assertSee('href="'.e(route('tasks.index')).'"', false);
    $response->assertSee('← Tasks');
});
