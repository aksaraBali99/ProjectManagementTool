{{-- Attach/detach existing company documents, or create a new one and
     attach it in the same step. Self-contained per inclusion
     (document.currentScript, not a global id) — matches _subtasks.blade.php
     and _comments.blade.php, since this is only ever rendered once per
     Edit Task page but follows the same pattern for consistency. --}}
<div class="document-container" data-task-id="{{ $task->id }}" data-organization-id="{{ $task->organization_id }}">
    <div class="document-list space-y-2">
        @foreach ($attachedDocuments as $document)
            <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2" data-document-id="{{ $document->id }}">
                <div>
                    <a href="{{ route('file-downloads.show', ['url' => $document->url]) }}" target="_blank" rel="noopener" class="text-[12px] font-medium text-brand-600 hover:underline">{{ $document->name }}</a>
                    <span class="ml-2 rounded-sm bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-600">{{ $document->access_level->label() }}</span>
                </div>
                @if ($canEdit)
                    <button type="button" class="detach-document-btn text-[11px] text-gray-500 hover:underline">Detach</button>
                @endif
            </div>
        @endforeach
        @if ($attachedDocuments->isEmpty())
            <p class="document-empty text-[11px] text-gray-500">No documents attached.</p>
        @endif
    </div>

    @if ($canEdit)
        <div class="mt-2 flex items-center gap-2">
            <select class="attach-document-select flex-1 rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                <option value="">Select a document…</option>
                @foreach ($availableDocuments as $document)
                    <option value="{{ $document->id }}">{{ $document->name }} ({{ $document->access_level->label() }})</option>
                @endforeach
            </select>
            <button type="button" class="attach-document-btn rounded-md border border-gray-300 px-3 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">Attach</button>
        </div>
    @endif

    {{-- Creating a new document is a different capability from editing
         this task (DocumentPolicy::create / manage_documents) — gated
         separately from $canEdit above, not folded into it. --}}
    @if ($canManageDocuments)
        @php
            // task #73 phase 1: a Client-role uploader (in this task's
            // company) never sees the access-level dropdown — always
            // saved as Public, enforced server-side regardless in
            // DocumentUploadService::resolveAccessLevel() even if this
            // markup were somehow bypassed.
            $isClientUploader = auth()->user()->isClientInOrg($task->organization_id);
        @endphp
        <button type="button" class="toggle-new-document mt-2 text-[11px] font-medium text-brand-600 hover:underline">+ Add new document</button>
        <div class="new-document-form mt-2 hidden space-y-2 rounded-md border border-gray-200 p-3">
            <div class="inline-flex rounded-md border border-gray-300 p-0.5" role="tablist">
                <button type="button" data-new-document-mode="link" class="new-document-mode-tab rounded px-3 py-1 text-[11px] font-medium">Add link</button>
                <button type="button" data-new-document-mode="upload" class="new-document-mode-tab rounded px-3 py-1 text-[11px] font-medium">Upload file</button>
            </div>

            <input type="text" class="new-document-name w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" placeholder="Document name">

            <div class="new-document-panel" data-panel="link">
                <input type="url" class="new-document-link w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" placeholder="https://…">
            </div>
            <div class="new-document-panel hidden" data-panel="upload">
                <input type="file" class="new-document-file w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                <p class="mt-1 text-[10px] text-gray-500">PDF, Word, Excel, PowerPoint, text or CSV — up to 20MB.</p>
            </div>

            @if ($isClientUploader)
                <input type="hidden" class="new-document-access" value="public">
            @else
                <select class="new-document-access w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    @foreach (\App\Enums\DocumentAccessLevel::cases() as $accessCase)
                        <option value="{{ $accessCase->value }}" {{ $accessCase === \App\Enums\DocumentAccessLevel::Internal ? 'selected' : '' }}>{{ $accessCase->label() }}</option>
                    @endforeach
                </select>
            @endif
            <button type="button" class="create-and-attach-btn rounded-md bg-brand-600 px-3 py-2 text-[12px] font-medium text-white hover:bg-brand-700">Create &amp; attach</button>
            <p class="new-document-error text-[11px] text-red-600" style="display: none;"></p>
        </div>
    @endif
</div>
<script>
    (function () {
        const container = document.currentScript.previousElementSibling;
        const taskId = container.dataset.taskId;
        const organizationId = container.dataset.organizationId;
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
            row.innerHTML = '<div><a href="' + escapeHtml(downloadUrl) + '" target="_blank" rel="noopener" class="text-[12px] font-medium text-brand-600 hover:underline">' + escapeHtml(doc.name) + '</a>'
                + '<span class="ml-2 rounded-sm bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-600">' + escapeHtml(accessLabel) + '</span></div>'
                + '<button type="button" class="detach-document-btn text-[11px] text-gray-500 hover:underline">Detach</button>';
            listEl.appendChild(row);
            wireDetach(row);
        }

        const attachSelect = container.querySelector('.attach-document-select');
        const attachBtn = container.querySelector('.attach-document-btn');
        if (attachBtn) {
            attachBtn.addEventListener('click', function () {
                const documentId = attachSelect.value;
                if (! documentId) return;

                requestOrThrow('/tasks/' + taskId + '/documents', 'POST', { document_id: Number(documentId) }, 'Failed to attach document.')
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        appendDocumentRow(data.document);
                        const chosenOption = attachSelect.querySelector('option[value="' + documentId + '"]');
                        if (chosenOption) chosenOption.remove();
                        attachSelect.value = '';
                    })
                    .catch(function (error) {
                        alert(error.message);
                    });
            });
        }

        const toggleBtn = container.querySelector('.toggle-new-document');
        const newForm = container.querySelector('.new-document-form');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                newForm.classList.toggle('hidden');
            });
        }

        // task #73 phase 1: the same Add link / Upload file toggle as the
        // standalone Documents page's own create form, just driven by
        // plain class toggles instead of a form-native hidden input,
        // since this whole widget is a fetch()-based component, not a
        // real <form> submission.
        let newDocumentMode = 'link';
        const modeTabs = newForm ? newForm.querySelectorAll('.new-document-mode-tab') : [];
        const modePanels = newForm ? newForm.querySelectorAll('.new-document-panel') : [];

        function activateNewDocumentMode(mode) {
            newDocumentMode = mode;
            modePanels.forEach(function (panel) {
                panel.classList.toggle('hidden', panel.dataset.panel !== mode);
            });
            modeTabs.forEach(function (tab) {
                const active = tab.dataset.newDocumentMode === mode;
                tab.classList.toggle('bg-brand-600', active);
                tab.classList.toggle('text-white', active);
                tab.classList.toggle('text-gray-600', !active);
            });
        }
        modeTabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                activateNewDocumentMode(tab.dataset.newDocumentMode);
            });
        });
        activateNewDocumentMode('link');

        const fileInput = container.querySelector('.new-document-file');
        if (fileInput) {
            fileInput.addEventListener('change', function () {
                const nameInput = container.querySelector('.new-document-name');
                if (nameInput && ! nameInput.value && fileInput.files[0]) {
                    nameInput.value = fileInput.files[0].name;
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

                responsePromise
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        appendDocumentRow(data.document);
                        nameInput.value = '';
                        linkInput.value = '';
                        if (fileInput) fileInput.value = '';
                        newForm.classList.add('hidden');
                    })
                    .catch(function (error) {
                        errorEl.textContent = error.message;
                        errorEl.style.display = '';
                    });
            });
        }
    })();
</script>
