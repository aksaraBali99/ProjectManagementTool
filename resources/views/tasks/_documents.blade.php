{{-- Attach/detach existing company documents, or create a new one and
     attach it in the same step. Self-contained per inclusion
     (document.currentScript, not a global id) — matches _subtasks.blade.php
     and _comments.blade.php, since this is only ever rendered once per
     Edit Task page but follows the same pattern for consistency. --}}
<div class="document-container" data-task-id="{{ $task->id }}" data-organization-id="{{ $task->organization_id }}">
    <div class="document-list space-y-2">
        @foreach ($attachedDocuments as $document)
            <div class="flex items-center justify-between rounded-[8px] border border-gray-200 px-3 py-2" data-document-id="{{ $document->id }}">
                <div>
                    <a href="{{ $document->link }}" target="_blank" rel="noopener" class="text-[12px] font-medium text-[#1D9E75] hover:underline">{{ $document->name }}</a>
                    <span class="ml-2 rounded-[5px] bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-600">{{ ucfirst($document->access_level) }}</span>
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
            <select class="attach-document-select flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                <option value="">Select a document…</option>
                @foreach ($availableDocuments as $document)
                    <option value="{{ $document->id }}">{{ $document->name }} ({{ ucfirst($document->access_level) }})</option>
                @endforeach
            </select>
            <button type="button" class="attach-document-btn rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">Attach</button>
        </div>

        <button type="button" class="toggle-new-document mt-2 text-[11px] font-medium text-[#1D9E75] hover:underline">+ Add new document</button>
        <div class="new-document-form mt-2 hidden space-y-2 rounded-[8px] border border-gray-200 p-3">
            <input type="text" class="new-document-name w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]" placeholder="Document name">
            <input type="url" class="new-document-link w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]" placeholder="https://…">
            <select class="new-document-access w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                <option value="private">Private</option>
                <option value="internal" selected>Internal</option>
                <option value="public">Public</option>
            </select>
            <button type="button" class="create-and-attach-btn rounded-[8px] bg-[#1D9E75] px-3 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">Create &amp; attach</button>
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
                const documentId = row.dataset.documentId;
                request('/tasks/' + taskId + '/documents/' + documentId, 'DELETE')
                    .then(function (response) {
                        if (! response.ok) throw new Error();
                        row.remove();
                        if (! listEl.querySelector('[data-document-id]')) {
                            const empty = document.createElement('p');
                            empty.className = 'document-empty text-[11px] text-gray-500';
                            empty.textContent = 'No documents attached.';
                            listEl.appendChild(empty);
                        }
                    })
                    .catch(function () {
                        alert('Failed to detach document.');
                    });
            });
        }

        listEl.querySelectorAll('[data-document-id]').forEach(wireDetach);

        function appendDocumentRow(doc) {
            clearEmptyState();
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between rounded-[8px] border border-gray-200 px-3 py-2';
            row.dataset.documentId = doc.id;
            const accessLabel = doc.access_level.charAt(0).toUpperCase() + doc.access_level.slice(1);
            row.innerHTML = '<div><a href="' + escapeHtml(doc.link) + '" target="_blank" rel="noopener" class="text-[12px] font-medium text-[#1D9E75] hover:underline">' + escapeHtml(doc.name) + '</a>'
                + '<span class="ml-2 rounded-[5px] bg-gray-100 px-1.5 py-0.5 text-[10px] text-gray-600">' + escapeHtml(accessLabel) + '</span></div>'
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

                request('/tasks/' + taskId + '/documents', 'POST', { document_id: Number(documentId) })
                    .then(function (response) {
                        if (! response.ok) throw new Error();
                        return response.json();
                    })
                    .then(function (data) {
                        appendDocumentRow(data.document);
                        const chosenOption = attachSelect.querySelector('option[value="' + documentId + '"]');
                        if (chosenOption) chosenOption.remove();
                        attachSelect.value = '';
                    })
                    .catch(function () {
                        alert('Failed to attach document.');
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

        const createBtn = container.querySelector('.create-and-attach-btn');
        if (createBtn) {
            createBtn.addEventListener('click', function () {
                const nameInput = container.querySelector('.new-document-name');
                const linkInput = container.querySelector('.new-document-link');
                const accessSelect = container.querySelector('.new-document-access');
                const errorEl = container.querySelector('.new-document-error');
                errorEl.style.display = 'none';

                request('/documents', 'POST', {
                    organization_id: Number(organizationId),
                    name: nameInput.value.trim(),
                    link: linkInput.value.trim(),
                    access_level: accessSelect.value,
                    task_id: Number(taskId),
                })
                    .then(function (response) {
                        if (! response.ok) return response.json().then(function (body) { throw body; });
                        return response.json();
                    })
                    .then(function (data) {
                        appendDocumentRow(data.document);
                        nameInput.value = '';
                        linkInput.value = '';
                        newForm.classList.add('hidden');
                    })
                    .catch(function (body) {
                        errorEl.textContent = (body && body.message) ? body.message : 'Failed to create document.';
                        errorEl.style.display = '';
                    });
            });
        }
    })();
</script>
