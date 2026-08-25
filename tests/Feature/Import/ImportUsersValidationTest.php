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
});

test('a Username match to one user and Email match to a different user is a hard error', function () {
    User::factory()->create(['username' => 'alice', 'email' => 'alice@example.com']);
    User::factory()->create(['username' => 'bob', 'email' => 'bob@example.com']);

    $batch = runImportValidation([
        'Users' => [[
            'username' => 'alice',
            'name' => 'Alice B',
            'type' => 'Employee',
            'email' => 'bob@example.com',
            'phone' => '+6281234567890',
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Users')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('Ambiguous');
});

test('Employee type with only a Client-role Company Roles row is a hard error', function () {
    $batch = runImportValidation([
        'Users' => [[
            'username' => 'jdoe',
            'name' => 'Jane Doe',
            'type' => 'Employee',
            'email' => 'jane@example.com',
            'phone' => '+6281234567890',
        ]],
        'Company Roles' => [[
            'username' => 'jdoe',
            'company' => 'Org A',
            'role' => 'Client',
        ]],
    ], $this->owner);

    $userRow = $batch->importRows()->where('sheet_name', 'Users')->firstOrFail();

    expect($userRow->validation_status->value)->toBe('error');
    expect($userRow->validation_message)->toContain('Client-only role set');
});

test('a user with zero Company Roles rows is a hard error', function () {
    $batch = runImportValidation([
        'Users' => [[
            'username' => 'jdoe',
            'name' => 'Jane Doe',
            'type' => 'Employee',
            'email' => 'jane@example.com',
            'phone' => '+6281234567890',
        ]],
    ], $this->owner);

    $userRow = $batch->importRows()->where('sheet_name', 'Users')->firstOrFail();

    expect($userRow->validation_status->value)->toBe('error');
    expect($userRow->validation_message)->toContain('no Company Roles row');
});

test('a Client-type user with zero Projects still passes', function () {
    $batch = runImportValidation([
        'Users' => [[
            'username' => 'client.acme',
            'name' => 'Acme Contact',
            'type' => 'Client',
            'email' => 'contact@acme.com',
            'phone' => '+6281234567890',
        ]],
        'Company Roles' => [[
            'username' => 'client.acme',
            'company' => 'Org A',
            'role' => 'Client',
        ]],
    ], $this->owner);

    $userRow = $batch->importRows()->where('sheet_name', 'Users')->firstOrFail();

    expect($userRow->validation_status->value)->toBe('valid');
});

test('blank Employee ID auto-generates a sequential id, uniquely across the same file', function () {
    $batch = runImportValidation([
        'Users' => [
            ['username' => 'user1', 'name' => 'User One', 'type' => 'Employee', 'email' => 'one@example.com', 'phone' => '+6281234567890'],
            ['username' => 'user2', 'name' => 'User Two', 'type' => 'Employee', 'email' => 'two@example.com', 'phone' => '+6281234567891'],
        ],
        'Company Roles' => [
            ['username' => 'user1', 'company' => 'Org A', 'role' => 'Staff'],
            ['username' => 'user2', 'company' => 'Org A', 'role' => 'Staff'],
        ],
    ], $this->owner);

    $rows = $batch->importRows()->where('sheet_name', 'Users')->get();

    expect($rows)->toHaveCount(2);
    $rows->each(fn ($row) => expect($row->validation_status->value)->toBe('valid'));
});
