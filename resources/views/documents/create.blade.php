@extends('layouts.authenticated')

@section('title', 'Add document — Solava')

@section('content')
<div class="mx-auto max-w-xl">
    <a href="{{ route('documents.index', array_filter(['organization' => $organization->id, 'folder' => $folder?->id])) }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Documents</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Add document</h1>
    <p class="mt-1 text-[11px] text-gray-500">
        Adding to <span class="font-medium text-[#1F2937]">{{ $organization->name }}</span>@if ($folder) / <span class="font-medium text-[#1F2937]">{{ $folder->name }}</span>@endif.
    </p>

    @php
        // Sticky across a validation-error redisplay: whichever mode was
        // actually submitted (a `file` upload has no `old()` value at
        // all, so `old('link')` alone can't tell these apart) — takes
        // priority over $initialMode, the "+ New" menu's own preselection
        // (DocumentController::create()'s ?mode=upload|link), which only
        // matters on a fresh, error-free visit to this page.
        $initialMode = old('_mode', $initialMode);
    @endphp

    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4" id="create-document-form" novalidate>
        @csrf
        <input type="hidden" name="organization_id" value="{{ $organization->id }}">
        <input type="hidden" name="from_documents_page" value="1">
        <input type="hidden" name="_mode" id="document-mode-input" value="{{ $initialMode }}">
        {{-- task #73 phase 2: "Uploads and add-link on the Documents page
             go into the current folder" — carried through as a hidden
             field, validated server-side against this company regardless
             (DocumentController::store()). --}}
        @if ($folder)
            <input type="hidden" name="folder_id" value="{{ $folder->id }}">
        @endif

        <div>
            <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Source</span>
            <div class="mt-1 inline-flex rounded-md border border-gray-300 p-0.5" role="tablist">
                <button type="button" data-document-mode-tab="link"
                    class="rounded px-3 py-1.5 text-[12px] font-medium">Add link</button>
                <button type="button" data-document-mode-tab="upload"
                    class="rounded px-3 py-1.5 text-[12px] font-medium">Upload file</button>
            </div>
        </div>

        <div data-document-mode-panel="link">
            <label for="link" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Document link <span class="text-red-600">*</span></label>
            <input id="link" name="link" type="url" value="{{ old('link') }}"
                placeholder="https://…"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
            @error('link')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div data-document-mode-panel="upload" class="hidden">
            <label for="file" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">File <span class="text-red-600">*</span></label>
            <input id="file" name="file" type="file"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
            <p class="mt-1 text-[10px] text-gray-500">PDF, Word, Excel, PowerPoint, text or CSV — up to 20MB.</p>
            @error('file')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Name <span class="text-red-600">*</span></label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
            @error('name')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @if ($isClientUploader)
            {{-- task #73 phase 1: a Client-role uploader never sees this
                 dropdown at all — always saved as Public, enforced
                 server-side regardless of this hidden value. --}}
            <input type="hidden" name="access_level" value="public">
        @else
            <div>
                <label for="access_level" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Access level <span class="text-red-600">*</span></label>
                <select id="access_level" name="access_level" required
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    @foreach (\App\Enums\DocumentAccessLevel::cases() as $level)
                        <option value="{{ $level->value }}" {{ old('access_level') === $level->value ? 'selected' : '' }}>{{ $level->label() }}</option>
                    @endforeach
                </select>
                @error('access_level')
                    <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
                Add document
            </button>
            <a href="{{ route('documents.index', $organization) }}" class="text-[12px] text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</div>

<script>
(function () {
    var form = document.getElementById('create-document-form');
    if (!form) return;

    var modeInput = document.getElementById('document-mode-input');
    var tabs = form.querySelectorAll('[data-document-mode-tab]');
    var panels = form.querySelectorAll('[data-document-mode-panel]');
    var linkField = document.getElementById('link');
    var fileField = document.getElementById('file');

    function activate(mode) {
        modeInput.value = mode;

        panels.forEach(function (panel) {
            panel.classList.toggle('hidden', panel.dataset.documentModePanel !== mode);
        });

        tabs.forEach(function (tab) {
            var active = tab.dataset.documentModeTab === mode;
            tab.classList.toggle('bg-brand-600', active);
            tab.classList.toggle('text-white', active);
            tab.classList.toggle('text-gray-600', !active);
        });

        // Only the field for the active mode is required/submitted —
        // the server itself also rejects both/neither via
        // required_without/prohibits, this just keeps the client-side
        // experience consistent with that.
        if (linkField) linkField.required = mode === 'link';
        if (fileField) fileField.required = mode === 'upload';
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activate(tab.dataset.documentModeTab);
        });
    });

    // Auto-fill Name from the chosen file, same convenience the task
    // edit page's own upload option gets — editable afterward.
    if (fileField) {
        fileField.addEventListener('change', function () {
            var nameField = document.getElementById('name');
            if (nameField && !nameField.value && fileField.files[0]) {
                nameField.value = fileField.files[0].name;
            }
        });
    }

    activate(modeInput.value === 'upload' ? 'upload' : 'link');
})();
</script>

@include('users._unsaved-changes-guard', ['formId' => 'create-document-form'])
@endsection
