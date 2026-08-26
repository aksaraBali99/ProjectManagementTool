<?php

use App\Enums\ImportBatchStatus;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Services\Import\ImportCommitService;
use App\Services\Import\ImportIdCodec;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->owner = createOwner();

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
});

test('commit is blocked via HTTP while any hard errors remain', function () {
    $batch = runImportValidation([
        'Departments' => [['name' => 'Marketing', 'company' => 'Ghost Co']],
    ], $this->owner);

    $this->actingAs($this->owner)->post("/import/{$batch->id}/commit")->assertStatus(422);
    expect($batch->fresh()->status)->toBe(ImportBatchStatus::PendingReview);
});

test('commit via HTTP requires the warnings-acknowledgment flag when warnings exist', function () {
    User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane.doe@example.com']);

    $batch = runImportValidation([
        'Users' => [[
            'username' => 'jdoe2',
            'name' => 'Jane Doe',
            'type' => 'Employee',
            'email' => 'jane.doe2@example.com',
            'phone' => '+6281234567891',
        ]],
        'Company Roles' => [['username' => 'jdoe2', 'company' => 'Org A', 'role' => 'Staff']],
    ], $this->owner);

    $this->actingAs($this->owner)->post("/import/{$batch->id}/commit")->assertStatus(422);
    expect($batch->fresh()->status)->toBe(ImportBatchStatus::PendingReview);

    $this->actingAs($this->owner)->post("/import/{$batch->id}/commit", ['acknowledge_warnings' => '1'])->assertOk();
    expect($batch->fresh()->status)->toBe(ImportBatchStatus::Committed);
});

test('a full successful commit writes every sheet in dependency order and marks the batch committed', function () {
    $batch = runImportValidation([
        'Companies' => [['name' => 'New Co']],
        'Departments' => [['name' => 'Marketing', 'company' => 'New Co']],
        'Users' => [[
            'username' => 'jdoe',
            'name' => 'Jane Doe',
            'type' => 'Employee',
            'email' => 'jane@example.com',
            'phone' => '+6281234567890',
        ]],
        'Company Roles' => [['username' => 'jdoe', 'company' => 'New Co', 'role' => 'Staff']],
        'Projects' => [['name' => 'Website Refresh', 'description' => 'd', 'company' => 'New Co']],
        'Tasks' => [[
            'task_ref' => '1',
            'project_name' => 'Website Refresh',
            'company' => 'New Co',
            'department' => 'Marketing',
            'title' => 'Draft homepage copy',
            'priority' => 'Medium',
            'status' => 'Pending',
            'assignee_username' => 'jdoe',
            'start_date' => '01/09/2026',
        ]],
        'Subtasks' => [['task_ref' => '1', 'title' => 'Research competitors']],
        'Task Documents' => [['task_ref' => '1', 'name' => 'Brief', 'link' => 'https://example.com/brief']],
        'Task Comments' => [['task_ref' => '1', 'body' => 'Kicking this off.']],
    ], $this->owner);

    $summary = app(ImportCommitService::class)->commit($batch, $this->owner);

    expect($batch->fresh()->status)->toBe(ImportBatchStatus::Committed);
    expect($batch->fresh()->uuid)->not->toBeNull();

    $this->assertDatabaseHas('organizations', ['name' => 'New Co']);
    $organization = Organization::where('name', 'New Co')->firstOrFail();

    $this->assertDatabaseHas('departments', ['name' => 'Marketing', 'organization_id' => $organization->id]);

    $user = User::where('username', 'jdoe')->firstOrFail();
    $this->assertDatabaseHas('org_members', [
        'organization_id' => $organization->id,
        'user_id' => $user->id,
        'role_id' => Role::where('slug', 'staff')->value('id'),
    ]);

    $project = Project::where('name', 'Website Refresh')->firstOrFail();
    expect($project->organization_id)->toBe($organization->id);

    $task = Task::withoutGlobalScopes()->where('title', 'Draft homepage copy')->firstOrFail();
    expect($task->project_id)->toBe($project->id);
    expect($task->department_id)->toBe(Department::where('name', 'Marketing')->value('id'));
    expect($task->assignee_id)->toBe($user->id);
    expect($task->start_date->format('Y-m-d'))->toBe('2026-09-01');

    $this->assertDatabaseHas('subtasks', ['task_id' => $task->id, 'title' => 'Research competitors']);
    $this->assertDatabaseHas('documents', ['name' => 'Brief', 'link' => 'https://example.com/brief']);
    $this->assertDatabaseHas('comments', ['task_id' => $task->id, 'body' => 'Kicking this off.']);

    expect($summary->countsBySheet['Companies']['created'])->toBe(1);
    expect($summary->countsBySheet['Tasks']['created'])->toBe(1);
});

