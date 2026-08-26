<?php

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
    $this->projectA = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);
});

function makeTaskForAnalytics(Organization $org, Project $project, Department $department, array $overrides = []): Task
{
    return Task::create(array_merge([
        'organization_id' => $org->id,
        'project_id' => $project->id,
        'department_id' => $department->id,
        'title' => 'Task '.uniqid(),
        'priority' => 'medium',
        'status' => 'pending',
    ], $overrides));
}

test('a non-owner/non-super-admin cannot access the analytics page', function () {
    $this->actingAs($this->management)->get('/analytics')->assertForbidden();
});

test('an owner can access the analytics page', function () {
    $this->actingAs($this->owner)->get('/analytics')->assertOk();
});

test('a staff user cannot access the analytics page', function () {
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->first()->id]);

    $this->actingAs($staff)->get('/analytics')->assertForbidden();
});

test('a soft-deleted task is excluded from the analytics counts', function () {
    makeTaskForAnalytics($this->orgA, $this->projectA, $this->deptA, ['status' => 'completed']);
    $deleted = makeTaskForAnalytics($this->orgA, $this->projectA, $this->deptA, ['status' => 'completed']);
    $deleted->delete();

    $response = $this->actingAs($this->owner)->get('/analytics');

    $response->assertOk();
    $completion = $response->viewData('completionByCompany')->firstWhere('label', 'Org A');
    expect($completion['total'])->toBe(1)
        ->and($completion['completed'])->toBe(1);

    $statusCounts = $response->viewData('statusCounts');
    expect($statusCounts->firstWhere('label', 'Completed')['count'])->toBe(1);
});

test('an inactive company is excluded from the analytics entirely', function () {
    $inactiveOrg = Organization::create(['name' => 'Inactive Co', 'slug' => 'inactive-co', 'accent_color' => '#000000', 'is_active' => false]);
    $inactiveDept = Department::create(['organization_id' => $inactiveOrg->id, 'name' => 'Ops', 'color' => '#000000']);
    $inactiveProject = Project::create(['organization_id' => $inactiveOrg->id, 'name' => 'Inactive Project', 'description' => 'd']);
    makeTaskForAnalytics($inactiveOrg, $inactiveProject, $inactiveDept);

    $response = $this->actingAs($this->owner)->get('/analytics');

    $response->assertOk();
    $companyLabels = $response->viewData('completionByCompany')->pluck('label');
    expect($companyLabels)->not->toContain('Inactive Co');

    $statusCounts = $response->viewData('statusCounts');
    expect($statusCounts->sum('count'))->toBe(0);
});

test('the completion rate per company is calculated correctly', function () {
    makeTaskForAnalytics($this->orgA, $this->projectA, $this->deptA, ['status' => 'completed']);
    makeTaskForAnalytics($this->orgA, $this->projectA, $this->deptA, ['status' => 'completed']);
    makeTaskForAnalytics($this->orgA, $this->projectA, $this->deptA, ['status' => 'pending']);
    makeTaskForAnalytics($this->orgA, $this->projectA, $this->deptA, ['status' => 'in_progress']);

    $response = $this->actingAs($this->owner)->get('/analytics');

    $completion = $response->viewData('completionByCompany')->firstWhere('label', 'Org A');
    expect($completion['rate'])->toBe(50.0);
});

test('overdue count only includes past-due, non-completed tasks', function () {
    makeTaskForAnalytics($this->orgA, $this->projectA, $this->deptA, ['status' => 'pending', 'due_date' => now()->subDays(2)]);
    makeTaskForAnalytics($this->orgA, $this->projectA, $this->deptA, ['status' => 'completed', 'due_date' => now()->subDays(2)]);
    makeTaskForAnalytics($this->orgA, $this->projectA, $this->deptA, ['status' => 'pending', 'due_date' => now()->addDays(2)]);
    makeTaskForAnalytics($this->orgA, $this->projectA, $this->deptA, ['status' => 'pending']);

    $response = $this->actingAs($this->owner)->get('/analytics');

    expect($response->viewData('overdueCount'))->toBe(1);
});

test('staff workload counts open tasks per staff assignee, excluding completed tasks and non-staff assignees', function () {
    $staff = User::factory()->create(['name' => 'Staff Member']);
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->first()->id]);

    makeTaskForAnalytics($this->orgA, $this->projectA, $this->deptA, ['status' => 'pending', 'assignee_id' => $staff->id]);
    makeTaskForAnalytics($this->orgA, $this->projectA, $this->deptA, ['status' => 'in_progress', 'assignee_id' => $staff->id]);
    makeTaskForAnalytics($this->orgA, $this->projectA, $this->deptA, ['status' => 'completed', 'assignee_id' => $staff->id]);
    makeTaskForAnalytics($this->orgA, $this->projectA, $this->deptA, ['status' => 'pending', 'assignee_id' => $this->management->id]);

    $response = $this->actingAs($this->owner)->get('/analytics');

    $workload = $response->viewData('staffWorkload')->firstWhere('label', 'Staff Member');
    expect($workload['count'])->toBe(2);

    $managementInWorkload = $response->viewData('staffWorkload')->firstWhere('label', $this->management->name);
    expect($managementInWorkload)->toBeNull();
});

test('every chart data-chart-labels/values attribute is valid, parseable JSON even when a label contains a double quote', function () {
    $quotedOrg = Organization::create(['name' => 'Bob\'s "Best" Co', 'slug' => 'bobs-best-co', 'accent_color' => '#000000']);
    $quotedDept = Department::create(['organization_id' => $quotedOrg->id, 'name' => 'Ops', 'color' => '#000000']);
    $quotedProject = Project::create(['organization_id' => $quotedOrg->id, 'name' => 'P', 'description' => 'd']);
    makeTaskForAnalytics($quotedOrg, $quotedProject, $quotedDept, ['status' => 'completed']);

    $response = $this->actingAs($this->owner)->get('/analytics');

    $response->assertOk();
    $count = preg_match_all("/data-chart-labels='(.*?)'/s", $response->getContent(), $matches);
    expect($count)->toBeGreaterThan(0);

    foreach ($matches[1] as $attribute) {
        json_decode($attribute, true);
        expect(json_last_error())->toBe(JSON_ERROR_NONE);
    }

    $completionLabels = json_decode($matches[1][0], true);
    expect($completionLabels)->toContain('Bob\'s "Best" Co');
});
