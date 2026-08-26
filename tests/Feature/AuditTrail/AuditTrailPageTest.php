<?php

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->owner = createOwner();

    $this->superAdmin = User::factory()->create();
    $this->superAdmin->roles()->attach(Role::where('slug', 'super_admin')->first()->id);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);

    $this->deptA = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);

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

test('a non-owner/non-super-admin cannot access the Audit Trail page', function () {
    $this->actingAs($this->management)->get('/audit-trail')->assertForbidden();
});

test('an owner and a super_admin can access the Audit Trail page', function () {
    $this->actingAs($this->owner)->get('/audit-trail')->assertOk();
    $this->actingAs($this->superAdmin)->get('/audit-trail')->assertOk();
});

test('the Audit Trail page shows a readable description of what changed, not raw JSON', function () {
    // assignee_id/due_date set explicitly to null (not omitted) — Eloquent
    // treats a key entirely absent from the original attributes as
    // "changed" the first time it's set, even to the same null value,
    // which would otherwise spuriously trigger the reassigned action
    // instead of the status_changed one this test is checking.
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => null,
        'due_date' => null,
        'title' => 'Readable rendering task',
        'description' => 'd',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $this->actingAs($this->management)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Readable rendering task',
        'description' => 'd',
        'priority' => 'medium',
        'status' => 'in_progress',
    ]);

    $response = $this->actingAs($this->owner)->get('/audit-trail');

    $response->assertOk();
    $response->assertSee('Status changed from Pending to Active');
    $response->assertDontSee('{&quot;status&quot;', false);
    $response->assertDontSee('&quot;old&quot;', false);
});

test('the Audit Trail page filters by entity type, company, action, user, and date range', function () {
    $otherOrg = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);

    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Filter target task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $subtask = $task->subtasks()->create(['title' => 'Filter target subtask']);
    $this->actingAs($this->management)->patch("/subtasks/{$subtask->id}/toggle");

    // A second, unrelated entry that every filter below should exclude.
    $otherTask = Task::create([
        'organization_id' => $otherOrg->id,
        'project_id' => Project::create(['organization_id' => $otherOrg->id, 'name' => 'Other project', 'description' => 'd'])->id,
        'department_id' => Department::create(['organization_id' => $otherOrg->id, 'name' => 'Ops', 'color' => '#000000'])->id,
        'title' => 'Unrelated task',
        'priority' => 'low',
        'status' => 'pending',
    ]);
    $this->actingAs($this->owner)->post('/tasks', [
        'project_id' => $otherTask->project_id,
        'department_id' => $otherTask->department_id,
        'title' => 'Owner-created task',
        'priority' => 'low',
        'status' => 'pending',
    ]);

    $byOrg = $this->actingAs($this->owner)->get('/audit-trail?organization_id='.$this->orgA->id);
    $byOrg->assertSee('Filter target subtask');
    $byOrg->assertDontSee('Owner-created task');
    $byOrg->assertDontSee('Unrelated task');

    $byEntity = $this->actingAs($this->owner)->get('/audit-trail?entity_type=subtask');
    $byEntity->assertSee('Filter target subtask');
    $byEntity->assertDontSee('Filter target task');

    $byAction = $this->actingAs($this->owner)->get('/audit-trail?action=subtask.status_changed');
    $byAction->assertSee('Filter target subtask');
    $byAction->assertDontSee('Owner-created task');

    $byUser = $this->actingAs($this->owner)->get('/audit-trail?user_id='.$this->management->id);
    $byUser->assertSee('Filter target subtask');
    $byUser->assertDontSee('Owner-created task');

    $today = now()->toDateString();
    $byDate = $this->actingAs($this->owner)->get('/audit-trail?date_from='.$today.'&date_to='.$today);
    $byDate->assertSee('Filter target subtask');

    $yesterday = now()->subDay()->toDateString();
    $futureless = $this->actingAs($this->owner)->get('/audit-trail?date_to='.$yesterday);
    $futureless->assertDontSee('Filter target subtask');
});

test('audit log entries cannot be edited or deleted via any route', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Immutable entry task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $this->actingAs($this->management)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Immutable entry task',
        'priority' => 'high',
        'status' => 'pending',
    ]);
    $log = AuditLog::firstOrFail();

    $this->actingAs($this->owner)->put("/audit-trail/{$log->id}", ['action' => 'tampered'])->assertNotFound();
    $this->actingAs($this->owner)->patch("/audit-trail/{$log->id}", ['action' => 'tampered'])->assertNotFound();
    $this->actingAs($this->owner)->delete("/audit-trail/{$log->id}")->assertNotFound();
    $this->actingAs($this->owner)->delete('/audit-log/'.$log->id)->assertNotFound();

    expect($log->fresh()->action)->not->toBe('tampered');
    $this->assertDatabaseHas('audit_log', ['id' => $log->id]);
});

test('the audit trail list renders with mobile card-stacking markup alongside the desktop table', function () {
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Responsive markup task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->owner)->get('/audit-trail');

    $response->assertOk();
    $response->assertSee('hidden bg-gray-50 md:table-header-group', false);
    $response->assertSee('md:table-cell', false);
});
