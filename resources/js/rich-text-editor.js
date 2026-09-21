import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import CodeBlockLowlight from '@tiptap/extension-code-block-lowlight';
import { lowlight } from './code-highlight.js';

// THE rich-text editor. Task Description and Comments (new + edit) all mount
// this same module — there is deliberately no second configuration. Later
// media phases (image/audio/video) extend the `extensions` list and the
// TOOLBAR below, and every editor in the app picks the change up.
//
// The feature set is intentionally exactly what App\Support\RichText's
// sanitizer allows (p, br, strong, em, u, h1-h3, ul/ol/li, a, pre/code):
// anything the editor could emit but the server strips would silently
// vanish on save, so the two lists must move together.

const ICON_ATTRS = 'width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';

const ICONS = {
    bulletList: '<svg ' + ICON_ATTRS + '><line x1="6" y1="4" x2="14" y2="4"/><line x1="6" y1="8" x2="14" y2="8"/><line x1="6" y1="12" x2="14" y2="12"/><circle cx="2.5" cy="4" r=".6" fill="currentColor"/><circle cx="2.5" cy="8" r=".6" fill="currentColor"/><circle cx="2.5" cy="12" r=".6" fill="currentColor"/></svg>',
    orderedList: '<svg ' + ICON_ATTRS + '><line x1="7" y1="4" x2="14" y2="4"/><line x1="7" y1="8" x2="14" y2="8"/><line x1="7" y1="12" x2="14" y2="12"/><path d="M2 3l1-.6V6" /><path d="M1.8 9.6c.3-.8 1.9-.8 1.9.2 0 .9-1.9 1.3-1.9 2.2H3.8"/></svg>',
    link: '<svg ' + ICON_ATTRS + '><path d="M6.5 9.5a3 3 0 0 0 4.2 0l2-2a3 3 0 0 0-4.2-4.2l-.7.7"/><path d="M9.5 6.5a3 3 0 0 0-4.2 0l-2 2a3 3 0 0 0 4.2 4.2l.7-.7"/></svg>',
    codeBlock: '<svg ' + ICON_ATTRS + '><polyline points="5.5 4.5 2 8 5.5 11.5"/><polyline points="10.5 4.5 14 8 10.5 11.5"/></svg>',
};

const TOOLBAR = [
    { key: 'bold', label: 'Bold (Ctrl+B)', html: '<span style="font-weight:600">B</span>', run: (e) => e.chain().focus().toggleBold().run(), active: (e) => e.isActive('bold') },
    { key: 'italic', label: 'Italic (Ctrl+I)', html: '<span style="font-style:italic">I</span>', run: (e) => e.chain().focus().toggleItalic().run(), active: (e) => e.isActive('italic') },
    { key: 'underline', label: 'Underline (Ctrl+U)', html: '<span style="text-decoration:underline">U</span>', run: (e) => e.chain().focus().toggleUnderline().run(), active: (e) => e.isActive('underline') },
    { separator: true },
    { key: 'bulletList', label: 'Bulleted list', html: ICONS.bulletList, run: (e) => e.chain().focus().toggleBulletList().run(), active: (e) => e.isActive('bulletList') },
    { key: 'orderedList', label: 'Numbered list', html: ICONS.orderedList, run: (e) => e.chain().focus().toggleOrderedList().run(), active: (e) => e.isActive('orderedList') },
    { separator: true },
    { key: 'link', label: 'Link', html: ICONS.link, run: null, active: (e) => e.isActive('link') },
    { key: 'codeBlock', label: 'Code block', html: ICONS.codeBlock, run: (e) => e.chain().focus().toggleCodeBlock().run(), active: (e) => e.isActive('codeBlock') },
];

const HEADING_OPTIONS = [
    { value: 'p', label: 'Normal text' },
    { value: '1', label: 'Heading 1' },
    { value: '2', label: 'Heading 2' },
    { value: '3', label: 'Heading 3' },
];

function parseJson(value, fallback) {
    try {
        return value ? JSON.parse(value) : fallback;
    } catch (error) {
        return fallback;
    }
}

function el(tag, className, attrs) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    Object.keys(attrs || {}).forEach(function (name) { node.setAttribute(name, attrs[name]); });

    return node;
}

