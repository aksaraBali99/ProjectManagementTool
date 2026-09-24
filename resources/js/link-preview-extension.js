import { Node, mergeAttributes } from '@tiptap/core';

// Smart Links (task #4) — turns a bare URL, pasted alone on its own line,
// into a small preview card showing the linked page's real title (see
// buildLinkPreview()'s paste-handling in rich-text-editor.js for the
// detection itself). group: 'block' (unlike file-chip's inline chip) —
// a preview card takes its own line, the same visual weight as an
// embedded image, matching Jira/Confluence's own Smart Links layout.
// atom: true / draggable: true for the same reason as every other media
// node here: no editable content, selected/moved as one unit.
//
// The saved HTML is plain, self-contained attributes — href, title,
// domain, image — with no children at all (unlike file-chip, whose name
// is its own text content): everything the card needs to render is baked
// in as a snapshot from the moment of paste (LinkPreviewService), so
// re-rendering it later never re-fetches or shows a different result to
// a different viewer — see the link_previews migration's own docblock.
//
// A NodeView still builds the icon/image/title/domain layout, same
// reasoning as the other custom nodes here: keeps the sanitizer allowlist
// to plain attributes on one tag, no nested <img>/<svg>/<div> markup ever
// needing its own allowlist entries.
export const LinkPreview = Node.create({
    name: 'linkPreview',
    group: 'block',
    atom: true,
    draggable: true,

    addAttributes() {
        return {
            href: { default: null },
            title: { default: null },
            domain: { default: null },
            image: { default: null },
        };
    },

    parseHTML() {
        return [{ tag: 'link-preview[href]' }];
    },

    renderHTML({ HTMLAttributes }) {
        return ['link-preview', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes)];
    },

    addOptions() {
        return {
            HTMLAttributes: {},
        };
    },

    addNodeView() {
        return ({ node }) => {
            const card = document.createElement('link-preview');
            card.className = 'link-preview-card';
            card.contentEditable = 'false';
            if (node.attrs.href) card.setAttribute('href', node.attrs.href);

            if (node.attrs.image) {
                const img = document.createElement('img');
                img.className = 'link-preview-image';
                img.src = node.attrs.image;
                img.alt = '';
                // A broken/expired third-party image (the source site
                // changed or removed it well after this card was saved)
                // shouldn't leave a broken-image icon sitting in the
                // card — just drop the image slot entirely.
                img.addEventListener('error', function () { img.remove(); }, { once: true });
                card.appendChild(img);
            }

            const body = document.createElement('div');
            body.className = 'link-preview-body';

            const title = document.createElement('div');
            title.className = 'link-preview-title';
            title.textContent = node.attrs.title || '';

            const domainRow = document.createElement('div');
            domainRow.className = 'link-preview-domain';
            domainRow.innerHTML = '<span class="link-preview-icon" aria-hidden="true"><svg width="11" height="11" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6.5 9.5a3 3 0 0 0 4.2 0l2-2a3 3 0 0 0-4.2-4.2l-.7.7"/><path d="M9.5 6.5a3 3 0 0 0-4.2 0l-2 2a3 3 0 0 0 4.2 4.2l.7-.7"/></svg></span>';
            const domainLabel = document.createElement('span');
            domainLabel.textContent = node.attrs.domain || '';
            domainRow.appendChild(domainLabel);

            body.append(title, domainRow);
            card.appendChild(body);

            return { dom: card };
        };
    },

    addCommands() {
        return {
            setLinkPreview: (options) => ({ commands }) => commands.insertContent({ type: this.name, attrs: options }),
        };
    },
});
