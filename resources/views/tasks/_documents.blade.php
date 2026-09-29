{{-- Attach/detach existing company documents, or create a new one and
     attach it in the same step. Self-contained per inclusion
     (document.currentScript, not a global id) — matches _subtasks.blade.php
     and _comments.blade.php, since this is only ever rendered once per
     Edit Task page but follows the same pattern for consistency. --}}
<div class="document-container" data-task-id="{{ $task->id }}" data-organization-id="{{ $task->organization_id }}"
     data-project-has-client="{{ $projectHasClient ? '1' : '0' }}" data-can-unlink-documents="{{ $canUnlinkDocuments ? '1' : '0' }}">
    <div class="document-list space-y-2">
        @foreach ($attachedDocuments as $document)
            <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2" data-document-id="{{ $document->id }}">
                <div>
                    <a href="{{ route('file-downloads.show', ['url' => $document->url]) }}" target="_blank" rel="noopener" class="text-[12px] font-medium text-brand-600 hover:underline">{{ $document->name }}</a>
                    <span class="ml-2 rounded-sm bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-600">{{ $document->access_level->label() }}</span>
                </div>
                {{-- task #73 phase 2: TaskPolicy::unlinkDocuments() —
                     manage_documents + task view, NOT $canEdit (task-edit
                     rights) — deliberately its own gate, separate from the
                     attach-document panel. --}}
                @if ($canUnlinkDocuments)
                    <button type="button" class="detach-document-btn text-[11px] text-gray-500 hover:underline">Detach</button>
                @endif
            </div>
        @endforeach
        @if ($attachedDocuments->isEmpty())
            <p class="document-empty text-[11px] text-gray-500">No documents attached.</p>
        @endif
    </div>

    {{-- task #73 (UI merge): ONE entry point replacing the old separate
         "Attach existing" and "+ Add new document" buttons/panels — one
         search box that filters existing documents as you type, with
         "Upload as a new file" / "Add a link instead" always available
         below the results. Gated solely by TaskPolicy::attachDocuments()
         (manage_documents + task view + not a Client-role user) — the
         exact condition the old "Attach existing" button already used.
         This is a real, deliberate narrowing versus the old "+ Add new
         document" button's own gate (DocumentPolicy::create(), which a
         Client-role uploader COULD pass): a Client can no longer create-
         and-attach their own document from this page at all, since they
         never see this merged entry point. Flagged and confirmed with
         the task's own author before building — not an oversight. --}}
    @if ($canAttachDocuments)
        @php
            // task #73 phase 1: a Client-role uploader (in this task's
            // company) never sees the access-level dropdown — always
            // saved as Public, enforced server-side regardless in
            // DocumentUploadService::resolveAccessLevel() even if this
            // markup were somehow bypassed. Kept even though the merged
            // panel's own gate already excludes Client — DocumentPolicy::
            // create() itself doesn't, so this stays a real branch.
            $isClientUploader = auth()->user()->isClientInOrg($task->organization_id);
        @endphp
        <div class="mt-2">
            <button type="button" class="attach-document-toggle text-[11px] font-medium text-brand-600 hover:underline"
                aria-haspopup="dialog" aria-expanded="false" aria-controls="attach-document-panel-{{ $task->id }}">
                Attach document
            </button>
        </div>

        <div id="attach-document-panel-{{ $task->id }}" class="attach-document-panel mt-2 hidden space-y-2 rounded-md border border-gray-200 p-3" role="dialog" aria-label="Attach a document">
            {{-- View 1: search + matching documents + the two "create
                 instead" action rows. --}}
            <div class="attach-document-search-view space-y-2">
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
                <button type="button" class="new-document-back text-[11px] text-gray-500 hover:underline">&larr; Back to search</button>

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
                        <p class="mt-1 text-[10px] text-gray-500">PDF, Word, Excel, PowerPoint, text or CSV — up to 20MB.</p>
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
        const container = document.currentScript.previousElementSibling;
        const taskId = container.dataset.taskId;
        const organizationId = container.dataset.organizationId;
        const projectHasClient = container.dataset.projectHasClient === '1';
        const canUnlinkDocuments = container.dataset.canUnlinkDocuments === '1';
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
            const detachButtonHtml = canUnlinkDocuments
                ? '<button type="button" class="detach-document-btn text-[11px] text-gray-500 hover:underline">Detach</button>'
                : '';
            row.innerHTML = '<div><a href="' + escapeHtml(downloadUrl) + '" target="_blank" rel="noopener" class="text-[12px] font-medium text-brand-600 hover:underline">' + escapeHtml(doc.name) + '</a>'
                + '<span class="ml-2 rounded-sm bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-600">' + escapeHtml(accessLabel) + '</span></div>'
                + detachButtonHtml;
            listEl.appendChild(row);
            wireDetach(row);
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

            function fileIconClass(doc) {
                return doc.mime_type ? 'ti-file-text' : 'ti-link';
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
                searchInput.value = '';
                updateResultsHeading('');
                searchInput.focus();
                fetchResults(1, false);
            }

            function closePanel(returnFocus) {
                attachToggle.setAttribute('aria-expanded', 'false');
                attachPanel.classList.add('hidden');
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

            loadMoreBtn.addEventListener('click', function () {
                fetchResults(currentPage + 1, true);
            });

            uploadActionBtn.addEventListener('click', function () {
                openCreateForm('upload');
            });
            linkActionBtn.addEventListener('click', function () {
                openCreateForm('link');
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

        function openCreateForm(mode) {
            const searchView = container.querySelector('.attach-document-search-view');
            const searchInput = container.querySelector('.attach-document-search');
            activateNewDocumentMode(mode);

            const nameInput = container.querySelector('.new-document-name');
            if (nameInput && ! nameInput.value && searchInput) {
                nameInput.value = searchInput.value.trim();
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
                const searchInput = container.querySelector('.attach-document-search');
                if (searchInput) searchInput.focus();
            });
        }

        // task #73 (attach panel polish): strips the extension for the
        // name auto-fill ("Peace and Conflict Grade 1.pdf" -> "Peace and
        // Conflict Grade 1") — matches the last ".xyz" only, so an
        // incidental earlier dot in the filename (e.g. "report.v2.pdf")
        // is left alone and only the real extension is removed.
        function stripExtension(filename) {
            return filename.replace(/\.[^.]+$/, '');
        }

        const fileInput = container.querySelector('.new-document-file');
        const fileNameEl = container.querySelector('.new-document-file-name');
        if (fileInput) {
            fileInput.addEventListener('change', function () {
                const file = fileInput.files[0];

                if (fileNameEl) {
                    fileNameEl.textContent = file ? file.name : 'No file selected';
                    fileNameEl.title = file ? file.name : '';
                }

                // Only auto-fills an EMPTY name field — never overwrites a
                // value already there, whether the user typed it or it
                // came from clicking "Upload [name] as a new file" on a
                // search result.
                const nameInput = container.querySelector('.new-document-name');
                if (nameInput && ! nameInput.value && file) {
                    nameInput.value = stripExtension(file.name);
                }
            });
        }

        const createBtn = container.querySelector('.create-and-attach-btn');
        if (createBtn) {
            createBtn.addEventListener('click', function () {
                const nameInput = container.querySelector('.new-document-name');
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
                        nameInput.value = '';
                        linkInput.value = '';
                        if (fileInput) fileInput.value = '';
                        if (fileNameEl) { fileNameEl.textContent = 'No file selected'; fileNameEl.title = ''; }
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
    })();
</script>
