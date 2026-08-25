<?php

use App\Enums\ImportBatchStatus;
use App\Models\ImportBatch;
use App\Models\Role;
use App\Models\User;
use App\Services\Import\ImportValidator;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::where('slug', 'owner')->first()->id);

    $this->staff = User::factory()->create();
});

test('the import index page shows an upload form', function () {
    $response = $this->actingAs($this->owner)->get('/import');

    $response->assertOk();
    $response->assertSee(route('import.upload'), false);
    $response->assertSee('type="file"', false);
});

test('a non-admin cannot upload an import file', function () {
    $file = buildImportTestFile([]);

    $this->actingAs($this->staff)->post('/import/upload', ['file' => $file])->assertForbidden();
});

test('uploading a valid file creates a pending_review batch and import_rows for every non-empty sheet', function () {
    $file = buildImportTestFile([
        'Companies' => [
            ['name' => 'Acme Corp'],
        ],
    ]);

    $response = $this->actingAs($this->owner)->post('/import/upload', ['file' => $file]);

    $batch = ImportBatch::firstOrFail();
    $response->assertRedirect(route('import.review', $batch));

    expect($batch->status)->toBe(ImportBatchStatus::PendingReview);
    expect($batch->uploaded_by)->toBe($this->owner->id);
    $this->assertDatabaseHas('import_rows', [
        'import_batch_id' => $batch->id,
        'sheet_name' => 'Companies',
        'resolved_action' => 'insert',
        'validation_status' => 'valid',
    ]);
});

test('a file exceeding the row-count cap is rejected before any rows are written', function () {
    $rows = array_fill(0, ImportValidator::maxTotalRows() + 1, ['name' => 'Company']);
    $file = buildImportTestFile(['Companies' => $rows]);

    $response = $this->actingAs($this->owner)->post('/import/upload', ['file' => $file]);

    $response->assertSessionHasErrors('file');
    expect(ImportBatch::count())->toBe(0);
    $this->assertDatabaseCount('import_rows', 0);
});
