import { Node, mergeAttributes } from '@tiptap/core';

// No official @tiptap/extension-video exists, same situation as audio in
// Phase 4 — and the per-vendor decision here is the same too: plain
// progressive playback via native HTML5 <video> (Cloudflare R2, no
// dedicated streaming service, no adaptive bitrate — see the Rich Media
// Enhancement Guide), so a custom node mirroring audio-extension.js is
// enough. No custom NodeView: native <video controls> play/pause/seek/
// volume/fullscreen already work normally inside a contentEditable
// ancestor in every real browser, the same reason audio's node doesn't
// need one either. No poster/thumbnail generation for this phase — out of
// scope per task #4 phase 5, flagged back rather than added speculatively.
//
// atom: true (no editable content, can't place a cursor "inside" it) and
// draggable: true match how the Image and Audio nodes both behave —
// selected as a single unit, moved as a whole.
export const Video = Node.create({
    name: 'video',
    group: 'block',
    atom: true,
    draggable: true,

    addAttributes() {
        return {
            src: {
                default: null,
            },
            // Always rendered — same reasoning as Audio's controls
            // attribute: this app never offers a controls-less player,
            // so it's a fixed part of every video node's HTML output,
            // not a real editor-controlled attribute.
            controls: {
                default: true,
                parseHTML: () => true,
                renderHTML: () => ({ controls: 'controls' }),
            },
        };
    },

    parseHTML() {
        return [{ tag: 'video[src]' }];
    },

    renderHTML({ HTMLAttributes }) {
        return ['video', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes)];
    },

    addOptions() {
        return {
            HTMLAttributes: {},
        };
    },

    addCommands() {
        return {
            setVideo: (options) => ({ commands }) => commands.insertContent({ type: this.name, attrs: options }),
        };
    },
});
