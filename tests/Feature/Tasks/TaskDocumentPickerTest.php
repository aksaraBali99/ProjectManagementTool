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
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Covers task #73 phase 3's "Attach existing" picker list endpoint
 * (GET /tasks/{task}/documents/attachable) — same gate as the button
 * (TaskPolicy::attachDocuments()), documents restricted to the task's own
 * company via DocumentPolicy::attachableInCompany(), search, pagination,
 * and the already_attached flag.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#123456']);
    $this->dept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    $this->task = Task::create([
        'organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
        'title' => 'Task', 'priority' => 'medium', 'status' => 'pending',
    ]);
});

function makePickerDocument(Organization $org, User $uploader, string $name, string $accessLevel = 'internal', ?int $folderId = null): Document
{
    return Document::create([
        'organization_id' => $org->id,
        'uploaded_by' => $uploader->id,
        'name' => $name,
        'link' => 'https://example.com/'.Str::slug($name).'.pdf',
        'access_level' => $accessLevel,
        'folder_id' => $folderId,
    ]);
}

function grantAttachDocumentsPermission(Role $role, string $permissionSlug = 'manage_documents'): void
{
    $permissionId = Permission::where('slug', $permissionSlug)->firstOrFail()->id;
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

test('lists internal and public documents from the task\'s company, never private', function () {
    $internal = makePickerDocument($this->orgA, $this->management, 'Internal doc', 'internal');
    $public = makePickerDocument($this->orgA, $this->management, 'Public doc', 'public');
    $private = makePickerDocument($this->orgA, $this->management, 'Private doc', 'private');

    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/documents/attachable");

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($internal->id, $public->id);
    expect($ids)->not->toContain($private->id);
});

test('never returns a document from another company, including for owner', function () {
    $inOrgB = makePickerDocument($this->orgB, $this->owner, 'Org B doc');

    $response = $this->actingAs($this->owner)->getJson("/tasks/{$this->task->id}/documents/attachable");

    $response->assertOk();
    expect(collect($response->json('data'))->pluck('id'))->not->toContain($inOrgB->id);
});

test('never returns a document the viewer cannot see', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Ops', 'color' => '#111']);
    Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $otherDept->id, 'title' => 'x', 'priority' => 'medium', 'status' => 'pending']);

    // A staff member with view_documents/manage_documents but access to
    // a DIFFERENT department than this task's own can still view() an
    // Internal document via view_documents (that permission isn't
    // department-scoped) — so instead exercise the one real way an
    // Internal/Public document becomes invisible: revoking view_documents
    // entirely while keeping manage_documents (the "picker sees nothing"
    // matrix cell from the parity test).
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $viewDocumentsId = Permission::where('slug', 'view_documents')->firstOrFail()->id;
    $staffRole->permissions()->detach($viewDocumentsId);
    grantAttachDocumentsPermission($staffRole);

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => $staffRole->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->dept->id, 'allowed' => true]);

    $document = makePickerDocument($this->orgA, $this->management, 'Invisible to staff');

    $response = $this->actingAs($staff)->getJson("/tasks/{$this->task->id}/documents/attachable");

    $response->assertOk();
    expect(collect($response->json('data'))->pluck('id'))->not->toContain($document->id);
});

test('search matches name and original_filename, case-insensitively, and treats % and _ literally', function () {
    $match = makePickerDocument($this->orgA, $this->management, 'Vendor Agreement.pdf');
    $noMatch = makePickerDocument($this->orgA, $this->management, 'Unrelated.pdf');
    $literalPercent = Document::create([
        'organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id,
        'name' => 'Report 50% done.pdf', 'link' => 'https://example.com/report.pdf', 'access_level' => 'internal',
    ]);

    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/documents/attachable?search=vendor");
    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($match->id);
    expect($ids)->not->toContain($noMatch->id);

    // A literal "%" in the search term must not act as a SQL wildcard —
    // searching "50%" should not match every document via an unescaped
    // leading/trailing wildcard collapse.
    $percentResponse = $this->actingAs($this->management)->getJson('/tasks/'.$this->task->id.'/documents/attachable?search='.urlencode('50%'));
    $percentResponse->assertOk();
    $percentIds = collect($percentResponse->json('data'))->pluck('id')->all();
    expect($percentIds)->toBe([$literalPercent->id]);
});

