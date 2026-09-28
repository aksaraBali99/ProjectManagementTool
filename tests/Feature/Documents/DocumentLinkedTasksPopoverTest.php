<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Covers task #73's "Linked tasks" popover: a new endpoint
 * (DocumentController::linkedTasks()) that lazily supplies the popover's
 * content. The existing column NUMBER (DocumentController::index()'s raw,
 * scope-oblivious task_documents count) is untouched — these tests are
 * entirely about the new endpoint and the popover's own, separate
 * viewer-scoped breakdown.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#123456']);
    $this->dept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
});

function makeDocumentForLinkedTasksTest(Organization $org, User $uploader): Document
{
    return Document::create([
        'organization_id' => $org->id,
        'uploaded_by' => $uploader->id,
        'name' => 'Doc.pdf',
        'link' => 'https://example.com/doc.pdf',
        'access_level' => 'internal',
    ]);
}

function makeTaskForLinkedTasksTest(Organization $org, Project $project, Department $dept, string $title): Task
{
    return Task::create([
        'organization_id' => $org->id, 'project_id' => $project->id, 'department_id' => $dept->id,
        'title' => $title, 'priority' => 'medium', 'status' => 'pending',
    ]);
}

test('the endpoint returns viewable task titles and URLs for a document the viewer can see', function () {
    $task = makeTaskForLinkedTasksTest($this->orgA, $this->project, $this->dept, 'Write the report');
    $document = makeDocumentForLinkedTasksTest($this->orgA, $this->management);
    $document->tasks()->attach($task->id);

    $response = $this->actingAs($this->management)->getJson("/documents/{$document->id}/linked-tasks");

    $response->assertOk();
    expect($response->json('tasks'))->toBe([
        ['id' => $task->id, 'title' => 'Write the report', 'url' => route('tasks.edit', $task->id)],
    ]);
    expect($response->json('viewable_total'))->toBe(1);
    expect($response->json('hidden_count'))->toBe(0);
});

test('a viewer who cannot see some linked tasks gets only a count for those, with no titles or IDs anywhere', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Ops', 'color' => '#111']);
    $hiddenTask = makeTaskForLinkedTasksTest($this->orgA, $this->project, $otherDept, 'Top Secret Task');
    $visibleTask = makeTaskForLinkedTasksTest($this->orgA, $this->project, $this->dept, 'Visible task');

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->dept->id, 'allowed' => true]);
    grantManageDocumentsForLinkedTasksTest(Role::where('slug', 'staff')->firstOrFail());

    $document = makeDocumentForLinkedTasksTest($this->orgA, $staff);
    $document->tasks()->attach([$hiddenTask->id, $visibleTask->id]);

    $response = $this->actingAs($staff)->getJson("/documents/{$document->id}/linked-tasks");

    $response->assertOk();
    expect($response->json('tasks'))->toBe([
        ['id' => $visibleTask->id, 'title' => 'Visible task', 'url' => route('tasks.edit', $visibleTask->id)],
    ]);
    expect($response->json('viewable_total'))->toBe(1);
    expect($response->json('hidden_count'))->toBe(1);
    expect($response->getContent())->not->toContain('Top Secret Task');
});

test('if all linked tasks are hidden from the viewer, the response contains only the count', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Ops', 'color' => '#111']);
    $hiddenTask = makeTaskForLinkedTasksTest($this->orgA, $this->project, $otherDept, 'Top Secret Task');

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    grantManageDocumentsForLinkedTasksTest(Role::where('slug', 'staff')->firstOrFail());

    $document = makeDocumentForLinkedTasksTest($this->orgA, $staff);
    $document->tasks()->attach($hiddenTask->id);

    $response = $this->actingAs($staff)->getJson("/documents/{$document->id}/linked-tasks");

    $response->assertOk();
    expect($response->json('tasks'))->toBe([]);
    expect($response->json('viewable_total'))->toBe(0);
    expect($response->json('hidden_count'))->toBe(1);
    expect($response->getContent())->not->toContain('Top Secret Task');
});

test('a document the viewer cannot see returns 404', function () {
    $privateDocument = Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->owner->id,
        'name' => 'Private.pdf',
        'link' => 'https://example.com/private.pdf',
        'access_level' => 'private',
    ]);

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    grantViewDocumentsForLinkedTasksTest(Role::where('slug', 'staff')->firstOrFail());

    $this->actingAs($staff)->getJson("/documents/{$privateDocument->id}/linked-tasks")->assertNotFound();
});

