@extends('layouts.authenticated')

@section('title', 'Documents — Solava')

@section('content')
<div>
    <div class="flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Documents</h1>
        @if ($organization && $canManage)
            <a href="{{ route('documents.create', array_filter(['organization' => $organization->id, 'folder' => $folder?->id])) }}"
               class="rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
                + Add new document
            </a>
        @endif
    </div>

    @if (session('status'))
        <div class="mt-3 rounded-md bg-brand-50 px-3 py-2 text-[12px] text-brand-800">{{ session('status') }}</div>
    @endif

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">You don't have access to any companies yet.</p>
    @else
        <x-company-tabs :organizations="$organizations" :active="$organization" route="documents.index">
        {{-- Breadcrumb: company root > folder > subfolder. Switching
             company tab (the tabs above) always lands on that company's
             root — this nav is only about moving within the CURRENT tab. --}}
        <nav class="mb-3 flex flex-wrap items-center gap-1 text-[11px]" aria-label="Folder breadcrumb">
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

        @if ($canManageFolders)
            <div class="mb-3">
                <button type="button" class="new-folder-toggle text-[11px] font-medium text-brand-600 hover:underline">+ New folder</button>
                <div class="new-folder-form mt-2 hidden flex items-start gap-2 rounded-md border border-gray-200 p-3">
                    <div class="flex-1">
                        <input type="text" class="new-folder-name w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" placeholder="Folder name">
                        <p class="new-folder-error mt-1 text-[11px] text-red-600" style="display: none;"></p>
                    </div>
                    <button type="button" class="create-folder-btn rounded-md bg-brand-600 px-3 py-2 text-[12px] font-medium text-white hover:bg-brand-700">Create</button>
                </div>
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
                                @if ($canManageThisFolder)
                                    <span class="ml-2 inline-flex gap-2">
                                        <button type="button" class="rename-folder-btn text-[11px] text-gray-500 hover:underline">Rename</button>
                                        <button type="button" class="delete-folder-btn text-[11px] text-gray-500 hover:underline">Delete</button>
                                    </span>
                                @endif
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
                        </tr>
                    @endforeach
                    @forelse ($documents as $document)
                        <tr class="block px-3 py-2.5 md:table-row md:px-0 md:py-0" data-document-id="{{ $document->id }}">
                            <td class="text-[12px] font-medium text-[#1F2937] md:table-cell md:px-3 md:py-2.5">
                                <a href="{{ route('file-downloads.show', ['url' => $document->url]) }}" target="_blank" rel="noopener noreferrer" class="hover:underline">{{ $document->name }}</a>
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Access level</span>
                                @if ($document->access_level === \App\Enums\DocumentAccessLevel::Private)
                                    <span class="rounded-sm bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">{{ $document->access_level->label() }}</span>
                                @elseif ($document->access_level === \App\Enums\DocumentAccessLevel::Internal)
                                    <span class="rounded-sm bg-[#FDF1D9] px-2 py-0.5 text-[10px] font-medium text-[#8A5A00]">{{ $document->access_level->label() }}</span>
                                @else
                                    <span class="rounded-sm bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">{{ $document->access_level->label() }}</span>
                                @endif
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
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Linked tasks</span>
                                <span>{{ $linkedTaskCounts[$document->id] ?? 0 }}</span>
                            </td>
                        </tr>
                    @empty
                        @if ($folders->isEmpty())
                            <x-empty-table-row colspan="5" py="6">No folders or documents here yet.</x-empty-table-row>
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
                if (response.ok) return response;
                return response.json().catch(function () { return null; }).then(function (data) {
                    const fieldErrors = data && data.errors ? Object.values(data.errors)[0] : null;
                    const message = (Array.isArray(fieldErrors) && fieldErrors[0]) || (data && data.message) || fallback;
                    throw new Error(message);
                });
            });
        }

        // + New folder
        const toggleBtn = document.querySelector('.new-folder-toggle');
        const newFolderForm = document.querySelector('.new-folder-form');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                newFolderForm.classList.toggle('hidden');
            });
        }
        const createFolderBtn = document.querySelector('.create-folder-btn');
        if (createFolderBtn) {
            createFolderBtn.addEventListener('click', function () {
                const nameInput = document.querySelector('.new-folder-name');
                const errorEl = document.querySelector('.new-folder-error');
                errorEl.style.display = 'none';

                requestOrThrow('/document-folders', 'POST', {
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

                    requestOrThrow('/document-folders/' + folderId, 'PUT', { name: newName.trim() }, 'Failed to rename folder.')
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

                    requestOrThrow('/document-folders/' + folderId, 'DELETE', undefined, 'Failed to delete folder.')
                        .then(function () {
                            window.location.reload();
                        })
                        .catch(function (error) {
                            alert(error.message);
                        });
                });
            }
        });
    })();
</script>
@endif
@endsection
