<?php

use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Policies\DocumentPolicy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

/**
 * task #73 phase 2: DocumentPolicy::viewableIds() is a batched stand-in
 * for calling Gate::allows('view', $document) per document — this asserts
 * the two never disagree, across every branch DocumentPolicy::view() has.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->uploader = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $this->uploader->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
});

function makeDocumentSetForParity(Organization $org, User $uploader): Collection
{
    return collect([
        Document::create(['organization_id' => $org->id, 'uploaded_by' => $uploader->id, 'name' => 'Private', 'link' => 'https://example.com/private.pdf', 'access_level' => 'private']),
        Document::create(['organization_id' => $org->id, 'uploaded_by' => $uploader->id, 'name' => 'Internal', 'link' => 'https://example.com/internal.pdf', 'access_level' => 'internal']),
        Document::create(['organization_id' => $org->id, 'uploaded_by' => $uploader->id, 'name' => 'Public', 'link' => 'https://example.com/public.pdf', 'access_level' => 'public']),
    ]);
}

/** Asserts Gate::allows('view', ...) and viewableIds() agree, per document. */
function assertDocumentViewParity(User $user, Collection $documents, int $organizationId): void
{
    $batchedIds = app(DocumentPolicy::class)->viewableIds($user, $organizationId, $documents)->all();

    foreach ($documents as $document) {
        $gateResult = Gate::forUser($user)->allows('view', $document);
        $batchedResult = in_array($document->id, $batchedIds, true);

        expect($batchedResult)->toBe($gateResult, "Mismatch for document [{$document->name}] ({$document->access_level->value})");
    }
}

test('owner sees every access level', function () {
    $documents = makeDocumentSetForParity($this->org, $this->uploader);
    assertDocumentViewParity($this->owner, $documents, $this->org->id);
    expect(app(DocumentPolicy::class)->viewableIds($this->owner, $this->org->id, $documents))->toHaveCount(3);
});

test('management sees every access level', function () {
    $management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    $documents = makeDocumentSetForParity($this->org, $this->uploader);
    assertDocumentViewParity($management, $documents, $this->org->id);
    expect(app(DocumentPolicy::class)->viewableIds($management, $this->org->id, $documents))->toHaveCount(3);
});

test('a view_documents holder sees internal/public but private only if they uploaded it', function () {
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);

    $documents = makeDocumentSetForParity($this->org, $this->uploader);
    assertDocumentViewParity($staff, $documents, $this->org->id);
    expect(app(DocumentPolicy::class)->viewableIds($staff, $this->org->id, $documents)->all())
        ->toBe([$documents[1]->id, $documents[2]->id]);

    // The uploader themselves, also a view_documents holder, sees their
    // own private document too.
    assertDocumentViewParity($this->uploader, $documents, $this->org->id);
    expect(app(DocumentPolicy::class)->viewableIds($this->uploader, $this->org->id, $documents))->toHaveCount(3);
});

test('view_documents revoked leaves only the client-of-project path, and that never surfaces private/internal', function () {
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $viewDocumentsId = Permission::where('slug', 'view_documents')->firstOrFail()->id;
    $staffRole->permissions()->detach($viewDocumentsId);

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $staff->id, 'role_id' => $staffRole->id]);

    $documents = makeDocumentSetForParity($this->org, $this->uploader);
    assertDocumentViewParity($staff, $documents, $this->org->id);
    expect(app(DocumentPolicy::class)->viewableIds($staff, $this->org->id, $documents))->toBeEmpty();
});

test('a client sees a public document only when linked to a task on their own project, never internal or private', function () {
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->firstOrFail()->id]);
    $this->project->clients()->attach($client->id);

    $task = Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->project->id,
        'department_id' => Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000'])->id,
        'title' => 'T', 'priority' => 'medium', 'status' => 'pending',
    ]);

    $documents = makeDocumentSetForParity($this->org, $this->uploader);
    $documents->each(fn (Document $d) => $task->documents()->attach($d->id));

    assertDocumentViewParity($client, $documents, $this->org->id);
    expect(app(DocumentPolicy::class)->viewableIds($client, $this->org->id, $documents)->all())->toBe([$documents[2]->id]);
});

test('a client sees nothing once the only linking task is deactivated (soft-deleted), matching view()', function () {
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->firstOrFail()->id]);
    $this->project->clients()->attach($client->id);

    $task = Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->project->id,
        'department_id' => Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000'])->id,
        'title' => 'T', 'priority' => 'medium', 'status' => 'pending',
    ]);

    $documents = makeDocumentSetForParity($this->org, $this->uploader);
    $documents->each(fn (Document $d) => $task->documents()->attach($d->id));
    $task->delete();

    assertDocumentViewParity($client, $documents, $this->org->id);
    expect(app(DocumentPolicy::class)->viewableIds($client, $this->org->id, $documents))->toBeEmpty();
});

test('a client with no linking task at all sees nothing', function () {
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->firstOrFail()->id]);
    $this->project->clients()->attach($client->id);

    $documents = makeDocumentSetForParity($this->org, $this->uploader);

    assertDocumentViewParity($client, $documents, $this->org->id);
    expect(app(DocumentPolicy::class)->viewableIds($client, $this->org->id, $documents))->toBeEmpty();
});

test('viewableIds() returns an empty collection for an empty document set', function () {
    expect(app(DocumentPolicy::class)->viewableIds($this->owner, $this->org->id, collect())->isEmpty())->toBeTrue();
});
