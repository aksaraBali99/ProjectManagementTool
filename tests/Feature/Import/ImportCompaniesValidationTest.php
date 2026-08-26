<?php

use App\Models\Organization;
use App\Services\Import\ImportIdCodec;

beforeEach(function () {
    $this->owner = createOwner();
});

test('a blank ID Companies row is marked insert', function () {
    $batch = runImportValidation([
        'Companies' => [['name' => 'New Co']],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Companies')->firstOrFail();

    expect($row->resolved_action->value)->toBe('insert');
    expect($row->validation_status->value)->toBe('valid');
});

test('a Companies row with a valid ID is matched and marked update', function () {
    $organization = Organization::create(['name' => 'Existing Co', 'slug' => 'existing-co', 'accent_color' => '#1D9E75']);

    $batch = runImportValidation([
        'Companies' => [[
            'id' => ImportIdCodec::encode(ImportIdCodec::COMPANY_PREFIX, $organization->id),
            'name' => 'Existing Co Renamed',
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Companies')->firstOrFail();

    expect($row->resolved_action->value)->toBe('update');
    expect($row->validation_status->value)->toBe('valid');
});

test('a Companies row with an unrecognized ID is a hard error', function () {
    $batch = runImportValidation([
        'Companies' => [[
            'id' => 'CO-9999',
            'name' => 'Ghost Co',
        ]],
    ], $this->owner);

    $row = $batch->importRows()->where('sheet_name', 'Companies')->firstOrFail();

    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('does not match any existing company');
});
