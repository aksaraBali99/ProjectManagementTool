<?php

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::where('slug', 'owner')->first()->id);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);
});

test('a user cannot hold Client role and any other role across companies in the same file', function () {
    $batch = runImportValidation([
        'Users' => [[
            'username' => 'jdoe',
            'name' => 'Jane Doe',
            'type' => 'Client',
            'email' => 'jane@example.com',
            'phone' => '+6281234567890',
        ]],
        'Company Roles' => [
            ['username' => 'jdoe', 'company' => 'Org A', 'role' => 'Client'],
            ['username' => 'jdoe', 'company' => 'Org B', 'role' => 'Staff'],
        ],
    ], $this->owner);

    $userRow = $batch->importRows()->where('sheet_name', 'Users')->firstOrFail();

    expect($userRow->validation_status->value)->toBe('error');
    expect($userRow->validation_message)->toContain('cannot hold another role');
});

test('a duplicate username+company pair within the file is a hard error', function () {
    $batch = runImportValidation([
        'Users' => [[
            'username' => 'jdoe',
            'name' => 'Jane Doe',
            'type' => 'Employee',
            'email' => 'jane@example.com',
            'phone' => '+6281234567890',
        ]],
        'Company Roles' => [
            ['username' => 'jdoe', 'company' => 'Org A', 'role' => 'Staff'],
            ['username' => 'jdoe', 'company' => 'Org A', 'role' => 'Management'],
        ],
    ], $this->owner);

    $rows = $batch->importRows()->where('sheet_name', 'Company Roles')->orderBy('row_number')->get();

    expect($rows[0]->validation_status->value)->toBe('valid');
    expect($rows[1]->validation_status->value)->toBe('error');
    expect($rows[1]->validation_message)->toContain('Duplicate');
});

test('Company Roles rows are always marked sync, never insert/update', function () {
    $batch = runImportValidation([
        'Users' => [[
            'username' => 'jdoe',
            'name' => 'Jane Doe',
            'type' => 'Employee',
            'email' => 'jane@example.com',
            'phone' => '+6281234567890',
        ]],
        'Company Roles' => [
            ['username' => 'jdoe', 'company' => 'Org A', 'role' => 'Staff'],
        ],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Company Roles')->firstOrFail();

    expect($row->resolved_action->value)->toBe('sync');
});

test('an invalid Role value is a hard error', function () {
    User::factory()->create(['username' => 'jdoe']);

    $batch = runImportValidation([
        'Company Roles' => [
            ['username' => 'jdoe', 'company' => 'Org A', 'role' => 'Manager'],
        ],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Company Roles')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
});