// Keeps a link's target a real web/mail address. Bare "example.com" gets
// https:// (what people mean); anything with another scheme is left for
// the Link extension's own allowlist to reject.
function normalizeHref(raw) {
    const value = raw.trim();
    if (value === '') return '';
    if (/^(https?:|mailto:)/i.test(value)) return value;
    if (/^[a-z][a-z0-9+.-]*:/i.test(value)) return value;

    return 'https://' + value;
}

function buildToolbar(editor, onLinkClick) {
    const bar = el('div', 'rte-toolbar', { role: 'toolbar', 'aria-label': 'Text formatting' });

    const heading = el('select', 'rte-heading', { 'aria-label': 'Text style', title: 'Text style' });
    HEADING_OPTIONS.forEach(function (option) {
        const opt = el('option');
        opt.value = option.value;
        opt.textContent = option.label;
        heading.appendChild(opt);
    });
    heading.addEventListener('change', function () {
        const chain = editor.chain().focus();
        if (heading.value === 'p') chain.setParagraph().run();
        else chain.setHeading({ level: Number(heading.value) }).run();
    });
    bar.appendChild(heading);
    bar.appendChild(el('span', 'rte-sep', { 'aria-hidden': 'true' }));

    const buttons = [];
    TOOLBAR.forEach(function (item) {
        if (item.separator) {
            bar.appendChild(el('span', 'rte-sep', { 'aria-hidden': 'true' }));
            return;
        }

        const button = el('button', 'rte-btn', { type: 'button', title: item.label, 'aria-label': item.label, 'aria-pressed': 'false' });
        button.innerHTML = item.html;
        // mousedown, not click: keeps the editor's selection/focus so the
        // command applies to what the user had selected.
        button.addEventListener('mousedown', function (event) { event.preventDefault(); });
        button.addEventListener('click', function () {
            if (item.key === 'link') onLinkClick();
            else item.run(editor);
        });
        bar.appendChild(button);
        buttons.push({ item: item, button: button });
    });

    function refresh() {
        buttons.forEach(function (entry) {
            const on = entry.item.active(editor);
            entry.button.classList.toggle('is-active', on);
            entry.button.setAttribute('aria-pressed', on ? 'true' : 'false');
        });

        const level = [1, 2, 3].find(function (l) { return editor.isActive('heading', { level: l }); });
        heading.value = level ? String(level) : 'p';
    }

    return { element: bar, refresh: refresh };
}

// Inline URL entry instead of window.prompt(): styled like the rest of the
// form, and dismissable with Escape without losing the editor selection.
function buildLinkBar(editor) {
    const wrap = el('div', 'rte-linkbar');
    wrap.hidden = true;

    const input = el('input', 'rte-link-input', { type: 'text', placeholder: 'https://example.com', 'aria-label': 'Link address', autocomplete: 'off' });
    const apply = el('button', 'rte-link-btn rte-link-btn--primary', { type: 'button' });
    apply.textContent = 'Apply';
    const remove = el('button', 'rte-link-btn', { type: 'button' });
    remove.textContent = 'Remove';
    const cancel = el('button', 'rte-link-btn', { type: 'button' });
    cancel.textContent = 'Cancel';
    const error = el('span', 'rte-link-error');
    error.hidden = true;

    wrap.append(input, apply, remove, cancel, error);

    function close() {
        wrap.hidden = true;
        error.hidden = true;
        editor.commands.focus();
    }

    function open() {
        const current = editor.getAttributes('link').href || '';
        input.value = current;
        remove.hidden = ! current;
        error.hidden = true;
        wrap.hidden = false;
        input.focus();
        input.select();
    }

    function applyLink() {
        const href = normalizeHref(input.value);
        if (href === '') {
            editor.chain().focus().extendMarkRange('link').unsetLink().run();
            close();
            return;
        }

        const { empty } = editor.state.selection;
        const onExistingLink = editor.isActive('link');
        let ok;

        if (empty && ! onExistingLink) {
            // Nothing selected: insert the address itself as the linked text.
            ok = editor.chain().focus().insertContent({
                type: 'text',
                text: href,
                marks: [{ type: 'link', attrs: { href: href } }],
            }).run();
        } else {
            ok = editor.chain().focus().extendMarkRange('link').setLink({ href: href }).run();
        }

        // false only when the Link extension refused the URL (a disallowed
        // scheme such as javascript:).
        if (! ok) {
            error.textContent = 'Enter a valid web or email address.';
            error.hidden = false;
            input.focus();
            return;
        }

        close();
    }

    apply.addEventListener('click', applyLink);
    remove.addEventListener('click', function () {
        editor.chain().focus().extendMarkRange('link').unsetLink().run();
        close();
    });
    cancel.addEventListener('click', close);
    input.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            applyLink();
        } else if (event.key === 'Escape') {
            event.preventDefault();
            close();
        }
    });

    return { element: wrap, open: open };
}

