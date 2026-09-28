@extends('layouts.authenticated')

@section('title', 'Documents — Solava')

@section('content')
<div>
    <h1 class="text-[14px] font-medium text-[#1F2937]">Documents</h1>

    @if (session('status'))
        <div class="mt-3 rounded-md bg-brand-50 px-3 py-2 text-[12px] text-brand-800">{{ session('status') }}</div>
    @endif

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">You don't have access to any companies yet.</p>
    @else
        <x-company-tabs :organizations="$organizations" :active="$organization" route="documents.index">
        {{-- task #73: ONE "+ New" menu replaces the old header "+ Add new
             document" button and the separate "+ New folder" link — both
             acted on the current company/folder, so the control lives
             here, on the breadcrumb row, not in the page header above the
             tabs. flex-wrap so a long breadcrumb wraps onto its own line
             on a narrow screen rather than pushing the button off-screen
             or shrinking it. --}}
        <div class="mb-3 flex flex-wrap items-start justify-between gap-2">
            {{-- Breadcrumb: company root > folder > subfolder. Switching
                 company tab (the tabs above) always lands on that
                 company's root — this nav is only about moving within
                 the CURRENT tab. --}}
            <nav class="flex flex-wrap items-center gap-1 text-[11px]" aria-label="Folder breadcrumb">
                <a href="{{ route('documents.index', $organization) }}"
                   class="{{ $folder === null ? 'font-medium text-[#1F2937]' : 'text-gray-500 hover:underline' }}">
                    {{ $organization->name }}
                </a>
                @foreach ($breadcrumb as $crumb)
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('documents.index', ['organization' => $organization, 'folder' => $crumb->id]) }}"
                       class="{{ $folder && $folder->id === $crumb->id ? 'font-medium text-[#1F2937]' : 'text-gray-500 hover:underline' }}">
                        {{ $crumb->name }}
                    </a>
                @endforeach
            </nav>

            {{-- task #73: manage_documents in THIS company (the same
                 check the old header button already used) decides whether
                 the whole menu renders at all — no button, not a disabled
                 one, when it doesn't pass. Upload file / Add link /
                 New folder all sit behind this one gate; none of them
                 have their own separate visibility check (their
                 endpoints still enforce the real permission regardless). --}}
            @if ($canManage)
                {{-- ml-auto: when the breadcrumb is long enough to wrap
                     (flex-wrap above), this button drops onto its own line
                     alone — without ml-auto it would sit flush left on that
                     line instead of staying right-aligned, which also drags
                     its right-0-anchored dropdown off the left edge of a
                     narrow viewport. --}}
                <div class="relative ml-auto shrink-0" data-new-menu>
                    <button type="button"
                        class="new-menu-toggle inline-flex items-center gap-1 rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700"
                        aria-haspopup="menu" aria-expanded="false">
                        + New
                        <i class="ti ti-chevron-down text-[12px]" aria-hidden="true"></i>
                    </button>
                    <div class="new-menu-dropdown absolute right-0 z-20 mt-1 hidden w-48 rounded-md border border-gray-200 bg-white py-1 shadow-lg" role="menu" aria-label="Create new">
                        <a href="{{ route('documents.create', array_filter(['organization' => $organization->id, 'folder' => $folder?->id, 'mode' => 'upload'])) }}"
                           role="menuitem" tabindex="-1"
                           class="new-menu-item flex items-center gap-2 px-3 py-2 text-[12px] text-gray-700 hover:bg-gray-50 focus:bg-gray-50 focus:outline-none">
                            <i class="ti ti-upload text-[14px] text-gray-500" aria-hidden="true"></i>
                            Upload file
                        </a>
                        <a href="{{ route('documents.create', array_filter(['organization' => $organization->id, 'folder' => $folder?->id, 'mode' => 'link'])) }}"
                           role="menuitem" tabindex="-1"
                           class="new-menu-item flex items-center gap-2 px-3 py-2 text-[12px] text-gray-700 hover:bg-gray-50 focus:bg-gray-50 focus:outline-none">
                            <i class="ti ti-link text-[14px] text-gray-500" aria-hidden="true"></i>
                            Add link
                        </a>
                        <button type="button" data-new-menu-action="new-folder"
                           role="menuitem" tabindex="-1"
                           class="new-menu-item flex w-full items-center gap-2 px-3 py-2 text-left text-[12px] text-gray-700 hover:bg-gray-50 focus:bg-gray-50 focus:outline-none">
                            <i class="ti ti-folder-plus text-[14px] text-gray-500" aria-hidden="true"></i>
                            New folder
                        </button>
                    </div>
                </div>
            @endif
        </div>

        {{-- New folder: same existing inline form as before, just opened
             from the "+ New" menu's own item instead of a dedicated
             always-visible link. Gated separately from $canManage above
             (DocumentFolderPolicy::create(), not DocumentPolicy::create())
             even though the two agree for every real role today, so this
             stays correct if that ever changes. --}}
        @if ($canManageFolders)
            <div class="new-folder-form mb-3 hidden flex items-start gap-2 rounded-md border border-gray-200 p-3">
                <div class="flex-1">
                    <input type="text" class="new-folder-name w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" placeholder="Folder name">
                    <p class="new-folder-error mt-1 text-[11px] text-red-600" style="display: none;"></p>
                </div>
                <button type="button" class="create-folder-btn rounded-md bg-brand-600 px-3 py-2 text-[12px] font-medium text-white hover:bg-brand-700">Create</button>
            </div>
        @endif

        <div class="overflow-hidden rounded-lg border border-gray-200"
             data-documents-container
             data-organization-id="{{ $organization->id }}"
             data-folder-id="{{ $folder?->id }}">
            <table class="w-full md:min-w-full md:divide-y md:divide-gray-200">
                <x-table-header>
                    <x-th>Name</x-th>
                    <x-th>Access level</x-th>
                    <x-th>Uploaded by / Created by</x-th>
                    <x-th>Origin</x-th>
                    <x-th>Linked tasks</x-th>
                    <x-th>Actions</x-th>
                </x-table-header>
                <tbody class="document-list block divide-y divide-gray-100 bg-white md:table-row-group">
                    {{-- Folders first (alphabetical, already ordered by the
                         controller), then documents. Folders have no
                         access_level/origin/linked-task concept of their
                         own, so those cells are simply blank on a folder row. --}}
                    @foreach ($folders as $subfolder)
                        @php $canManageThisFolder = $canManageAnyFolder && ($isPrivilegedManager || $subfolder->created_by === auth()->id()); @endphp
                        <tr class="folder-row block px-3 py-2.5 md:table-row md:px-0 md:py-0" data-folder-id="{{ $subfolder->id }}">
                            <td class="text-[12px] font-medium text-[#1F2937] md:table-cell md:px-3 md:py-2.5">
                                <a href="{{ route('documents.index', ['organization' => $organization, 'folder' => $subfolder->id]) }}" class="folder-name inline-flex items-center gap-1.5 hover:underline">
                                    <span aria-hidden="true">📁</span>
                                    <span>{{ $subfolder->name }}</span>
                                </a>
                            </td>
                            <td class="hidden md:table-cell md:px-3 md:py-2.5"></td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Created by</span>
                                <span class="inline-flex items-center gap-1.5">
                                    <x-avatar :user="$subfolder->creator" size="16px" />
                                    {{ $subfolder->creator->name }}
                                    <span class="text-gray-400">· {{ $subfolder->created_at->format('M j, Y') }}</span>
                                </span>
                            </td>
                            <td class="hidden md:table-cell md:px-3 md:py-2.5"></td>
                            <td class="hidden md:table-cell md:px-3 md:py-2.5"></td>
                            <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                                @if ($canManageThisFolder)
                                    <span class="inline-flex gap-2">
                                        <button type="button" class="rename-folder-btn text-[11px] text-gray-500 hover:underline">Rename</button>
                                        <button type="button" class="delete-folder-btn text-[11px] text-gray-500 hover:underline">Delete</button>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @forelse ($documents as $document)
                        @php $canManageThisDocument = $hasManageDocuments && ($isPrivilegedManager || $document->uploaded_by === auth()->id()); @endphp
                        <tr class="document-row block px-3 py-2.5 md:table-row md:px-0 md:py-0" data-document-id="{{ $document->id }}">
                            <td class="text-[12px] font-medium text-[#1F2937] md:table-cell md:px-3 md:py-2.5">
                                <a href="{{ route('file-downloads.show', ['url' => $document->url]) }}" target="_blank" rel="noopener noreferrer" class="document-name-link hover:underline">{{ $document->name }}</a>
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Access level</span>
                                <span class="document-access-badge">
                                    @if ($document->access_level === \App\Enums\DocumentAccessLevel::Private)
                                        <span class="rounded-sm bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">{{ $document->access_level->label() }}</span>
                                    @elseif ($document->access_level === \App\Enums\DocumentAccessLevel::Internal)
                                        <span class="rounded-sm bg-[#FDF1D9] px-2 py-0.5 text-[10px] font-medium text-[#8A5A00]">{{ $document->access_level->label() }}</span>
                                    @else
                                        <span class="rounded-sm bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">{{ $document->access_level->label() }}</span>
                                    @endif
                                </span>
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Uploaded by</span>
                                <span class="inline-flex items-center gap-1.5">
                                    <x-avatar :user="$document->uploader" size="16px" />
                                    {{ $document->uploader->name }}
                                </span>
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Origin</span>
                                @if ($document->origin_task_id === null)
                                    <span>Documents page</span>
                                @elseif ($originTasks->has($document->origin_task_id))
                                    <a href="{{ route('tasks.edit', $document->origin_task_id) }}" class="text-brand-600 hover:underline">{{ $originTasks[$document->origin_task_id]->title }}</a>
                                @else
                                    <span class="text-gray-400">From a task</span>
                                @endif
                            </td>
                            @php $linkedTaskCount = $linkedTaskCounts[$document->id] ?? 0; @endphp
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Linked tasks</span>
                                {{-- task #73: count 0 stays plain, non-interactive text — no
                                     popover, nothing to show. Above 0, it's a button whose
                                     popover content is fetched lazily on first open
                                     (documents.linked-tasks), never rendered into this page's
                                     own HTML. --}}
                                @if ($linkedTaskCount > 0)
                                    <button type="button" class="linked-tasks-trigger text-brand-600 underline decoration-dotted underline-offset-2 hover:text-brand-700"
                                        data-document-id="{{ $document->id }}"
                                        data-linked-tasks-url="{{ route('documents.linked-tasks', $document) }}"
                                        aria-haspopup="dialog" aria-expanded="false"
                                        aria-label="{{ $linkedTaskCount }} linked {{ Str::plural('task', $linkedTaskCount) }}, show list">{{ $linkedTaskCount }}</button>
                                @else
                                    <span>{{ $linkedTaskCount }}</span>
                                @endif
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                                @if ($canManageThisDocument)
                                    <span class="inline-flex gap-2">
                                        <button type="button" class="edit-document-btn text-[11px] text-gray-500 hover:underline">Edit</button>
                                        <button type="button" class="delete-document-btn text-[11px] text-gray-500 hover:underline">Delete</button>
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @if ($canManageThisDocument)
                            <tr class="document-panel-row hidden" data-panel-for-document="{{ $document->id }}">
                                <td colspan="6" class="border-t border-gray-100 bg-gray-50 px-3 py-3">
                                    {{-- Edit: rename / move / access level. --}}
                                    <div class="edit-document-panel hidden space-y-2">
                                        <div>
                                            <label class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Name</label>
                                            <input type="text" class="edit-document-name mt-1 w-full max-w-sm rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" value="{{ $document->name }}">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Folder</label>
                                            <select class="edit-document-folder mt-1 w-full max-w-sm rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                                                <option value="" {{ $document->folder_id === null ? 'selected' : '' }}>{{ $organization->name }} (root)</option>
                                                @foreach ($allFolders as $folderOption)
                                                    <option value="{{ $folderOption->id }}" {{ $document->folder_id === $folderOption->id ? 'selected' : '' }}>{{ $folderOption->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Access level</label>
                                            <select class="edit-document-access mt-1 w-full max-w-sm rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                                                @foreach (\App\Enums\DocumentAccessLevel::cases() as $accessCase)
                                                    <option value="{{ $accessCase->value }}" {{ $document->access_level === $accessCase ? 'selected' : '' }}>{{ $accessCase->label() }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex items-center gap-3 pt-1">
                                            <button type="button" class="save-document-btn rounded-md bg-brand-600 px-3 py-2 text-[12px] font-medium text-white hover:bg-brand-700">Save</button>
                                            <button type="button" class="cancel-edit-document-btn text-[12px] text-gray-600 hover:underline">Cancel</button>
                                        </div>
                                        <p class="edit-document-error text-[11px] text-red-600" style="display: none;"></p>
                                        {{-- "Blocked" (to Private, while linked) — same shape/wording as delete's own block. --}}
                                        <div class="edit-document-blocked hidden rounded-md border border-red-200 bg-red-50 p-3 text-[11px]">
                                            <p class="blocked-message font-medium text-red-800"></p>
                                            <ul class="blocked-task-list mt-1.5 space-y-1"></ul>
                                            <p class="blocked-hidden-count mt-1 text-gray-500"></p>
                                        </div>
                                    </div>

                                    {{-- Delete: preview first, then either the blocked (linked-tasks) state or a plain permanent-delete confirmation. --}}
                                    <div class="delete-document-panel hidden space-y-2">
                                        <div class="delete-document-blocked hidden rounded-md border border-red-200 bg-red-50 p-3 text-[11px]">
                                            <p class="blocked-message font-medium text-red-800"></p>
                                            <ul class="blocked-task-list mt-1.5 space-y-1"></ul>
                                            <p class="blocked-hidden-count mt-1 text-gray-500"></p>
                                            <div class="blocked-actions mt-2 flex items-center gap-3"></div>
                                        </div>
                                        <div class="delete-document-confirm hidden rounded-md border border-gray-200 p-3 text-[12px]">
                                            <p class="delete-confirm-message text-[#1F2937]"></p>
                                            <div class="mt-2 flex items-center gap-3">
                                                <button type="button" class="confirm-delete-document-btn rounded-md bg-red-600 px-3 py-2 text-[12px] font-medium text-white hover:bg-red-700">Delete permanently</button>
                                                <button type="button" class="cancel-delete-document-btn text-[12px] text-gray-600 hover:underline">Cancel</button>
                                            </div>
                                        </div>
                                        <p class="delete-document-error text-[11px] text-red-600" style="display: none;"></p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        @if ($folders->isEmpty())
                            <x-empty-table-row colspan="6" py="6">No folders or documents here yet.</x-empty-table-row>
                        @endif
                    @endforelse
                </tbody>
            </table>
        </div>
        </x-company-tabs>
    @endif
</div>

@if (! $organizations->isEmpty() && $organization)
<script>
    (function () {
        const container = document.querySelector('[data-documents-container]');
        if (! container) return;

        const organizationId = container.dataset.organizationId;
        const currentFolderId = container.dataset.folderId || '';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function requestOrThrow(url, method, body, fallback) {
            return fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: body ? JSON.stringify(body) : undefined,
            }).then(function (response) {
                return response.json().catch(function () { return {}; }).then(function (data) {
                    return { ok: response.ok, status: response.status, data: data };
                });
            });
        }

        // Same as requestOrThrow, but throws on a non-ok response instead
        // of resolving with it — for the simpler fire-and-reload actions
        // (folder create/rename/delete) that don't need to inspect a
        // structured error body.
        function requestOrThrowSimple(url, method, body, fallback) {
            return requestOrThrow(url, method, body, fallback).then(function (result) {
                if (result.ok) return result;
                const fieldErrors = result.data && result.data.errors ? Object.values(result.data.errors)[0] : null;
                const message = (Array.isArray(fieldErrors) && fieldErrors[0]) || (result.data && result.data.message) || fallback;
                throw new Error(message);
            });
        }

        // task #73: the create menu — a plain button + role="menu"
        // dropdown, since the app has no existing menu/dropdown component
        // to reuse. Only one can ever be open at a time (there's only one
        // per page — the menu is per company tab, and only one tab's
        // panel is ever rendered), so this doesn't need to coordinate
        // across multiple instances the way the per-row Edit/Delete
        // panels below do.
        const newMenuRoot = document.querySelector('[data-new-menu]');
        if (newMenuRoot) {
            const menuToggle = newMenuRoot.querySelector('.new-menu-toggle');
            const menuDropdown = newMenuRoot.querySelector('.new-menu-dropdown');
            const menuItems = Array.prototype.slice.call(newMenuRoot.querySelectorAll('[role="menuitem"]'));

            function openMenu() {
                menuDropdown.classList.remove('hidden');
                menuToggle.setAttribute('aria-expanded', 'true');
            }

            function closeMenu(returnFocus) {
                menuDropdown.classList.add('hidden');
                menuToggle.setAttribute('aria-expanded', 'false');
                if (returnFocus) menuToggle.focus();
            }

            function isOpen() {
                return ! menuDropdown.classList.contains('hidden');
            }

            menuToggle.addEventListener('click', function () {
                if (isOpen()) {
                    closeMenu(false);
                } else {
                    openMenu();
                    if (menuItems[0]) menuItems[0].focus();
                }
            });

            // Enter/Space opening the button is native <button> behavior
            // already (both fire a click) — this only adds the part native
            // behavior doesn't cover: arrow-key movement between items,
            // and jumping straight to the first/last item on open.
            menuToggle.addEventListener('keydown', function (event) {
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                    event.preventDefault();
                    openMenu();
                    const index = event.key === 'ArrowDown' ? 0 : menuItems.length - 1;
                    if (menuItems[index]) menuItems[index].focus();
                }
            });

            menuItems.forEach(function (item, index) {
                item.addEventListener('keydown', function (event) {
                    if (event.key === 'ArrowDown') {
                        event.preventDefault();
                        const next = menuItems[(index + 1) % menuItems.length];
                        if (next) next.focus();
                    } else if (event.key === 'ArrowUp') {
                        event.preventDefault();
                        const previous = menuItems[(index - 1 + menuItems.length) % menuItems.length];
                        if (previous) previous.focus();
                    } else if (event.key === 'Escape') {
                        event.preventDefault();
                        closeMenu(true);
                    } else if (event.key === 'Tab') {
                        // Tabbing out of the menu closes it, same as
                        // clicking outside — it shouldn't stay open once
                        // focus has moved elsewhere on the page.
                        closeMenu(false);
                    }
                });
            });

            document.addEventListener('click', function (event) {
                if (isOpen() && ! newMenuRoot.contains(event.target)) {
                    closeMenu(false);
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && isOpen() && newMenuRoot.contains(document.activeElement)) {
                    closeMenu(true);
                }
            });

            // The folder-creation item is the one menu entry that isn't a
            // plain link — it opens the same existing inline form the old
            // standalone folder-creation link used to, just triggered from
            // here now.
            const newFolderMenuItem = newMenuRoot.querySelector('[data-new-menu-action="new-folder"]');
            const newFolderFormEl = document.querySelector('.new-folder-form');
            if (newFolderMenuItem && newFolderFormEl) {
                newFolderMenuItem.addEventListener('click', function () {
                    closeMenu(false);
                    newFolderFormEl.classList.remove('hidden');
                    const nameInput = newFolderFormEl.querySelector('.new-folder-name');
                    if (nameInput) nameInput.focus();
                });
            }
        }

        const createFolderBtn = document.querySelector('.create-folder-btn');
        if (createFolderBtn) {
            createFolderBtn.addEventListener('click', function () {
                const nameInput = document.querySelector('.new-folder-name');
                const errorEl = document.querySelector('.new-folder-error');
                errorEl.style.display = 'none';

                requestOrThrowSimple('/document-folders', 'POST', {
                    organization_id: Number(organizationId),
                    parent_id: currentFolderId ? Number(currentFolderId) : null,
                    name: nameInput.value.trim(),
                }, 'Failed to create folder.')
                    .then(function () {
                        window.location.reload();
                    })
                    .catch(function (error) {
                        errorEl.textContent = error.message;
                        errorEl.style.display = '';
                    });
            });
        }

        // Rename / delete a folder
        container.querySelectorAll('.folder-row').forEach(function (row) {
            const folderId = row.dataset.folderId;

            const renameBtn = row.querySelector('.rename-folder-btn');
            if (renameBtn) {
                renameBtn.addEventListener('click', function () {
                    const currentName = row.querySelector('.folder-name span:last-child').textContent;
                    const newName = window.prompt('Rename folder', currentName);
                    if (newName === null || newName.trim() === '' || newName.trim() === currentName) return;

                    requestOrThrowSimple('/document-folders/' + folderId, 'PUT', { name: newName.trim() }, 'Failed to rename folder.')
                        .then(function () {
                            window.location.reload();
                        })
                        .catch(function (error) {
                            alert(error.message);
                        });
                });
            }

            const deleteBtn = row.querySelector('.delete-folder-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function () {
                    if (! confirm('Delete this folder? This only works while it is empty.')) return;

                    requestOrThrowSimple('/document-folders/' + folderId, 'DELETE', undefined, 'Failed to delete folder.')
                        .then(function () {
                            window.location.reload();
                        })
                        .catch(function (error) {
                            alert(error.message);
                        });
                });
            }
        });

        // Renders the shared "blocked" shape (message + viewable tasks
        // with Unlink buttons + hidden count) into a given container —
        // used identically by the Edit panel's blocked-to-private state
        // and the Delete panel's blocked state, since the server sends
        // both in the exact same shape.
        //
        // onCancel is optional: when given (the Delete panel), each
        // task's Unlink button and a single Cancel button are rendered
        // together into a shared .blocked-actions row, in that order —
        // Unlink(s) first (left), Cancel last (right) — regardless of how
        // many tasks are linked, and the list itself becomes plain,
        // read-only titles. Without it (the Edit panel, which has no
        // .blocked-actions element and its own Save/Cancel row above),
        // each task keeps its own inline Unlink button instead.
        function renderBlocked(blockedEl, data, documentId, onUnlinked, onCancel) {
            blockedEl.querySelector('.blocked-message').textContent = data.message;

            const list = blockedEl.querySelector('.blocked-task-list');
            list.innerHTML = '';
            (data.linked_tasks || []).forEach(function (task) {
                const item = document.createElement('li');
                item.className = onCancel ? '' : 'flex items-center justify-between gap-2';
                const link = document.createElement('a');
                link.href = '/tasks/' + task.id + '/edit';
                link.target = '_blank';
                link.rel = 'noopener';
                link.className = 'text-brand-600 hover:underline';
                link.textContent = task.title;
                item.appendChild(link);

                if (! onCancel) {
                    const unlinkBtn = document.createElement('button');
                    unlinkBtn.type = 'button';
                    unlinkBtn.className = 'text-gray-500 hover:underline';
                    unlinkBtn.textContent = 'Unlink';
                    unlinkBtn.addEventListener('click', function () {
                        requestOrThrowSimple('/tasks/' + task.id + '/documents/' + documentId, 'DELETE', undefined, 'Failed to unlink.')
                            .then(onUnlinked)
                            .catch(function (error) { alert(error.message); });
                    });
                    item.appendChild(unlinkBtn);
                }

                list.appendChild(item);
            });

            const hiddenCountEl = blockedEl.querySelector('.blocked-hidden-count');
            if (data.hidden_linked_task_count > 0) {
                hiddenCountEl.textContent = 'and ' + data.hidden_linked_task_count + ' more tasks you don\'t have access to.';
                hiddenCountEl.style.display = '';
            } else {
                hiddenCountEl.style.display = 'none';
            }

            const actionsEl = blockedEl.querySelector('.blocked-actions');
            if (onCancel && actionsEl) {
                actionsEl.innerHTML = '';

                (data.linked_tasks || []).forEach(function (task) {
                    const unlinkBtn = document.createElement('button');
                    unlinkBtn.type = 'button';
                    unlinkBtn.className = 'rounded-md border border-gray-300 bg-white px-3 py-1.5 text-[11px] font-medium text-gray-700 hover:bg-gray-50';
                    unlinkBtn.textContent = (data.linked_tasks.length > 1) ? 'Unlink "' + task.title + '"' : 'Unlink';
                    unlinkBtn.addEventListener('click', function () {
                        requestOrThrowSimple('/tasks/' + task.id + '/documents/' + documentId, 'DELETE', undefined, 'Failed to unlink.')
                            .then(onUnlinked)
                            .catch(function (error) { alert(error.message); });
                    });
                    actionsEl.appendChild(unlinkBtn);
                });

                const cancelBtn = document.createElement('button');
                cancelBtn.type = 'button';
                cancelBtn.className = 'cancel-delete-document-btn text-[12px] text-gray-600 hover:underline';
                cancelBtn.textContent = 'Cancel';
                cancelBtn.addEventListener('click', onCancel);
                actionsEl.appendChild(cancelBtn);
            }

            blockedEl.classList.remove('hidden');
        }

        // Edit / Delete per document
        container.querySelectorAll('.document-row').forEach(function (row) {
            const documentId = row.dataset.documentId;
            const panelRow = container.querySelector('[data-panel-for-document="' + documentId + '"]');
            if (! panelRow) return;

            const editBtn = row.querySelector('.edit-document-btn');
            const deleteBtn = row.querySelector('.delete-document-btn');
            const editPanel = panelRow.querySelector('.edit-document-panel');
            const deletePanel = panelRow.querySelector('.delete-document-panel');

            function closeAllPanels() {
                panelRow.classList.add('hidden');
                editPanel.classList.add('hidden');
                deletePanel.classList.add('hidden');
                editPanel.querySelector('.edit-document-blocked').classList.add('hidden');
                editPanel.querySelector('.edit-document-error').style.display = 'none';
                deletePanel.querySelector('.delete-document-blocked').classList.add('hidden');
                deletePanel.querySelector('.delete-document-confirm').classList.add('hidden');
                deletePanel.querySelector('.delete-document-error').style.display = 'none';
            }

            if (editBtn) {
                editBtn.addEventListener('click', function () {
                    const alreadyOpen = ! panelRow.classList.contains('hidden') && ! editPanel.classList.contains('hidden');
                    closeAllPanels();
                    if (! alreadyOpen) {
                        panelRow.classList.remove('hidden');
                        editPanel.classList.remove('hidden');
                    }
                });
            }

            const cancelEditBtn = editPanel.querySelector('.cancel-edit-document-btn');
            if (cancelEditBtn) {
                cancelEditBtn.addEventListener('click', closeAllPanels);
            }

            function submitEdit(extra) {
                const errorEl = editPanel.querySelector('.edit-document-error');
                const blockedEl = editPanel.querySelector('.edit-document-blocked');
                errorEl.style.display = 'none';
                blockedEl.classList.add('hidden');

                const folderValue = editPanel.querySelector('.edit-document-folder').value;
                const payload = Object.assign({
                    name: editPanel.querySelector('.edit-document-name').value.trim(),
                    folder_id: folderValue ? Number(folderValue) : null,
                    access_level: editPanel.querySelector('.edit-document-access').value,
                }, extra || {});

                requestOrThrow('/documents/' + documentId, 'PUT', payload, 'Failed to save document.')
                    .then(function (result) {
                        if (result.ok) {
                            window.location.reload();
                            return;
                        }

                        if (result.data && result.data.requires_confirmation) {
                            if (confirm(result.data.message + ' Continue?')) {
                                submitEdit({ confirm_public_visibility: true });
                            }
                            return;
                        }

                        if (result.data && result.data.linked_tasks) {
                            renderBlocked(blockedEl, result.data, documentId, function () {
                                submitEdit(extra);
                            });
                            return;
                        }

                        errorEl.textContent = (result.data && result.data.message) || 'Failed to save document.';
                        errorEl.style.display = '';
                    });
            }

            const saveBtn = editPanel.querySelector('.save-document-btn');
            if (saveBtn) {
                saveBtn.addEventListener('click', function () { submitEdit(); });
            }

            if (deleteBtn) {
                deleteBtn.addEventListener('click', function () {
                    const alreadyOpen = ! panelRow.classList.contains('hidden') && ! deletePanel.classList.contains('hidden');
                    closeAllPanels();
                    if (alreadyOpen) return;

                    panelRow.classList.remove('hidden');
                    deletePanel.classList.remove('hidden');
                    checkDeleteDependencies();
                });
            }

            function checkDeleteDependencies() {
                const blockedEl = deletePanel.querySelector('.delete-document-blocked');
                const confirmEl = deletePanel.querySelector('.delete-document-confirm');
                const errorEl = deletePanel.querySelector('.delete-document-error');
                blockedEl.classList.add('hidden');
                confirmEl.classList.add('hidden');
                errorEl.style.display = 'none';

                const documentName = row.querySelector('.document-name-link').textContent;

                requestOrThrowSimple('/documents/' + documentId + '/dependencies', 'GET', undefined, 'Failed to load document status.')
                    .then(function (result) {
                        const data = result.data;
                        if (data.linked_task_count > 0) {
                            renderBlocked(blockedEl, {
                                message: 'This document is still attached to ' + data.linked_task_count + ' tasks. Remove it from them first.',
                                linked_tasks: data.viewable_tasks,
                                hidden_linked_task_count: data.hidden_linked_task_count,
                            }, documentId, checkDeleteDependencies, closeAllPanels);
                        } else {
                            confirmEl.querySelector('.delete-confirm-message').textContent =
                                'Permanently delete "' + documentName + '"? This can\'t be undone.';
                            confirmEl.classList.remove('hidden');
                        }
                    })
                    .catch(function (error) {
                        errorEl.textContent = error.message;
                        errorEl.style.display = '';
                    });
            }

            // The plain delete-confirmation state's own static Cancel
            // button — the blocked (linked-tasks) state's Cancel is
            // rendered dynamically by renderBlocked() (via its onCancel
            // param) and wired there instead, since it doesn't exist in
            // the initial page markup at all.
            deletePanel.querySelectorAll('.cancel-delete-document-btn').forEach(function (btn) {
                btn.addEventListener('click', closeAllPanels);
            });

            const confirmDeleteBtn = deletePanel.querySelector('.confirm-delete-document-btn');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', function () {
                    requestOrThrowSimple('/documents/' + documentId, 'DELETE', undefined, 'Failed to delete document.')
                        .then(function () {
                            window.location.reload();
                        })
                        .catch(function (error) {
                            const errorEl = deletePanel.querySelector('.delete-document-error');
                            errorEl.textContent = error.message;
                            errorEl.style.display = '';
                        });
                });
            }
        });

        // task #73: the "Linked tasks" popover. One shared element,
        // reparented next to whichever trigger is currently active,
        // rather than one DOM node per row — there's no pagination on
        // this page, and pre-rendering N popovers full of task titles
        // would defeat both "don't put task titles in the page HTML" and
        // lazy-loading. position: fixed (not absolute) is what lets a
        // single shared node do this: a fixed element ignores the table
        // wrapper's own overflow-hidden (which would otherwise clip a
        // popover that needs to extend past a cell near the right edge),
        // and inserting it as a DOM sibling right after the active
        // trigger (insertAdjacentElement moves an existing node, it
        // doesn't clone it) is what makes Tab flow from the trigger
        // straight into the popover's own links, with no manual focus
        // trap needed.
        const linkedTasksCache = {};
        let activeLinkedTasksTrigger = null;
        let linkedTasksOpenTimer = null;
        let linkedTasksCloseTimer = null;

        const linkedTasksPopover = document.createElement('div');
        linkedTasksPopover.className = 'linked-tasks-popover hidden fixed z-50 w-72 max-w-[calc(100vw-16px)] rounded-md border border-gray-200 bg-white p-2 text-[12px] shadow-lg';
        linkedTasksPopover.setAttribute('role', 'dialog');
        linkedTasksPopover.setAttribute('aria-label', 'Linked tasks');

        function isLinkedTasksPopoverOpen() {
            return ! linkedTasksPopover.classList.contains('hidden');
        }

        function clearLinkedTasksTimers() {
            if (linkedTasksOpenTimer) { clearTimeout(linkedTasksOpenTimer); linkedTasksOpenTimer = null; }
            if (linkedTasksCloseTimer) { clearTimeout(linkedTasksCloseTimer); linkedTasksCloseTimer = null; }
        }

        function positionLinkedTasksPopover(trigger) {
            const rect = trigger.getBoundingClientRect();
            const margin = 8;

            const popoverRect = linkedTasksPopover.getBoundingClientRect();

            // Shift left rather than overflow the right edge — this
            // column sits near it, so this is the common case, not an
            // edge case.
            let left = rect.left;
            if (left + popoverRect.width > window.innerWidth - margin) {
                left = window.innerWidth - margin - popoverRect.width;
            }
            left = Math.max(margin, left);

            // Flip above the trigger rather than overflow the bottom edge.
            let top = rect.bottom + 4;
            if (top + popoverRect.height > window.innerHeight - margin) {
                top = rect.top - popoverRect.height - 4;
            }
            top = Math.max(margin, top);

            linkedTasksPopover.style.left = left + 'px';
            linkedTasksPopover.style.top = top + 'px';
        }

        function renderLinkedTasksLoading() {
            linkedTasksPopover.innerHTML = '<p class="text-gray-500">Loading…</p>';
        }

        function renderLinkedTasksError() {
            linkedTasksPopover.innerHTML = '<p class="text-gray-500">Couldn’t load tasks</p>';
        }

        function pluralizeTasks(count) {
            return count === 1 ? 'task' : 'tasks';
        }

        function renderLinkedTasksData(data) {
            linkedTasksPopover.innerHTML = '';

            const tasks = data.tasks || [];

            // All linked tasks hidden from this viewer: show only that
            // line, nothing else (no empty list above it).
            if (tasks.length === 0 && data.hidden_count > 0) {
                const hiddenOnly = document.createElement('p');
                hiddenOnly.className = 'text-gray-500';
                hiddenOnly.textContent = 'and ' + data.hidden_count + ' more ' + pluralizeTasks(data.hidden_count) + ' you don’t have access to.';
                linkedTasksPopover.appendChild(hiddenOnly);

                return;
            }

            if (tasks.length === 0) {
                const empty = document.createElement('p');
                empty.className = 'text-gray-500';
                empty.textContent = 'No tasks to show.';
                linkedTasksPopover.appendChild(empty);

                return;
            }

            const list = document.createElement('div');
            list.className = 'space-y-1';
            tasks.forEach(function (task) {
                const link = document.createElement('a');
                link.href = task.url;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.title = task.title;
                link.textContent = task.title;
                link.className = 'block truncate text-brand-600 hover:underline';
                list.appendChild(link);
            });
            linkedTasksPopover.appendChild(list);

            const remainingViewable = data.viewable_total - tasks.length;
            if (remainingViewable > 0) {
                const more = document.createElement('p');
                more.className = 'mt-1 text-gray-400';
                more.textContent = 'and ' + remainingViewable + ' more ' + pluralizeTasks(remainingViewable);
                linkedTasksPopover.appendChild(more);
            }

            if (data.hidden_count > 0) {
                const hidden = document.createElement('p');
                hidden.className = 'mt-1 text-gray-400';
                hidden.textContent = 'and ' + data.hidden_count + ' more ' + pluralizeTasks(data.hidden_count) + ' you don’t have access to.';
                linkedTasksPopover.appendChild(hidden);
            }
        }

        function loadAndRenderLinkedTasks(trigger) {
            const documentId = trigger.dataset.documentId;
            const cached = linkedTasksCache[documentId];

            if (cached && cached.status === 'loaded') {
                renderLinkedTasksData(cached.data);
                positionLinkedTasksPopover(trigger);

                return;
            }

            renderLinkedTasksLoading();
            positionLinkedTasksPopover(trigger);

            if (cached && cached.status === 'loading') return;

            linkedTasksCache[documentId] = { status: 'loading' };

            fetch(trigger.dataset.linkedTasksUrl, { headers: { Accept: 'application/json' } })
                .then(function (response) {
                    if (! response.ok) throw new Error('Failed to load linked tasks.');

                    return response.json();
                })
                .then(function (data) {
                    linkedTasksCache[documentId] = { status: 'loaded', data: data };

                    if (activeLinkedTasksTrigger === trigger) {
                        renderLinkedTasksData(data);
                        positionLinkedTasksPopover(trigger);
                    }
                })
                .catch(function () {
                    delete linkedTasksCache[documentId]; // not cached — retried on the next open

                    if (activeLinkedTasksTrigger === trigger) {
                        renderLinkedTasksError();
                        positionLinkedTasksPopover(trigger);
                    }
                });
        }

        function openLinkedTasksPopover(trigger) {
            clearLinkedTasksTimers();
            activeLinkedTasksTrigger = trigger;
            trigger.setAttribute('aria-expanded', 'true');
            trigger.insertAdjacentElement('afterend', linkedTasksPopover);
            linkedTasksPopover.classList.remove('hidden');
            loadAndRenderLinkedTasks(trigger);
        }

        function closeLinkedTasksPopover(returnFocus) {
            clearLinkedTasksTimers();

            if (activeLinkedTasksTrigger) {
                activeLinkedTasksTrigger.setAttribute('aria-expanded', 'false');
                if (returnFocus) activeLinkedTasksTrigger.focus();
            }

            linkedTasksPopover.classList.add('hidden');
            activeLinkedTasksTrigger = null;
        }

        function scheduleLinkedTasksClose() {
            clearLinkedTasksTimers();
            linkedTasksCloseTimer = setTimeout(function () { closeLinkedTasksPopover(false); }, 200);
        }

        document.querySelectorAll('.linked-tasks-trigger').forEach(function (trigger) {
            // A mouse click focuses the button before the click event
            // fires — without this flag, the focus handler below would
            // open it and the click handler would immediately toggle it
            // shut again. Keyboard focus (Tab, no preceding mousedown on
            // this element) isn't affected.
            let openedByPointer = false;

            trigger.addEventListener('mousedown', function () {
                openedByPointer = true;
            });

            trigger.addEventListener('mouseenter', function () {
                clearLinkedTasksTimers();
                linkedTasksOpenTimer = setTimeout(function () { openLinkedTasksPopover(trigger); }, 150);
            });

            trigger.addEventListener('mouseleave', function () {
                if (linkedTasksOpenTimer) { clearTimeout(linkedTasksOpenTimer); linkedTasksOpenTimer = null; }
                scheduleLinkedTasksClose();
            });

            trigger.addEventListener('focus', function () {
                if (openedByPointer) return;
                clearLinkedTasksTimers();
                openLinkedTasksPopover(trigger);
            });

            // Also the tap handler: a tap fires a click same as a mouse
            // click, so this one handler covers both "click" and "on
            // touch devices a tap toggles it".
            trigger.addEventListener('click', function (event) {
                event.preventDefault();
                openedByPointer = false;
                clearLinkedTasksTimers();

                if (activeLinkedTasksTrigger === trigger && isLinkedTasksPopoverOpen()) {
                    closeLinkedTasksPopover(false);
                } else {
                    openLinkedTasksPopover(trigger);
                }
            });

            trigger.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && activeLinkedTasksTrigger === trigger) {
                    closeLinkedTasksPopover(true);
                }
            });
        });

        linkedTasksPopover.addEventListener('mouseenter', function () {
            clearLinkedTasksTimers();
        });
        linkedTasksPopover.addEventListener('mouseleave', function () {
            scheduleLinkedTasksClose();
        });
        linkedTasksPopover.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeLinkedTasksPopover(true);
            }
        });

        document.addEventListener('click', function (event) {
            if (! isLinkedTasksPopoverOpen()) return;
            if (activeLinkedTasksTrigger && activeLinkedTasksTrigger.contains(event.target)) return;
            if (linkedTasksPopover.contains(event.target)) return;

            closeLinkedTasksPopover(false);
        });

        // Tabbing forward out of the popover's last link (or back out of
        // the trigger without entering it) leaves focus on neither the
        // trigger nor the popover — close rather than leave a stale
        // popover open away from wherever focus actually is. The timeout
        // lets document.activeElement settle on whatever received focus
        // before checking it.
        document.addEventListener('focusout', function () {
            if (! activeLinkedTasksTrigger) return;

            setTimeout(function () {
                if (! isLinkedTasksPopoverOpen()) return;

                const active = document.activeElement;
                const stillInside = active === activeLinkedTasksTrigger || linkedTasksPopover.contains(active);

                if (! stillInside) {
                    closeLinkedTasksPopover(false);
                }
            }, 0);
        });
    })();
</script>
@endif
@endsection
