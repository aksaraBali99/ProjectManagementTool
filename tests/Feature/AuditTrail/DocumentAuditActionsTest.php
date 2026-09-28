<?php

use App\Enums\NotificationEventType;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\NotificationSetting;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditEventNotifier;
use Illuminate\Support\Str;

/**
 * task #73 phase 2: the seven new audit actions (document.renamed,
 * document.moved, document.access_level_changed, document.deleted,
 * folder.created, folder.renamed, folder.deleted) follow the app's
 * existing free-form action-string convention — no enum, no registration
 * needed anywhere. This confirms that holds: they show up in the Audit
 * Trail's action filter and get a readable label automatically, and
 * AuditEventNotifier ignores them (they're not in NotificationEventType's
 * curated list) rather than erroring or notifying anyone.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
});

test('each new document/folder audit action appears in the Audit Trail action filter and gets a readable label', function (string $action) {
    AuditLog::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'action' => $action,
        'entity_type' => str_starts_with($action, 'folder.') ? 'document_folder' : 'document',
        'entity_id' => 1,
        'changes' => ['name' => 'Example'],
    ]);

    $response = $this->actingAs($this->owner)->get('/audit-trail');

    $response->assertOk();
    // The filter dropdown lists the raw action string as both value and label.
    $response->assertSee('value="'.$action.'"', false);

    $entry = AuditLog::where('action', $action)->firstOrFail();
    expect($entry->actionLabel())->not->toBe($action);
    expect($entry->actionLabel())->toBe(Str::headline(str_replace('.', ' ', $action)));
})->with('phase2Actions');

test('none of the new document/folder audit actions trigger a notification', function (string $action) {
    $recipient = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $recipient->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);

    // A broad "notify everyone for everything" admin rule, so if
    // AuditEventNotifier somehow mapped one of these actions to a real
    // event type, this would catch it.
    foreach (NotificationEventType::cases() as $eventType) {
        NotificationSetting::create([
            'owner_id' => $this->owner->id,
            'event_type' => $eventType->value,
            'channel' => 'in_app',
            'recipients' => ['type' => 'role', 'role' => 'staff'],
            'is_active' => true,
        ]);
    }

    $auditLog = AuditLog::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'action' => $action,
        'entity_type' => str_starts_with($action, 'folder.') ? 'document_folder' : 'document',
        'entity_id' => 1,
        'changes' => ['name' => 'Example'],
    ]);

    app(AuditEventNotifier::class)->notify($auditLog);

    expect($recipient->notifications()->count())->toBe(0);
})->with('phase2Actions');

dataset('phase2Actions', fn () => [
    ['document.renamed'],
    ['document.moved'],
    ['document.access_level_changed'],
    ['document.deleted'],
    ['folder.created'],
    ['folder.renamed'],
    ['folder.deleted'],
]);

test('folder.created/renamed/deleted are written for real folder actions and appear in the Audit Trail', function () {
    $response = $this->actingAs($this->management)->postJson('/document-folders', [
        'organization_id' => $this->org->id,
        'name' => 'Contracts',
    ]);
    $response->assertCreated();
    $folder = DocumentFolder::where('name', 'Contracts')->firstOrFail();

    $this->actingAs($this->management)->putJson("/document-folders/{$folder->id}", ['name' => 'Renamed'])->assertOk();
    $this->actingAs($this->management)->deleteJson("/document-folders/{$folder->id}")->assertOk();

    $this->assertDatabaseHas('audit_log', ['action' => 'folder.created', 'entity_id' => $folder->id]);
    $this->assertDatabaseHas('audit_log', ['action' => 'folder.renamed', 'entity_id' => $folder->id]);
    $this->assertDatabaseHas('audit_log', ['action' => 'folder.deleted', 'entity_id' => $folder->id]);

    $page = $this->actingAs($this->owner)->get('/audit-trail')->assertOk();
    $page->assertSee('Folder Created');
    $page->assertSee('Folder Renamed');
    $page->assertSee('Folder Deleted');
});

test('document.renamed/moved/access_level_changed/deleted are written for real document actions and appear in the Audit Trail', function () {
    $document = Document::create([
        'organization_id' => $this->org->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Original',
        'link' => 'https://example.com/original.pdf',
        'access_level' => 'internal',
    ]);
    $folder = DocumentFolder::create(['organization_id' => $this->org->id, 'name' => 'Contracts', 'created_by' => $this->management->id]);

    $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['name' => 'Renamed doc'])->assertOk();
    $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['folder_id' => $folder->id])->assertOk();
    $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['access_level' => 'private'])->assertOk();
    $this->actingAs($this->management)->deleteJson("/documents/{$document->id}")->assertOk();

    $this->assertDatabaseHas('audit_log', ['action' => 'document.renamed', 'entity_id' => $document->id]);
    $this->assertDatabaseHas('audit_log', ['action' => 'document.moved', 'entity_id' => $document->id]);
    $this->assertDatabaseHas('audit_log', ['action' => 'document.access_level_changed', 'entity_id' => $document->id]);
    $this->assertDatabaseHas('audit_log', ['action' => 'document.deleted', 'entity_id' => $document->id]);

    $page = $this->actingAs($this->owner)->get('/audit-trail')->assertOk();
    $page->assertSee('Document Renamed');
    $page->assertSee('Document Moved');
    $page->assertSee('Document Access Level Changed');
    $page->assertSee('Document Deleted');
});
