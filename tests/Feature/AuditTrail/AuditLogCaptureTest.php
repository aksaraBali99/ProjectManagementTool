<?php

use App\Models\AuditLog;
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

test('changing a task\'s status creates a correctly-populated audit_log entry with accurate before/after values', function () {
    // description is set to match exactly what the PUT below submits — the
    // controller always writes description on every update (even when
    // untouched by the user), so leaving it null here would produce a
    // spurious extra diff entry unrelated to what this test is checking.
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Status test task',
        'description' => 'Same description',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $this->actingAs($this->management)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Status test task',
        'description' => 'Same description',
        'priority' => 'medium',
        'status' => 'in_progress',
    ]);

    $log = AuditLog::where('entity_type', 'task')->where('entity_id', $task->id)->where('action', 'task.status_changed')->firstOrFail();
    expect($log->organization_id)->toBe($this->orgA->id)
        ->and($log->user_id)->toBe($this->management->id)
        ->and($log->changes)->toBe(['status' => ['old' => 'pending', 'new' => 'in_progress']]);
});

test('reassigning a task captures both the old and new assignee correctly', function () {
    $originalAssignee = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $originalAssignee->id, 'role_id' => Role::where('slug', 'staff')->first()->id]);
    $this->projectA->staff()->attach($originalAssignee->id);

    $newAssignee = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $newAssignee->id, 'role_id' => Role::where('slug', 'staff')->first()->id]);
    $this->projectA->staff()->attach($newAssignee->id);

    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $originalAssignee->id,
        'title' => 'Reassignment test task',
        'description' => 'Same description',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $this->actingAs($this->management)->put("/tasks/{$task->id}", [
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'assignee_id' => $newAssignee->id,
        'title' => 'Reassignment test task',
        'description' => 'Same description',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $log = AuditLog::where('entity_type', 'task')->where('entity_id', $task->id)->where('action', 'task.reassigned')->firstOrFail();
    expect($log->changes['assignee_id'])->toBe(['old' => $originalAssignee->id, 'new' => $newAssignee->id]);
});

test('creating a comment writes a comment.created audit_log entry', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task with a comment',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->post("/tasks/{$task->id}/comments", ['body' => 'Audited comment']);
    $response->assertCreated();

    $commentId = $response->json('comment.id');
    $log = AuditLog::where('entity_type', 'comment')->where('entity_id', $commentId)->firstOrFail();
    expect($log->action)->toBe('comment.created')
        ->and($log->organization_id)->toBe($this->orgA->id)
        ->and($log->user_id)->toBe($this->management->id)
        ->and($log->changes['body'])->toBe('Audited comment');
});

test('a task mutated with no authenticated user in scope (e.g. a direct fixture) writes no audit_log entry', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Unauthenticated creation',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    expect(AuditLog::where('entity_type', 'task')->where('entity_id', $task->id)->exists())->toBeFalse();
});
