<?php

use App\Enums\ImportBatchStatus;
use App\Models\ImportBatch;
use App\Models\ImportRow;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->owner = User::factory()->create();
});

test('a pending_review batch older than 24h is marked abandoned and its rows/file deleted', function () {
    Storage::fake('local');
    Storage::disk('local')->put('imports/1/test.xlsx', 'fake content');

    $batch = ImportBatch::create([
        'uploaded_by' => $this->owner->id,
        'file_name' => 'test.xlsx',
        'stored_path' => 'imports/1/test.xlsx',
        'status' => 'pending_review',
    ]);
    $batch->forceFill(['created_at' => now()->subDays(2)])->save();

    $row = ImportRow::create([
        'import_batch_id' => $batch->id,
        'sheet_name' => 'Companies',
        'row_number' => 3,
        'raw_data' => ['name' => 'Acme'],
        'resolved_action' => 'insert',
        'validation_status' => 'valid',
    ]);

    $this->artisan('imports:abandon-stale')->assertExitCode(0);

    expect($batch->fresh()->status)->toBe(ImportBatchStatus::Abandoned);
    $this->assertDatabaseMissing('import_rows', ['id' => $row->id]);
    Storage::disk('local')->assertMissing('imports/1/test.xlsx');
});

test('a pending_review batch newer than 24h is left untouched', function () {
    $batch = ImportBatch::create([
        'uploaded_by' => $this->owner->id,
        'file_name' => 'test.xlsx',
        'status' => 'pending_review',
    ]);

    $this->artisan('imports:abandon-stale')->assertExitCode(0);

    expect($batch->fresh()->status)->toBe(ImportBatchStatus::PendingReview);
});

test('a committed or partially_committed batch is never touched regardless of age', function () {
    $committed = ImportBatch::create([
        'uploaded_by' => $this->owner->id,
        'file_name' => 'committed.xlsx',
        'status' => 'committed',
    ]);
    $committed->forceFill(['created_at' => now()->subDays(5)])->save();

    $partiallyCommitted = ImportBatch::create([
        'uploaded_by' => $this->owner->id,
        'file_name' => 'partial.xlsx',
        'status' => 'partially_committed',
    ]);
    $partiallyCommitted->forceFill(['created_at' => now()->subDays(5)])->save();

    $this->artisan('imports:abandon-stale')->assertExitCode(0);

    expect($committed->fresh()->status)->toBe(ImportBatchStatus::Committed);
    expect($partiallyCommitted->fresh()->status)->toBe(ImportBatchStatus::PartiallyCommitted);
});
