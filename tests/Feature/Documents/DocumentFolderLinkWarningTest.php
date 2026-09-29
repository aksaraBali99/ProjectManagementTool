<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

/**
 * Covers task #73 phase 4's extension to DocumentDependencyService: a
 * file that lives (or would live) inside a folder linked to one or more
 * tasks gets a warn-and-confirm treatment on delete, move, and switching
 * to Private — never a hard block, unlike the DIRECT task_documents link
 * case (see DocumentEditTest/DocumentDeleteTest for that unchanged
 * behavior). "Same response shape/wording as the direct-link block" does
 * NOT apply here on purpose — this is a genuinely different response
 * (requires_confirmation: true), confirmed with the task's own author
 * after the Step 0 report flagged the original wording as inaccurate.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
});

function makeWarningTestFolder(Organization $org, User $creator, string $name = 'Folder'): DocumentFolder
{
    return DocumentFolder::create(['organization_id' => $org->id, 'name' => $name, 'created_by' => $creator->id]);
}

function makeWarningTestTask(Organization $org, Department $dept, Project $project, string $title = 'T'): Task
{
    return Task::create(['organization_id' => $org->id, 'project_id' => $project->id, 'department_id' => $dept->id, 'title' => $title, 'priority' => 'medium', 'status' => 'pending']);
}

test('the dependencies() preview includes the folder_warning the Delete dialog needs to show upfront, before any request is even attempted', function () {
    $folder = makeWarningTestFolder($this->orgA, $this->management, 'Launch assets');
    $task = makeWarningTestTask($this->orgA, $this->dept, $this->project);
    $task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);
    $document = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal', 'folder_id' => $folder->id]);

    $response = $this->actingAs($this->management)->getJson("/documents/{$document->id}/dependencies");

    $response->assertOk();
    expect($response->json('folder_warning'))->not->toBeNull();
    expect($response->json('folder_warning.confirm_field'))->toBe('confirm_folder_effect');
    expect($response->json('folder_warning.requires_confirmation'))->toBeTrue();
});

test('the dependencies() preview\'s folder_warning is null for a file not in any linked folder', function () {
    $folder = makeWarningTestFolder($this->orgA, $this->management, 'Never linked');
    $document = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal', 'folder_id' => $folder->id]);

    $response = $this->actingAs($this->management)->getJson("/documents/{$document->id}/dependencies");

    $response->assertOk();
    expect($response->json('folder_warning'))->toBeNull();
});

test('deleting a file inside a linked folder warns and requires confirmation, naming the correct task list', function () {
    $folder = makeWarningTestFolder($this->orgA, $this->management, 'Launch assets');
    $task = makeWarningTestTask($this->orgA, $this->dept, $this->project, 'Prepare launch');
    $task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);
    $document = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal', 'folder_id' => $folder->id]);

    $response = $this->actingAs($this->management)->deleteJson("/documents/{$document->id}");

    $response->assertStatus(422);
    expect($response->json('requires_confirmation'))->toBeTrue();
    expect($response->json('confirm_field'))->toBe('confirm_folder_effect');
    expect($response->json('message'))->toBe("This file is included in 1 tasks through its folder 'Launch assets'.");
    expect(collect($response->json('linked_tasks'))->pluck('title'))->toContain('Prepare launch');
    $this->assertDatabaseHas('documents', ['id' => $document->id]);
});

test('confirming the folder effect allows the delete to proceed', function () {
    $folder = makeWarningTestFolder($this->orgA, $this->management, 'Launch assets');
    $task = makeWarningTestTask($this->orgA, $this->dept, $this->project);
    $task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);
    $document = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal', 'folder_id' => $folder->id]);

    $response = $this->actingAs($this->management)->deleteJson("/documents/{$document->id}", ['confirm_folder_effect' => true]);

    $response->assertOk();
    $this->assertDatabaseMissing('documents', ['id' => $document->id]);
});

test('the folder-link warning names only the tasks the deleter can view, with a hidden count for the rest', function () {
    $folder = makeWarningTestFolder($this->orgA, $this->management, 'Launch assets');
    $viewableTask = makeWarningTestTask($this->orgA, $this->dept, $this->project, 'Viewable task');
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Ops', 'color' => '#111']);
    $hiddenTask = makeWarningTestTask($this->orgA, $otherDept, $this->project, 'Hidden task');
    $viewableTask->folders()->attach($folder->id, ['linked_by' => $this->management->id]);
    $hiddenTask->folders()->attach($folder->id, ['linked_by' => $this->management->id]);

    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $staffRole->permissions()->syncWithoutDetaching([Permission::where('slug', 'manage_documents')->firstOrFail()->id]);
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => $staffRole->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->dept->id, 'allowed' => true]);

    // The deleter must be the document's own uploader (or management-tier)
    // to pass DocumentPolicy::canManage() at all - staff is neither
    // management-tier here, so it must be the uploader.
    $document = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $staff->id, 'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal', 'folder_id' => $folder->id]);

    $response = $this->actingAs($staff)->deleteJson("/documents/{$document->id}");

    $response->assertStatus(422);
    expect(collect($response->json('linked_tasks'))->pluck('title')->all())->toBe(['Viewable task']);
    expect($response->json('hidden_linked_task_count'))->toBe(1);
});

test('moving a file OUT of a linked folder warns', function () {
    $linkedFolder = makeWarningTestFolder($this->orgA, $this->management, 'Linked');
    $unlinkedFolder = makeWarningTestFolder($this->orgA, $this->management, 'Unlinked');
    $task = makeWarningTestTask($this->orgA, $this->dept, $this->project);
    $task->folders()->attach($linkedFolder->id, ['linked_by' => $this->management->id]);
    $document = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal', 'folder_id' => $linkedFolder->id]);

    $response = $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['folder_id' => $unlinkedFolder->id]);

    $response->assertStatus(422);
    expect($response->json('requires_confirmation'))->toBeTrue();
    $this->assertDatabaseHas('documents', ['id' => $document->id, 'folder_id' => $linkedFolder->id]);
});

test('moving a file INTO a linked folder warns, even though it was never linked before', function () {
    $unlinkedFolder = makeWarningTestFolder($this->orgA, $this->management, 'Unlinked');
    $linkedFolder = makeWarningTestFolder($this->orgA, $this->management, 'Linked');
    $task = makeWarningTestTask($this->orgA, $this->dept, $this->project);
    $task->folders()->attach($linkedFolder->id, ['linked_by' => $this->management->id]);
    $document = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal', 'folder_id' => $unlinkedFolder->id]);

    $response = $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['folder_id' => $linkedFolder->id]);

    $response->assertStatus(422);
    expect($response->json('requires_confirmation'))->toBeTrue();
    $this->assertDatabaseHas('documents', ['id' => $document->id, 'folder_id' => $unlinkedFolder->id]);
});

test('confirming the folder effect allows the move (either direction) to proceed', function () {
    $unlinkedFolder = makeWarningTestFolder($this->orgA, $this->management, 'Unlinked');
    $linkedFolder = makeWarningTestFolder($this->orgA, $this->management, 'Linked');
    $task = makeWarningTestTask($this->orgA, $this->dept, $this->project);
    $task->folders()->attach($linkedFolder->id, ['linked_by' => $this->management->id]);
    $document = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal', 'folder_id' => $unlinkedFolder->id]);

    $response = $this->actingAs($this->management)->putJson("/documents/{$document->id}", [
        'folder_id' => $linkedFolder->id,
        'confirm_folder_effect' => true,
    ]);

    $response->assertOk();
    expect(Document::find($document->id)->folder_id)->toBe($linkedFolder->id);
});

test('moving between two different linked folders warns once, covering both', function () {
    $folderA = makeWarningTestFolder($this->orgA, $this->management, 'Folder A');
    $folderB = makeWarningTestFolder($this->orgA, $this->management, 'Folder B');
    $taskA = makeWarningTestTask($this->orgA, $this->dept, $this->project, 'Task A');
    $taskB = makeWarningTestTask($this->orgA, $this->dept, $this->project, 'Task B');
    $taskA->folders()->attach($folderA->id, ['linked_by' => $this->management->id]);
    $taskB->folders()->attach($folderB->id, ['linked_by' => $this->management->id]);
    $document = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal', 'folder_id' => $folderA->id]);

    $response = $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['folder_id' => $folderB->id]);

    $response->assertStatus(422);
    expect(collect($response->json('linked_tasks'))->pluck('title')->all())->toBe(['Task A', 'Task B']);
});

test('moving a file between two folders that are both unlinked does not warn', function () {
    $folderA = makeWarningTestFolder($this->orgA, $this->management, 'Folder A');
    $folderB = makeWarningTestFolder($this->orgA, $this->management, 'Folder B');
    $document = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal', 'folder_id' => $folderA->id]);

    $response = $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['folder_id' => $folderB->id]);

    $response->assertOk();
});

test('switching a file inside a linked folder to Private warns instead of blocking', function () {
    $folder = makeWarningTestFolder($this->orgA, $this->management, 'Launch assets');
    $task = makeWarningTestTask($this->orgA, $this->dept, $this->project, 'Prepare launch');
    $task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);
    $document = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal', 'folder_id' => $folder->id]);

    $response = $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['access_level' => 'private']);

    $response->assertStatus(422);
    expect($response->json('requires_confirmation'))->toBeTrue();
    expect($response->json('message'))->toBe("This file is included in 1 tasks through its folder 'Launch assets'.");
    expect(Document::find($document->id)->access_level->value)->toBe('internal');
});

test('confirming the folder effect allows the Private switch to proceed', function () {
    $folder = makeWarningTestFolder($this->orgA, $this->management, 'Launch assets');
    $task = makeWarningTestTask($this->orgA, $this->dept, $this->project);
    $task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);
    $document = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal', 'folder_id' => $folder->id]);

    $response = $this->actingAs($this->management)->putJson("/documents/{$document->id}", [
        'access_level' => 'private',
        'confirm_folder_effect' => true,
    ]);

    $response->assertOk();
    expect(Document::find($document->id)->access_level->value)->toBe('private');
});

test('a document DIRECTLY linked to a task is still hard-blocked, even if it also sits in a linked folder', function () {
    $folder = makeWarningTestFolder($this->orgA, $this->management, 'Launch assets');
    $task = makeWarningTestTask($this->orgA, $this->dept, $this->project);
    $task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);
    $document = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal', 'folder_id' => $folder->id]);
    $task->documents()->attach($document->id);

    // Direct-link block wins, even with confirm_folder_effect set - a
    // hard block is never overridable by the folder-derived confirm flag.
    $response = $this->actingAs($this->management)->deleteJson("/documents/{$document->id}", ['confirm_folder_effect' => true]);

    $response->assertStatus(422);
    expect($response->json('requires_confirmation'))->toBeFalsy();
    expect($response->json('message'))->toBe('This document is still attached to 1 tasks. Remove it from them first.');
});

test('a file not in any linked folder deletes, moves and switches to Private without any warning', function () {
    $folder = makeWarningTestFolder($this->orgA, $this->management, 'Never linked');
    $otherFolder = makeWarningTestFolder($this->orgA, $this->management, 'Also never linked');
    $document = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal', 'folder_id' => $folder->id]);

    $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['folder_id' => $otherFolder->id])->assertOk();
    $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['access_level' => 'private'])->assertOk();
    $this->actingAs($this->management)->deleteJson("/documents/{$document->id}")->assertOk();
});
