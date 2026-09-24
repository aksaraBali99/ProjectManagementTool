import { Node, mergeAttributes } from '@tiptap/core';

// Document upload + embedding in the editor (task #4) — mirrors Jira, not
// this app's other three media types: attaching a document is NOT an
// inline preview/embed like image/audio/video. It's a small clickable
// reference — a "chip" — to a real Document record (the same model the
// standalone Documents page and the Edit Task page's existing "attach an
// existing document" picker both already use), inserted at the cursor
// position rather than replacing a whole line.
//
// group: 'inline' / inline: true (unlike every other media node here,
// which are all 'block') — a file reference reads naturally inline within
// a sentence ("See attached: report.pdf"), matching how Jira's own
// attachment chips work. atom: true for the same reason as the others:
// no editable content, selected/deleted as one unit.
//
// A custom <file-chip> tag, deliberately not <a> or <span>: <a> would put
// this through the sanitizer's stricter link-scheme/relative-link rules
// (meant for user-typed hyperlinks to arbitrary external sites, not a
// same-origin reference to a file this app itself stored — see
// RichText's sanitizer), and would also collide with the editor's own
// Link mark parsing plain "<a href>" text; <span> is unwrapped by the
// sanitizer entirely (it's what the emoji node's markup collapses to).
// A distinct tag name avoids both, the same way audio/video each got
// their own tag rather than overloading an existing one.
//
// No custom renderHTML icon markup: the chip's *saved* HTML is
// deliberately plain (a bare <file-chip href>Name</file-chip>) — the
// icon is added client-side only, by the NodeView below for the live
// editor and by file-chip-thumbnail.js for read-only rendering, never
// part of the sanitized string. This keeps the sanitizer allowlist small
// (just the tag + href) and matches the same "plain saved shape, richer
// live rendering" split resizable-image.js/resizable-video.js already
// use for their own resize handles / play-icon overlays.
export const FileChip = Node.create({
    name: 'fileChip',
    group: 'inline',
    inline: true,
    atom: true,

    addAttributes() {
        return {
            href: {
                default: null,
            },
            // The chip's own visible label. Also its text content in the
            // saved HTML (see renderHTML) — a document reference is real,
            // readable content, unlike audio/video's "nothing to read"
            // atoms, so RichText::plainText() needs no special-casing for
            // it: strip_tags() already leaves this text right where it is.
            name: {
                default: null,
                parseHTML: (element) => element.textContent || '',
            },
        };
    },

    parseHTML() {
        return [{ tag: 'file-chip[href]' }];
    },

    renderHTML({ HTMLAttributes, node }) {
        return ['file-chip', mergeAttributes(this.options.HTMLAttributes, { href: HTMLAttributes.href }), node.attrs.name || ''];
    },

    addOptions() {
        return {
            HTMLAttributes: {},
        };
    },

    // A NodeView (not plain declarative rendering) specifically so the
    // icon can be real inline SVG in the live editor without that SVG
    // ever leaking into renderHTML's saved-HTML output (see the module
    // doc comment above) — same reasoning as resizable-image.js/
    // resizable-video.js needing a NodeView for their own editor-only
    // chrome.
    addNodeView() {
        return ({ node }) => {
            const chip = document.createElement('file-chip');
            chip.className = 'file-chip';
            if (node.attrs.href) chip.setAttribute('href', node.attrs.href);
            chip.contentEditable = 'false';

            const icon = document.createElement('span');
            icon.className = 'file-chip-icon';
            icon.setAttribute('aria-hidden', 'true');
            icon.innerHTML = '<svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 1.5H4.5a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V5z"/><path d="M9.5 1.5V5h3.5"/></svg>';

            const label = document.createElement('span');
            label.className = 'file-chip-name';
            label.textContent = node.attrs.name || '';

            chip.append(icon, label);

            return {
                dom: chip,
                // No `update` override needed: href/name never change
                // after insertion (there's no resize-style commit path
                // for a file chip), so the default "rebuild on any
                // attrs/content mismatch" NodeView behavior is enough.
            };
        };
    },

    addCommands() {
        return {
            setFileChip: (options) => ({ commands }) => commands.insertContent({ type: this.name, attrs: options }),
        };
    },
});
