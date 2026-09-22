import { common, createLowlight } from 'lowlight';

// One lowlight instance for the whole app: the editor uses it for live
// highlighting inside the code-block node, and highlightCodeBlocks() below
// uses it for already-saved content shown read-only (task drilldown,
// comment list, the non-editable task view). Sharing it keeps highlight.js's
// grammars in a single lazily-loaded chunk instead of two copies.
export const lowlight = createLowlight(common);

function toDom(node) {
    if (node.type === 'text') {
        return document.createTextNode(node.value);
    }

    const el = document.createElement(node.tagName || 'span');
    const classes = node.properties && node.properties.className;
    if (classes) el.className = classes.join(' ');
    (node.children || []).forEach(function (child) { el.appendChild(toDom(child)); });

    return el;
}

// Saved content is plain <pre><code class="language-x">…</code></pre> —
// the server never stores highlight markup — so read-only views get their
// syntax colors from this client-side pass. Elements are built via the DOM
// (text nodes, not innerHTML), so code containing "<" or "&" can't turn
// into markup.
export function highlightCodeBlocks(scope) {
    (scope || document).querySelectorAll('[data-rich-text-content] pre code:not([data-highlighted])').forEach(function (code) {
        const source = code.textContent;
        const match = /(?:^|\s)language-([\w+#-]+)/.exec(code.className);

        let tree;
        try {
            tree = match && lowlight.registered(match[1])
                ? lowlight.highlight(match[1], source)
                : lowlight.highlightAuto(source);
        } catch (error) {
            return;
        }

        code.replaceChildren.apply(code, tree.children.map(toDom));
        code.dataset.highlighted = '1';
        code.classList.add('hljs');
    });
}
