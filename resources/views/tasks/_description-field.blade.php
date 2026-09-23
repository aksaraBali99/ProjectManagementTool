{{-- Task Description's own view/edit split (task #4, follow-up fix) —
     mirrors the exact pattern Comments already used (tasks/_comments.blade.php):
     read-only <x-rich-text> by default (so an embedded image is clickable
     for the Phase 3 lightbox — a live TipTap instance intercepts that click
     for node selection/resize instead), an explicit Edit control rather
     than click-anywhere-to-edit (Jira's own users report that as an
     accidental-edit annoyance), and the live editor only mounted once Edit
     is actually clicked — "no editor initialization at all" in view mode.

     $task and $value (the current description — old('description', ...)
     already resolved by the caller) are required. Only rendered inside the
     $canEdit branch of tasks/edit.blade.php, so an Edit control always
     applies here; a user without edit permission never reaches this
     partial at all — they get the read-only summary block instead (see
     the @else branch further down that same page), which already uses
     <x-rich-text> too.

     Self-contained per inclusion (document.currentScript, not a global
     id), matching _comments.blade.php's own pattern. --}}
@php $descriptionHtml = \App\Support\RichText::toHtml($value); @endphp
<div class="mt-1" data-description-field data-task-id="{{ $task->id }}">
    <div class="description-view-row flex items-start justify-between gap-2">
        <x-rich-text :value="$value" class="description-view min-w-0 flex-1" empty="—" />
        {{-- A proper bordered button (matching the "Attach"/"Post" secondary-
             button style used elsewhere on this page), not a small text
             link — easy to spot as the one way into editing this field. --}}
        <button type="button" class="edit-description-btn shrink-0 rounded-md border border-gray-300 bg-white px-3 py-1.5 text-[12px] font-medium text-gray-700 hover:bg-gray-50">Edit</button>
    </div>
    <div class="description-edit-controls mt-1 hidden items-center gap-2">
        <button type="button" class="save-description-btn rounded-md bg-brand-600 px-3 py-1.5 text-[12px] font-medium text-white hover:bg-brand-700">Save</button>
        <button type="button" class="cancel-description-btn rounded-md border border-gray-300 px-3 py-1.5 text-[12px] font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
    </div>
    <input type="hidden" name="description" value="{{ $descriptionHtml }}" data-description-fallback-input>
</div>
<script>
    (function () {
        const container = document.currentScript.previousElementSibling;
        const taskId = container.dataset.taskId;
        const viewRow = container.querySelector('.description-view-row');
        const editBtn = container.querySelector('.edit-description-btn');
        const editControls = container.querySelector('.description-edit-controls');
        const fallbackInput = container.querySelector('[data-description-fallback-input]');
        const form = container.closest('form');

        // Same deferred-module wait as _comments.blade.php — app.js (where
        // createRichTextEditor is mounted from) is a module script and may
        // not have run yet when this inline script executes.
        function withEditor(root) {
            return new Promise(function (resolve) {
                function go() { window.solavaRichText.mount(root).then(resolve); }
                if (window.solavaRichText) go();
                else document.addEventListener('DOMContentLoaded', go);
            });
        }

        let editor = null;

        editBtn.addEventListener('click', function () {
            const currentHtml = fallbackInput.value;
            // Snapshot, not clear: some OTHER field on this page may
            // already be genuinely dirty, and Cancel below must restore
            // that state exactly, not assume this editor was the only
            // change in flight (see the guard's own doc comment).
            const wasDirtyBeforeEdit = form && form.__unsavedGuardIsDirty ? form.__unsavedGuardIsDirty() : false;

            viewRow.style.display = 'none';
            editBtn.style.display = 'none';
            // While the live editor is mounted, ITS OWN hidden input (below)
            // is what should submit as "description" — never two same-named
            // inputs in one form at once.
            fallbackInput.removeAttribute('name');

            const editRoot = document.createElement('div');
            editRoot.className = 'description-editor rte-loading';
            editRoot.setAttribute('data-rich-text', '');
            editRoot.dataset.content = currentHtml;
            editRoot.dataset.label = 'Description';
            editRoot.dataset.placeholder = 'Add a description…';
            editRoot.dataset.imageTaskId = taskId;
            editRoot.dataset.imageContext = 'description';
            editRoot.dataset.audioTaskId = taskId;
            editRoot.dataset.audioContext = 'description';

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'description';
            hiddenInput.setAttribute('data-rte-input', '');
            hiddenInput.value = currentHtml;
            editRoot.appendChild(hiddenInput);

            fallbackInput.insertAdjacentElement('afterend', editRoot);
            editControls.classList.remove('hidden');
            editControls.classList.add('flex');

            withEditor(editRoot).then(function (created) {
                editor = created;
                editor.focus();
            });

            function restore() {
                if (editor) editor.destroy();
                editor = null;
                editRoot.remove();
                editControls.classList.add('hidden');
                editControls.classList.remove('flex');
                viewRow.style.display = '';
                editBtn.style.display = '';
                fallbackInput.setAttribute('name', 'description');
            }

            function onCancel() {
                restore();
                if (form && form.__unsavedGuardSetDirty) form.__unsavedGuardSetDirty(wasDirtyBeforeEdit);
                cleanup();
            }

            function onSave() {
                // No separate persistence endpoint (unlike Comments, which
                // saves independently of the rest of the task form) —
                // Description is one field of the same Edit Task form, and
                // that form already syncs this editor's hidden input on
                // submit (createRichTextEditor wires that generically for
                // any root inside a <form>), so submitting the whole form
                // is both correct and exactly what happens today when
                // Description was always-live. A full-page redirect follows
                // (TaskManagementController::update()), which naturally
                // re-renders in view mode showing the newly saved content —
                // no client-side "return to view mode" handling needed here.
                if (form) form.requestSubmit();
            }

            container.querySelector('.cancel-description-btn').addEventListener('click', onCancel);
            container.querySelector('.save-description-btn').addEventListener('click', onSave);
            editRoot.addEventListener('rte:submit', onSave);

            function cleanup() {
                container.querySelector('.cancel-description-btn').removeEventListener('click', onCancel);
                container.querySelector('.save-description-btn').removeEventListener('click', onSave);
            }
        });
    })();
</script>
