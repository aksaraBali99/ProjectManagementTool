<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;

beforeEach(function () {
    $this->owner = createOwner();

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->deptA = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->projectA = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);
});

function validTaskRow(array $overrides = []): array
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

test('an exact (Project, Title) match updates the existing task rather than inserting', function () {
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Original title',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $batch = runImportValidation([
        'Tasks' => [validTaskRow(['title' => 'Original title', 'status' => 'Active'])],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Tasks')->firstOrFail();

    expect($row->resolved_action->value)->toBe('update');
    expect($row->validation_status->value)->toBe('valid');
});

test('a row with a title that doesn\'t exactly match an existing task inserts a new one instead of updating (title is effectively immutable via import)', function () {
    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Original title',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $batch = runImportValidation([
        'Tasks' => [validTaskRow(['title' => 'Original title, edited'])],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Tasks')->firstOrFail();

    expect($row->resolved_action->value)->toBe('insert');
    expect(Task::where('title', 'Original title')->exists())->toBeTrue();
});

test('a duplicate Task Ref within the file is a hard error on the later row', function () {
    $batch = runImportValidation([
        'Tasks' => [
            validTaskRow(['task_ref' => '1', 'title' => 'Task One']),
            validTaskRow(['task_ref' => '1', 'title' => 'Task Two']),
        ],
    ], $this->owner);

    $rows = $batch->importRows()->where('sheet_name', 'Tasks')->orderBy('row_number')->get();

    expect($rows[0]->validation_status->value)->toBe('valid');
    expect($rows[1]->validation_status->value)->toBe('error');
    expect($rows[1]->validation_message)->toContain('Duplicate');
});

test('the same task title is valid in two different projects (confirms project-scoped matching)', function () {
    $projectB = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project B', 'description' => 'd']);

    $batch = runImportValidation([
        'Tasks' => [
            validTaskRow(['task_ref' => '1', 'project_name' => 'Project A', 'title' => 'Kickoff meeting']),
            validTaskRow(['task_ref' => '2', 'project_name' => 'Project B', 'title' => 'Kickoff meeting']),
        ],
    ], $this->owner);

    $rows = $batch->importRows()->where('sheet_name', 'Tasks')->orderBy('row_number')->get();

    expect($rows[0]->validation_status->value)->toBe('valid');
    expect($rows[0]->resolved_action->value)->toBe('insert');
    expect($rows[1]->validation_status->value)->toBe('valid');
    expect($rows[1]->resolved_action->value)->toBe('insert');
});

test('Task Assignee accepts any company member regardless of role', function () {
    $batch = runImportValidation([
        'Users' => [[
            'username' => 'client.acme',
            'name' => 'Acme Contact',
            'type' => 'Client',
            'email' => 'contact@acme.com',
            'phone' => '+6281234567890',
        ]],
        'Company Roles' => [
            ['username' => 'client.acme', 'company' => 'Org A', 'role' => 'Client'],
        ],
        'Tasks' => [validTaskRow(['assignee_username' => 'client.acme'])],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Tasks')->firstOrFail();

    expect($row->validation_status->value)->toBe('valid');
});

test('an Assignee not belonging to the task\'s company is a hard error', function () {
    $orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);

    $batch = runImportValidation([
        'Users' => [[
            'username' => 'jdoe',
            'name' => 'Jane Doe',
            'type' => 'Employee',
            'email' => 'jane@example.com',
            'phone' => '+6281234567890',
        ]],
        'Company Roles' => [
            ['username' => 'jdoe', 'company' => 'Org B', 'role' => 'Staff'],
        ],
        'Tasks' => [validTaskRow(['assignee_username' => 'jdoe'])],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Tasks')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('does not belong to');
});
