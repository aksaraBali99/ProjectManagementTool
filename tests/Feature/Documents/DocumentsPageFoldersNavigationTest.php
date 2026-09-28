<?php

use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->owner = createOwner();
    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#123456']);
    $this->dept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);
});

test('the breadcrumb shows company root, folder, and subfolder, and folders render before documents', function () {
    $parent = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Contracts', 'created_by' => $this->management->id]);
    $child = DocumentFolder::create(['organization_id' => $this->orgA->id, 'parent_id' => $parent->id, 'name' => '2026', 'created_by' => $this->management->id]);
    $grandchildDocument = Document::create([
        'organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Lease.pdf',
        'link' => 'https://example.com/lease.pdf', 'access_level' => 'internal', 'folder_id' => $child->id,
    ]);
    $subSubFolder = DocumentFolder::create(['organization_id' => $this->orgA->id, 'parent_id' => $child->id, 'name' => 'Scanned', 'created_by' => $this->management->id]);

    $response = $this->actingAs($this->management)->get('/documents/'.$this->orgA->id.'?folder='.$child->id);

    $response->assertOk();
    $response->assertSeeInOrder([$this->orgA->name, $parent->name, $child->name], false);
    expect($response->viewData('folder')->id)->toBe($child->id);
    expect($response->viewData('breadcrumb')->pluck('id')->all())->toBe([$parent->id, $child->id]);
    expect($response->viewData('folders')->pluck('id')->all())->toBe([$subSubFolder->id]);
    expect($response->viewData('documents')->pluck('id')->all())->toBe([$grandchildDocument->id]);
});

test('an unknown folder id 404s', function () {
    $this->actingAs($this->management)->get('/documents/'.$this->orgA->id.'?folder=999999')->assertNotFound();
});

test('a cross-company folder id 404s rather than showing another company\'s folder', function () {
    $folderInOrgB = DocumentFolder::create(['organization_id' => $this->orgB->id, 'name' => 'Org B folder', 'created_by' => $this->owner->id]);

    $this->actingAs($this->management)->get('/documents/'.$this->orgA->id.'?folder='.$folderInOrgB->id)->assertNotFound();
});

test('switching company tabs goes to that company\'s root, not the same folder id', function () {
    OrgMember::create(['organization_id' => $this->orgB->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
    $folder = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Contracts', 'created_by' => $this->management->id]);

    $response = $this->actingAs($this->management)->get('/documents/'.$this->orgA->id.'?folder='.$folder->id);

    $response->assertOk();
    // The company tab for Org B links to plain /documents/{orgB}, no
    // folder query string at all.
    $response->assertDontSee('href="'.route('documents.index', $this->orgB).'?folder', false);
    $response->assertSee('href="'.route('documents.index', $this->orgB).'"', false);
});

test('origin shows "Documents page" for a root upload, a task title link when the viewer can see it, and "From a task" otherwise', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
        'title' => 'Origin task', 'priority' => 'medium', 'status' => 'pending',
    ]);

    $fromDocumentsPage = Document::create([
        'organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Root upload',
        'link' => 'https://example.com/root.pdf', 'access_level' => 'internal',
    ]);
    $fromViewableTask = Document::create([
        'organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'From viewable task',
        'link' => 'https://example.com/viewable.pdf', 'access_level' => 'internal', 'origin_task_id' => $task->id,
    ]);

    $response = $this->actingAs($this->management)->get('/documents/'.$this->orgA->id);

    $response->assertOk();
    $response->assertSee('Documents page');
    $response->assertSee('Origin task');
    $response->assertSee(route('tasks.edit', $task->id), false);

    // Now with a viewer who CANNOT see the origin task (a staff member
    // with no department access to it).
    $staffOutsideDept = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staffOutsideDept->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    // access_level internal is visible to any view_documents holder
    // (staff has it by default), so they can see the DOCUMENT itself —
    // just not its origin task.
    $hiddenResponse = $this->actingAs($staffOutsideDept)->get('/documents/'.$this->orgA->id);
    $hiddenResponse->assertOk();
    $hiddenResponse->assertSee('From a task');
    $hiddenResponse->assertDontSee('Origin task');
});

