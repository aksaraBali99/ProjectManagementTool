import { Node, mergeAttributes } from '@tiptap/core';

// Smart Links (task #4) — turns a bare URL into a small inline chip
// showing the linked page's real title, once resolved. Fixed after an
// initial version rendered a much larger block-level card (image
// thumbnail + title + domain, its own row): the actual spec is a
// COMPACT INLINE chip, the same visual weight as file-chip
// (document upload + embedding) — icon + title + domain, no thumbnail,
// no content preview. group: 'inline' / inline: true (not 'block' like
// the first version) for exactly that reason.
//
// The chip's own class is literally `.file-chip` (see addNodeView()
// below and link-preview-thumbnail.js) — not a parallel, merely-similar
// class — so the two chip types are guaranteed pixel-identical rather
// than independently maintained to look alike.
//
// title is stored as the node's own text content in the saved HTML
// (mirroring file-chip's name), not just an attribute: a resolved
// link's title is real, readable content, so RichText::plainText()
// needs no special-casing for a link-preview-only description, the
// same reasoning file-chip's own name already relies on. domain stays a
// plain attribute — there's nothing wrong with it also appearing in the
// text content, but it's the chip's secondary, smaller-weight
// information, not its primary label.
export const LinkPreview = Node.create({
    name: 'linkPreview',
    group: 'inline',
    inline: true,
    atom: true,

    addAttributes() {
        return {
            href: {
                default: null,
            },
            domain: {
                default: null,
            },
            title: {
                default: null,
                parseHTML: (element) => element.textContent || '',
            },
        };
    },

    parseHTML() {
        return [{ tag: 'link-preview[href]' }];
    },

    renderHTML({ HTMLAttributes, node }) {
        return [
            'link-preview',
            mergeAttributes(this.options.HTMLAttributes, { href: HTMLAttributes.href, domain: HTMLAttributes.domain }),
            node.attrs.title || '',
        ];
    },

    addOptions() {
        return {
            HTMLAttributes: {},
        };
    },

    addNodeView() {
        return ({ node }) => {
            const chip = document.createElement('link-preview');
            // Literally the file-chip class, not a lookalike — see the
            // module doc comment above.
            chip.className = 'file-chip';
            chip.contentEditable = 'false';
            if (node.attrs.href) chip.setAttribute('href', node.attrs.href);

            const icon = document.createElement('span');
            icon.className = 'file-chip-icon';
            icon.setAttribute('aria-hidden', 'true');
            icon.innerHTML = '<svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6.5 9.5a3 3 0 0 0 4.2 0l2-2a3 3 0 0 0-4.2-4.2l-.7.7"/><path d="M9.5 6.5a3 3 0 0 0-4.2 0l-2 2a3 3 0 0 0 4.2 4.2l.7-.7"/></svg>';

            const label = document.createElement('span');
            label.className = 'file-chip-name';
            label.textContent = chipLabel(node.attrs.title, node.attrs.domain);

            chip.append(icon, label);

            return { dom: chip };
        };
    },

    addCommands() {
        return {
            setLinkPreview: (options) => ({ commands }) => commands.insertContent({ type: this.name, attrs: options }),
        };
    },
});

// "Title · domain" when both exist (the normal case); falls back to
// whichever one is present if the other's missing rather than a blank
// label. Kept as a plain function (not exported/shared) — the read-only
// counterpart (link-preview-thumbnail.js) keeps its own identical copy,
// the same small-duplication-over-a-shared-module choice this codebase
// already makes for file-chip/video-thumbnail's own icon markup.
function chipLabel(title, domain) {
    if (title && domain) return title + ' · ' + domain;

    return title || domain || '';
}
