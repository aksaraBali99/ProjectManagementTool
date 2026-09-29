<?php

use App\Exceptions\DocumentAlreadyAttachedException;
use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskDocumentLinker;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Covers TaskDocumentLinker::attach()'s own exception-classification logic
 * directly (bypassing the HTTP endpoint), since this is about the service's
 * own SQLSTATE-23000-handling code, not any one caller's request/response
 * shape.
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

function makeLinkerTestDocument(Organization $org, User $uploader): Document
{
    return Document::create([
        'organization_id' => $org->id, 'uploaded_by' => $uploader->id,
        'name' => 'Doc.pdf', 'link' => 'https://example.com/doc.pdf', 'access_level' => 'internal',
    ]);
}

test('a genuine primary-key duplicate raised as a database-level race is reported as DocumentAlreadyAttachedException', function () {
    $document = makeLinkerTestDocument($this->org, $this->management);
    $linker = app(TaskDocumentLinker::class);

    // Bypasses the service's own exists() pre-check by inserting the pivot
    // row directly, so attach()'s INSERT below can only ever hit the
    // database's own primary-key constraint - exactly what a genuine
    // concurrent race (two requests both passing the exists() check before
    // either commits) looks like from inside the try/catch.
    DB::table('task_documents')->insert(['task_id' => $this->task->id, 'document_id' => $document->id]);

    expect(fn () => $linker->attach($this->task, $document))->toThrow(DocumentAlreadyAttachedException::class);
});

test('a foreign-key violation on the same SQLSTATE is never mislabeled as a duplicate attach', function () {
    // Regression guard: a bare `$e->getCode() === '23000'` check used to
    // treat ANY integrity-constraint violation as "Already attached" -
    // but task_documents' task_id/document_id columns are also foreign
    // keys (cascadeOnDelete'd to tasks/documents), and a concurrent delete
    // of either row between the service's exists() check and its INSERT
    // raises the identical SQLSTATE for a real data-integrity failure, not
    // a duplicate. Simulated here with a Document instance whose id was
    // never actually persisted, so the INSERT can only violate the
    // document_id foreign key - never the primary key, since no row with
    // this (task_id, document_id) pair exists to collide with.
    $phantomDocument = new Document([
        'organization_id' => $this->org->id, 'uploaded_by' => $this->management->id,
        'name' => 'Phantom.pdf', 'link' => 'https://example.com/phantom.pdf', 'access_level' => 'internal',
    ]);
    $phantomDocument->id = 999999;
    $phantomDocument->exists = true;

    $linker = app(TaskDocumentLinker::class);

    // Asserting QueryException specifically (not just "any exception") is
    // the actual regression guard: DocumentAlreadyAttachedException is an
    // unrelated class, so if the fix regressed and swallowed this as a
    // duplicate again, this assertion would fail with the wrong exception
    // type rather than passing for an unrelated reason.
    expect(fn () => $linker->attach($this->task, $phantomDocument))->toThrow(QueryException::class);

    expect($this->task->documents()->count())->toBe(0);
});
