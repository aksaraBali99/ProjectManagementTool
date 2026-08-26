<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->owner = createOwner();

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);
    $this->deptA = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->projectA = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);
});

function validSubtaskTaskRow(array $overrides = []): array
{
    return array_merge([
        'task_ref' => '1',
        'project_name' => 'Project A',
        'company' => 'Org A',
        'department' => 'Marketing',
        'title' => 'Draft homepage copy',
        'priority' => 'Medium',
        'status' => 'Pending',
    ], $overrides);
}

test('a blank Subtask Assignee is valid (inherits the parent Task\'s assignee)', function () {
    $batch = runImportValidation([
        'Tasks' => [validSubtaskTaskRow()],
        'Subtasks' => [['task_ref' => '1', 'title' => 'Research competitors']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Subtasks')->firstOrFail();

    expect($row->validation_status->value)->toBe('valid');
});

test('a Subtask Assignee that matches no user in the file or database is a hard error', function () {
    $batch = runImportValidation([
        'Tasks' => [validSubtaskTaskRow()],
        'Subtasks' => [['task_ref' => '1', 'title' => 'Research competitors', 'assignee_username' => 'ghost.user']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Subtasks')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('is not a valid user');
});

test('a Subtask Assignee not belonging to the parent Task\'s company is a hard error', function () {
    $staff = User::factory()->create(['username' => 'jdoe']);
    OrgMember::create([
        'organization_id' => $this->orgB->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->value('id'),
    ]);

    $batch = runImportValidation([
        'Tasks' => [validSubtaskTaskRow()],
        'Subtasks' => [['task_ref' => '1', 'title' => 'Research competitors', 'assignee_username' => 'jdoe']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Subtasks')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('does not belong to');
});

test('a Subtask Assignee belonging to the parent Task\'s company is valid', function () {
    $staff = User::factory()->create(['username' => 'jdoe']);
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->value('id'),
    ]);

    $batch = runImportValidation([
        'Tasks' => [validSubtaskTaskRow()],
        'Subtasks' => [['task_ref' => '1', 'title' => 'Research competitors', 'assignee_username' => 'jdoe']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Subtasks')->firstOrFail();

    expect($row->validation_status->value)->toBe('valid');
});
