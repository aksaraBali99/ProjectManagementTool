<?php

use App\Models\Department;
use App\Models\Organization;
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
});

test('a near-duplicate user name/email is a warning, not a hard error', function () {
    User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane.doe@example.com']);

    $batch = runImportValidation([
        'Users' => [[
            'username' => 'jdoe2',
            'name' => 'Jane Doe', // exact name match to an existing different user
            'type' => 'Employee',
            'email' => 'jane.doe2@example.com',
            'phone' => '+6281234567891',
        ]],
        'Company Roles' => [
            ['username' => 'jdoe2', 'company' => 'Org A', 'role' => 'Staff'],
        ],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Users')->firstOrFail();

    expect($row->validation_status->value)->toBe('warning');
    expect($row->resolved_action->value)->toBe('insert');
    expect($row->validation_message)->toContain('similar');
});

test('a near-duplicate task title within the same project is a warning, not a hard error', function () {
    $department = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $project->id,
        'department_id' => $department->id,
        'title' => 'Draft homepage copy',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $batch = runImportValidation([
        'Tasks' => [[
            'task_ref' => '1',
            'project_name' => 'Project A',
            'company' => 'Org A',
            'department' => 'Marketing',
            'title' => 'Draft homepage copy!',
            'priority' => 'Medium',
            'status' => 'Pending',
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Tasks')->firstOrFail();

    expect($row->validation_status->value)->toBe('warning');
    expect($row->resolved_action->value)->toBe('insert');
    expect($row->validation_message)->toContain('similar');
});

test('warnings do not block insertion of the row into import_rows as valid-enough-to-review', function () {
    User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane.doe@example.com']);

    $batch = runImportValidation([
        'Users' => [[
            'username' => 'jdoe2',
            'name' => 'Jane Doe',
            'type' => 'Employee',
            'email' => 'jane.doe2@example.com',
            'phone' => '+6281234567891',
        ]],
        'Company Roles' => [
            ['username' => 'jdoe2', 'company' => 'Org A', 'role' => 'Staff'],
        ],
    ], $this->owner);

    expect($batch->importRows()->where('validation_status', 'error')->count())->toBe(0);
});
