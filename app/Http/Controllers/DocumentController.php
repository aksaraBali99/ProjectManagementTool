<?php

namespace App\Http\Controllers;

use App\Enums\DocumentAccessLevel;
use App\Exceptions\FileStorageException;
use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Models\Document;
use App\Models\Organization;
use App\Models\Task;
use App\Services\DocumentUploadService;
use App\Services\FileStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    use ResolvesCurrentOrganization;

    public function index(?Organization $organization = null): View
    {
        $user = auth()->user();

        // task #73 phase 1: the Documents page itself now requires
        // view_documents somewhere, for every role including management —
        // no separate role check, and no exception for Client (who never
        // holds it, see PermissionSeeder / the forced-state map on the
        // Role Permissions screen). See canAccessDocumentsPage()'s own
        // docblock for why a user with no organization membership at all
        // still passes this (a 404-style "nothing here for you yet" empty
        // state, not a 403 — there's no page they were denied).
        abort_unless($user->canAccessDocumentsPage(), 403);

        $organizations = Organization::whereIn('id', $user->documentOrganizationIds())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($organizations->isEmpty()) {
            return view('documents.index', [
                'organizations' => $organizations,
                'organization' => null,
                'documents' => collect(),
                'canManage' => false,
            ]);
        }

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        $documents = Document::where('organization_id', $organization->id)
            ->with('uploader')
            ->get()
            ->filter(fn (Document $document) => Gate::allows('view', $document))
            ->sortBy('name')
            ->values();

        return view('documents.index', [
            'organizations' => $organizations,
            'organization' => $organization,
            'documents' => $documents,
            'canManage' => Gate::allows('create', [Document::class, $organization->id]),
        ]);
    }

    public function create(?Organization $organization = null): View
    {
        $manageableOrgIds = auth()->user()->documentManageableOrganizationIds();
        abort_if(empty($manageableOrgIds), 403);

        $organizations = Organization::whereIn('id', $manageableOrgIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        abort_if($organizations->isEmpty(), 403);

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        Gate::authorize('create', [Document::class, $organization->id]);

        return view('documents.create', [
            'organization' => $organization,
            // task #73 phase 1: a Client-role uploader (manage_documents
            // stays tickable for Client) never sees the access-level
            // dropdown at all — the form always saves Public for them,
            // enforced server-side regardless in
            // DocumentUploadService::resolveAccessLevel().
            'isClientUploader' => auth()->user()->isClientInOrg($organization->id),
        ]);
    }

    /**
     * Creates a document in a company's library. Three callers, same
     * endpoint: the standalone Add Document page (from_documents_page=1,
     * redirects to the Documents list), the Task list page's "+ Add new
     * document" button (plain form POST, no task_id, redirects back to
     * the Task list), and the Task Edit page's inline "add new document"
     * (AJAX, includes task_id to attach the new document to that task in
     * the same step) — each of those three now offers "Add link" (this
     * endpoint's original, still-supported shape) alongside a new "Upload
     * file" option (task #73 phase 1), never both at once.
     *
     * organization_id always comes from this validated request body, not
     * from anything else the client could tamper with — Gate::authorize
     * below is what actually keeps it honest (a request naming an
     * organization_id the caller can't manage_documents in is rejected
     * regardless of what value it claims).
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:2048', 'url', 'required_without:file', 'prohibits:file'],
            'file' => ['nullable', 'file', 'required_without:link', 'prohibits:link'],
            'access_level' => ['required', Rule::enum(DocumentAccessLevel::class)],
            'task_id' => ['nullable', 'integer', 'exists:tasks,id'],
        ], [], [
            'organization_id' => 'company',
        ]);

        Gate::authorize('create', [Document::class, $data['organization_id']]);

        $task = null;
        if (! empty($data['task_id'])) {
            $task = Task::findOrFail($data['task_id']);
            // 'view', not 'update' — creating and attaching a new document
            // is authorized entirely by DocumentPolicy::create() above
            // (manage_documents), a deliberately separate capability from
            // editing the task itself (see TaskManagementController::edit()'s
            // own docblock for $canManageDocuments). This only needs to
            // confirm the task is one the uploader can at least see, so a
            // manage_documents holder in one company can't attach to a
            // task in an org/project they have no visibility into at all
            // — task #73 phase 1: this is also what lets a Client with
            // manage_documents (who can never pass TaskPolicy::update)
            // attach a document to a task on their own project.
            Gate::authorize('view', $task);

            if ($task->organization_id !== $data['organization_id']) {
                abort(422, 'Document must belong to the task\'s company.');
            }
        }

        $uploader = auth()->user();
        $uploadService = app(DocumentUploadService::class);
        // A Client-role uploader (manage_documents stays tickable for
        // Client, see PermissionSeeder) never sees the access-level
        // dropdown and is always saved as Public — resolveAccessLevel()
        // is the one place that's enforced, shared with the file-upload
        // branch below via DocumentUploadService::createRecord().
        $accessLevel = $uploadService->resolveAccessLevel(
            $uploader,
            $data['organization_id'],
            DocumentAccessLevel::from($data['access_level']),
        );

        try {
            if ($request->hasFile('file')) {
                // origin_task_id/key layout differ by which of the two
                // upload places this came from (see DocumentUploadService's
                // own docblock): a task-scoped upload keys under
                // tasks/{id}/..., a Documents-page one under
                // organizations/{id}/....
                $document = $task !== null
                    ? $uploadService->uploadForTask($request->file('file'), $task, $accessLevel, $uploader, $data['name'])
                    : $uploadService->uploadForOrganization($request->file('file'), $data['organization_id'], $accessLevel, $uploader, $data['name']);
            } else {
                $document = Document::create([
                    'organization_id' => $data['organization_id'],
                    'uploaded_by' => $uploader->id,
                    'name' => $data['name'],
                    'link' => $data['link'],
                    'access_level' => $accessLevel,
                ]);

                if ($task !== null) {
                    $task->documents()->syncWithoutDetaching([$document->id]);
                }
            }
        } catch (FileStorageException $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withErrors(['file' => $e->getMessage()])->withInput();
        }

        if ($request->expectsJson()) {
            return response()->json(['document' => $document], 201);
        }

        if ($request->boolean('from_documents_page')) {
            return redirect()->route('documents.index', $document->organization_id)->with('status', 'Document added.');
        }

        return back()->with('status', 'Document added.');
    }

    /**
     * Every place a document/file-chip link is followed (the Documents
     * page, a task's Documents tab, a file-chip clicked inside a saved
     * description/comment) routes through here instead of linking
     * straight at `$document->link`, so a document this app itself
     * uploaded downloads under its real original name instead of the
     * bare tasks/{id}/documents/{uuid}.ext storage key a direct link
     * would save as.
     *
     * Takes the link itself, not a {document} route-bound id: a file-chip
     * embedded in already-saved rich text only ever has the raw URL (see
     * file-chip-extension.js) — there's no document_id anywhere in that
     * HTML to bind against — so resolving by the exact `link` value is
     * what one endpoint can serve both that and the two blade views that
     * do have a real Document on hand equally. The exact-match lookup
     * doubles as the only access check this needs against being handed an
     * arbitrary attacker-supplied URL: nothing resolves unless some
     * existing Document row's `link` already equals it verbatim.
     */
    public function download(Request $request): StreamedResponse|RedirectResponse
    {
        $data = $request->validate(['url' => ['required', 'string', 'max:2048']]);

        $document = Document::where('link', $data['url'])->first();

        // task #73 phase 1: a document uploaded through either new Phase 1
        // upload endpoint has link=null (see DocumentUploadService's own
        // docblock — an uploaded file's URL is always the computed one,
        // Document::url(), never a stored `link`), so the exact-`link`
        // lookup above can never find it. Reverse-resolve the storage_key
        // it was computed from instead, via the same disk/base-URL logic
        // Document::url() itself uses.
        if ($document === null) {
            $path = app(FileStorageService::class)->pathFromUrl($data['url']);

            if ($path !== null) {
                $document = Document::where('storage_key', $path)->first();
            }
        }

        abort_unless($document !== null, 404);

        // The stricter DocumentPolicy::view isn't the only door in here on
        // purpose — RichTextDocumentController's own docblock already
        // establishes that a document attached via a file-chip is meant
        // to be visible to anyone who can view the task it's attached to,
        // not gated behind the separate view_documents/management-tier
        // rule DocumentPolicy::view enforces for the standalone Documents
        // page. Either door being open is enough. Preserved unchanged for
        // the new storage_key resolution path too — it authorizes against
        // the resolved Document exactly the same way regardless of which
        // lookup found it.
        $authorized = Gate::allows('view', $document)
            || $document->tasks()->get()->contains(fn (Task $task) => Gate::allows('view', $task));
        abort_unless($authorized, 403);

        // $document->url, not ->link: the computed accessor is the only
        // value guaranteed non-null for both a legacy link-only document
        // and a Phase 1 upload (whose `link` column is null).
        return app(FileStorageService::class)->download($document->url, $document->name)
            ?? redirect()->away($document->url);
    }
}