// @mention autocomplete. Same behavior the comment box had as a plain
// textarea: picking a name inserts the literal text "@Full Name ", picked
// user IDs are tracked, and at read time an ID is only reported if its
// "@Name" text is still in the document — so deleting the text drops the
// mention without re-parsing anything.
function setupMentions(editor, users) {
    const mentioned = new Map();
    let dropdown = null;
    let matches = [];
    let activeIndex = 0;
    let range = null;

    function seedFromText() {
        const text = editor.getText();
        users.slice().sort(function (a, b) { return b.name.length - a.name.length; }).forEach(function (user) {
            if (text.indexOf('@' + user.name) !== -1) mentioned.set(user.id, '@' + user.name);
        });
    }

    function close() {
        if (dropdown) {
            dropdown.remove();
            dropdown = null;
        }
        matches = [];
        range = null;
    }

    function activeQuery() {
        const { $from, empty } = editor.state.selection;
        if (! empty || $from.parent.type.name === 'codeBlock') return null;

        const before = $from.parent.textBetween(0, $from.parentOffset, undefined, '￼');
        const at = before.lastIndexOf('@');
        if (at === -1) return null;

        const between = before.slice(at + 1);
        if (/[\s￼]/.test(between)) return null;

        const preceding = at === 0 ? '' : before[at - 1];
        if (preceding && ! /[\s￼]/.test(preceding)) return null;

        return { from: $from.pos - between.length - 1, to: $from.pos, query: between };
    }

    function highlight() {
        Array.prototype.forEach.call(dropdown.children, function (child, i) {
            child.classList.toggle('is-active', i === activeIndex);
        });
    }

    function position() {
        const caret = editor.view.coordsAtPos(range.to);
        const margin = 4;
        const spaceBelow = window.innerHeight - caret.bottom - margin - 8;
        const spaceAbove = caret.top - margin - 8;
        const above = spaceBelow < dropdown.scrollHeight && spaceAbove > spaceBelow;

        dropdown.style.maxHeight = Math.max(80, above ? spaceAbove : spaceBelow) + 'px';
        dropdown.style.left = Math.min(caret.left, window.innerWidth - dropdown.offsetWidth - 8) + 'px';
        dropdown.style.top = above
            ? (caret.top - margin - dropdown.offsetHeight) + 'px'
            : (caret.bottom + margin) + 'px';
    }

    function pick(user) {
        const target = range;
        close();
        // A text node, not a string: a string is parsed as HTML.
        editor.chain().focus().insertContentAt({ from: target.from, to: target.to }, { type: 'text', text: '@' + user.name + ' ' }).run();
        mentioned.set(user.id, '@' + user.name);
    }

    function render() {
        if (dropdown) dropdown.remove();
        dropdown = el('div', 'rte-mentions', { role: 'listbox' });
        matches.forEach(function (user) {
            const item = el('button', 'rte-mention-option', { type: 'button', role: 'option' });
            item.textContent = user.name;
            // mousedown, not click: fires before the editor blurs.
            item.addEventListener('mousedown', function (event) {
                event.preventDefault();
                pick(user);
            });
            dropdown.appendChild(item);
        });
        document.body.appendChild(dropdown);
        highlight();
        position();
    }

    function refresh() {
        const q = activeQuery();
        if (! q) {
            close();
            return;
        }

        const needle = q.query.toLowerCase();
        const found = users.filter(function (user) {
            return user.name.toLowerCase().indexOf(needle) !== -1;
        }).slice(0, 6);

        if (found.length === 0) {
            close();
            return;
        }

        const same = dropdown && found.length === matches.length && found.every(function (u, i) { return u.id === matches[i].id; });
        matches = found;
        range = q;
        if (same) {
            position();
        } else {
            activeIndex = 0;
            render();
        }
    }

    // Returns true when the key was consumed by the open dropdown.
    function handleKey(event) {
        if (! dropdown || matches.length === 0) return false;

        if (event.key === 'ArrowDown') {
            activeIndex = (activeIndex + 1) % matches.length;
            highlight();
        } else if (event.key === 'ArrowUp') {
            activeIndex = (activeIndex - 1 + matches.length) % matches.length;
            highlight();
        } else if (event.key === 'Enter' || event.key === 'Tab') {
            pick(matches[activeIndex]);
        } else if (event.key === 'Escape') {
            close();
        } else {
            return false;
        }

        return true;
    }

    seedFromText();

    return {
        refresh: refresh,
        close: close,
        handleKey: handleKey,
        ids: function () {
            const text = editor.getText();
            const ids = [];
            mentioned.forEach(function (display, id) {
                if (text.indexOf(display) !== -1) ids.push(id);
            });
            return ids;
        },
        reset: function () { mentioned.clear(); },
    };
}

