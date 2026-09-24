// Turns a plain, server-rendered <link-preview href title domain image>
// inside read-only rich text (task drilldown, comments, Description's
// view mode) into the same icon/image/title/domain card the live editor's
// NodeView already shows (task #4, Smart Links — see
// link-preview-extension.js for why the card markup lives here and not in
// the saved HTML itself). Dynamically imported and only run when there's
// actually a card to enhance, same lazy-load shape as code-highlight.js,
// video-thumbnail.js and file-chip-thumbnail.js.
//
// Doesn't decide what a click does — app.js's delegated new-tab-opening
// listener does, the same as it already does for file-chip.
export function enhanceLinkPreviews(scope) {
    const target = scope || document;

    target.querySelectorAll('[data-rich-text-content] link-preview:not([data-preview-enhanced])').forEach(function (card) {
        card.setAttribute('data-preview-enhanced', '');
        card.classList.add('link-preview-card');

        const title = card.getAttribute('title') || '';
        const domain = card.getAttribute('domain') || '';
        const image = card.getAttribute('image');

        if (image) {
            const img = document.createElement('img');
            img.className = 'link-preview-image';
            img.src = image;
            img.alt = '';
            img.addEventListener('error', function () { img.remove(); }, { once: true });
            card.appendChild(img);
        }

        const body = document.createElement('div');
        body.className = 'link-preview-body';

        const titleEl = document.createElement('div');
        titleEl.className = 'link-preview-title';
        titleEl.textContent = title;

        const domainRow = document.createElement('div');
        domainRow.className = 'link-preview-domain';
        domainRow.innerHTML = '<span class="link-preview-icon" aria-hidden="true"><svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6.5 9.5a3 3 0 0 0 4.2 0l2-2a3 3 0 0 0-4.2-4.2l-.7.7"/><path d="M9.5 6.5a3 3 0 0 0-4.2 0l-2 2a3 3 0 0 0 4.2 4.2l.7-.7"/></svg></span>';
        const domainLabel = document.createElement('span');
        domainLabel.textContent = domain;
        domainRow.appendChild(domainLabel);

        body.append(titleEl, domainRow);
        card.appendChild(body);
    });
}
