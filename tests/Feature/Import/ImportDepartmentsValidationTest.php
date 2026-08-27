<?php

use App\Models\Department;
use App\Models\Organization;
use App\Services\Import\ImportIdCodec;

beforeEach(function () {
    $this->owner = createOwner();
});

test('a Department row resolves an existing company by name and is marked insert', function () {
    Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);

    $batch = runImportValidation([
        'Departments' => [['name' => 'Marketing', 'company' => 'Org A']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Departments')->firstOrFail();

    expect($row->resolved_action->value)->toBe('insert');
    expect($row->validation_status->value)->toBe('valid');
});

test('a Department row can reference a company being created in the same file', function () {
    $batch = runImportValidation([
        'Companies' => [['name' => 'Brand New Co']],
        'Departments' => [['name' => 'Sales', 'company' => 'Brand New Co']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Departments')->firstOrFail();

    expect($row->validation_status->value)->toBe('valid');
});

test('a Department row referencing a nonexistent company is a hard error', function () {
    $batch = runImportValidation([
        'Departments' => [['name' => 'Marketing', 'company' => 'Ghost Co']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Departments')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('does not exist');
});

test('a Department row is blocked when its referenced Company row failed validation', function () {
    $batch = runImportValidation([
        'Companies' => [['id' => 'CO-9999', 'name' => 'Ghost Co']],
        'Departments' => [['name' => 'Marketing', 'company' => 'Ghost Co']],
    ], $this->owner);

    $companyRow = $batch->importRows()->where('sheet_name', 'Companies')->firstOrFail();
    $deptRow = $batch->importRows()->where('sheet_name', 'Departments')->firstOrFail();

    expect($companyRow->validation_status->value)->toBe('error');
    expect($deptRow->resolved_action->value)->toBe('blocked');
    expect($deptRow->validation_message)->toContain('Blocked');
});

test('a Department row with a valid ID matching an existing department is marked update', function () {
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $department = Department::create(['organization_id' => $organization->id, 'name' => 'Marketing', 'color' => '#000000']);

    $batch = runImportValidation([
        'Departments' => [[
            'id' => ImportIdCodec::encode(ImportIdCodec::DEPARTMENT_PREFIX, $department->id),
            'name' => 'Marketing Renamed',
            'company' => 'Org A',
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Departments')->firstOrFail();

    expect($row->resolved_action->value)->toBe('update');
});

test('a Department row with a valid ID but no actual change is marked no_change', function () {
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $department = Department::create(['organization_id' => $organization->id, 'name' => 'Marketing', 'color' => '#000000']);

    $batch = runImportValidation([
        'Departments' => [[
            'id' => ImportIdCodec::encode(ImportIdCodec::DEPARTMENT_PREFIX, $department->id),
            'name' => 'Marketing',
            'company' => 'Org A',
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Departments')->firstOrFail();

    expect($row->resolved_action->value)->toBe('no_change');
});
