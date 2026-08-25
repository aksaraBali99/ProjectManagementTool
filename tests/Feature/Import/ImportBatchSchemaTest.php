<?php

use App\Models\AuditLog;
use App\Models\ImportBatch;
use App\Models\ImportRow;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::where('slug', 'owner')->first()->id);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
});

test('import_rows cascade-deletes when its batch is deleted', function () {
    $batch = ImportBatch::create([
        'uploaded_by' => $this->owner->id,
        'file_name' => 'test.xlsx',
        'status' => 'pending_review',
    ]);

    $row = ImportRow::create([
        'import_batch_id' => $batch->id,
        'sheet_name' => 'Companies',
        'row_number' => 3,
        'raw_data' => ['name' => 'Acme'],
        'resolved_action' => 'insert',
        'validation_status' => 'valid',
    ]);

    $batch->delete();

    $this->assertDatabaseMissing('import_rows', ['id' => $row->id]);
});

test('the Audit Trail page can filter entries by import batch', function () {
    $batch = ImportBatch::create([
        'uploaded_by' => $this->owner->id,
        'file_name' => 'test.xlsx',
        'status' => 'committed',
        'uuid' => (string) Str::uuid(),
    ]);

    $importedEntry = AuditLog::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->owner->id,
        'action' => 'organization.created',
        'entity_type' => 'organization',
        'entity_id' => $this->orgA->id,
        'import_batch_id' => $batch->uuid,
        'changes' => ['name' => $this->orgA->name, 'source' => 'import', 'import_batch_id' => $batch->uuid],
    ]);

    $unrelatedEntry = AuditLog::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->owner->id,
        'action' => 'organization.updated',
        'entity_type' => 'organization',
        'entity_id' => $this->orgA->id,
        'changes' => ['name' => ['old' => 'Old', 'new' => $this->orgA->name]],
    ]);

    $response = $this->actingAs($this->owner)->get('/audit-trail?import_batch_id='.$batch->id);

    $response->assertOk();
    $response->assertSee('via import #'.$batch->id);
    $response->assertSee($importedEntry->actionLabel());
    $response->assertDontSee($unrelatedEntry->actionLabel());
});