test('a viewer from another company gets 404', function () {
    $document = makeDocumentForLinkedTasksTest($this->orgA, $this->management);

    $staffInOrgB = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgB->id, 'user_id' => $staffInOrgB->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    grantViewDocumentsForLinkedTasksTest(Role::where('slug', 'staff')->firstOrFail());

    $this->actingAs($staffInOrgB)->getJson("/documents/{$document->id}/linked-tasks")->assertNotFound();
});

test('super_admin and owner do not get a false 404 for a cross-company document (existing cross-company visibility is unchanged)', function () {
    $document = makeDocumentForLinkedTasksTest($this->orgA, $this->management);

    $superAdmin = User::factory()->create();
    $superAdmin->roles()->attach(Role::where('slug', 'super_admin')->firstOrFail()->id);

    $this->actingAs($superAdmin)->getJson("/documents/{$document->id}/linked-tasks")->assertOk();
    $this->actingAs($this->owner)->getJson("/documents/{$document->id}/linked-tasks")->assertOk();
});

test('the cap is 10 tasks and viewable_total reports the true total', function () {
    $document = makeDocumentForLinkedTasksTest($this->orgA, $this->management);
    $tasks = collect(range(1, 12))->map(fn (int $i) => makeTaskForLinkedTasksTest($this->orgA, $this->project, $this->dept, "Task {$i}"));
    $document->tasks()->attach($tasks->pluck('id')->all());

    $response = $this->actingAs($this->management)->getJson("/documents/{$document->id}/linked-tasks");

    $response->assertOk();
    expect($response->json('tasks'))->toHaveCount(10);
    expect($response->json('viewable_total'))->toBe(12);
    expect($response->json('hidden_count'))->toBe(0);
});

test('a deleted (soft-deleted) task never appears, and does not count toward either total', function () {
    $activeTask = makeTaskForLinkedTasksTest($this->orgA, $this->project, $this->dept, 'Active task');
    $deletedTask = makeTaskForLinkedTasksTest($this->orgA, $this->project, $this->dept, 'Deactivated task');
    $deletedTask->delete();

    $document = makeDocumentForLinkedTasksTest($this->orgA, $this->management);
    $document->tasks()->attach([$activeTask->id, $deletedTask->id]);

    $response = $this->actingAs($this->management)->getJson("/documents/{$document->id}/linked-tasks");

    $response->assertOk();
    expect($response->json('tasks'))->toBe([
        ['id' => $activeTask->id, 'title' => 'Active task', 'url' => route('tasks.edit', $activeTask->id)],
    ]);
    expect($response->json('viewable_total'))->toBe(1);
    expect($response->json('hidden_count'))->toBe(0);
});

test('tasks are ordered by most recently linked first', function () {
    $first = makeTaskForLinkedTasksTest($this->orgA, $this->project, $this->dept, 'Linked first');
    $second = makeTaskForLinkedTasksTest($this->orgA, $this->project, $this->dept, 'Linked second');
    $third = makeTaskForLinkedTasksTest($this->orgA, $this->project, $this->dept, 'Linked third');

    $document = makeDocumentForLinkedTasksTest($this->orgA, $this->management);
    $document->tasks()->attach($first->id, ['created_at' => now()->subMinutes(10), 'updated_at' => now()->subMinutes(10)]);
    $document->tasks()->attach($second->id, ['created_at' => now()->subMinutes(5), 'updated_at' => now()->subMinutes(5)]);
    $document->tasks()->attach($third->id, ['created_at' => now(), 'updated_at' => now()]);

    $response = $this->actingAs($this->management)->getJson("/documents/{$document->id}/linked-tasks");

    $response->assertOk();
    expect($response->json('tasks.*.title'))->toBe(['Linked third', 'Linked second', 'Linked first']);
});

