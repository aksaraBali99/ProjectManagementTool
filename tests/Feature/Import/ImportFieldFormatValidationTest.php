<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\Project;

beforeEach(function () {
    $this->owner = createOwner();

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
});

test('a malformed email on the Users tab is a hard error, matching the in-app Add User form', function () {
    $batch = runImportValidation([
        'Users' => [[
            'username' => 'jdoe',
            'name' => 'Jane Doe',
            'type' => 'Employee',
            'email' => 'not-an-email',
            'phone' => '+6281234567890',
        ]],
        'Company Roles' => [['username' => 'jdoe', 'company' => 'Org A', 'role' => 'Staff']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Users')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('valid email');
});

test('a weak explicit password on the Users tab is a hard error, matching the in-app password rules', function () {
    $batch = runImportValidation([
        'Users' => [[
            'username' => 'jdoe',
            'name' => 'Jane Doe',
            'type' => 'Employee',
            'email' => 'jane@example.com',
            'phone' => '+6281234567890',
            'password' => 'weakpass',
        ]],
        'Company Roles' => [['username' => 'jdoe', 'company' => 'Org A', 'role' => 'Staff']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Users')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
});

test('a strong explicit password on the Users tab passes', function () {
    $batch = runImportValidation([
        'Users' => [[
            'username' => 'jdoe',
            'name' => 'Jane Doe',
            'type' => 'Employee',
            'email' => 'jane@example.com',
            'phone' => '+6281234567890',
            'password' => 'Str0ng!Passw0rd',
        ]],
        'Company Roles' => [['username' => 'jdoe', 'company' => 'Org A', 'role' => 'Staff']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Users')->firstOrFail();

    expect($row->validation_status->value)->toBe('valid');
});

test('an invalid phone number on the Users tab is a hard error', function () {
    $batch = runImportValidation([
        'Users' => [[
            'username' => 'jdoe',
            'name' => 'Jane Doe',
            'type' => 'Employee',
            'email' => 'jane@example.com',
            'phone' => '123',
        ]],
        'Company Roles' => [['username' => 'jdoe', 'company' => 'Org A', 'role' => 'Staff']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Users')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
});

test('a Username or Name over 255 characters on the Users tab is a hard error', function () {
    $batch = runImportValidation([
        'Users' => [[
            'username' => 'jdoe',
            'name' => str_repeat('a', 256),
            'type' => 'Employee',
            'email' => 'jane@example.com',
            'phone' => '+6281234567890',
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Users')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('255 characters');
});

test('a Company Name over 255 characters is a hard error', function () {
    $batch = runImportValidation([
        'Companies' => [['name' => str_repeat('a', 256)]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Companies')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('255 characters');
});

test('a Department Name over 255 characters is a hard error', function () {
    $batch = runImportValidation([
        'Departments' => [['name' => str_repeat('a', 256), 'company' => 'Org A']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Departments')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('255 characters');
});

test('a Project Name over 255 characters is a hard error', function () {
    $batch = runImportValidation([
        'Projects' => [['name' => str_repeat('a', 256), 'description' => 'd', 'company' => 'Org A']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Projects')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('255 characters');
});

test('a Task Title over 255 characters is a hard error', function () {
    Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000']);
    Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $batch = runImportValidation([
        'Tasks' => [[
            'task_ref' => '1',
            'project_name' => 'Project A',
            'company' => 'Org A',
            'department' => 'Marketing',
            'title' => str_repeat('a', 256),
            'priority' => 'Medium',
            'status' => 'Pending',
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Tasks')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('255 characters');
});

test('a malformed Start Date or Due Date on the Tasks tab is a hard error', function () {
    Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000']);
    Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $batch = runImportValidation([
        'Tasks' => [[
            'task_ref' => '1',
            'project_name' => 'Project A',
            'company' => 'Org A',
            'department' => 'Marketing',
            'title' => 'A task',
            'priority' => 'Medium',
            'status' => 'Pending',
            'due_date' => '31/02/2026', // February has no 31st
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Tasks')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('valid date');
});

test('a malformed Task Documents Link is a hard error', function () {
    Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000']);
    Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $batch = runImportValidation([
        'Tasks' => [[
            'task_ref' => '1',
            'project_name' => 'Project A',
            'company' => 'Org A',
            'department' => 'Marketing',
            'title' => 'A task',
            'priority' => 'Medium',
            'status' => 'Pending',
        ]],
        'Task Documents' => [['task_ref' => '1', 'name' => 'Doc', 'link' => 'not a url']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Task Documents')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('valid URL');
});