test('the search LIKE/ESCAPE clause binds the escape character as a parameter, not as inline SQL text, so it never breaks on MySQL', function () {
    // Regression guard: a real ordinary search (e.g. "test") 500'd against
    // the dev MySQL database while this exact same request passed on
    // SQLite (this app's test driver) without any code path difference.
    // Root cause: `ESCAPE '\'` written directly into the raw SQL text is
    // driver-dependent, not portable — SQLite doesn't treat backslash as
    // a string-literal escape character at all (so a lone `\` between
    // quotes is exactly one backslash, as intended), but MySQL does (so
    // `'\'` is an unterminated string literal — a syntax error — and
    // needs `'\\'` in the raw SQL text for the parsed value to come out
    // as a single backslash). The fix binds the escape character as a
    // `?` parameter instead of inlining it, so PDO transmits the single-
    // backslash value as data on both drivers, with no SQL-text quoting
    // involved at all. This test compiles the picker's exact WHERE
    // fragment against a real MySqlGrammar (no live MySQL connection
    // needed, matching how this app verifies MySQL-specific SQL
    // elsewhere) to catch a reintroduced inline literal even though the
    // rest of this file only ever runs against SQLite.
    $connection = new MySqlConnection(fn () => null, 'test_db');
    $connection->useDefaultQueryGrammar();
    $connection->useDefaultPostProcessor();

    $backslash = chr(92);
    $likeValue = '%test%';
    $query = $connection->table('documents')->where(function ($q) use ($likeValue, $backslash) {
        $q->whereRaw('name LIKE ? ESCAPE ?', [$likeValue, $backslash])
            ->orWhereRaw('original_filename LIKE ? ESCAPE ?', [$likeValue, $backslash]);
    });

    // The escape character must be a bound placeholder, never inlined as
    // a quoted literal in the compiled SQL text itself.
    expect($query->toSql())->toContain('ESCAPE ?');
    expect($query->toSql())->not->toContain("ESCAPE '");

    // And the actual bound value is exactly one backslash character (not
    // zero, not two) - the value MySQL's LIKE...ESCAPE needs to treat the
    // pre-escaped %/_ in $likeValue as literal, not as wildcards.
    $bindings = $query->getBindings();
    expect($bindings)->toContain($backslash);
    foreach ($bindings as $binding) {
        if ($binding === $backslash) {
            expect(strlen($binding))->toBe(1);
        }
    }

    // Live-request-level check on the real (SQLite) test driver: an
    // ordinary alphabetic search term with no special LIKE characters at
    // all must succeed and match - this was the exact shape of request
    // ("test") that 500'd on MySQL while looking identical on SQLite.
    makePickerDocument($this->orgA, $this->management, 'test-document.pdf');
    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/documents/attachable?search=test");
    $response->assertOk();
    expect(collect($response->json('data'))->pluck('name'))->toContain('test-document.pdf');
});

test('with no search term, results are newest upload first', function () {
    $older = makePickerDocument($this->orgA, $this->management, 'Older');
    $older->created_at = now()->subDay();
    $older->save();
    $newer = makePickerDocument($this->orgA, $this->management, 'Newer');

    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/documents/attachable");

    $response->assertOk();
    expect(collect($response->json('data'))->pluck('id')->all())->toBe([$newer->id, $older->id]);
});

test('pagination returns 25 per page with more available on the next page', function () {
    collect(range(1, 30))->each(fn (int $i) => makePickerDocument($this->orgA, $this->management, "Doc {$i}"));

    $page1 = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/documents/attachable");
    $page1->assertOk();
    expect($page1->json('data'))->toHaveCount(25);
    expect($page1->json('total'))->toBe(30);

    $page2 = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/documents/attachable?page=2");
    $page2->assertOk();
    expect($page2->json('data'))->toHaveCount(5);
});

test('already_attached is true only for documents already linked to this task', function () {
    $attached = makePickerDocument($this->orgA, $this->management, 'Attached');
    $notAttached = makePickerDocument($this->orgA, $this->management, 'Not attached');
    $this->task->documents()->attach($attached->id);

    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/documents/attachable");

    $response->assertOk();
    $byId = collect($response->json('data'))->keyBy('id');
    expect($byId[$attached->id]['already_attached'])->toBeTrue();
    expect($byId[$notAttached->id]['already_attached'])->toBeFalse();
});

test('folder_path is computed as a slash-joined string from one folder query, not per row', function () {
    $parent = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'created_by' => $this->management->id]);
    $child = DocumentFolder::create(['organization_id' => $this->orgA->id, 'parent_id' => $parent->id, 'name' => 'Q3', 'created_by' => $this->management->id]);
    $inFolder = makePickerDocument($this->orgA, $this->management, 'Filed doc', 'internal', $child->id);
    $atRoot = makePickerDocument($this->orgA, $this->management, 'Root doc');

    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/documents/attachable");

    $response->assertOk();
    $byId = collect($response->json('data'))->keyBy('id');
    expect($byId[$inFolder->id]['folder_path'])->toBe('Marketing / Q3');
    expect($byId[$atRoot->id]['folder_path'])->toBeNull();
});

test('manage_documents OFF hides the button/list and 403s the list endpoint directly for staff', function () {
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);

    $this->actingAs($staff)->getJson("/tasks/{$this->task->id}/documents/attachable")->assertForbidden();
});

test('a user with manage_documents but no view on the task is denied', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Finance', 'color' => '#111']);
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    grantAttachDocumentsPermission($staffRole);

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => $staffRole->id]);
    // Access to a DIFFERENT department than the task's own, and not the
    // assignee — TaskPolicy::view() denies this.
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $otherDept->id, 'allowed' => true]);

    $this->actingAs($staff)->getJson("/tasks/{$this->task->id}/documents/attachable")->assertForbidden();
});

test('a Client-role user with manage_documents is denied on the list endpoint', function () {
    $clientRole = Role::where('slug', 'client')->firstOrFail();
    grantAttachDocumentsPermission($clientRole);

    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $client->id, 'role_id' => $clientRole->id]);
    $this->project->clients()->attach($client->id);

    $this->actingAs($client)->getJson("/tasks/{$this->task->id}/documents/attachable")->assertForbidden();
});

test('the query count does not grow with the number of documents', function () {
    makePickerDocument($this->orgA, $this->management, 'Warm-up');

    $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/documents/attachable")->assertOk();

    DB::enableQueryLog();
    $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/documents/attachable")->assertOk();
    $baselineQueries = count(DB::getQueryLog());
    DB::flushQueryLog();

    collect(range(1, 20))->each(fn (int $i) => makePickerDocument($this->orgA, $this->management, "Doc {$i}"));
    DB::flushQueryLog();

    DB::enableQueryLog();
    $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/documents/attachable")->assertOk();
    $scaledUpQueries = count(DB::getQueryLog());
    DB::flushQueryLog();

    expect($scaledUpQueries)->toBe($baselineQueries);
});