test('a Task\'s Project Name resolves to a Project inserted earlier in the same commit, not to raw_data text', function () {
    $batch = runImportValidation([
        'Projects' => [['name' => 'Brand New Project', 'description' => 'd', 'company' => 'Org A']],
        'Tasks' => [[
            'task_ref' => '1',
            'project_name' => 'Brand New Project',
            'company' => 'Org A',
            'department' => 'Marketing',
            'title' => 'First task',
            'priority' => 'Medium',
            'status' => 'Pending',
        ]],
    ], $this->owner);

    // The Department doesn't exist yet either — create it directly so
    // the Task row (which only needs Department to already exist,
    // unlike Project which is created in the SAME commit) resolves.
    Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000']);

    // Re-run validation now that the department exists.
    $batch = runImportValidation([
        'Projects' => [['name' => 'Brand New Project', 'description' => 'd', 'company' => 'Org A']],
        'Tasks' => [[
            'task_ref' => '1',
            'project_name' => 'Brand New Project',
            'company' => 'Org A',
            'department' => 'Marketing',
            'title' => 'First task',
            'priority' => 'Medium',
            'status' => 'Pending',
        ]],
    ], $this->owner);

    app(ImportCommitService::class)->commit($batch, $this->owner);

    $project = Project::where('name', 'Brand New Project')->firstOrFail();
    $task = Task::withoutGlobalScopes()->where('title', 'First task')->firstOrFail();

    expect($task->project_id)->toBe($project->id);
});

test('Company Roles commit only touches (username,company) pairs present in the file, leaving other pairs untouched', function () {
    $orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);
    $user = User::factory()->create(['username' => 'jdoe']);
    OrgMember::create(['organization_id' => $orgB->id, 'user_id' => $user->id, 'role_id' => Role::where('slug', 'management')->value('id')]);

    $batch = runImportValidation([
        'Company Roles' => [['username' => 'jdoe', 'company' => 'Org A', 'role' => 'Staff']],
    ], $this->owner);

    app(ImportCommitService::class)->commit($batch, $this->owner);

    $this->assertDatabaseHas('org_members', [
        'organization_id' => $this->orgA->id,
        'user_id' => $user->id,
        'role_id' => Role::where('slug', 'staff')->value('id'),
    ]);
    $this->assertDatabaseHas('org_members', [
        'organization_id' => $orgB->id,
        'user_id' => $user->id,
        'role_id' => Role::where('slug', 'management')->value('id'),
    ]);
});

test('a later-stage failure marks the batch partially_committed and records completed stages', function () {
    $batch = runImportValidation([
        'Companies' => [['name' => 'New Co']],
    ], $this->owner);

    // Force the Departments stage (which runs after Companies) to blow
    // up by injecting a Departments row referencing a company that
    // doesn't actually exist by the time commit runs — resolveOrganizationId()
    // would return null and the subsequent Department::create() call
    // fails on the NOT NULL organization_id column.
    $batch->importRows()->create([
        'sheet_name' => 'Departments',
        'row_number' => 3,
        'raw_data' => ['id' => null, 'name' => 'Ghost Dept', 'company' => 'Totally Nonexistent Co'],
        'resolved_action' => 'insert',
        'validation_status' => 'valid',
    ]);

    expect(fn () => app(ImportCommitService::class)->commit($batch, $this->owner))->toThrow(TypeError::class);

    $batch->refresh();
    expect($batch->status)->toBe(ImportBatchStatus::PartiallyCommitted);
    expect($batch->completed_stages)->toBe(['companies']);
    $this->assertDatabaseHas('organizations', ['name' => 'New Co']);
});

