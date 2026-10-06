{{-- Attach/detach existing company documents, or create a new one and
     attach it in the same step. Self-contained per inclusion
     (document.currentScript, not a global id) — matches _subtasks.blade.php
     and _comments.blade.php, since this is only ever rendered once per
     Edit Task page but follows the same pattern for consistency. --}}
<div class="document-container" data-task-id="{{ $task->id }}" data-organization-id="{{ $task->organization_id }}"
     data-project-has-client="{{ $projectHasClient ? '1' : '0' }}" data-can-unlink-documents="{{ $canUnlinkDocuments ? '1' : '0' }}"
     data-can-detach-any-document="{{ $canDetachAnyDocument ? '1' : '0' }}" data-current-user-id="{{ auth()->id() }}">
    <div class="document-list space-y-2">
        @foreach ($attachedDocuments as $document)
            <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2" data-document-id="{{ $document->id }}">
                <div>
                    <a href="{{ route('file-downloads.show', ['url' => $document->url]) }}" target="_blank" rel="noopener" class="text-[12px] font-medium text-brand-600 hover:underline">{{ $document->name }}</a>
                    <span class="ml-2 rounded-sm bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-600">{{ $document->access_level->label() }}</span>
                    {{-- Author + date, matching the Documents page's own
                         list — 'uploader' is eager-loaded above so this
                         never becomes an N+1 across attached rows. --}}
                    <p class="mt-0.5 text-[11px] text-gray-500">{{ $document->uploader->name }} · {{ $document->created_at->format('M j, Y') }}</p>
                </div>
                {{-- TaskPolicy::detachDocument(), split into its two halves
                     so the per-row check costs nothing: $canUnlinkDocuments
                     is the task-level gate (manage_documents + task view,
                     NOT $canEdit), $canDetachAnyDocument the management-
                     tier override, and the uploader comparison is the only
                     thing evaluated per document. Without the last two, any
                     manage_documents holder could detach anyone's document
                     — a Staff member removing one management attached, or a
                     Client removing a Public one staff attached. --}}
                @if ($canUnlinkDocuments && ($canDetachAnyDocument || $document->uploaded_by === auth()->id()))
                    <button type="button" class="detach-document-btn text-[11px] text-gray-500 hover:underline">Detach</button>
                @endif
            </div>
        @endforeach
        @if ($attachedDocuments->isEmpty())
            <p class="document-empty text-[11px] text-gray-500">No documents attached.</p>
        @endif
    </div>

    {{-- task #73 phase 4: linked folders — visually distinct from the
         directly-attached files above (folder icon, tinted background),
         each with an expand control for its DIRECT children only. Already
         filtered to an empty collection for a Client-role viewer server-
         side (TaskManagementController::edit()) — a hard, query-level
         exclusion, not a client-side hide, so this whole block simply
         never renders anything for them rather than rendering-then-hiding. --}}
    <div class="folder-list mt-2 space-y-2">
        @foreach ($linkedFolders as $folder)
            <div class="folder-row rounded-md border border-gray-200 bg-gray-50 px-3 py-2" data-folder-id="{{ $folder->id }}" data-linked="1">
                <div class="flex items-center justify-between">
                    <button type="button" class="folder-expand-toggle flex min-w-0 items-center gap-1.5 text-left text-[12px] font-medium text-[#1F2937]" aria-expanded="false">
                        <i class="ti ti-chevron-right folder-expand-icon shrink-0 text-[12px] text-gray-400" aria-hidden="true"></i>
                        <i class="ti ti-folder shrink-0 text-[13px] text-amber-500" aria-hidden="true"></i>
                        <span class="truncate">{{ $folder->name }}</span>
                    </button>
                    {{-- Same gate as the file Detach button above —
                         TaskPolicy::unlinkDocuments(), reused verbatim, no
                         folder-specific policy method. --}}
                    @if ($canUnlinkDocuments)
                        <button type="button" class="detach-folder-btn shrink-0 text-[11px] text-gray-500 hover:underline">Detach</button>
                    @endif
                </div>
                <div class="folder-expand-content mt-2 ml-5 hidden space-y-1"></div>
            </div>
        @endforeach
    </div>

    {{-- task #73 (UI merge): ONE entry point replacing the old separate
         "Attach existing" and "+ Add new document" buttons/panels — one
         search box that filters existing documents as you type, with
         "Upload as a new file" / "Add a link instead" always available
         below the results.

         Two different capabilities share this panel, so it has two gates:
         - $canAttachDocuments (TaskPolicy::attachDocuments(): manage_
           documents + task view + NOT a Client) — searching and attaching
           the company's EXISTING documents, plus attaching folders.
         - $canManageDocuments (DocumentPolicy::create(), which a Client
           holding manage_documents DOES pass) — creating a brand-new
           document from here, by upload or link.

         The merged panel originally used attachDocuments() alone, which
         meant a Client with "Add & edit documents" had no way to add a
         document on this page at all, even though DocumentController::
         store() would happily accept it (forcing Public and attaching to
         the task). So a create-only user now gets the panel with just the
         two action rows: no search box, no results, no "Attach folder" —
         they must never browse the company's existing documents. The
         button reads "Add document" rather than "Attach document" for
         them, since attaching isn't what they can do. --}}
    @if ($canAttachDocuments || $canManageDocuments)
        @php
            // task #73 phase 1: a Client-role uploader (in this task's
            // company) never sees the access-level dropdown — always
            // saved as Public, enforced server-side regardless in
            // DocumentUploadService::resolveAccessLevel() even if this
            // markup were somehow bypassed.
            //
            // This used to be a defensive leftover, because the panel's
            // only gate excluded Client outright. It is now the PRIMARY
            // path: the create-only branch above exists precisely for a
            // Client holding manage_documents, and
            // DocumentPolicy::create()'s Client branch requires
            // isClientInOrg() — the same check as here — so on that
            // branch this is always true and the hidden public input is
            // what actually renders. Load-bearing, not vestigial.
            $isClientUploader = auth()->user()->isClientInOrg($task->organization_id);
        @endphp
        <div class="mt-2 flex items-center gap-3">
            <button type="button" class="attach-document-toggle text-[11px] font-medium text-brand-600 hover:underline"
                aria-haspopup="dialog" aria-expanded="false" aria-controls="attach-document-panel-{{ $task->id }}">
                {{ $canAttachDocuments ? 'Attach document' : 'Add document' }}
            </button>
            {{-- task #73 phase 4: a sibling toggle/panel, same structure as
                 "Attach document" above minus the "create instead" rows
                 (folders aren't created from this picker). Attaching a
                 folder is squarely an "attach existing" action, so it
                 stays on $canAttachDocuments alone — a create-only Client
                 never gets this button or its panel in the HTML. --}}
            @if ($canAttachDocuments)
                <button type="button" class="attach-folder-toggle text-[11px] font-medium text-brand-600 hover:underline"
                    aria-haspopup="dialog" aria-expanded="false" aria-controls="attach-folder-panel-{{ $task->id }}">
                    Attach folder
                </button>
            @endif
        </div>

        @if ($canAttachDocuments)
            <div id="attach-folder-panel-{{ $task->id }}" class="attach-folder-panel mt-2 hidden space-y-2 rounded-md border border-gray-200 p-3" role="dialog" aria-label="Attach a folder">
                <label for="attach-folder-search-{{ $task->id }}" class="sr-only">Search or attach a folder</label>
                <input type="text" id="attach-folder-search-{{ $task->id }}" class="attach-folder-search w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" placeholder="Search or attach a folder…">

                <div>
                    <p class="attach-folder-results-heading text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Folders</p>
                    <ul class="attach-folder-results mt-1 space-y-1" aria-label="Folders"></ul>
                    <p class="attach-folder-status mt-1 text-[11px] text-gray-500"></p>
                    <button type="button" class="attach-folder-load-more hidden text-[11px] font-medium text-brand-600 hover:underline">Load more</button>
                </div>

                <div class="flex items-center gap-3 pt-1">
                    <button type="button" class="attach-folder-close text-[12px] text-gray-600 hover:underline">Close</button>
                </div>
            </div>
        @endif

        {{-- aria-label tracks the toggle's own label: a create-only user
             activates "Add document", so being announced into a dialog
             called "Attach a document" — one containing no attach
             affordance at all — would be actively misleading. The id stays
             as-is; it's internal plumbing, not announced. --}}
        <div id="attach-document-panel-{{ $task->id }}" class="attach-document-panel mt-2 hidden space-y-2 rounded-md border border-gray-200 p-3" role="dialog" aria-label="{{ $canAttachDocuments ? 'Attach a document' : 'Add a document' }}">
            {{-- View 1: search + matching documents + the two "create
                 instead" action rows. --}}
            <div class="attach-document-search-view space-y-2">
                {{-- Everything that browses EXISTING documents is omitted
                     entirely (not merely hidden) for a create-only user —
                     a Client must never see the company's document list,
                     and the search endpoint would 403 them anyway. The two
                     action rows below are all they get. --}}
                @if ($canAttachDocuments)
                    <label for="attach-document-search-{{ $task->id }}" class="sr-only">Search or attach a document</label>
                    <input type="text" id="attach-document-search-{{ $task->id }}" class="attach-document-search w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" placeholder="Search or attach a document…">

                    <div>
                        {{-- "Recently added" while the search box is empty, not
                             "Matching documents" — an empty query still shows
                             the same newest-first list (Phase 3's default
                             ordering), and labelling it as "matching" reads
                             like "these are the only files available" rather
                             than "these are just the most recent ones". --}}
                        <p class="attach-document-results-heading text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Recently added</p>
                        <ul class="attach-document-results mt-1 space-y-1" aria-label="Recently added"></ul>
                        <p class="attach-document-status mt-1 text-[11px] text-gray-500"></p>
                        <button type="button" class="attach-document-load-more hidden text-[11px] font-medium text-brand-600 hover:underline">Load more</button>
                    </div>

                    <hr class="border-gray-200">
                @endif

                <div class="space-y-1">
                    <button type="button" class="attach-document-upload-action block w-full rounded-md border border-gray-200 px-3 py-2 text-left text-[12px] text-gray-700 hover:border-brand-600 hover:bg-brand-50">
                        <i class="ti ti-upload text-[13px] text-gray-500" aria-hidden="true"></i>
                        <span class="attach-document-upload-label">Upload a new file</span>
                    </button>
                    <button type="button" class="attach-document-link-action block w-full rounded-md border border-gray-200 px-3 py-2 text-left text-[12px] text-gray-700 hover:border-brand-600 hover:bg-brand-50">
                        <i class="ti ti-link text-[13px] text-gray-500" aria-hidden="true"></i>
                        <span class="attach-document-link-label">Add a link</span>
                    </button>
                </div>
            </div>

            {{-- View 2: the create-a-new-document sub-form — same fields/
                 endpoint as before, just entered via one of the two
                 action rows above instead of its own separate toggle.
                 Starts hidden; a click on either action row picks the
                 mode and reveals this instead of the search view. --}}
            <div class="new-document-form hidden space-y-2 rounded-md border border-gray-200 p-3">
                {{-- "Back to search" only makes sense if there IS a search
                     view to go back to; a create-only user returns to just
                     the two action rows. --}}
                <button type="button" class="new-document-back text-[11px] text-gray-500 hover:underline">&larr; {{ $canAttachDocuments ? 'Back to search' : 'Back' }}</button>

                {{-- flex + order-first (only on the upload panel below) is
                     what puts the file picker before the name field on the
                     Upload tab while leaving the Add link tab's order
                     (name, then link) untouched — the upload panel is
                     hidden, not removed, on the link tab, so its order
                     never affects that tab's visible layout. --}}
                <div class="flex flex-col gap-2">
                    <input type="text" class="new-document-name w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" placeholder="Document name">

                    <div class="new-document-panel" data-panel="link">
                        <input type="url" class="new-document-link w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" placeholder="https://…">
                    </div>
                    <div class="new-document-panel hidden order-first" data-panel="upload">
                        {{-- task #73 (attach panel polish): a custom-styled
                             control, not the bare native input — the
                             browser's own "Choose File" button used to
                             render directly on top of the selected
                             filename text once a file was picked. The
                             native input itself stays real, focusable and
                             keyboard-operable (sr-only, not display:none/
                             visibility:hidden) — a <label for="..."> is
                             what makes the visible button trigger it, with
                             no separate tab stop of its own (labels aren't
                             focusable; only the input they're bound to is),
                             so Tab reaches exactly one control here, same
                             as before. --}}
                        <div class="flex items-center gap-2">
                            <label for="new-document-file-{{ $task->id }}" class="new-document-file-trigger cursor-pointer rounded-md border border-gray-300 bg-white px-3 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">Choose file</label>
                            <span class="new-document-file-name min-w-0 flex-1 truncate text-[12px] text-gray-500">No file selected</span>
                        </div>
                        <input type="file" id="new-document-file-{{ $task->id }}" class="new-document-file sr-only">
                        <p class="mt-1 text-[10px] text-gray-500">Document (PDF, Word, Excel, PowerPoint, text, CSV — 20MB), image (10MB), audio (50MB) or video (200MB).</p>
                    </div>

                    @if ($isClientUploader)
                        <input type="hidden" class="new-document-access" value="public">
                    @else
                        {{-- task #73 phase 3: no Private option here — a document
                             created and attached to a task in the same step is
                             never allowed to be Private (enforced server-side in
                             DocumentController::store() regardless of what this
                             markup offers). Private stays available on the
                             standalone Documents page's own Add Document form,
                             which never attaches to a task. --}}
                        <select class="new-document-access w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                            @foreach (\App\Enums\DocumentAccessLevel::cases() as $accessCase)
                                @continue($accessCase === \App\Enums\DocumentAccessLevel::Private)
                                <option value="{{ $accessCase->value }}" {{ $accessCase === \App\Enums\DocumentAccessLevel::Internal ? 'selected' : '' }}>{{ $accessCase->label() }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                {{-- Caption differs per tab — "Upload & Attach" on the
                     Upload tab (the flow is upload-first there), "Create &
                     attach" unchanged on the Add link tab — swapped in JS
                     by activateNewDocumentMode(), same submit handler and
                     endpoint either way. --}}
                <button type="button" class="create-and-attach-btn rounded-md bg-brand-600 px-3 py-2 text-[12px] font-medium text-white hover:bg-brand-700">Create &amp; attach</button>
                <p class="new-document-error text-[11px] text-red-600" style="display: none;"></p>
            </div>

            <div class="flex items-center gap-3 pt-1">
                <button type="button" class="attach-document-close text-[12px] text-gray-600 hover:underline">Close</button>
            </div>
        </div>
    @endif
</div>
<script>
    (function () {
        // document.currentScript is only valid during THIS script's own
        // synchronous execution — it becomes null once we're inside an
        // async callback (like init(), below), so it must be captured
        // here, immediately, not inside that callback.
        const container = document.currentScript.previousElementSibling;

        // This is a plain classic script (not type="module"), which
        // executes immediately/synchronously as soon as the parser reaches
        // it, ALWAYS before app.js (loaded as a deferred type="module"
        // script via the layout's own Vite directive in <head>) has run —
        // window.solavaDocumentNameAutofill below is guaranteed undefined
        // at that point, not just usually. Same wait-for-it pattern already
        // used for window.solavaRichText elsewhere (see
        // tasks/_description-field.blade.php's own withEditor()): try now,
        // fall back to DOMContentLoaded (by which point every deferred/
        // module script is guaranteed to have run) if it isn't ready yet.
        function init() {
        const taskId = container.dataset.taskId;
        const organizationId = container.dataset.organizationId;
        const projectHasClient = container.dataset.projectHasClient === '1';
        const canUnlinkDocuments = container.dataset.canUnlinkDocuments === '1';
        // The same two halves the server-rendered rows above use, so a row
        // added without a reload gets exactly the same Detach visibility as
        // one that came from a full page render.
        const canDetachAnyDocument = container.dataset.canDetachAnyDocument === '1';
        const currentUserId = Number(container.dataset.currentUserId);
        const listEl = container.querySelector('.document-list');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function request(url, method, body) {
            return fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: body ? JSON.stringify(body) : undefined,
            });
        }

        // task #73 phase 1: the "Upload file" option sends a real
        // multipart/form-data body (a File Blob can't ride inside JSON)
        // — deliberately no Content-Type header here, so the browser sets
        // its own multipart boundary, exactly like a plain HTML form
        // submission would.
        function requestForm(url, method, formData) {
            return fetch(url, {
                method: method,
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
            });
        }

        // Surfaces the backend's actual validation/authorization message
        // instead of a generic "failed" string, falling back to that
        // generic string only when the response carries no message of its own.
        function throwOnFailure(responsePromise, fallback) {
            return responsePromise.then(function (response) {
                if (response.ok) return response;
                return response.json().catch(function () { return null; }).then(function (data) {
                    const fieldErrors = data && data.errors ? Object.values(data.errors)[0] : null;
                    const message = (Array.isArray(fieldErrors) && fieldErrors[0]) || (data && data.message) || fallback;
                    throw new Error(message);
                });
            });
        }

        function requestOrThrow(url, method, body, fallback) {
            return throwOnFailure(request(url, method, body), fallback);
        }

        function clearEmptyState() {
            const empty = listEl.querySelector('.document-empty');
            if (empty) empty.remove();
        }

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value;
            return div.innerHTML;
        }

        function wireDetach(row) {
            const btn = row.querySelector('.detach-document-btn');
            if (! btn) return;
            btn.addEventListener('click', function () {
                // Detaching only unlinks the document from this task - it
                // stays in the company's document library - but still
                // worth a confirmation since it's one accidental click away.
                if (! confirm('Remove this document from the task? It will stay in the company\'s document library.')) return;

                const documentId = row.dataset.documentId;
                requestOrThrow('/tasks/' + taskId + '/documents/' + documentId, 'DELETE', undefined, 'Failed to detach document.')
                    .then(function () {
                        row.remove();
                        if (! listEl.querySelector('[data-document-id]')) {
                            const empty = document.createElement('p');
                            empty.className = 'document-empty text-[11px] text-gray-500';
                            empty.textContent = 'No documents attached.';
                            listEl.appendChild(empty);
                        }
                    })
                    .catch(function (error) {
                        alert(error.message);
                    });
            });
        }

        listEl.querySelectorAll('[data-document-id]').forEach(wireDetach);

        // Matches Carbon's ->format('M j, Y') used for this same "author +
        // date" line on the initial page-load rows below, and on the
        // Documents page's own list — so a freshly attached document's row
        // shows dates in the same style as one rendered on page load, not
        // a different locale-dependent format.
        function formatDocumentDate(isoString) {
            return new Date(isoString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }

        function appendDocumentRow(doc) {
            clearEmptyState();
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between rounded-md border border-gray-200 px-3 py-2';
            row.dataset.documentId = doc.id;
            const accessLabel = doc.access_level.charAt(0).toUpperCase() + doc.access_level.slice(1);
            const downloadUrl = '/file-downloads?url=' + encodeURIComponent(doc.url);
            // task #73 phase 3 fix: the Detach button used to render here
            // unconditionally, regardless of canUnlinkDocuments — unlike
            // the initial page-load rows above, which correctly gate it.
            // A viewer without unlinkDocuments rights who created-and-
            // attached would see a button the endpoint would then 403 on.
            //
            // Now mirrors the server-rendered condition exactly, uploader
            // check included. doc.uploaded_by is present on both payloads
            // that reach here (TaskDocumentController::attach() and
            // DocumentController::store() both return the whole model, and
            // Document declares no $hidden).
            const detachButtonHtml = (canUnlinkDocuments && (canDetachAnyDocument || Number(doc.uploaded_by) === currentUserId))
                ? '<button type="button" class="detach-document-btn text-[11px] text-gray-500 hover:underline">Detach</button>'
                : '';
            // Author + date, matching the Documents page's own list — the
            // response this renders from always carries 'uploader' loaded
            // (TaskDocumentController::attach() / DocumentController::store()
            // both load it before responding), so doc.uploader.name is
            // never undefined here.
            row.innerHTML = '<div><a href="' + escapeHtml(downloadUrl) + '" target="_blank" rel="noopener" class="text-[12px] font-medium text-brand-600 hover:underline">' + escapeHtml(doc.name) + '</a>'
                + '<span class="ml-2 rounded-sm bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-600">' + escapeHtml(accessLabel) + '</span>'
                + '<p class="mt-0.5 text-[11px] text-gray-500">' + escapeHtml(doc.uploader.name) + ' · ' + escapeHtml(formatDocumentDate(doc.created_at)) + '</p></div>'
                + detachButtonHtml;
            listEl.appendChild(row);
            wireDetach(row);
        }

        // task #73 phase 4: linked folders — a visually distinct row per
        // folder (icon + tinted background), each independently
        // expandable to show its DIRECT children only. buildFolderRow()
        // is shared by the initial (server-rendered) linked-folder rows,
        // a freshly-attached folder (appendFolderRow()), and every
        // subfolder discovered by expanding one — a subfolder isn't
        // itself "linked" (linked=false: no Detach button, since managing
        // it happens on the Documents page, not here), but is expandable
        // the exact same way, recursively, at any depth.
        const folderListEl = container.querySelector('.folder-list');

        function buildFolderRow(folder, linked) {
            const row = document.createElement('div');
            row.className = 'folder-row rounded-md border border-gray-200 bg-gray-50 px-3 py-2';
            row.dataset.folderId = folder.id;
            row.dataset.linked = linked ? '1' : '0';

            const detachButtonHtml = (linked && canUnlinkDocuments)
                ? '<button type="button" class="detach-folder-btn shrink-0 text-[11px] text-gray-500 hover:underline">Detach</button>'
                : '';

            row.innerHTML = '<div class="flex items-center justify-between">'
                + '<button type="button" class="folder-expand-toggle flex min-w-0 items-center gap-1.5 text-left text-[12px] font-medium text-[#1F2937]" aria-expanded="false">'
                + '<i class="ti ti-chevron-right folder-expand-icon shrink-0 text-[12px] text-gray-400" aria-hidden="true"></i>'
                + '<i class="ti ti-folder shrink-0 text-[13px] text-amber-500" aria-hidden="true"></i>'
                + '<span class="truncate">' + escapeHtml(folder.name) + '</span>'
                + '</button>'
                + detachButtonHtml
                + '</div>'
                + '<div class="folder-expand-content mt-2 ml-5 hidden space-y-1"></div>';

            wireFolderExpand(row);
            if (linked) wireFolderDetach(row);

            return row;
        }

        function wireFolderDetach(row) {
            const btn = row.querySelector('.detach-folder-btn');
            if (! btn) return;
            btn.addEventListener('click', function () {
                // Same confirmation wording/intent as wireDetach() above —
                // unlinking only removes the task_folder_links row, the
                // folder and everything inside it are untouched.
                if (! confirm('Remove this folder from the task? The folder and its contents will stay in the company\'s document library.')) return;

                const folderId = row.dataset.folderId;
                requestOrThrow('/tasks/' + taskId + '/folders/' + folderId, 'DELETE', undefined, 'Failed to detach folder.')
                    .then(function () {
                        row.remove();
                    })
                    .catch(function (error) {
                        alert(error.message);
                    });
            });
        }

        // Lazy-fetch-and-cache: a folder's contents are only ever fetched
        // once per page view, on first expand — re-collapsing and re-
        // expanding just toggles visibility of what's already rendered,
        // rather than re-querying the server every time. This is a
        // deliberate trade-off (contents can go stale within the same page
        // view if changed elsewhere) accepted for the same reason the
        // picker's own results aren't kept continuously live.
        function wireFolderExpand(row) {
            const toggle = row.querySelector('.folder-expand-toggle');
            const icon = row.querySelector('.folder-expand-icon');
            const content = row.querySelector('.folder-expand-content');
            let loaded = false;

            function renderEmptyState() {
                content.innerHTML = '<p class="text-[11px] text-gray-500">Nothing here.</p>';
            }

            function renderContents(data) {
                content.innerHTML = '';

                (data.folders || []).forEach(function (subfolder) {
                    content.appendChild(buildFolderRow(subfolder, false));
                });

                (data.documents || []).forEach(function (doc) {
                    const fileRow = document.createElement('div');
                    fileRow.className = 'flex items-center justify-between rounded-md border border-gray-200 px-3 py-2';
                    const accessLabel = doc.access_level.charAt(0).toUpperCase() + doc.access_level.slice(1);
                    const downloadUrl = '/file-downloads?url=' + encodeURIComponent(doc.url);
                    // View/open only from this task-page context — no
                    // unlink/delete/move action here, per the task's own
                    // spec; those live on the Documents page.
                    fileRow.innerHTML = '<div><a href="' + escapeHtml(downloadUrl) + '" target="_blank" rel="noopener" class="text-[12px] font-medium text-brand-600 hover:underline">' + escapeHtml(doc.name) + '</a>'
                        + '<span class="ml-2 rounded-sm bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-600">' + escapeHtml(accessLabel) + '</span></div>';
                    content.appendChild(fileRow);
                });

                if (! (data.folders || []).length && ! (data.documents || []).length) {
                    renderEmptyState();
                }
            }

            toggle.addEventListener('click', function () {
                const expanded = toggle.getAttribute('aria-expanded') === 'true';

                if (expanded) {
                    toggle.setAttribute('aria-expanded', 'false');
                    icon.classList.remove('ti-chevron-down');
                    icon.classList.add('ti-chevron-right');
                    content.classList.add('hidden');
                    return;
                }

                toggle.setAttribute('aria-expanded', 'true');
                icon.classList.remove('ti-chevron-right');
                icon.classList.add('ti-chevron-down');
                content.classList.remove('hidden');

                if (loaded) return;
                loaded = true;
                content.innerHTML = '<p class="text-[11px] text-gray-500">Loading…</p>';

                fetch('/tasks/' + taskId + '/folders/' + row.dataset.folderId + '/expand', { headers: { Accept: 'application/json' } })
                    .then(function (response) {
                        if (! response.ok) throw new Error('Failed to load folder contents.');
                        return response.json();
                    })
                    .then(renderContents)
                    .catch(function (error) {
                        content.innerHTML = '<p class="text-[11px] text-red-600"></p>';
                        content.querySelector('p').textContent = error.message;
                        loaded = false;
                    });
            });
        }

        if (folderListEl) {
            folderListEl.querySelectorAll('.folder-row').forEach(function (row) {
                wireFolderExpand(row);
                if (row.dataset.linked === '1') wireFolderDetach(row);
            });
        }

        function appendFolderRow(folder) {
            if (folderListEl) folderListEl.appendChild(buildFolderRow(folder, true));
        }

        // task #73 (UI merge): the merged attach-a-document panel — a
        // hidden panel toggled by JS, the same pattern as the Documents
        // page's own Edit/Delete panels (no modal component exists in
        // this app). One button/panel now covers what used to be two
        // separate entry points (an existing-document picker and a
        // create-new-document mini-form).
        const attachToggle = container.querySelector('.attach-document-toggle');
        const attachPanel = container.querySelector('.attach-document-panel');
        if (attachToggle && attachPanel) {
            const searchView = attachPanel.querySelector('.attach-document-search-view');
            const searchInput = attachPanel.querySelector('.attach-document-search');
            const resultsEl = attachPanel.querySelector('.attach-document-results');
            const statusEl = attachPanel.querySelector('.attach-document-status');
            const loadMoreBtn = attachPanel.querySelector('.attach-document-load-more');
            const closeBtn = attachPanel.querySelector('.attach-document-close');
            const uploadActionBtn = attachPanel.querySelector('.attach-document-upload-action');
            const linkActionBtn = attachPanel.querySelector('.attach-document-link-action');
            const uploadLabelEl = attachPanel.querySelector('.attach-document-upload-label');
            const linkLabelEl = attachPanel.querySelector('.attach-document-link-label');
            const resultsHeadingEl = attachPanel.querySelector('.attach-document-results-heading');

            let currentPage = 1;
            let hasMore = false;
            let searchDebounceTimer = null;
            let requestToken = 0; // guards a stale response (an old search, or an auto-chained page fetch) from rendering after a newer one supersedes it

            // task #73 (attach panel polish): "Recently added" while the
            // search box is empty — the exact same newest-first list and
            // criteria as today, just labelled honestly instead of as
            // "matching" results (which reads as "these are the only
            // files available"). Switches to "Matching documents" as soon
            // as the user types anything, and back again if they clear it.
            function updateResultsHeading(query) {
                const heading = query ? 'Matching documents' : 'Recently added';
                resultsHeadingEl.textContent = heading;
                resultsEl.setAttribute('aria-label', heading);
            }

            function formatBytes(bytes) {
                if (bytes === null || bytes === undefined) return null;
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return Math.round(bytes / 1024) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            }

            // task #73 (document form fixes, code-review follow-up): mirrors
            // Document::iconClass() (image/audio/video by mime_type prefix,
            // generic file for anything else, link icon only for a document
            // with no mime_type at all) — this picker used to only tell
            // "has a mime_type" apart from "doesn't", showing every
            // image/audio/video result with the same generic file icon the
            // standalone Documents page had already moved past.
            function fileIconClass(doc) {
                if (! doc.mime_type) return 'ti-link';
                if (doc.mime_type.indexOf('image/') === 0) return 'ti-photo';
                if (doc.mime_type.indexOf('audio/') === 0) return 'ti-music';
                if (doc.mime_type.indexOf('video/') === 0) return 'ti-video';
                return 'ti-file-text';
            }

            function renderResultItem(doc) {
                // Already-attached documents are never shown at all here
                // (not shown-and-disabled) — the endpoint still returns
                // them with already_attached: true (the query itself is
                // unchanged, see TaskDocumentController::index()), this
                // just filters them out client-side before rendering.
                if (doc.already_attached) return;

                const li = document.createElement('li');
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'flex w-full items-start gap-2 rounded-md border border-gray-200 px-3 py-2 text-left text-[12px] hover:border-brand-600 hover:bg-brand-50';

                const metaParts = [doc.uploader_name, new Date(doc.uploaded_at).toLocaleDateString()];
                if (doc.folder_path) metaParts.push(doc.folder_path);
                const sizeLabel = formatBytes(doc.size_bytes);
                if (sizeLabel) metaParts.push(sizeLabel);

                let html = '<i class="ti ' + fileIconClass(doc) + ' mt-0.5 shrink-0 text-[14px] text-gray-400" aria-hidden="true"></i>'
                    + '<span class="min-w-0"><span class="block truncate font-medium text-[#1F2937]">' + escapeHtml(doc.name) + '</span>'
                    + '<span class="block text-[11px] text-gray-500">' + escapeHtml(metaParts.join(' · ')) + '</span>';

                if (doc.access_level === 'public' && projectHasClient) {
                    html += '<span class="block text-[11px] text-amber-700">Visible to this project’s client once linked</span>';
                }

                html += '</span>';

                btn.innerHTML = html;
                btn.addEventListener('click', function () {
                    attachDocument(doc.id, btn);
                });

                li.appendChild(btn);
                resultsEl.appendChild(li);
            }

            // task #73 (code review follow-up): the old native <select>
            // this list replaced gave free arrow-key navigation and
            // type-ahead; individual <button> rows don't, by default,
            // regressing a keyboard-only user from one arrow-key press to
            // Tabbing through every result (up to 25 per page) one at a
            // time to reach one that isn't the first. This restores
            // arrow-key movement as an addition, not a replacement — Tab
            // still visits every button individually exactly as before,
            // for anyone who prefers or needs that; Up/Down/Home/End are
            // just a faster way to get to a specific row without giving
            // up native button semantics (no roving tabindex, no listbox/
            // option ARIA reinterpretation of what are still just buttons).
            // Guarded like the search/load-more listeners below: there is no
            // results list at all for a create-only user, and an
            // unconditional addEventListener on null here would throw and
            // take the whole panel's script down with it.
            if (resultsEl) {
                resultsEl.addEventListener('keydown', function (event) {
                    if (! ['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) return;

                    const buttons = Array.from(resultsEl.querySelectorAll('button'));
                    const currentIndex = buttons.indexOf(document.activeElement);
                    if (currentIndex === -1) return;

                    let targetIndex;
                    if (event.key === 'ArrowDown') targetIndex = Math.min(currentIndex + 1, buttons.length - 1);
                    else if (event.key === 'ArrowUp') targetIndex = Math.max(currentIndex - 1, 0);
                    else if (event.key === 'Home') targetIndex = 0;
                    else targetIndex = buttons.length - 1;

                    event.preventDefault();
                    buttons[targetIndex].focus();
                });
            }

            function attachDocument(documentId, triggerEl) {
                if (triggerEl) triggerEl.disabled = true;
                statusEl.textContent = '';

                requestOrThrow('/tasks/' + taskId + '/documents', 'POST', { document_id: documentId }, 'Failed to attach document.')
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        appendDocumentRow(data.document);
                        closePanel(true);
                    })
                    .catch(function (error) {
                        statusEl.textContent = error.message;
                        if (triggerEl) triggerEl.disabled = false;
                    });
            }

            // Filtering already-attached rows out is client-side (the
            // query itself is unchanged — see TaskDocumentController::
            // index() — deliberately, so this is small and doesn't touch
            // Phase 3's endpoint). The one risk that comes with that: a
            // page whose real rows are ALL already-attached would
            // otherwise render zero new visible rows while still leaving
            // "Load more" sitting there — indistinguishable from a
            // stalled or broken list. So after every fetch, if nothing
            // new became visible and more pages exist, immediately chain
            // into the next page instead of waiting for another click;
            // it keeps going until a page contributes at least one
            // visible row or there's truly nothing left. Server-side
            // filtering (excluding already-attached in the query itself)
            // would avoid this entirely, but wasn't worth doing for this
            // small a tweak — see the PR description.
            function fetchResults(page, append) {
                const token = ++requestToken;
                const search = searchInput.value.trim();
                if (! append) statusEl.textContent = 'Loading…';
                loadMoreBtn.classList.add('hidden');

                const url = '/tasks/' + taskId + '/documents/attachable?page=' + page
                    + (search ? '&search=' + encodeURIComponent(search) : '');

                fetch(url, { headers: { Accept: 'application/json' } })
                    .then(function (response) {
                        if (! response.ok) throw new Error('Failed to load documents.');
                        return response.json();
                    })
                    .then(function (data) {
                        if (token !== requestToken) return; // a newer request already landed

                        if (! append) resultsEl.innerHTML = '';

                        const beforeCount = resultsEl.children.length;
                        (data.data || []).forEach(renderResultItem);
                        const addedVisible = resultsEl.children.length > beforeCount;

                        currentPage = data.current_page;
                        hasMore = data.current_page < data.last_page;

                        if (! addedVisible && hasMore) {
                            fetchResults(currentPage + 1, true);
                            return;
                        }

                        loadMoreBtn.classList.toggle('hidden', ! hasMore);

                        if (resultsEl.children.length === 0) {
                            statusEl.textContent = 'No documents found.';
                        } else {
                            statusEl.textContent = '';
                        }
                    })
                    .catch(function (error) {
                        if (token !== requestToken) return;
                        statusEl.textContent = error.message;
                    });
            }

            function showSearchView() {
                searchView.classList.remove('hidden');
                newForm.classList.add('hidden');
            }

            function openPanel() {
                attachToggle.setAttribute('aria-expanded', 'true');
                attachPanel.classList.remove('hidden');
                showSearchView();
                // task #73 (code-review follow-up): a fresh open never
                // starts with a stale file/name left over from an attempt
                // abandoned without going through Back or Close.
                resetNewDocumentForm();

                // A create-only user (a Client holding manage_documents)
                // gets no search box or results list at all, so there's
                // nothing to reset and — importantly — nothing to fetch:
                // /documents/attachable would 403 them, surfacing an error
                // in a panel that was working exactly as intended. Focus
                // the first thing they CAN use instead.
                if (! searchInput) {
                    if (uploadActionBtn) uploadActionBtn.focus();

                    return;
                }

                searchInput.value = '';
                updateResultsHeading('');
                searchInput.focus();
                fetchResults(1, false);
            }

            function closePanel(returnFocus) {
                attachToggle.setAttribute('aria-expanded', 'false');
                attachPanel.classList.add('hidden');
                resetNewDocumentForm();
                if (returnFocus) attachToggle.focus();
            }

            attachToggle.addEventListener('click', function () {
                if (attachPanel.classList.contains('hidden')) {
                    openPanel();
                } else {
                    closePanel(false);
                }
            });

            closeBtn.addEventListener('click', function () {
                closePanel(true);
            });

            attachPanel.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closePanel(true);
                }
            });

            // Both are absent for a create-only user — see openPanel().
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const query = searchInput.value.trim();
                    uploadLabelEl.textContent = query ? 'Upload "' + query + '" as a new file' : 'Upload a new file';
                    linkLabelEl.textContent = query ? 'Add "' + query + '" as a link' : 'Add a link';
                    updateResultsHeading(query);

                    if (searchDebounceTimer) clearTimeout(searchDebounceTimer);
                    searchDebounceTimer = setTimeout(function () {
                        fetchResults(1, false);
                    }, 300);
                });
            }

            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function () {
                    fetchResults(currentPage + 1, true);
                });
            }

            uploadActionBtn.addEventListener('click', function () {
                openCreateForm('upload');
            });
            linkActionBtn.addEventListener('click', function () {
                openCreateForm('link');
            });
        }

        // task #73 phase 4: the folder-attach panel — the document
        // panel's sibling, same search/paginate/attach shape, minus the
        // "create instead" rows (folders aren't created from here) and the
        // already-attached client-side filtering (the picker's own query,
        // DocumentFolder::scopeAttachableTo(), already excludes linked
        // folders server-side, so there's no equivalent auto-chain-past-
        // an-all-filtered-page concern the document picker has).
        const attachFolderToggle = container.querySelector('.attach-folder-toggle');
        const attachFolderPanel = container.querySelector('.attach-folder-panel');
        if (attachFolderToggle && attachFolderPanel) {
            const folderSearchInput = attachFolderPanel.querySelector('.attach-folder-search');
            const folderResultsEl = attachFolderPanel.querySelector('.attach-folder-results');
            const folderStatusEl = attachFolderPanel.querySelector('.attach-folder-status');
            const folderLoadMoreBtn = attachFolderPanel.querySelector('.attach-folder-load-more');
            const folderCloseBtn = attachFolderPanel.querySelector('.attach-folder-close');

            let folderCurrentPage = 1;
            let folderHasMore = false;
            let folderSearchDebounceTimer = null;
            let folderRequestToken = 0;

            function renderFolderResultItem(folder) {
                const li = document.createElement('li');
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'flex w-full items-start gap-2 rounded-md border border-gray-200 px-3 py-2 text-left text-[12px] hover:border-brand-600 hover:bg-brand-50';
                btn.innerHTML = '<i class="ti ti-folder mt-0.5 shrink-0 text-[14px] text-amber-500" aria-hidden="true"></i>'
                    + '<span class="min-w-0 truncate">' + escapeHtml(folder.path) + '</span>';
                btn.addEventListener('click', function () {
                    attachFolder(folder.id, btn);
                });
                li.appendChild(btn);
                folderResultsEl.appendChild(li);
            }

            function attachFolder(folderId, triggerEl) {
                if (triggerEl) triggerEl.disabled = true;
                folderStatusEl.textContent = '';

                requestOrThrow('/tasks/' + taskId + '/folders', 'POST', { folder_id: folderId }, 'Failed to attach folder.')
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        appendFolderRow(data.folder);
                        closeFolderPanel(true);
                    })
                    .catch(function (error) {
                        folderStatusEl.textContent = error.message;
                        if (triggerEl) triggerEl.disabled = false;
                    });
            }

            function fetchFolderResults(page, append) {
                const token = ++folderRequestToken;
                const search = folderSearchInput.value.trim();
                if (! append) folderStatusEl.textContent = 'Loading…';
                folderLoadMoreBtn.classList.add('hidden');

                const url = '/tasks/' + taskId + '/folders/attachable?page=' + page
                    + (search ? '&search=' + encodeURIComponent(search) : '');

                fetch(url, { headers: { Accept: 'application/json' } })
                    .then(function (response) {
                        if (! response.ok) throw new Error('Failed to load folders.');
                        return response.json();
                    })
                    .then(function (data) {
                        if (token !== folderRequestToken) return;

                        if (! append) folderResultsEl.innerHTML = '';
                        (data.data || []).forEach(renderFolderResultItem);

                        folderCurrentPage = data.current_page;
                        folderHasMore = data.current_page < data.last_page;
                        folderLoadMoreBtn.classList.toggle('hidden', ! folderHasMore);

                        if (folderResultsEl.children.length === 0) {
                            folderStatusEl.textContent = 'No folders found.';
                        } else {
                            folderStatusEl.textContent = '';
                        }
                    })
                    .catch(function (error) {
                        if (token !== folderRequestToken) return;
                        folderStatusEl.textContent = error.message;
                    });
            }

            function openFolderPanel() {
                attachFolderToggle.setAttribute('aria-expanded', 'true');
                attachFolderPanel.classList.remove('hidden');
                folderSearchInput.value = '';
                folderSearchInput.focus();
                fetchFolderResults(1, false);
            }

            function closeFolderPanel(returnFocus) {
                attachFolderToggle.setAttribute('aria-expanded', 'false');
                attachFolderPanel.classList.add('hidden');
                if (returnFocus) attachFolderToggle.focus();
            }

            attachFolderToggle.addEventListener('click', function () {
                if (attachFolderPanel.classList.contains('hidden')) {
                    openFolderPanel();
                } else {
                    closeFolderPanel(false);
                }
            });

            folderCloseBtn.addEventListener('click', function () {
                closeFolderPanel(true);
            });

            attachFolderPanel.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeFolderPanel(true);
                }
            });

            // Same arrow-key roving addition as the document picker's
            // results list — see its own comment for the reasoning.
            folderResultsEl.addEventListener('keydown', function (event) {
                if (! ['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) return;

                const buttons = Array.from(folderResultsEl.querySelectorAll('button'));
                const currentIndex = buttons.indexOf(document.activeElement);
                if (currentIndex === -1) return;

                let targetIndex;
                if (event.key === 'ArrowDown') targetIndex = Math.min(currentIndex + 1, buttons.length - 1);
                else if (event.key === 'ArrowUp') targetIndex = Math.max(currentIndex - 1, 0);
                else if (event.key === 'Home') targetIndex = 0;
                else targetIndex = buttons.length - 1;

                event.preventDefault();
                buttons[targetIndex].focus();
            });

            folderSearchInput.addEventListener('input', function () {
                if (folderSearchDebounceTimer) clearTimeout(folderSearchDebounceTimer);
                folderSearchDebounceTimer = setTimeout(function () {
                    fetchFolderResults(1, false);
                }, 300);
            });

            folderLoadMoreBtn.addEventListener('click', function () {
                fetchFolderResults(folderCurrentPage + 1, true);
            });
        }

        // task #73 (UI merge): the create-a-new-document sub-form, now
        // entered from one of the two action rows inside the merged
        // panel above instead of its own separate always-visible toggle
        // button. Its own fields/endpoint are unchanged.
        const newForm = container.querySelector('.new-document-form');
        let newDocumentMode = 'link';
        const modePanels = newForm ? newForm.querySelectorAll('.new-document-panel') : [];
        const createAndAttachBtn = newForm ? newForm.querySelector('.create-and-attach-btn') : null;

        // task #73 (attach panel polish): the submit button's caption
        // differs per tab - "Upload & Attach" on the Upload tab, since
        // that flow is upload-first, vs. the Add link tab's unchanged
        // "Create & attach". Same submit handler and endpoint either way.
        function activateNewDocumentMode(mode) {
            newDocumentMode = mode;
            modePanels.forEach(function (panel) {
                panel.classList.toggle('hidden', panel.dataset.panel !== mode);
            });
            if (createAndAttachBtn) {
                createAndAttachBtn.textContent = mode === 'upload' ? 'Upload & Attach' : 'Create & attach';
            }
        }

        const fileInput = container.querySelector('.new-document-file');
        const fileNameEl = container.querySelector('.new-document-file-name');
        const nameInput = container.querySelector('.new-document-name');

        // task #73 (document form fixes, code-review follow-up): the
        // auto-fill-on-select/re-select logic (extension-stripping, and
        // never clobbering a manual edit) now lives in
        // resources/js/document-name-autofill.js, shared with the
        // Documents page's own upload form — see its own docblock.
        const nameAutofill = window.solavaDocumentNameAutofill.wireFileNameAutofill({
            fileInput: fileInput,
            fileNameEl: fileNameEl,
            nameInput: nameInput,
        });

        // task #73 (code-review follow-up): clears the create-a-new-
        // document sub-form back to its untouched state — the file input,
        // its filename display, the Name/link fields, any shown error, and
        // the auto-fill "protected" tracking above (so a genuinely fresh
        // attempt can auto-fill again). Called whenever an in-progress
        // attempt is abandoned (Back to search, closing the whole panel,
        // reopening it) or completes (a successful upload/create) — a
        // stale file or name from one attempt can otherwise silently
        // survive into the next: previously, selecting a file, clicking
        // Back, then starting a DIFFERENT attempt (e.g. "Upload '<new
        // search>' as a new file") left both the old file and its derived
        // name in place, since nothing reset either one.
        function resetNewDocumentForm() {
            if (nameInput) nameInput.value = '';
            nameAutofill.reset();
            if (fileInput) fileInput.value = '';
            if (fileNameEl) { fileNameEl.textContent = 'No file selected'; fileNameEl.title = ''; }
            const linkInput = container.querySelector('.new-document-link');
            if (linkInput) linkInput.value = '';
            const errorEl = container.querySelector('.new-document-error');
            if (errorEl) { errorEl.style.display = 'none'; errorEl.textContent = ''; }
        }

        function openCreateForm(mode) {
            const searchView = container.querySelector('.attach-document-search-view');
            const searchInput = container.querySelector('.attach-document-search');
            activateNewDocumentMode(mode);

            // task #73 (attach panel polish): pre-fills Name from whatever
            // the user had typed into search (e.g. clicking "Upload
            // 'Budget' as a new file") — protected the same way a manual
            // edit is, so a file selected afterward in this same attempt
            // doesn't silently overwrite it.
            if (nameInput && ! nameInput.value && searchInput && searchInput.value.trim()) {
                nameInput.value = searchInput.value.trim();
                nameAutofill.protect();
            }

            if (searchView) searchView.classList.add('hidden');
            newForm.classList.remove('hidden');
            if (nameInput) nameInput.focus();
        }

        const backBtn = newForm ? newForm.querySelector('.new-document-back') : null;
        if (backBtn) {
            backBtn.addEventListener('click', function () {
                const searchView = container.querySelector('.attach-document-search-view');
                newForm.classList.add('hidden');
                if (searchView) searchView.classList.remove('hidden');
                resetNewDocumentForm();

                // Focus must land on something still visible. The Back
                // button itself has just been hidden along with newForm,
                // so skipping this entirely (as happens for a create-only
                // user, who has no search box) drops focus to <body> —
                // which also strands the Escape-to-close handler, since
                // that's bound to attachPanel. Fall back to the first
                // control that IS present for them.
                const searchInput = container.querySelector('.attach-document-search');
                const uploadAction = container.querySelector('.attach-document-upload-action');
                if (searchInput) {
                    searchInput.focus();
                } else if (uploadAction) {
                    uploadAction.focus();
                }
            });
        }

        const createBtn = container.querySelector('.create-and-attach-btn');
        if (createBtn) {
            createBtn.addEventListener('click', function () {
                const linkInput = container.querySelector('.new-document-link');
                const accessInput = container.querySelector('.new-document-access');
                const errorEl = container.querySelector('.new-document-error');
                errorEl.style.display = 'none';

                let responsePromise;
                if (newDocumentMode === 'upload') {
                    if (! fileInput.files[0]) {
                        errorEl.textContent = 'Choose a file to upload.';
                        errorEl.style.display = '';
                        return;
                    }
                    const formData = new FormData();
                    formData.append('organization_id', organizationId);
                    formData.append('name', nameInput.value.trim());
                    formData.append('file', fileInput.files[0]);
                    formData.append('access_level', accessInput.value);
                    formData.append('task_id', taskId);
                    responsePromise = throwOnFailure(requestForm('/documents', 'POST', formData), 'Failed to upload document.');
                } else {
                    responsePromise = requestOrThrow('/documents', 'POST', {
                        organization_id: Number(organizationId),
                        name: nameInput.value.trim(),
                        link: linkInput.value.trim(),
                        access_level: accessInput.value,
                        task_id: Number(taskId),
                    }, 'Failed to create document.');
                }

                // Guards against a double-click or an impatient second
                // click during a slow request firing two POST /documents
                // calls — each would succeed independently (two distinct
                // new Document rows, both attached to the task, no
                // collision to reject) with nothing to warn the user a
                // duplicate was created. Mirrors the picker's own
                // attachDocument(), which disables its trigger the same way.
                createBtn.disabled = true;

                responsePromise
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        appendDocumentRow(data.document);
                        resetNewDocumentForm();
                        const attachPanel = container.querySelector('.attach-document-panel');
                        const attachToggle = container.querySelector('.attach-document-toggle');
                        newForm.classList.add('hidden');
                        if (attachPanel) attachPanel.classList.add('hidden');
                        if (attachToggle) {
                            attachToggle.setAttribute('aria-expanded', 'false');
                            attachToggle.focus();
                        }
                    })
                    .catch(function (error) {
                        errorEl.textContent = error.message;
                        errorEl.style.display = '';
                    })
                    .finally(function () {
                        createBtn.disabled = false;
                    });
            });
        }
        }

        if (window.solavaDocumentNameAutofill) init();
        else document.addEventListener('DOMContentLoaded', init);
    })();
</script>
