// task #73 (document form fixes, code-review follow-up): shared by the
// Documents page's own upload form (documents/create.blade.php) and the
// Task edit page's merged attach panel (tasks/_documents.blade.php) — both
// used to carry near-identical copies of this logic, which had already let
// the same re-selection bug slip into both places once; a future fix (or
// bug) now only has one place to land.
//
// Strips a file's extension for the Name auto-fill ("Peace and Conflict
// Grade 1.pdf" -> "Peace and Conflict Grade 1") — matches the last ".xyz"
// only when something precedes it, so an incidental earlier dot in the
// filename (e.g. "report.v2.pdf") is left alone and only the real
// extension is removed, and a dotfile-style name with nothing before the
// extension (e.g. ".env") is left as-is instead of being stripped down to
// an empty string.
export function stripExtension(filename) {
    return filename.replace(/^(.+)\.[^.]+$/, '$1');
}

/**
 * Wires a custom file input's change event to (1) update a filename-display
 * element and (2) auto-fill a Name field from the selected file's name,
 * extension stripped — but only while the Name field isn't "protected".
 *
 * A field becomes protected the moment the user types/edits it themselves
 * (listened for here via a plain 'input' event, which never fires for our
 * own programmatic `nameInput.value = ...` writes) — never overwritten
 * again after that, even if the user later clears it or retypes the exact
 * text we'd have auto-filled anyway: an 'input' event fired either way, so
 * this can't mistake a deliberate retype for an untouched auto-fill the
 * way a plain string-equality check on the field's current value could.
 *
 * A caller with its own, non-file-selection way of pre-filling the same
 * Name field (e.g. the Task edit page's "Upload '<search query>' as a new
 * file" action) can mark that as protected too via the returned protect()
 * — so a later file selection won't clobber a deliberate pre-fill from
 * elsewhere — and clear the flag via reset() once the surrounding form is
 * abandoned/reset, so the next fresh attempt can auto-fill again.
 *
 * @param {{fileInput: HTMLInputElement, fileNameEl: HTMLElement|null, nameInput: HTMLInputElement|null}} elements
 * @returns {{protect: () => void, reset: () => void}}
 */
export function wireFileNameAutofill({ fileInput, fileNameEl, nameInput }) {
    let protectedName = false;

    if (nameInput) {
        nameInput.addEventListener('input', function () {
            protectedName = true;
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const file = fileInput.files[0];

            if (fileNameEl) {
                fileNameEl.textContent = file ? file.name : 'No file selected';
                fileNameEl.title = file ? file.name : '';
            }

            if (nameInput && file && !protectedName) {
                nameInput.value = stripExtension(file.name);
            }
        });
    }

    return {
        protect: function () { protectedName = true; },
        reset: function () { protectedName = false; },
    };
}
