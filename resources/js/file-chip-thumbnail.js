// Turns a plain, server-rendered <file-chip href="...">Name</file-chip>
// inside read-only rich text (task drilldown, comments, Description's view
// mode — anywhere carrying [data-rich-text-content]) into the same
// icon-plus-name chip the live editor's NodeView already shows (task #4,
// document upload + embedding — see file-chip-extension.js for why the
// icon lives here and not in the saved HTML itself). Dynamically imported
// and only run when there's actually a chip to enhance, same lazy-load
// shape as code-highlight.js and video-thumbnail.js.
//
// Doesn't decide what a click does — app.js's initFileChipDelegation is
// the single place that opens a chip's document in a new tab, for the
// same reason initLightboxDelegation owns image/video clicks rather than
// this module attaching its own per-chip listener.
export function enhanceFileChips(scope) {
    const target = scope || document;

    target.querySelectorAll('[data-rich-text-content] file-chip:not([data-chip-enhanced])').forEach(function (chip) {
        chip.setAttribute('data-chip-enhanced', '');
        chip.classList.add('file-chip');

        const name = chip.textContent;
        chip.textContent = '';

        const icon = document.createElement('span');
        icon.className = 'file-chip-icon';
        icon.setAttribute('aria-hidden', 'true');
        icon.innerHTML = '<svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 1.5H4.5a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V5z"/><path d="M9.5 1.5V5h3.5"/></svg>';

        const label = document.createElement('span');
        label.className = 'file-chip-name';
        label.textContent = name;

        chip.append(icon, label);
    });
}
