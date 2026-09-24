{{-- Task Description's own view/edit split — read-only <x-rich-text> by
     default (so an embedded image/video is clickable for the Phase 3
     lightbox — a live TipTap instance intercepts that click for node
     selection/resize instead), an explicit Edit control rather than
     click-anywhere-to-edit (Jira's own users report that as an
     accidental-edit annoyance), and the live editor only mounted once
     Edit is actually clicked — "no editor initialization at all" in view
     mode. This is the same shape tasks/_comments.blade.php already uses
     for editing an EXISTING comment.

     Save/Cancel (task #4, description autosave) is deliberately NOT
     part of this any more: entering edit mode has no separate save
     step and nothing to cancel back out of — the whole Edit Task form
     autosaves the moment focus genuinely leaves the editor (see
     onSettledBlur() in rich-text-editor.js), the same "click out to
     save" the task's own instruction asked for. Only HOW the field
     saves changed; the view/edit split itself, and so the lightbox
     staying clickable in view mode, is unchanged from before.

     The read-only view row is bordered like every other field's input
     on this form (Title, Priority, ...) rather than sitting as bare
     text — the live editor mounted in its place already carries that
     same border via .rte's own default styling, so the two states read
     as one consistent field boundary rather than the box appearing only
     once you start editing.

     $task and $value (the current description — old('description', ...)
     already resolved by the caller) are required. Only rendered inside the
     $canEdit branch of tasks/edit.blade.php, so an Edit control always
     applies here; a user without edit permission never reaches this
     partial at all — they get the read-only summary block instead (see
     the @else branch further down that same page), which already uses
     <x-rich-text> too.

     Self-contained per inclusion (document.currentScript, not a global
     id), matching _comments.blade.php's own pattern. --}}
@php
    $descriptionHtml = \App\Support\RichText::toHtml($value);
    $iconAttrs = 'width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';
@endphp
<div class="mt-1" data-description-field data-task-id="{{ $task->id }}">
    {{-- rounded-md border border-gray-300 bg-white px-3 py-2: the exact
         box every other field's <input>/<select> on this form already
         uses, so the read-only row reads as a field boundary of its own,
         not bare floating text — the pencil sits inside that same box
         rather than as a separate free-floating control beside it. --}}
    <div class="description-view-row flex items-start justify-between gap-4 rounded-md border border-gray-300 bg-white px-3 py-2">
        {{-- text-[12px] text-[#1F2937]: the exact body-text token this app
             uses for a field's displayed value elsewhere (e.g. the read-only
             Title display just below, tasks/edit.blade.php's own
             $canEdit-false branch) — not an approximate Tailwind gray. --}}
        <x-rich-text :value="$value" class="description-view min-w-0 flex-1 text-[12px] text-[#1F2937]" empty="—" />
        <button type="button" class="edit-description-btn shrink-0" title="Edit description" aria-label="Edit description">
            <svg {!! $iconAttrs !!}><path d="M11.5 2.5a1.5 1.5 0 0 1 2 2L5 13l-3 1 1-3z"/><path d="M9.5 4.5l2 2"/></svg>
        </button>
    </div>
    <input type="hidden" name="description" value="{{ $descriptionHtml }}" data-description-fallback-input>
</div>
<script>
    (function () {
        const container = document.currentScript.previousElementSibling;
        const taskId = container.dataset.taskId;
        const viewRow = container.querySelector('.description-view-row');
        const editBtn = container.querySelector('.edit-description-btn');
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

            viewRow.style.display = 'none';
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
            editRoot.dataset.videoTaskId = taskId;
            editRoot.dataset.videoContext = 'description';
            editRoot.dataset.documentTaskId = taskId;
            editRoot.dataset.documentContext = 'description';
            editRoot.dataset.linkPreviewTaskId = taskId;
            editRoot.dataset.linkPreviewContext = 'description';

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'description';
            hiddenInput.setAttribute('data-rte-input', '');
            hiddenInput.value = currentHtml;
            editRoot.appendChild(hiddenInput);

            viewRow.insertAdjacentElement('afterend', editRoot);

            withEditor(editRoot).then(function (created) {
                editor = created;
                editor.focus();

                // task #4, description autosave: no Save/Cancel step any
                // more — the whole Edit Task form submits itself the
                // moment focus genuinely leaves the editor.
                // onSettledBlur() (rich-text-editor.js) is what tells
                // "genuinely" apart from "clicked a toolbar button" (see
                // its own doc comment there). A full-page redirect follows
                // (TaskManagementController::update()), which naturally
                // re-renders back in view mode showing the newly saved
                // content — no client-side "return to view mode" handling
                // needed here, same as the removed Save button never
                // needed one either.
                created.onSettledBlur(function () {
                    if (form) form.requestSubmit();
                });
            });

            // Ctrl/Cmd+Enter saves immediately too, the same shortcut
            // every other rich-text box on this app already wires to its
            // own "submit" action (Comments' Post/Save buttons).
            editRoot.addEventListener('rte:submit', function () {
                if (form) form.requestSubmit();
            });
        });
    })();
</script>
