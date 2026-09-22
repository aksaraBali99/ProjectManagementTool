import Image from '@tiptap/extension-image';
import { ResizableNodeView, getRenderedAttributes, mergeAttributes } from '@tiptap/core';

// @tiptap/extension-image's own addNodeView() (3.31.3, the latest published
// version as of task #4's image-resize addition) already builds TipTap's own
// ResizableNodeView (@tiptap/core) correctly for directions/min-size/aspect-
// ratio — but never reads or forwards a max size, so
// Image.configure({ resize: { maxWidth, maxHeight } }) is silently ignored
// by the stock extension. ResizableNodeView ITSELF already accepts a max
// size (its own `options.max`); this is a one-field gap in the wrapper, not
// a missing core capability, and there's no newer release that closes it.
//
// This extends Image to add exactly that one field, keeping every other
// line of the stock addNodeView() implementation verbatim below — so this
// stays on 100% of TipTap's own native resize infrastructure (the same
// ResizableNodeView class, the same drag/handle/aspect-ratio logic the
// stock extension itself uses) rather than a third-party resize package or
// a hand-rolled drag implementation.
//
// Exactly one Image node type is registered from this file's export — see
// rich-text-editor.js's extensions list — which matters here specifically:
// the documented "resized dimensions vanish on reload" failure mode happens
// when a plain (non-resizable) Image registration and a resize-aware one
// both try to parse <img>, and whichever "wins" for a given node strips the
// other's width/height. There is only ever the one registration here.
export const ResizableImage = Image.extend({
    addNodeView() {
        if (! this.options.resize || ! this.options.resize.enabled || typeof document === 'undefined') return null;

        const { directions, minWidth, minHeight, maxWidth, maxHeight, alwaysPreserveAspectRatio } = this.options.resize;
        const resizeManagedAttributes = new Set(['src', 'width', 'height']);

        return ({ node, getPos, HTMLAttributes, editor }) => {
            const el = document.createElement('img');
            el.draggable = false;

            const mergedAttributes = mergeAttributes(this.options.HTMLAttributes, HTMLAttributes);
            Object.entries(mergedAttributes).forEach(function (entry) {
                const key = entry[0];
                const value = entry[1];
                if (value == null || key === 'src' || key === 'width' || key === 'height') return;
                el.setAttribute(key, value);
            });
            if (mergedAttributes.src !== null) el.src = mergedAttributes.src;

            let previousHTMLAttributes = Object.assign({}, HTMLAttributes);

            function syncImageSource(src) {
                if (typeof src === 'string' && src !== '') {
                    if (el.getAttribute('src') !== src) el.src = src;
                    return;
                }
                if (el.hasAttribute('src')) el.removeAttribute('src');
                if (el.src !== '') el.src = '';
            }
            syncImageSource(HTMLAttributes.src);

            function onUpdate(updatedNode) {
                if (updatedNode.type !== node.type) return false;

                const extensionAttributes = editor.extensionManager.attributes.filter(function (attribute) {
                    return attribute.type === updatedNode.type.name;
                });
                const newHTMLAttributes = getRenderedAttributes(updatedNode, extensionAttributes);

                Object.keys(previousHTMLAttributes).forEach(function (key) {
                    if (! resizeManagedAttributes.has(key) && ! (key in newHTMLAttributes)) el.removeAttribute(key);
                });
                Object.entries(newHTMLAttributes).forEach(function (entry) {
                    const key = entry[0];
                    const value = entry[1];
                    if (resizeManagedAttributes.has(key)) return;
                    if (value != null) el.setAttribute(key, value);
                    else el.removeAttribute(key);
                });

                syncImageSource(newHTMLAttributes.src);
                previousHTMLAttributes = newHTMLAttributes;

                return true;
            }

            const nodeView = new ResizableNodeView({
                element: el,
                editor: editor,
                node: node,
                getPos: getPos,
                onResize: function (width, height) {
                    el.style.width = width + 'px';
                    el.style.height = height + 'px';
                },
                // Committed once, on mouseup/touchend (not on every drag-move
                // frame) — this is what actually saves the size, via the same
                // updateAttributes() -> renderHTML() path every other node
                // attribute change already goes through, so width/height end
                // up as real <img width height> HTML attributes (task #4
                // phase 3's save format), not a transient style-only change.
                onCommit: (width, height) => {
                    const pos = getPos();
                    if (pos === undefined) return;
                    this.editor.chain().setNodeSelection(pos).updateAttributes(this.name, {
                        width: Math.round(width),
                        height: Math.round(height),
                    }).run();
                },
                onUpdate: onUpdate,
                options: {
                    directions: directions,
                    min: { width: minWidth, height: minHeight },
                    // The one addition over the stock extension's own
                    // addNodeView() — see this file's header comment.
                    max: { width: maxWidth, height: maxHeight },
                    preserveAspectRatio: alwaysPreserveAspectRatio === true,
                },
            });

            const dom = nodeView.dom;
            function showNodeView() {
                dom.style.visibility = '';
                dom.style.pointerEvents = '';
            }
            dom.style.visibility = 'hidden';
            dom.style.pointerEvents = 'none';
            if (el.complete && el.naturalWidth > 0) showNodeView();
            else {
                el.onload = showNodeView;
                el.onerror = showNodeView;
            }

            return nodeView;
        };
    },
});