test('the linked-task count comes from one grouped query, and is accurate per document', function () {
    $task1 = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T1', 'priority' => 'medium', 'status' => 'pending']);
    $task2 = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T2', 'priority' => 'medium', 'status' => 'pending']);

    $document = Document::create([
        'organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Shared doc',
        'link' => 'https://example.com/shared.pdf', 'access_level' => 'internal',
    ]);
    $document->tasks()->attach([$task1->id, $task2->id]);

    $unlinkedDocument = Document::create([
        'organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Unlinked doc',
        'link' => 'https://example.com/unlinked.pdf', 'access_level' => 'internal',
    ]);

    $response = $this->actingAs($this->management)->get('/documents/'.$this->orgA->id);

    $response->assertOk();
    $counts = $response->viewData('linkedTaskCounts');
    expect((int) $counts[$document->id])->toBe(2);
    expect($counts->has($unlinkedDocument->id))->toBeFalse();
});

test('uploading/adding a link from the Documents page files into the current folder; uploading from a task stays at the root', function () {
    $folder = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Contracts', 'created_by' => $this->management->id]);

    // Plain post(), not postJson() — expectsJson() would otherwise take
    // the JSON-response branch instead of the redirect this asserts on,
    // matching the real "Add Document" page's own plain form submission.
    $inFolder = $this->actingAs($this->management)->post('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'In folder',
        'link' => 'https://example.com/in-folder.pdf',
        'access_level' => 'internal',
        'folder_id' => $folder->id,
        'from_documents_page' => '1',
    ]);
    $inFolder->assertRedirect(route('documents.index', ['organization' => $this->orgA->id, 'folder' => $folder->id]));
    expect(Document::where('name', 'In folder')->firstOrFail()->folder_id)->toBe($folder->id);

    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'From task',
        'link' => 'https://example.com/from-task.pdf',
        'access_level' => 'internal',
        'task_id' => $task->id,
        // Even if a stray folder_id were somehow submitted alongside
        // task_id, it must be ignored — task-scoped creation always goes
        // to the root.
        'folder_id' => $folder->id,
    ])->assertCreated();
    expect(Document::where('name', 'From task')->firstOrFail()->folder_id)->toBeNull();
});

test('a folder_id from another company is rejected on the Documents-page upload/link endpoint', function () {
    $folderInOrgB = DocumentFolder::create(['organization_id' => $this->orgB->id, 'name' => 'Org B folder', 'created_by' => $this->owner->id]);

    $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Should fail',
        'link' => 'https://example.com/should-fail.pdf',
        'access_level' => 'internal',
        'folder_id' => $folderInOrgB->id,
        'from_documents_page' => '1',
    ])->assertStatus(404);

    $this->assertDatabaseMissing('documents', ['name' => 'Should fail']);
});

test('the Documents page query count does not grow with the number of folders or documents on it', function () {
    // Baseline: one folder, one document, one linked task, one origin task.
    $folder = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Folder 1', 'created_by' => $this->management->id]);
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $document = Document::create([
        'organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Doc 1',
        'link' => 'https://example.com/doc1.pdf', 'access_level' => 'internal', 'origin_task_id' => $task->id,
    ]);
    $document->tasks()->attach($task->id);

    // Warm-up request first — the first call in a test pays for some
    // request-scoped cache misses a later call on the same user doesn't.
    $this->actingAs($this->management)->get('/documents/'.$this->orgA->id)->assertOk();

    DB::enableQueryLog();
    $this->actingAs($this->management)->get('/documents/'.$this->orgA->id)->assertOk();
    $baselineQueries = count(DB::getQueryLog());
    DB::flushQueryLog();

    // Scale up: several more folders, documents, tasks, and links.
    foreach (range(1, 5) as $i) {
        DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Folder '.($i + 1), 'created_by' => $this->management->id]);

        $scaledTask = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T'.$i, 'priority' => 'medium', 'status' => 'pending']);
        $scaledDocument = Document::create([
            'organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id, 'name' => 'Doc '.($i + 1),
            'link' => 'https://example.com/doc'.($i + 1).'.pdf', 'access_level' => 'internal', 'origin_task_id' => $scaledTask->id,
        ]);
        $scaledDocument->tasks()->attach($scaledTask->id);
    }

    DB::flushQueryLog();

    DB::enableQueryLog();
    $response = $this->actingAs($this->management)->get('/documents/'.$this->orgA->id)->assertOk();
    $scaledUpQueries = count(DB::getQueryLog());
    DB::flushQueryLog();

    expect($response->viewData('documents'))->toHaveCount(6);
    expect($response->viewData('folders'))->toHaveCount(6);
    expect($scaledUpQueries)->toBe($baselineQueries);
});