export function createRichTextEditor(root) {
    const initial = root.dataset.content || '';
    const label = root.dataset.label || 'Rich text';
    const users = parseJson(root.dataset.mentions, []);
    const hiddenInput = root.querySelector('input[data-rte-input]');

    root.classList.add('rte');
    root.classList.remove('rte-loading');
    if ('compact' in root.dataset) root.classList.add('rte--compact');

    const content = el('div', 'rte-content');
    content.dataset.placeholder = root.dataset.placeholder || '';

    let mentions = null;
    let toolbar = null;
    let linkBar = null;

    // Trailing empty paragraphs (the editor keeps one after a final code
    // block so the cursor can leave it) aren't content worth saving.
    function serialize() {
        return editor.isEmpty ? '' : editor.getHTML().replace(/(?:<p><\/p>)+$/, '');
    }

    function syncInput() {
        if (hiddenInput) hiddenInput.value = serialize();
        root.classList.toggle('rte-empty', editor.isEmpty);
    }

    const editor = new Editor({
        element: content,
        content: initial,
        extensions: [
            StarterKit.configure({
                // Everything below is switched off because it has no toolbar
                // control and isn't in the server-side allowlist.
                codeBlock: false,
                code: false,
                strike: false,
                blockquote: false,
                horizontalRule: false,
                heading: { levels: [1, 2, 3] },
                link: {
                    openOnClick: false,
                    autolink: true,
                    defaultProtocol: 'https',
                    protocols: ['mailto'],
                    HTMLAttributes: { rel: 'noopener noreferrer nofollow', target: '_blank' },
                },
            }),
            CodeBlockLowlight.configure({ lowlight: lowlight }),
        ],
        editorProps: {
            attributes: { class: 'rte-prose rich-text', role: 'textbox', 'aria-multiline': 'true', 'aria-label': label },
            handleKeyDown: function (view, event) {
                if (mentions && mentions.handleKey(event)) return true;

                if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) {
                    root.dispatchEvent(new CustomEvent('rte:submit', { bubbles: true }));
                    return true;
                }

                return false;
            },
        },
        onUpdate: function () {
            syncInput();
            if (mentions) mentions.refresh();
            if (toolbar) toolbar.refresh();
        },
        onSelectionUpdate: function () {
            if (mentions) mentions.refresh();
            if (toolbar) toolbar.refresh();
        },
        onBlur: function () {
            // Delayed so a mousedown-picked option still runs first.
            if (mentions) setTimeout(mentions.close, 150);
        },
    });

    toolbar = buildToolbar(editor, function () { linkBar.open(); });
    linkBar = buildLinkBar(editor);
    if (users.length > 0) mentions = setupMentions(editor, users);

    root.prepend(toolbar.element, linkBar.element);
    root.appendChild(content);

    const form = root.closest('form');
    if (form) form.addEventListener('submit', syncInput);

    syncInput();
    toolbar.refresh();

    return {
        editor: editor,
        getHTML: serialize,
        isEmpty: function () { return editor.isEmpty; },
        focus: function () { editor.commands.focus('end'); },
        clear: function () {
            editor.commands.clearContent(true);
            if (mentions) mentions.reset();
        },
        getMentionedUserIds: function () { return mentions ? mentions.ids() : []; },
        destroy: function () {
            if (mentions) mentions.close();
            editor.destroy();
        },
    };
}
