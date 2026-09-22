import { Node, mergeAttributes } from '@tiptap/core';

// No official @tiptap/extension-audio exists, and the community options are
// either unmaintained or bundle their own player chrome — which conflicts
// with task #4 phase 4's actual requirement: native browser controls only,
// no custom player, no popup. That requirement is also simpler than what
// Phase 3's Image node needed (no resize, no nested content), so a plain
// declarative node — no custom NodeView at all — is enough: a native
// <audio controls> element's play/pause/seek/volume already work normally
// inside a contentEditable ancestor in every real browser, the same reason
// a stock <button> or <input> works there too. Image needed a NodeView
// specifically for drag-to-resize (task #4 resize); audio has nothing
// equivalent to manage, so it doesn't need one.
//
// atom: true (no editable content, can't place a cursor "inside" it) and
// draggable: true match how the Image node behaves — selected as a single
// unit, moved as a whole.
export const Audio = Node.create({
    name: 'audio',
    group: 'block',
    atom: true,
    draggable: true,

    addAttributes() {
        return {
            src: {
                default: null,
            },
            // Always rendered — this app never offers a controls-less
            // player (there'd be no way to play it), so it's not a
            // real editor-controlled attribute, just a fixed part of
            // every audio node's own HTML output.
            controls: {
                default: true,
                parseHTML: () => true,
                renderHTML: () => ({ controls: 'controls' }),
            },
        };
    },

    parseHTML() {
        return [{ tag: 'audio[src]' }];
    },

    renderHTML({ HTMLAttributes }) {
        return ['audio', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes)];
    },

    addOptions() {
        return {
            HTMLAttributes: {},
        };
    },

    addCommands() {
        return {
            setAudio: (options) => ({ commands }) => commands.insertContent({ type: this.name, attrs: options }),
        };
    },
});
