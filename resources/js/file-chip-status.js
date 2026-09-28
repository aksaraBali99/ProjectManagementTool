// task #73 phase 2: after a document is deleted, its file-chips (in
// read-only rich text AND the live editor) should show a muted,
// non-clickable "Document removed" state instead of silently 404ing
// (well, now showing a friendly message, see DocumentController::
// download()) when clicked. This is the ONE place that check happens —
// called once for a scope of newly-rendered/mounted content, never once
// per chip.
//
// Mechanism: collect every not-yet-checked <file-chip href> element in
// the given scope, send their unique hrefs in ONE request (capped) to
// /file-chip-status, resolved server-side the exact same way download()
// itself resolves a chip's href (by `link`, then by storage_key path —
// see DocumentController::resolveDocumentByUrl()). Whatever comes back
// as "missing" gets muted in place — no rewriting of the saved HTML/
// editor content, just a client-side visual + `href`-removal so the
// existing click delegation (app.js's initExternalReferenceDelegation)
// naturally treats it as non-clickable (it already no-ops on a chip with
// no href).
//
// Only file chips — link-preview chips and plain <a> links are never
// touched here.
const MAX_HREFS_PER_REQUEST = 50;

export function checkFileChipStatuses(scope) {
    const target = scope || document;
    const chips = Array.from(target.querySelectorAll('file-chip[href]:not([data-chip-checked])'));
    if (! chips.length) return;

    chips.forEach(function (chip) { chip.setAttribute('data-chip-checked', ''); });

    const hrefs = Array.from(new Set(chips.map(function (chip) { return chip.getAttribute('href'); })));
    if (! hrefs.length) return;

    // Capped, not chunked into multiple requests — a page with more file
    // chips than this is not the common case this phase optimizes for;
    // the excess simply isn't checked (they still work normally, and any
    // truly-removed one among them still 404s with the friendly message
    // on click).
    const cappedHrefs = hrefs.slice(0, MAX_HREFS_PER_REQUEST);

    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (! csrfTokenMeta) return;

    fetch('/file-chip-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfTokenMeta.content,
        },
        body: JSON.stringify({ hrefs: cappedHrefs }),
    })
        .then(function (response) { return response.ok ? response.json() : { missing: [] }; })
        .then(function (data) {
            const missing = new Set(data.missing || []);
            if (! missing.size) return;

            target.querySelectorAll('file-chip[href]').forEach(function (chip) {
                if (missing.has(chip.getAttribute('href'))) {
                    markFileChipRemoved(chip);
                }
            });
        })
        .catch(function () {
            // A failed status check just leaves chips as they were —
            // clicking one still safely falls back to download()'s own
            // friendly "This document has been removed." message if it
            // really is gone.
        });
}

function markFileChipRemoved(chip) {
    if (chip.dataset.chipRemoved !== undefined) return;
    chip.dataset.chipRemoved = '';
    chip.classList.add('file-chip--removed');
    chip.setAttribute('aria-disabled', 'true');
    // Removing href (not just styling) is what actually makes it
    // non-clickable — app.js's click delegation already no-ops on a
    // file-chip with no href attribute.
    chip.removeAttribute('href');

    const label = chip.querySelector('.file-chip-name');
    if (label) label.textContent = 'Document removed';
}
