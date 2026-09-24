// Turns a plain, server-rendered <link-preview href domain>Title</link-preview>
// inside read-only rich text into the same compact file-chip-styled chip
// the live editor's NodeView already shows (task #4, Smart Links — fixed
// to a compact inline chip, not the original block-level card; see
// link-preview-extension.js). Dynamically imported and only run when
// there's actually a chip to enhance, same lazy-load shape as
// code-highlight.js, video-thumbnail.js and file-chip-thumbnail.js.
//
// Doesn't decide what a click does — app.js's delegated new-tab-opening
// listener does, the same as it already does for file-chip.
export function enhanceLinkPreviews(scope) {
    const target = scope || document;

    target.querySelectorAll('[data-rich-text-content] link-preview:not([data-preview-enhanced])').forEach(function (card) {
        card.setAttribute('data-preview-enhanced', '');
        // Literally the file-chip class, not a lookalike — see
        // link-preview-extension.js's own doc comment.
        card.classList.add('file-chip');

        // title is the chip's own text content (mirroring file-chip's
        // name), never a separate HTML attribute — see
        // link-preview-extension.js's renderHTML().
        const title = card.textContent;
        const domain = card.getAttribute('domain') || '';
        card.textContent = '';

        const icon = document.createElement('span');
        icon.className = 'file-chip-icon';
        icon.setAttribute('aria-hidden', 'true');
        icon.innerHTML = '<svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6.5 9.5a3 3 0 0 0 4.2 0l2-2a3 3 0 0 0-4.2-4.2l-.7.7"/><path d="M9.5 6.5a3 3 0 0 0-4.2 0l-2 2a3 3 0 0 0 4.2 4.2l.7-.7"/></svg>';

        const label = document.createElement('span');
        label.className = 'file-chip-name';
        label.textContent = chipLabel(title, domain);

        card.append(icon, label);
    });
}

function chipLabel(title, domain) {
    if (title && domain) return title + ' · ' + domain;

    return title || domain || '';
}
