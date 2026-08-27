<?php

use App\Models\Organization;
use App\Models\User;
use App\Services\Import\ImportIdCodec;

beforeEach(function () {
    $this->owner = createOwner();

    $this->staff = User::factory()->create();

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
});

test('a non-admin cannot view the review page', function () {
    $batch = runImportValidation(['Companies' => [['name' => 'New Co']]], $this->owner);

    $this->actingAs($this->staff)->get("/import/{$batch->id}/review")->assertForbidden();
});

test('the review page groups rows by sheet in the canonical tab order', function () {
    $batch = runImportValidation([
        'Companies' => [['name' => 'New Co']],
        'Departments' => [['name' => 'Marketing', 'company' => 'Org A']],
    ], $this->owner);

    $response = $this->actingAs($this->owner)->get("/import/{$batch->id}/review");

    $response->assertOk();
    $content = $response->getContent();

    $companiesPos = strpos($content, 'Companies (1)');
    $departmentsPos = strpos($content, 'Departments (1)');

    expect($companiesPos)->not->toBeFalse();
    expect($departmentsPos)->not->toBeFalse();
    expect($companiesPos)->toBeLessThan($departmentsPos);
});

test('a batch with only warnings shows zero errors and is commit-eligible', function () {
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

    $response = $this->actingAs($this->owner)->get("/import/{$batch->id}/review");

    $response->assertOk();
    $response->assertSee('0 errors');
    $response->assertSee('1 warning');
});

test('the review page offers a way back to re-upload a corrected file', function () {
    $batch = runImportValidation(['Companies' => [['name' => 'New Co']]], $this->owner);

    $response = $this->actingAs($this->owner)->get("/import/{$batch->id}/review");

    $response->assertOk();
    $response->assertSee(route('import.index'), false);
    $response->assertSee('Re-upload a different file');
});

test('the review page hides No Change rows, showing only actionable ones', function () {
    $organization = Organization::create(['name' => 'Existing Co', 'slug' => 'existing-co', 'accent_color' => '#1D9E75']);

    $batch = runImportValidation([
        'Companies' => [
            ['id' => ImportIdCodec::encode(ImportIdCodec::COMPANY_PREFIX, $organization->id), 'name' => 'Existing Co'],
            ['name' => 'Brand New Co'],
        ],
    ], $this->owner);

    $noChangeRow = $batch->importRows()->where('sheet_name', 'Companies')->where('resolved_action', 'no_change')->firstOrFail();
    $insertRow = $batch->importRows()->where('sheet_name', 'Companies')->where('resolved_action', 'insert')->firstOrFail();

    $response = $this->actingAs($this->owner)->get("/import/{$batch->id}/review");

    $response->assertOk();
    $response->assertSee('Companies (1)');
    $response->assertSee('Brand New Co');
    $response->assertDontSee('Existing Co');
    expect($noChangeRow)->not->toBeNull();
    expect($insertRow)->not->toBeNull();
});

test('the review page never re-validates — it only reads what was already stored at upload time', function () {
    $batch = runImportValidation([
        'Companies' => [['name' => 'New Co']],
    ], $this->owner);

    $rowBefore = $batch->importRows()->where('sheet_name', 'Companies')->firstOrFail();
    expect($rowBefore->validation_status->value)->toBe('valid');

    // Mutate the stored row directly, bypassing validation entirely —
    // the review page must reflect this stored state, not recompute it.
    $rowBefore->update(['validation_status' => 'error', 'validation_message' => 'Manually forced for this test']);

    $response = $this->actingAs($this->owner)->get("/import/{$batch->id}/review");

    $response->assertOk();
    $response->assertSee('Manually forced for this test');
    $response->assertSee('1 error');
});
