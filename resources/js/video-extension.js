import { Node, mergeAttributes } from '@tiptap/core';

// No official @tiptap/extension-video exists, same situation as audio in
// Phase 4 — and the per-vendor decision here is the same too: plain
// progressive playback via native HTML5 <video> (Cloudflare R2, no
// dedicated streaming service, no adaptive bitrate — see the Rich Media
// Enhancement Guide), so a custom node mirroring audio-extension.js is
// enough for the *schema*.
//
// This node itself still has no NodeView (unlike resizable-video.js's
// ResizableVideo, which is what the editor actually registers as of the
// video resize + lightbox follow-up) — it exists on its own so
// resizable-video.js has a plain node to .extend(), the same relationship
// resizable-image.js has with @tiptap/extension-image's own Image.
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
            // not a real editor-controlled attribute. Kept even though the
            // inline embed no longer shows native controls (task #4, video
            // resize + lightbox) — the *expanded* lightbox playback still
            // wants a real `<video controls>`, and this stays the one
            // signal, harmless either way, that this app's videos are
            // always meant to be user-controllable somewhere.
            controls: {
                default: true,
                parseHTML: () => true,
                renderHTML: () => ({ controls: 'controls' }),
            },
            // width/height (task #4, video resize) — same plain
            // default-attribute shape as @tiptap/extension-image's own
            // width/height (no custom parseHTML/renderHTML needed: TipTap's
            // default attribute handling already reads/writes a same-named
            // HTML attribute when a value is present). Only ever written by
            // ResizableVideo's onCommit (resizable-video.js); absent on
            // every video saved before this feature, which is exactly the
            // "no resize data" backward-compat case.
            width: {
                default: null,
            },
            height: {
                default: null,
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
