<?php

use App\Exceptions\FolderAlreadyLinkedException;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\DocumentFolder;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Notifications\AuditEventDatabaseNotification;
use App\Services\TaskDocumentLinker;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Covers TaskDocumentLinker::attachFolder()/detachFolder() (task #73
 * phase 4) — the folder-link sibling of attach()/detach(), covered
 * separately in TaskDocumentLinkerTest.php (their shared duplicate-key
 * detection logic is exercised there, not re-proven here for folders).
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    $this->task = Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
        'title' => 'Task', 'priority' => 'medium', 'status' => 'pending',
    ]);
});

function makeLinkerFolderTestFolder(Organization $org, User $creator, string $name = 'Folder'): DocumentFolder
{
    return DocumentFolder::create(['organization_id' => $org->id, 'parent_id' => null, 'name' => $name, 'created_by' => $creator->id]);
}

test('attachFolder() records linked_by on the pivot row and writes a task.folder_attached audit entry', function () {
    $folder = makeLinkerFolderTestFolder($this->org, $this->management);
    $this->actingAs($this->management);

    app(TaskDocumentLinker::class)->attachFolder($this->task, $folder);

    $pivot = DB::table('task_folder_links')->where('task_id', $this->task->id)->where('folder_id', $folder->id)->first();
    expect($pivot)->not->toBeNull();
    expect($pivot->linked_by)->toBe($this->management->id);

    $entry = AuditLog::where('action', 'task.folder_attached')->where('entity_id', $this->task->id)->first();
    expect($entry)->not->toBeNull();
    expect($entry->entity_type)->toBe('task');
    expect($entry->organization_id)->toBe($this->task->organization_id);
    expect($entry->user_id)->toBe($this->management->id);
    expect($entry->changes)->toBe(['folder_id' => $folder->id, 'folder_name' => $folder->name]);
});

test('attachFolder() throws FolderAlreadyLinkedException for a repeat attach, and inserts no second row', function () {
    $folder = makeLinkerFolderTestFolder($this->org, $this->management);
    $this->actingAs($this->management);
    $linker = app(TaskDocumentLinker::class);
    $linker->attachFolder($this->task, $folder);

    expect(fn () => $linker->attachFolder($this->task, $folder))->toThrow(FolderAlreadyLinkedException::class);
    expect($this->task->folders()->count())->toBe(1);
});

test('a genuine primary-key duplicate raised as a database-level race is reported as FolderAlreadyLinkedException', function () {
    $folder = makeLinkerFolderTestFolder($this->org, $this->management);
    DB::table('task_folder_links')->insert(['task_id' => $this->task->id, 'folder_id' => $folder->id, 'linked_by' => $this->management->id]);

    $this->actingAs($this->management);
    expect(fn () => app(TaskDocumentLinker::class)->attachFolder($this->task, $folder))->toThrow(FolderAlreadyLinkedException::class);
});

test('a foreign-key violation is never mislabeled as a duplicate attach', function () {
    $phantomFolder = new DocumentFolder(['organization_id' => $this->org->id, 'name' => 'Phantom', 'created_by' => $this->management->id]);
    $phantomFolder->id = 999999;
    $phantomFolder->exists = true;

    $this->actingAs($this->management);
    expect(fn () => app(TaskDocumentLinker::class)->attachFolder($this->task, $phantomFolder))->toThrow(QueryException::class);
    expect($this->task->folders()->count())->toBe(0);
});

test('detachFolder() removes only the pivot row and writes a task.folder_unlinked audit entry', function () {
    $folder = makeLinkerFolderTestFolder($this->org, $this->management);
    $this->task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);
    $this->actingAs($this->management);

    app(TaskDocumentLinker::class)->detachFolder($this->task, $folder);

    expect($this->task->fresh()->folders()->count())->toBe(0);
    expect(DocumentFolder::find($folder->id))->not->toBeNull();

    $entry = AuditLog::where('action', 'task.folder_unlinked')->where('entity_id', $this->task->id)->first();
    expect($entry)->not->toBeNull();
    expect($entry->changes)->toBe(['folder_id' => $folder->id, 'folder_name' => $folder->name]);
});

test('detachFolder() writes no audit entry when nothing was actually linked', function () {
    $folder = makeLinkerFolderTestFolder($this->org, $this->management);
    $this->actingAs($this->management);

    app(TaskDocumentLinker::class)->detachFolder($this->task, $folder);

    expect(AuditLog::where('action', 'task.folder_unlinked')->exists())->toBeFalse();
});

test('a task.folder_attached entry appears on the Audit Trail page and triggers no notification', function () {
    $folder = makeLinkerFolderTestFolder($this->org, $this->management, 'Launch assets');

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/folders", ['folder_id' => $folder->id])->assertOk();

    $response = $this->actingAs($this->owner)->get('/audit-trail');
    $response->assertOk();
    $response->assertSee('Task Folder Attached');
    $response->assertSee('Launch assets');

    $this->assertDatabaseMissing('notifications', ['type' => AuditEventDatabaseNotification::class]);
});

test('a task.folder_unlinked entry appears on the Audit Trail page and triggers no notification', function () {
    $folder = makeLinkerFolderTestFolder($this->org, $this->management, 'Launch assets');
    $this->task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);

    $this->actingAs($this->management)->deleteJson("/tasks/{$this->task->id}/folders/{$folder->id}")->assertOk();

    $response = $this->actingAs($this->owner)->get('/audit-trail');
    $response->assertOk();
    $response->assertSee('Task Folder Unlinked');

    $this->assertDatabaseMissing('notifications', ['type' => AuditEventDatabaseNotification::class]);
});
