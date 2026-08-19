@php $guardId = 'unsaved-changes-'.$formId; @endphp
<dialog id="{{ $guardId }}-dialog" class="w-full max-w-sm rounded-[12px] border border-gray-200 p-6 shadow-lg backdrop:bg-black/30">
    <h2 class="text-[14px] font-medium text-[#1F2937]">Unsaved changes</h2>
    <p class="mt-2 text-[12px] text-gray-600">You have unsaved changes. It will be lost if you leave this page. Continue?</p>
    <div class="mt-4 flex items-center justify-end gap-3">
        <button type="button" id="{{ $guardId }}-stay" class="rounded-[8px] border border-gray-300 px-4 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">No</button>
        <button type="button" id="{{ $guardId }}-leave" class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">Yes</button>
    </div>
</dialog>

<script>
    (function () {
        const form = document.getElementById(@json($formId));
        if (! form) return;

        const dialog = document.getElementById(@json($guardId.'-dialog'));
        const stayBtn = document.getElementById(@json($guardId.'-stay'));
        const leaveBtn = document.getElementById(@json($guardId.'-leave'));

        let isDirty = false;
        let isSubmitting = false;
        let pendingHref = null;

        form.addEventListener('input', function () { isDirty = true; });
        form.addEventListener('change', function () { isDirty = true; });

        // Listens on the document (bubble phase) rather than the guarded form
        // directly, so it also picks up submits of *other* forms on the same
        // page (e.g. the Change Password modal) — any real save anywhere on
        // the page should suppress the leave-warning, not just this form's.
        document.addEventListener('submit', function (event) {
            if (! event.defaultPrevented) {
                isSubmitting = true;
            }
        });

        window.addEventListener('beforeunload', function (event) {
            if (isDirty && ! isSubmitting) {
                event.preventDefault();
                event.returnValue = '';
            }
        });

        document.addEventListener('click', function (event) {
            if (! isDirty || isSubmitting) return;

            const link = event.target.closest('a[href]');
            if (! link || event.defaultPrevented) return;
            if (event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;

            const href = link.getAttribute('href');
            if (link.target === '_blank' || href.startsWith('#') || href.startsWith('javascript:')) return;

            event.preventDefault();
            pendingHref = link.href;
            dialog.showModal();
        });

        stayBtn.addEventListener('click', function () {
            pendingHref = null;
            dialog.close();
        });

        leaveBtn.addEventListener('click', function () {
            isDirty = false;
            dialog.close();
            if (pendingHref) {
                window.location.href = pendingHref;
            }
        });
    })();
</script>
