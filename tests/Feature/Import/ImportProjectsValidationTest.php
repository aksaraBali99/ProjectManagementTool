<?php

use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Services\Import\ImportIdCodec;

beforeEach(function () {
    $this->owner = createOwner();

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);
});

test('a Client Username must hold the Client role in the project\'s own company, not just any company', function () {
    // client.acme is a Client only in Org B — referencing them from an
    // Org A project should fail even though they're a valid Client
    // somewhere.
    $batch = runImportValidation([
        'Users' => [[
            'username' => 'client.acme',
            'name' => 'Acme Contact',
            'type' => 'Client',
            'email' => 'contact@acme.com',
            'phone' => '+6281234567890',
        ]],
        'Company Roles' => [
            ['username' => 'client.acme', 'company' => 'Org B', 'role' => 'Client'],
        ],
        'Projects' => [[
            'name' => 'Website Refresh',
            'description' => 'd',
            'company' => 'Org A',
            'client_username' => 'client.acme',
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Projects')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('does not hold the Client role');
});

test('a Client Username holding Client role for the project\'s own company passes', function () {
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
        'Projects' => [[
            'name' => 'Website Refresh',
            'description' => 'd',
            'company' => 'Org A',
            'client_username' => 'client.acme',
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Projects')->firstOrFail();

    expect($row->validation_status->value)->toBe('valid');
});

test('Assigned Staff usernames must belong to the project\'s company', function () {
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
        'Projects' => [[
            'name' => 'Website Refresh',
            'description' => 'd',
            'company' => 'Org A',
            'staff_usernames' => 'jdoe',
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Projects')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('does not belong to');
});

test('a blank Client Username creates an internal project without error', function () {
    $batch = runImportValidation([
        'Projects' => [[
            'name' => 'Internal Tooling',
            'description' => 'd',
            'company' => 'Org A',
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Projects')->firstOrFail();

    expect($row->validation_status->value)->toBe('valid');
    expect($row->resolved_action->value)->toBe('insert');
});

test('a Projects row with a valid ID but no actual change is marked no_change', function () {
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

    $row = $batch->importRows()->where('sheet_name', 'Projects')->firstOrFail();

    expect($row->resolved_action->value)->toBe('no_change');
    expect($row->validation_status->value)->toBe('valid');
});

test('a Projects row with a valid ID and a changed description is marked update', function () {
    $project = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Website Refresh',
        'description' => 'Old description.',
    ]);

    $batch = runImportValidation([
        'Projects' => [[
            'id' => ImportIdCodec::encode(ImportIdCodec::PROJECT_PREFIX, $project->id),
            'name' => 'Website Refresh',
            'description' => 'New, updated description.',
            'company' => 'Org A',
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Projects')->firstOrFail();

    expect($row->resolved_action->value)->toBe('update');
});

test('a Projects row where only the staff list changed is marked update, not no_change', function () {
    $project = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Website Refresh',
        'description' => 'Redesign the homepage.',
    ]);

    $staff = User::factory()->create(['username' => 'jdoe']);
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->value('id'),
    ]);

    $batch = runImportValidation([
        'Projects' => [[
            'id' => ImportIdCodec::encode(ImportIdCodec::PROJECT_PREFIX, $project->id),
            'name' => 'Website Refresh',
            'description' => 'Redesign the homepage.',
            'company' => 'Org A',
            'staff_usernames' => 'jdoe',
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Projects')->firstOrFail();

    expect($row->resolved_action->value)->toBe('update');
});