test('a blocked-but-valid Subtask/Document/Comment is skipped at commit, not attached to a nonexistent task', function () {
    // The HTTP commit route would normally refuse this batch (the Tasks
    // row's own error trips the "Cannot commit while errors remain"
    // guard) — calling the service directly bypasses that, so this
    // exercises commitEligibleRows()'s own defense: it excludes
    // resolved_action='blocked' regardless of validation_status, which is
    // what stops these rows reaching Task::find(null)->subtasks()->create().
    $batch = runImportValidation([
        'Tasks' => [[
            // missing required Title -> this Task row fails its own validation
            'task_ref' => '1',
            'project_name' => 'Project A',
            'company' => 'Org A',
            'department' => 'Marketing',
            'priority' => 'Medium',
            'status' => 'Pending',
        ]],
        'Subtasks' => [['task_ref' => '1', 'title' => 'A subtask']],
        'Task Documents' => [['task_ref' => '1', 'name' => 'Doc', 'link' => 'https://example.com']],
        'Task Comments' => [['task_ref' => '1', 'body' => 'A comment']],
    ], $this->owner);

    $subtaskRow = $batch->importRows()->where('sheet_name', 'Subtasks')->firstOrFail();
    expect($subtaskRow->resolved_action->value)->toBe('blocked');
    expect($subtaskRow->validation_status->value)->toBe('valid');

    app(ImportCommitService::class)->commit($batch, $this->owner);

    $this->assertDatabaseCount('subtasks', 0);
    $this->assertDatabaseCount('documents', 0);
    $this->assertDatabaseCount('comments', 0);
});

test('committing a Projects row with no actual change is counted unchanged and writes no audit_log entry', function () {
    $project = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Website Refresh',
        'description' => 'Redesign the homepage.',
    ]);

    $batch = runImportValidation([
        'Projects' => [[
            'id' => ImportIdCodec::encode(ImportIdCodec::PROJECT_PREFIX, $project->id),
            'name' => 'Website Refresh',
            'description' => 'Redesign the homepage.',
            'company' => 'Org A',
        ]],
    ], $this->owner);

    $summary = app(ImportCommitService::class)->commit($batch, $this->owner);

    expect($summary->countsBySheet['Projects'])->toBe(['unchanged' => 1]);
    expect(AuditLog::where('entity_type', 'project')->where('entity_id', $project->id)->exists())->toBeFalse();
});

test('every created record writes an audit_log row tagged source=import with the batch uuid', function () {
    $batch = runImportValidation([
        'Companies' => [['name' => 'New Co']],
        'Tasks' => [], // ensure Tasks/Subtasks/etc stages still run cleanly with nothing to do
    ], $this->owner);

    $summary = app(ImportCommitService::class)->commit($batch, $this->owner);
    $uuid = $batch->fresh()->uuid;

    $organization = Organization::where('name', 'New Co')->firstOrFail();
    $entry = AuditLog::where('entity_type', 'organization')->where('entity_id', $organization->id)->firstOrFail();

    expect($entry->import_batch_id)->toBe($uuid);
    expect($entry->changes['source'])->toBe('import');
});

test('observer-produced audit rows for Tasks are also tagged with the import batch and suppress notifications', function () {
    $department = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000']);
    $project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $batch = runImportValidation([
        'Tasks' => [[
            'task_ref' => '1',
            'project_name' => 'Project A',
            'company' => 'Org A',
            'department' => 'Marketing',
            'title' => 'Observer-tagged task',
            'priority' => 'Medium',
            'status' => 'Pending',
        ]],
    ], $this->owner);

    Notification::fake();

    $this->actingAs($this->owner);
    app(ImportCommitService::class)->commit($batch, $this->owner);
    $uuid = $batch->fresh()->uuid;

    $task = Task::withoutGlobalScopes()->where('title', 'Observer-tagged task')->firstOrFail();
    $entry = AuditLog::where('entity_type', 'task')->where('entity_id', $task->id)->where('action', 'task.created')->firstOrFail();

    expect($entry->import_batch_id)->toBe($uuid);
    expect($entry->changes['source'] ?? null)->toBe('import');

    Notification::assertNothingSent();
});

test('Task Assignee outside project_staff is auto-attached to the project on import', function () {
    $department = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000']);
    $project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $batch = runImportValidation([
        'Users' => [[
            'username' => 'client.acme',
            'name' => 'Acme Contact',
            'type' => 'Client',
            'email' => 'contact@acme.com',
            'phone' => '+6281234567890',
        ]],
        'Company Roles' => [['username' => 'client.acme', 'company' => 'Org A', 'role' => 'Client']],
        'Tasks' => [[
            'task_ref' => '1',
            'project_name' => 'Project A',
            'company' => 'Org A',
            'department' => 'Marketing',
            'title' => 'Review task',
            'priority' => 'Medium',
            'status' => 'Pending',
            'assignee_username' => 'client.acme',
        ]],
    ], $this->owner);

    app(ImportCommitService::class)->commit($batch, $this->owner);

    $clientUser = User::where('username', 'client.acme')->firstOrFail();
    expect($project->clients()->where('users.id', $clientUser->id)->exists() || $project->staff()->where('users.id', $clientUser->id)->exists())->toBeTrue();
});
