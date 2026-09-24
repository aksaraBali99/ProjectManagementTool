import { Video } from './video-extension.js';
import { ResizableNodeView, mergeAttributes } from '@tiptap/core';

// Unlike resizable-image.js, there's no official @tiptap/extension-video to
// extend — Video (video-extension.js) is this app's own plain node. What
// IS reusable, and confirmed rather than assumed generic (see
// @tiptap/core's own ResizableNodeView doc comment, which lists "img,
// video, or iframe" as example elements), is @tiptap/core's
// ResizableNodeView itself: the same drag/handle/aspect-ratio/min-max
// class the stock Image extension's addNodeView() builds on. So this is a
// hand-built addNodeView() (there's no wrapper extension to lean on), but
// NOT a hand-built drag implementation — it's TipTap's own resize engine,
// same as images, just wired up manually instead of inherited from a
// first-party extension.
//
// The resized element itself is a small composite (a <video> + a play-icon
// overlay), not a bare media element, because task #4's video resize +
// lightbox follow-up changes what the INLINE embed looks like: no native
// controls, no direct inline play — a static-looking thumbnail (the
// browser's own first-frame render, nudged via a tiny currentTime seek
// rather than a generated poster image, per the task's explicit effort
// call) with a click-to-expand play button. Resizing this composite only
// ever changes the thumbnail's preview size; the expanded lightbox
// (app.js's initLightboxDelegation -> lightbox.js) always plays at its own
// fixed large size regardless.
export const ResizableVideo = Video.extend({
    addNodeView() {
        if (! this.options.resize || ! this.options.resize.enabled || typeof document === 'undefined') return null;

        const { directions, minWidth, minHeight, maxWidth, maxHeight, alwaysPreserveAspectRatio } = this.options.resize;

        return ({ node, getPos, HTMLAttributes, editor }) => {
            // The composite element ResizableNodeView actually resizes —
            // mirrors resizable-image.js's bare <img> el, except this is a
            // small DOM tree (video + overlay), so its own width/height
            // styles (set by onResize below) size the whole thumbnail.
            const thumb = document.createElement('div');
            thumb.className = 'video-thumb';
            thumb.draggable = false;

            const video = document.createElement('video');
            video.muted = true;
            video.preload = 'metadata';
            video.playsInline = true;
            video.setAttribute('playsinline', '');
            video.tabIndex = -1;

            const mergedAttributes = mergeAttributes(this.options.HTMLAttributes, HTMLAttributes);
            if (mergedAttributes.src != null) video.src = mergedAttributes.src;

            // The browser's own first frame, not a generated poster (task
            // #4 explicitly allows this lower-effort option) — most modern
            // browsers paint a black frame at currentTime 0 until playback
            // has actually started once, so nudging forward a fraction of
            // a second forces a real decoded frame to paint instead.
            // 'loadedmetadata', not 'loadeddata': with preload="metadata"
            // (deliberate — an unplayed video shouldn't fetch more than
            // that just to render as a thumbnail) the browser only fetches
            // duration/dimensions up front, never actual frame data, so
            // 'loadeddata' never fires on its own here. Setting
            // currentTime once metadata is known is what triggers the
            // browser to fetch and decode that one frame.
            video.addEventListener('loadedmetadata', function onLoadedMetadata() {
                video.removeEventListener('loadedmetadata', onLoadedMetadata);
                if (video.duration > 0.1) {
                    try {
                        video.currentTime = Math.min(0.1, video.duration / 2);
                    } catch {
                        // Some browsers throw for a not-yet-seekable video;
                        // the thumbnail just stays on frame 0 in that case.
                    }
                }
            });

            const playIcon = document.createElement('div');
            playIcon.className = 'video-thumb-play';
            playIcon.setAttribute('aria-hidden', 'true');
            playIcon.innerHTML = '<span class="video-thumb-play-icon"><svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M4.5 3.2v9.6a.6.6 0 0 0 .92.5l7.4-4.8a.6.6 0 0 0 0-1l-7.4-4.8a.6.6 0 0 0-.92.5z"/></svg></span>';

            thumb.append(video, playIcon);

            function syncSize() {
                const width = node.attrs.width;
                const height = node.attrs.height;
                if (width && height) {
                    thumb.setAttribute('data-has-size', '');
                    thumb.style.width = width + 'px';
                    thumb.style.height = height + 'px';
                } else {
                    thumb.removeAttribute('data-has-size');
                    thumb.style.width = '';
                    thumb.style.height = '';
                }
            }
            syncSize();

            function onUpdate(updatedNode) {
                if (updatedNode.type !== node.type) return false;

                if (updatedNode.attrs.src !== node.attrs.src) {
                    video.src = updatedNode.attrs.src || '';
                }
                node = updatedNode;
                // Covers both this node's own resize commit (onCommit
                // above already applies the style directly, so this is a
                // no-op re-application in that case) and any OTHER path
                // that changes width/height — undo/redo chief among them.
                syncSize();

                return true;
            }

            const nodeView = new ResizableNodeView({
                element: thumb,
                editor: editor,
                node: node,
                getPos: getPos,
                onResize: function (width, height) {
                    thumb.style.width = width + 'px';
                    thumb.style.height = height + 'px';
                },
                // Same commit-on-release (not per-frame) pattern as
                // resizable-image.js, so a resize lands as real width/
                // height HTML attributes via the normal updateAttributes
                // -> renderHTML path — never a transient style-only change
                // that would vanish on reload.
                onCommit: (width, height) => {
                    const pos = getPos();
                    if (pos === undefined) return;
                    thumb.setAttribute('data-has-size', '');
                    this.editor.chain().setNodeSelection(pos).updateAttributes(this.name, {
                        width: Math.round(width),
                        height: Math.round(height),
                    }).run();
                },
                onUpdate: onUpdate,
                options: {
                    directions: directions,
                    min: { width: minWidth, height: minHeight },
                    max: { width: maxWidth, height: maxHeight },
                    preserveAspectRatio: alwaysPreserveAspectRatio === true,
                },
            });

            return nodeView;
        };
    },
});