test('the query count does not grow with the number of linked tasks', function () {
    $document = makeDocumentForLinkedTasksTest($this->orgA, $this->management);
    $task = makeTaskForLinkedTasksTest($this->orgA, $this->project, $this->dept, 'Warm-up task');
    $document->tasks()->attach($task->id);

    // Warm-up: pay the one-time session/auth cost before either measurement.
    $this->actingAs($this->management)->getJson("/documents/{$document->id}/linked-tasks")->assertOk();

    DB::enableQueryLog();
    $this->actingAs($this->management)->getJson("/documents/{$document->id}/linked-tasks")->assertOk();
    $baselineQueries = count(DB::getQueryLog());
    DB::flushQueryLog();

    $moreTasks = collect(range(1, 15))->map(fn (int $i) => makeTaskForLinkedTasksTest($this->orgA, $this->project, $this->dept, "Task {$i}"));
    $document->tasks()->attach($moreTasks->pluck('id')->all());

    // flushQueryLog() only clears the log, it doesn't stop logging — flush
    // again so the fixture inserts above aren't counted below.
    DB::flushQueryLog();

    DB::enableQueryLog();
    $this->actingAs($this->management)->getJson("/documents/{$document->id}/linked-tasks")->assertOk();
    $scaledUpQueries = count(DB::getQueryLog());
    DB::flushQueryLog();

    expect($scaledUpQueries)->toBe($baselineQueries);
});

test('the Documents page renders no popover trigger for a document with zero linked tasks, and a trigger with ARIA attributes for one with linked tasks', function () {
    $withTask = makeDocumentForLinkedTasksTest($this->orgA, $this->management);
    $withTask->name = 'Has links.pdf';
    $withTask->save();
    $task = makeTaskForLinkedTasksTest($this->orgA, $this->project, $this->dept, 'Some task');
    $withTask->tasks()->attach($task->id);

    $withoutTasks = Document::create([
        'organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id,
        'name' => 'No links.pdf', 'link' => 'https://example.com/no-links.pdf', 'access_level' => 'internal',
    ]);

    $response = $this->actingAs($this->management)->get('/documents/'.$this->orgA->id);

    $response->assertOk();
    $content = $response->getContent();

    expect($content)->toContain('aria-haspopup="dialog"');
    expect($content)->toContain('data-linked-tasks-url="'.route('documents.linked-tasks', $withTask).'"');
    // The document with zero linked tasks gets no trigger at all — not
    // just a differently-styled one. (Every document row's own <tr> also
    // carries a data-document-id, unrelated to this trigger, so this
    // checks the trigger-specific attribute instead of that generic one.)
    expect($content)->not->toContain('data-linked-tasks-url="'.route('documents.linked-tasks', $withoutTasks).'"');
});

test('the popover script opens task links in a new tab without leaking an opener', function () {
    $response = $this->actingAs($this->management)->get('/documents/'.$this->orgA->id);

    $response->assertOk();
    $content = $response->getContent();

    expect($content)->toContain("target = '_blank'");
    expect($content)->toContain("rel = 'noopener noreferrer'");
});

/**
 * The real Role Matrix form always submits every editable role's
 * checkboxes together in one POST — see grantManageDocumentsForEditTest()
 * in DocumentEditTest.php for the full explanation.
 */
function grantManageDocumentsForLinkedTasksTest(Role $role): void
{
    $manageDocumentsId = Permission::where('slug', 'manage_documents')->firstOrFail()->id;
    grantPermissionForLinkedTasksTest($role, $manageDocumentsId);
}

function grantViewDocumentsForLinkedTasksTest(Role $role): void
{
    $viewDocumentsId = Permission::where('slug', 'view_documents')->firstOrFail()->id;
    grantPermissionForLinkedTasksTest($role, $viewDocumentsId);
}

function grantPermissionForLinkedTasksTest(Role $role, int $permissionId): void
{
    $editableRoles = Role::whereIn('slug', ['management', 'staff', 'client'])->get();

    $payload = [];
    foreach ($editableRoles as $editableRole) {
        $currentIds = $editableRole->permissions()->pluck('permissions.id')->all();
        $payload[$editableRole->id] = $editableRole->is($role)
            ? array_values(array_unique([...$currentIds, $permissionId]))
            : $currentIds;
    }

    $grantOwner = User::factory()->create();
    $grantOwner->roles()->attach(Role::where('slug', 'owner')->firstOrFail()->id);

    test()->actingAs($grantOwner)->put('/roles/permissions', ['role_permissions' => $payload])->assertRedirect();
}
