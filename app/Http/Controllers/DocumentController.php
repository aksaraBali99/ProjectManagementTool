<?php

namespace App\Http\Controllers;

use App\Enums\DocumentAccessLevel;
use App\Exceptions\FileStorageException;
use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Organization;
use App\Models\Task;
use App\Policies\DocumentPolicy;
use App\Services\DocumentDependencyService;
use App\Services\DocumentUploadService;
use App\Services\FileStorageService;
use App\Services\TaskDocumentLinker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DocumentController extends Controller
{
    use ResolvesCurrentOrganization;

    /**
     * task #73 phase 2: folders. $folder comes from a ?folder=ID query
     * value, not a route segment — company tabs keep working exactly as
     * before (route('documents.index', $tab), no folder param), which is
     * what makes "switching company tab goes to that company's root"
     * fall out for free rather than needing special-casing.
     *
     * No per-row queries anywhere below: edit/delete rights are two plain
     * booleans computed ONCE for the whole company ($hasManageDocuments
     * for documents, $canManageAnyFolder for folders, both already
     * excluding whichever roles could never pass regardless of row), a
     * single grouped query for linked-task counts, and one batched
     * Task::viewableIdsFor() call (not one Gate::allows() per document)
     * for the origin column.
     */
    public function index(Request $request, ?Organization $organization = null): View
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
                'folder' => null,
                'breadcrumb' => collect(),
                'folders' => collect(),
                'documents' => collect(),
                'allFolders' => collect(),
                'canManage' => false,
                'canManageFolders' => false,
                'hasManageDocuments' => false,
                'canManageAnyFolder' => false,
                'isPrivilegedManager' => false,
                'linkedTaskCounts' => collect(),
                'linkedFolderTaskCounts' => collect(),
                'originTasks' => collect(),
            ]);
        }

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        // An unknown or cross-company folder id 404s — DocumentFolder's
        // own BelongsToOrganization global scope already excludes any
        // folder outside $user->visibleOrganizationIds(), and this
        // ->where('organization_id', ...) on top of that further confirms
        // it's specifically the CURRENT tab's company, not merely some
        // other visible one.
        $folder = null;
        if ($folderId = $request->integer('folder')) {
            $folder = DocumentFolder::where('organization_id', $organization->id)->find($folderId);
            abort_if($folder === null, 404);
        }

        $breadcrumb = collect();
        for ($cursor = $folder; $cursor !== null; $cursor = $cursor->parent) {
            $breadcrumb->prepend($cursor);
        }

        $folders = DocumentFolder::where('organization_id', $organization->id)
            ->where('parent_id', $folder?->id)
            ->with('creator')
            ->orderBy('name')
            ->get();

        $allDocuments = Document::where('organization_id', $organization->id)
            ->where('folder_id', $folder?->id)
            ->with('uploader')
            ->get();

        // DocumentPolicy::viewableIds(), not a Gate::allows('view', ...)
        // call per document — the batched form of the exact same rule
        // (see its own docblock and DocumentViewableIdsParityTest).
        $viewableDocumentIds = app(DocumentPolicy::class)->viewableIds($user, $organization->id, $allDocuments);
        $documents = $allDocuments->whereIn('id', $viewableDocumentIds->all())->sortBy('name')->values();

        // One grouped query for every document on this page, not one
        // count() per row.
        $linkedTaskCounts = DB::table('task_documents')
            ->whereIn('document_id', $documents->pluck('id'))
            ->selectRaw('document_id, count(*) as aggregate')
            ->groupBy('document_id')
            ->pluck('aggregate', 'document_id');

        // task #73 phase 4: the folder equivalent — "linked to N tasks" on
        // a folder row, same one-grouped-query-not-one-per-row shape as
        // the document count above. Plain count, not the documents-page
        // count's hover-popover treatment — that popover is hand-rolled,
        // page-inline JS tightly coupled to document rows, not a reusable
        // component; not worth genericizing for this phase.
        $linkedFolderTaskCounts = DB::table('task_folder_links')
            ->whereIn('folder_id', $folders->pluck('id'))
            ->selectRaw('folder_id, count(*) as aggregate')
            ->groupBy('folder_id')
            ->pluck('aggregate', 'folder_id');

        // Origin column: "if origin_task_id is set, link to the task, but
        // only when the viewer can view it" — one batched visibility
        // check for every origin task on this page, then one query for
        // their titles, rather than a Gate::allows('view', $task) call
        // per document.
        $originTaskIds = $documents->pluck('origin_task_id')->filter()->unique()->values()->all();
        $viewableOriginTaskIds = Task::viewableIdsFor($user, $originTaskIds);
        $originTasks = Task::whereIn('id', $viewableOriginTaskIds->all())->get(['id', 'title'])->keyBy('id');

        // Edit/delete rights, computed ONCE for the whole company: the
        // per-row comparison in the view is then just
        // "$isPrivilegedManager || $document->uploaded_by === $user->id"
        // (documents) or "... || $folder->created_by === $user->id"
        // (folders) — a plain boolean comparison, no further queries.
        // Client is excluded for documents (DocumentPolicy::canManage()'s
        // own unconditional block) but NOT for folders (DocumentFolderPolicy
        // has no such restriction) — two separate booleans on purpose,
        // not one shared flag, so this mirrors each policy exactly.
        $hasManageDocuments = ! $user->isClientInOrg($organization->id)
            && $user->hasPermission('manage_documents', $organization->id);
        $canManageAnyFolder = $user->hasPermission('manage_documents', $organization->id);
        $isPrivilegedManager = $user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($organization->id);

        // Every folder in the company (not just the current level) — the
        // Edit panel's "move to" dropdown can target any folder in the
        // company, not just a sibling of the document's current one.
        $allFolders = DocumentFolder::where('organization_id', $organization->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('documents.index', [
            'organizations' => $organizations,
            'organization' => $organization,
            'folder' => $folder,
            'breadcrumb' => $breadcrumb,
            'folders' => $folders,
            'documents' => $documents,
            'allFolders' => $allFolders,
            'canManage' => Gate::allows('create', [Document::class, $organization->id]),
            'canManageFolders' => Gate::allows('create', [DocumentFolder::class, $organization->id]),
            'hasManageDocuments' => $hasManageDocuments,
            'canManageAnyFolder' => $canManageAnyFolder,
            'isPrivilegedManager' => $isPrivilegedManager,
            'linkedTaskCounts' => $linkedTaskCounts,
            'linkedFolderTaskCounts' => $linkedFolderTaskCounts,
            'originTasks' => $originTasks,
        ]);
    }

    public function create(Request $request, ?Organization $organization = null): View
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

        // task #73 phase 2: "Uploads and add-link on the Documents page
        // go into the current folder" — carried forward from the index
        // page's own ?folder=ID via the "+ Add new document" link, and
        // validated against this company the same way index() validates
        // it (an unknown or cross-company folder id 404s here too).
        $folder = null;
        if ($folderId = $request->integer('folder')) {
            $folder = DocumentFolder::where('organization_id', $organization->id)->find($folderId);
            abort_if($folder === null, 404);
        }

        return view('documents.create', [
            'organization' => $organization,
            'folder' => $folder,
            // task #73: the "+ New" menu's Upload file / Add link items
            // preselect this page's mode via ?mode=upload|link — a soft
            // UX preselection, not form data, so an unrecognized/missing
            // value just falls back to 'link' rather than erroring. The
            // view itself still lets old('_mode', ...) override this on a
            // validation-error redisplay, so resubmitting after a mistake
            // keeps whichever mode was actually being used, not resets to
            // whatever the link that opened the page originally asked for.
            'initialMode' => $request->query('mode') === 'upload' ? 'upload' : 'link',
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
            'folder_id' => ['nullable', 'integer'],
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

            // (int) cast, not a bare !== : $data['organization_id'] is a
            // STRING for a real multipart/form-data upload (raw HTTP
            // multipart fields are always text — the 'integer' validation
            // rule above only checks the format, it doesn't cast the
            // type), while $task->organization_id is a genuine PHP int
            // (Laravel's default PDO connector uses native, not emulated,
            // prepared statements). A strict compare between "1" and 1
            // incorrectly rejected every real-browser upload with a
            // task_id — never caught by tests, since Laravel's own
            // TestCase::post() preserves native types when mixing a file
            // with scalar fields instead of round-tripping through an
            // actual string-only multipart body the way a browser does.
            if ((int) $task->organization_id !== (int) $data['organization_id']) {
                abort(422, 'Document must belong to the task\'s company.');
            }
        }

        // task #73 phase 2: "Uploads and add-link on the Documents page
        // go into the current folder, with folder_id validated against
        // the company. Uploads from tasks, the editor, and other paths go
        // to the root." — folder_id is only ever honored here when there
        // is NO task in scope; a task-scoped request's folder_id (if any
        // was somehow submitted) is silently ignored, not merely
        // unvalidated, so a task-page request can never accidentally file
        // into a Documents-page folder.
        $folderId = null;
        if ($task === null && ! empty($data['folder_id'])) {
            $folder = DocumentFolder::where('organization_id', $data['organization_id'])->find($data['folder_id']);
            abort_if($folder === null, 404);
            $folderId = $folder->id;
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

        // task #73 phase 3: "private documents are never attached to
        // tasks" is unconditional — the same rule DocumentController::
        // update() already enforces for an EXISTING document being
        // switched to private while linked. Before this fix, a non-Client
        // uploader creating a document straight from the Task edit page's
        // inline upload/add-link form (task_id set) could pick Private
        // and attach it in the same step, which contradicted that rule.
        // Checked here, once, for both the upload and link branches below
        // (both share $accessLevel) — the access-level dropdown on that
        // form no longer offers Private at all, but this is what actually
        // enforces it regardless of what the client sends.
        if ($task !== null && $accessLevel === DocumentAccessLevel::Private) {
            abort(422, 'Private documents can\'t be attached to tasks. Change its access level first.');
        }

        try {
            if ($request->hasFile('file')) {
                // origin_task_id/key layout differ by which of the two
                // upload places this came from (see DocumentUploadService's
                // own docblock): a task-scoped upload keys under
                // tasks/{id}/..., a Documents-page one under
                // organizations/{id}/....
                $document = $task !== null
                    ? $uploadService->uploadForTask($request->file('file'), $task, $accessLevel, $uploader, $data['name'])
                    : $uploadService->uploadForOrganization($request->file('file'), $data['organization_id'], $accessLevel, $uploader, $data['name'], $folderId);
            } else {
                $document = Document::create([
                    'organization_id' => $data['organization_id'],
                    'uploaded_by' => $uploader->id,
                    'name' => $data['name'],
                    'link' => $data['link'],
                    'access_level' => $accessLevel,
                    'folder_id' => $folderId,
                    // task #73 phase 2 origin-coverage fix: this link
                    // branch previously never set origin_task_id even
                    // when task_id was given (the inline add-link form on
                    // the Task edit page) — the Documents page's origin
                    // column relies on it being accurate for every
                    // creation path that actually starts from a task.
                    'origin_task_id' => $task?->id,
                ]);

                if ($task !== null) {
                    app(TaskDocumentLinker::class)->attach($task, $document);
                }
            }
        } catch (FileStorageException $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withErrors(['file' => $e->getMessage()])->withInput();
        }

        if ($request->expectsJson()) {
            // 'uploader' loaded so the merged attach panel's create-and-
            // attach success handler (appendDocumentRow()) can show who
            // added it, same as the picker's own attach response already
            // does — without this, doc.uploader.name would be undefined
            // for a document created (not picked) from a task.
            return response()->json(['document' => $document->load('uploader')], 201);
        }

        if ($request->boolean('from_documents_page')) {
            // Lands back in the same folder the document was just added
            // to, not the company root — $folderId is null for a
            // root-level add, which array_filter() then drops entirely.
            return redirect()->route('documents.index', array_filter(['organization' => $document->organization_id, 'folder' => $folderId]))
                ->with('status', 'Document added.');
        }

        return back()->with('status', 'Document added.');
    }

    /**
     * task #73 phase 2: the ONE preview endpoint both the Edit dialog
     * (blocked-to-private / confirm-to-public) and the Delete dialog call
     * before showing anything — same authorization as actually editing or
     * deleting (DocumentPolicy::canManage(), shared by update()/delete()
     * below), since this reveals linked task titles that gate applies to
     * either way.
     *
     * task #73 phase 4: also previews the folder-derived warning (using
     * the document's CURRENT folder for both "old" and "new" — this
     * preview has no move/access-level change in mind yet, it's "if you
     * delete this file as-is, right now, does its folder membership
     * affect anything?"), so the Delete dialog can show the right state
     * up front instead of only discovering it when destroy() itself
     * re-checks. null when there's nothing to warn about.
     */
    public function dependencies(Document $document): JsonResponse
    {
        Gate::authorize('update', $document);

        $dependencyService = app(DocumentDependencyService::class);
        $viewer = auth()->user();

        $summary = $dependencyService->summarize($document, $viewer);
        $folderSummary = $dependencyService->folderDerivedSummary($viewer, $document->folder_id, $document->folder_id);

        return response()->json(array_merge($summary, [
            'folder_warning' => $folderSummary !== null ? $dependencyService->folderWarningResponse($folderSummary) : null,
        ]));
    }

    /**
     * task #73: the "Linked tasks" popover's lazy-loaded content. Gated on
     * view() (not update()'s manage_documents, like dependencies() above) —
     * this only reveals task titles/links, the same information the
     * Documents-page row itself already implies you can see, to anyone who
     * can already see the document; it isn't an edit/delete surface. A
     * viewer who can't view the document gets a plain 404, revealing
     * nothing about whether it exists.
     *
     * Deliberately NOT DocumentDependencyService::summarize(): that method
     * uses withTrashed() because deletion-blocking cares about a link to a
     * task even after the task is deactivated. This popover is a plain
     * "what is this attached to" display for the viewer to click into, so
     * a soft-deleted task is excluded entirely here — it never appears,
     * viewable or hidden, and doesn't count toward either total. That's
     * also why the popover's own totals can be lower than the "Linked
     * tasks" column count for a document attached only to deactivated
     * tasks (that column is a raw, scope-oblivious count and is explicitly
     * unchanged by this task).
     *
     * Three queries total regardless of how many tasks are linked: the
     * pivot-ordered id list, one batched Task::viewableIdsFor() call (one
     * query per organization among the linked tasks — always one in
     * practice, since attach()/store() only ever let a document link to a
     * task in its own company), and one title lookup for the at-most-10
     * capped result. No per-task queries.
     */
    public function linkedTasks(Document $document): JsonResponse
    {
        abort_unless(Gate::allows('view', $document), 404);

        $viewer = auth()->user();

        // Task's own SoftDeletingScope (applied automatically through this
        // Eloquent relation, unlike a raw task_documents join) is what
        // excludes a deactivated task here — "deleted tasks never appear".
        // Order is preserved from this query through every step below, so
        // "most recently linked" survives the visibility filter and the
        // cap without needing to re-sort afterward.
        $linkedTaskIds = $document->tasks()->orderByPivot('created_at', 'desc')->pluck('tasks.id')->all();

        $viewableTaskIds = Task::viewableIdsFor($viewer, $linkedTaskIds);
        $orderedViewableIds = collect($linkedTaskIds)->filter(fn (int $id) => $viewableTaskIds->contains($id))->values();

        $cappedIds = $orderedViewableIds->take(10);
        $titlesById = Task::whereIn('id', $cappedIds->all())->get(['id', 'title'])->keyBy('id');

        $tasks = $cappedIds->map(fn (int $id) => [
            'id' => $id,
            'title' => $titlesById[$id]->title,
            'url' => route('tasks.edit', $id),
        ])->values();

        return response()->json([
            'tasks' => $tasks,
            'viewable_total' => $orderedViewableIds->count(),
            'hidden_count' => count($linkedTaskIds) - $orderedViewableIds->count(),
        ]);
    }

    /**
     * Rename, move, and/or change access level — one endpoint, since the
     * three share the same authorization (DocumentPolicy::update()) and a
     * request can change any combination of them at once. Each field that
     * actually changes gets its own audit entry (document.renamed/
     * document.moved/document.access_level_changed), not one combined
     * "document.updated" — matching the task's own audit-action list.
     *
     * The access-level blocking/confirmation rules are re-derived from the
     * database here via DocumentDependencyService, never trusted from
     * whatever an earlier dependencies() preview call said — and, like
     * destroy(), that re-check and the actual field mutations happen
     * together inside one transaction with the row locked, so a task
     * attaching to this document between the check and the save can't
     * slip a Private/linked document past the block (the same race
     * destroy()'s own lockForUpdate() closes for deletion).
     *
     * task #73 phase 4: a SECOND, independent set of checks alongside the
     * direct-link ones above — whether this document's FOLDER membership
     * (not the document's own task_documents rows) is linked to any task.
     * Unlike the direct-link case, this is never a hard block: blocking
     * would force unlinking an entire folder just to remove, move, or
     * privatize one file inside it. Covers three distinct triggers with
     * one shared check (folderDerivedSummary() unions the old and new
     * folder's task links): moving OUT of a linked folder, moving INTO
     * one, and switching to Private while sitting in one (adapted from
     * the direct-link case's wording to say "through its folder").
     */
    public function update(Request $request, Document $document): JsonResponse
    {
        Gate::authorize('update', $document);

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'folder_id' => ['sometimes', 'nullable', 'integer'],
            'access_level' => ['sometimes', Rule::enum(DocumentAccessLevel::class)],
            'confirm_public_visibility' => ['sometimes', 'boolean'],
            'confirm_folder_effect' => ['sometimes', 'boolean'],
        ]);

        // Move: "the target must be a folder in the same company, or the
        // root. No rights over the folder are needed" — so the TARGET
        // folder's existence is resolved up front (organization_id is
        // immutable, so this particular lookup doesn't need to wait for
        // the lock below). What it actually means to move relative to the
        // document's CURRENT folder is resolved separately, inside the
        // transaction, from the locked row (see $targetFolderId below) —
        // code review follow-up: this used to default to $document->
        // folder_id, read before any lock, so a concurrent move landing
        // between this request's route-model-binding and the lock would
        // get silently reverted back to that stale value the moment this
        // request saved (this field wasn't even being changed, from the
        // caller's point of view).
        $requestedFolderId = array_key_exists('folder_id', $data) ? $data['folder_id'] : false;
        $targetFolder = null;
        if ($requestedFolderId !== false && $requestedFolderId !== null) {
            $targetFolder = DocumentFolder::where('organization_id', $document->organization_id)->find($requestedFolderId);
            abort_if($targetFolder === null, 404);
        }

        $dependencyService = app(DocumentDependencyService::class);
        $viewer = auth()->user();

        $result = DB::transaction(function () use ($document, $data, $requestedFolderId, $targetFolder, $dependencyService, $viewer, $request) {
            $locked = Document::whereKey($document->id)->lockForUpdate()->firstOrFail();
            $oldFolderId = $locked->folder_id;
            $targetFolderId = $requestedFolderId === false ? $oldFolderId : ($requestedFolderId === null ? null : $targetFolder->id);

            $accessLevelChanging = false;
            $requestedAccessLevel = null;

            if (array_key_exists('access_level', $data)) {
                $requestedAccessLevel = DocumentAccessLevel::from($data['access_level']);
                $accessLevelChanging = $requestedAccessLevel !== $locked->access_level;

                if ($accessLevelChanging) {
                    $summary = $dependencyService->summarize($locked, $viewer);

                    // "To private while attached directly to any task:
                    // blocked." Same response shape/wording as delete.
                    if ($requestedAccessLevel === DocumentAccessLevel::Private && $summary['linked_task_count'] > 0) {
                        return ['blocked' => true, 'response' => $dependencyService->blockedResponse($summary)];
                    }

                    // "To public from any other level while linked: requires
                    // explicit confirmation... Not blocked." — enforced as a
                    // required confirmation flag on the request, not merely
                    // a UI dialog: a request without it, when confirmation
                    // is actually needed, is rejected exactly like the
                    // private-block case, just with a different, non-
                    // blocking reason.
                    if ($requestedAccessLevel === DocumentAccessLevel::Public
                        && $summary['client_project_count'] > 0
                        && ! $request->boolean('confirm_public_visibility')) {
                        return ['blocked' => true, 'response' => [
                            'message' => "Visible to the clients of {$summary['client_project_count']} linked projects.",
                            'requires_confirmation' => true,
                            'client_project_count' => $summary['client_project_count'],
                            // task #73 phase 4: explicit now that a second
                            // requires_confirmation response shape exists
                            // with a different field (folderWarningResponse()'s
                            // confirm_folder_effect) — the shared JS handling
                            // reads this instead of hardcoding one name.
                            'confirm_field' => 'confirm_public_visibility',
                        ]];
                    }
                }
            }

            // task #73 phase 4: folder-derived warning — triggered by
            // EITHER a move (old folder != target folder, in either
            // direction) OR switching to Private while the folder isn't
            // changing (old === target). Both conditions feed the same
            // folderDerivedSummary(old, new) call: when the folder isn't
            // moving, old and new are the same value, so the union
            // collapses to just that one folder's tasks. Skipped
            // entirely — not even queried — when neither trigger applies
            // (a plain rename, or an access-level change that isn't a
            // move to Private), and skipped once explicitly confirmed.
            $folderMoving = $targetFolderId !== $oldFolderId;
            $goingPrivate = $accessLevelChanging && $requestedAccessLevel === DocumentAccessLevel::Private;

            if (($folderMoving || $goingPrivate) && ! $request->boolean('confirm_folder_effect')) {
                $folderSummary = $dependencyService->folderDerivedSummary($viewer, $oldFolderId, $targetFolderId);

                if ($folderSummary !== null) {
                    return ['blocked' => true, 'response' => $dependencyService->folderWarningResponse($folderSummary)];
                }
            }

            $auditEntries = [];

            if (array_key_exists('name', $data) && $data['name'] !== $locked->name) {
                $auditEntries[] = ['action' => 'document.renamed', 'changes' => ['name' => ['old' => $locked->name, 'new' => $data['name']]]];
                $locked->name = $data['name'];
            }

            if ($targetFolderId !== $locked->folder_id) {
                $auditEntries[] = ['action' => 'document.moved', 'changes' => ['folder_id' => ['old' => $locked->folder_id, 'new' => $targetFolderId]]];
                $locked->folder_id = $targetFolderId;
            }

            // $accessLevelChanging/$requestedAccessLevel reused from above
            // (code review follow-up: this used to recompute both from
            // $data['access_level'] a second time, identically — a future
            // edit to one copy's condition could silently diverge from
            // the other).
            if ($accessLevelChanging) {
                $auditEntries[] = ['action' => 'document.access_level_changed', 'changes' => ['access_level' => ['old' => $locked->access_level->value, 'new' => $requestedAccessLevel->value]]];
                $locked->access_level = $requestedAccessLevel;
            }

            $locked->save();

            foreach ($auditEntries as $entry) {
                AuditLog::create([
                    'organization_id' => $locked->organization_id,
                    'user_id' => auth()->id(),
                    'action' => $entry['action'],
                    'entity_type' => 'document',
                    'entity_id' => $locked->id,
                    'changes' => $entry['changes'],
                ]);
            }

            return ['blocked' => false, 'document' => $locked];
        });

        if ($result['blocked']) {
            return response()->json($result['response'], 422);
        }

        return response()->json(['document' => $result['document']->fresh('uploader')]);
    }

    /**
     * Hard delete, permanent, blocked while attached directly to any
     * task. The re-check inside the transaction (with the row locked) is
     * what actually decides whether this succeeds — the dependencies()
     * preview a caller fetched earlier is never trusted, since another
     * request could have attached this document to a task in between.
     *
     * task #73 phase 4: a SECOND, independent check after the direct-link
     * block above — if this document currently sits in a folder linked to
     * any task, deleting it is allowed but requires explicit confirmation
     * (confirm_folder_effect), same warn-not-block reasoning as update()'s
     * own folder-derived check. Only reached when the direct-link block
     * above didn't already stop the request, so the two can never
     * disagree about which is more restrictive.
     *
     * The stored file (if any) is removed only AFTER the transaction
     * commits, and only if storage_key is set — never derived from
     * `link`, which an external-link document has instead and which this
     * app never wrote to disk. A storage failure is logged, not fatal:
     * the document record is already gone by that point regardless.
     */
    public function destroy(Request $request, Document $document): JsonResponse
    {
        Gate::authorize('delete', $document);

        $dependencyService = app(DocumentDependencyService::class);
        $viewer = auth()->user();

        $result = DB::transaction(function () use ($document, $request, $dependencyService, $viewer) {
            $locked = Document::whereKey($document->id)->lockForUpdate()->firstOrFail();

            // withTrashed(): a task_documents row for a since-deactivated
            // task still counts as "attached to any task" — without this,
            // Task's own SoftDeletingScope would silently let a document
            // linked only to a deactivated task through, contradicting the
            // linked_task_ids: [] comment below (the row genuinely existed;
            // cascadeOnDelete on task_documents.document_id removes it a
            // moment later regardless, so this is about the block and the
            // audit trail being accurate, not data loss).
            if ($locked->tasks()->withTrashed()->count() > 0) {
                return ['blocked' => true, 'reason' => 'direct', 'document' => $locked];
            }

            if (! $request->boolean('confirm_folder_effect')) {
                $folderSummary = $dependencyService->folderDerivedSummary($viewer, $locked->folder_id, $locked->folder_id);

                if ($folderSummary !== null) {
                    return ['blocked' => true, 'reason' => 'folder', 'summary' => $folderSummary];
                }
            }

            AuditLog::create([
                'organization_id' => $locked->organization_id,
                'user_id' => auth()->id(),
                'action' => 'document.deleted',
                'entity_type' => 'document',
                'entity_id' => $locked->id,
                'changes' => [
                    'name' => $locked->name,
                    'uploaded_by' => $locked->uploaded_by,
                    'storage_key' => $locked->storage_key,
                    'original_filename' => $locked->original_filename,
                    // Always empty this phase — delete is blocked outright
                    // while any task_documents row exists, so there is
                    // never a linked task left by the time this runs.
                    // Phase 4 extends the rule this field exists for.
                    'linked_task_ids' => [],
                ],
            ]);

            $storageKey = $locked->storage_key;
            $locked->delete();

            return ['blocked' => false, 'storage_key' => $storageKey];
        });

        if ($result['blocked']) {
            if ($result['reason'] === 'folder') {
                return response()->json($dependencyService->folderWarningResponse($result['summary']), 422);
            }

            $summary = $dependencyService->summarize($result['document'], $viewer);

            return response()->json($dependencyService->blockedResponse($summary), 422);
        }

        if ($result['storage_key'] !== null) {
            try {
                app(FileStorageService::class)->delete($result['storage_key']);
            } catch (Throwable $e) {
                Log::warning('Failed to delete stored file for a deleted document.', [
                    'storage_key' => $result['storage_key'],
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        return response()->json(['deleted' => true]);
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
    public function download(Request $request): StreamedResponse|RedirectResponse|Response
    {
        $data = $request->validate(['url' => ['required', 'string', 'max:2048']]);

        $document = $this->resolveDocumentByUrl($data['url']);

        // task #73 phase 2: the custom 404 view, rendered directly with an
        // explicit message — not abort(404, '...'): that throws the same
        // NotFoundHttpException class Laravel's own router uses for a
        // genuinely unmatched URL, so sniffing $exception->getMessage() in
        // the view would just as easily surface the router's own internal
        // "The route x could not be found." text. Rendering here instead
        // means only THIS call ever sets $message, and a real unmatched
        // route still falls through to the view's generic copy.
        if ($document === null) {
            return response()->view('errors.404', ['message' => 'This document has been removed.'], 404);
        }

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

    /**
     * The exact two-step lookup download() itself uses (by `link`, then
     * by the storage_key path a computed URL resolves back to) — pulled
     * out so chipStatus() below resolves a raw href to a Document exactly
     * the same way, and the two can never quietly drift apart.
     */
    private function resolveDocumentByUrl(string $url): ?Document
    {
        $document = Document::where('link', $url)->first();

        if ($document === null) {
            $path = app(FileStorageService::class)->pathFromUrl($url);

            if ($path !== null) {
                $document = Document::where('storage_key', $path)->first();
            }
        }

        return $document;
    }

    /**
     * task #73 phase 2: the "Document removed" chip state. One batched
     * request (capped) tells the caller which of a list of file-chip
     * hrefs no longer resolve to a real Document — never one request per
     * chip, and this never reveals anything about a document's contents,
     * only whether the href still resolves at all, so no per-document
     * Gate check is needed here (unlike download() itself, which actually
     * serves the file). file-chip-status.js is the one place both the
     * live editor and read-only rich text collect their chip hrefs and
     * call this.
     */
    public function chipStatus(Request $request): JsonResponse
    {
        $data = $request->validate([
            'hrefs' => ['required', 'array', 'max:50'],
            'hrefs.*' => ['string', 'max:2048'],
        ]);

        $hrefs = collect($data['hrefs'])->unique()->values();

        // Genuinely two queries total, regardless of how many hrefs are in
        // the (capped) batch — not resolveDocumentByUrl() called once per
        // href, which would just move the N+1 into this one endpoint.
        $resolvedByLink = Document::whereIn('link', $hrefs)->pluck('link');
        $remainingHrefs = $hrefs->diff($resolvedByLink);

        $storage = app(FileStorageService::class);
        $pathsByHref = $remainingHrefs->mapWithKeys(fn (string $href) => [$href => $storage->pathFromUrl($href)])->filter();

        $resolvedStorageKeys = $pathsByHref->isEmpty()
            ? collect()
            : Document::whereIn('storage_key', $pathsByHref->values())->pluck('storage_key');

        $resolvedByStorageKey = $pathsByHref->filter(fn (?string $path) => $resolvedStorageKeys->contains($path))->keys();

        $missing = $hrefs->diff($resolvedByLink->merge($resolvedByStorageKey))->values();

        return response()->json(['missing' => $missing]);
    }
}
