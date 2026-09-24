{{-- Task Description (task #4, description autosave) — a permanently-live
     rich-text editor, exactly like the Add Task page's own Description
     field (tasks/create.blade.php) and the "New comment" box
     (tasks/_comments.blade.php): no separate read-only view, no Edit
     button, no Save/Cancel — the box itself IS the field, bordered like
     every other input on this form via .rte's own default border (not
     only on focus), and it just autosaves — submitting the whole Edit
     Task form the moment focus genuinely leaves the editor (see
     rich-text-editor.js's onSettledBlur()) — rather than needing an
     explicit save step.

     This is a deliberate REVERT of an earlier view/edit split (which
     existed so an embedded image could be clicked to open the Phase 3
     lightbox — a live editor intercepts that click for node
     selection/resize instead), per this task's own explicit instruction.
     An embedded image in Description is therefore back to not being
     lightbox-clickable while this page is open — the same trade-off the
     view/edit split existed to avoid, now accepted the other way. The
     read-only task drilldown and task list views are unaffected: they
     still render <x-rich-text> read-only there, where the lightbox still
     works fine.

     $task and $value (the current description — old('description', ...)
     already resolved by the caller) are required. Only rendered inside
     the $canEdit branch of tasks/edit.blade.php; a user without edit
     permission never reaches this partial at all — they get the
     read-only summary block instead (see the @else branch further down
     that same page). --}}
<div class="mt-1" data-description-field data-task-id="{{ $task->id }}">
    <x-rich-text-editor
        name="description"
        id="description"
        :value="$value"
        label="Description"
        placeholder="Add a description…"
        :image-task-id="$task->id" image-context="description"
        :audio-task-id="$task->id" audio-context="description"
        :video-task-id="$task->id" video-context="description"
        :document-task-id="$task->id" document-context="description"
        :link-preview-task-id="$task->id" link-preview-context="description"
    />
</div>
<script>
    (function () {
        const container = document.currentScript.previousElementSibling;
        const editRoot = container.querySelector('[data-rich-text]');
        const form = container.closest('form');

        // Same deferred-module wait as _comments.blade.php — app.js (where
        // createRichTextEditor is mounted from) is a deferred module script
        // and may not have run yet when this inline script executes.
        // initRichText() (app.js) already auto-mounts every [data-rich-text]
        // element on the page on its own; calling mount() again here just
        // returns that SAME cached instance (mountRichText() keys its
        // promise cache by the root element) — this isn't a second mount.
        function withEditor(root) {
            return new Promise(function (resolve) {
                function go() { window.solavaRichText.mount(root).then(resolve); }
                if (window.solavaRichText) go();
                else document.addEventListener('DOMContentLoaded', go);
            });
        }

        withEditor(editRoot).then(function (editor) {
            editor.onSettledBlur(function () {
                if (form) form.requestSubmit();
            });
        });

        // Ctrl/Cmd+Enter saves immediately too, the same shortcut every
        // other rich-text box on this app already wires to its own
        // "submit" action (Comments' Post/Save buttons).
        editRoot.addEventListener('rte:submit', function () {
            if (form) form.requestSubmit();
        });
    })();
</script>
