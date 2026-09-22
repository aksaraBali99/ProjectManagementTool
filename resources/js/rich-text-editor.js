import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import CodeBlockLowlight from '@tiptap/extension-code-block-lowlight';
import Emoji, { emojis as defaultEmojis } from '@tiptap/extension-emoji';
import { ResizableImage } from './resizable-image.js';
import { Audio } from './audio-extension.js';
import { isEmojiSupported } from 'is-emoji-supported';
import { lowlight } from './code-highlight.js';

// THE rich-text editor. Task Description and Comments (new + edit) all mount
// this same module — there is deliberately no second configuration. Later
// media phases (image/audio/video) extend the `extensions` list and the
// TOOLBAR below, and every editor in the app picks the change up.
//
// The feature set is intentionally exactly what App\Support\RichText's
// sanitizer allows (p, br, strong, em, u, h1-h3, ul/ol/li, a, pre/code, img,
// audio): anything the editor could emit but the server strips would
// silently vanish on save, so the two lists must move together.

const ICON_ATTRS = 'width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';

const ICONS = {
    bulletList: '<svg ' + ICON_ATTRS + '><line x1="6" y1="4" x2="14" y2="4"/><line x1="6" y1="8" x2="14" y2="8"/><line x1="6" y1="12" x2="14" y2="12"/><circle cx="2.5" cy="4" r=".6" fill="currentColor"/><circle cx="2.5" cy="8" r=".6" fill="currentColor"/><circle cx="2.5" cy="12" r=".6" fill="currentColor"/></svg>',
    orderedList: '<svg ' + ICON_ATTRS + '><line x1="7" y1="4" x2="14" y2="4"/><line x1="7" y1="8" x2="14" y2="8"/><line x1="7" y1="12" x2="14" y2="12"/><path d="M2 3l1-.6V6" /><path d="M1.8 9.6c.3-.8 1.9-.8 1.9.2 0 .9-1.9 1.3-1.9 2.2H3.8"/></svg>',
    link: '<svg ' + ICON_ATTRS + '><path d="M6.5 9.5a3 3 0 0 0 4.2 0l2-2a3 3 0 0 0-4.2-4.2l-.7.7"/><path d="M9.5 6.5a3 3 0 0 0-4.2 0l-2 2a3 3 0 0 0 4.2 4.2l.7-.7"/></svg>',
    codeBlock: '<svg ' + ICON_ATTRS + '><polyline points="5.5 4.5 2 8 5.5 11.5"/><polyline points="10.5 4.5 14 8 10.5 11.5"/></svg>',
    emoji: '<svg ' + ICON_ATTRS + '><circle cx="8" cy="8" r="6"/><path d="M5.4 9.6a3.2 3.2 0 0 0 5.2 0"/><circle cx="6" cy="6.6" r=".7" fill="currentColor" stroke="none"/><circle cx="10" cy="6.6" r=".7" fill="currentColor" stroke="none"/></svg>',
    image: '<svg ' + ICON_ATTRS + '><rect x="1.5" y="2.5" width="13" height="11" rx="1.5"/><circle cx="5.5" cy="6.5" r="1.2"/><path d="M2 12l3.5-3.5a1 1 0 0 1 1.4 0L10 11.5m2-2 .6-.6a1 1 0 0 1 1.4 0L14.5 11"/></svg>',
    audio: '<svg ' + ICON_ATTRS + '><path d="M9.5 2.5v9.2a2 2 0 1 1-1-1.73V4.7L5 5.6v6.1a2 2 0 1 1-1-1.73V4.8z" fill="currentColor" stroke="none"/></svg>',
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
    // Opens the emoji picker popover (built by buildEmojiPicker). `popup` items
    // report aria-expanded instead of aria-pressed, and "active" means "open".
    { key: 'emoji', label: 'Emoji', html: ICONS.emoji, popup: true, run: null, active: () => emojiPickerIsOpen() },
    // No `active` state — inserting an image doesn't toggle a mark/node the
    // cursor can currently be "inside", unlike the other buttons.
    { key: 'image', label: 'Insert image', html: ICONS.image, run: null, active: () => false },
    // Same reasoning as image (task #4 phase 4).
    { key: 'audio', label: 'Insert audio', html: ICONS.audio, run: null, active: () => false },
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

// `handlers` are the toolbar items with no single ProseMirror command of
// their own — they open other UI instead: { link, emoji, image, audio }.
function buildToolbar(editor, handlers) {
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

        const button = el('button', 'rte-btn', { type: 'button', title: item.label, 'aria-label': item.label });
        button.setAttribute(item.popup ? 'aria-expanded' : 'aria-pressed', 'false');
        if (item.popup) button.setAttribute('aria-haspopup', 'dialog');
        button.innerHTML = item.html;
        // mousedown, not click: keeps the editor's selection/focus so the
        // command applies to what the user had selected.
        button.addEventListener('mousedown', function (event) { event.preventDefault(); });
        button.addEventListener('click', function () {
            if (item.key === 'link') handlers.link();
            else if (item.key === 'emoji') handlers.emoji(button);
            else if (item.key === 'image') handlers.image();
            else if (item.key === 'audio') handlers.audio();
            else item.run(editor);
        });
        bar.appendChild(button);
        buttons.push({ item: item, button: button });
    });

    function refresh() {
        buttons.forEach(function (entry) {
            const on = entry.item.active(editor);
            entry.button.classList.toggle('is-active', on);
            entry.button.setAttribute(entry.item.popup ? 'aria-expanded' : 'aria-pressed', on ? 'true' : 'false');
        });

        const level = [1, 2, 3].find(function (l) { return editor.isActive('heading', { level: l }); });
        heading.value = level ? String(level) : 'p';
    }

    function button(key) {
        const entry = buttons.find(function (b) { return b.item.key === key; });

        return entry ? entry.button : null;
    }

    return { element: bar, refresh: refresh, button: button };
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

// Image upload. Where the file goes, and under whose permission, is read
// from `root.dataset` fresh on every click rather than fixed at editor
// construction — the Add Task page's editor has to send whichever project
// is *currently* selected (see rte-image-*-* attributes set/kept updated
// by each Blade page in resources/views/tasks/*.blade.php), and rereading
// them here is simpler than teaching this shared module about that.
//
// Two upload targets, matching RichTextImageController's two actions:
//   - data-image-task-id (+ data-image-context: description|comment) — an
//     existing task; POSTs to /tasks/{id}/images.
//   - data-image-pending-id (+ data-image-project-id, optionally
//     data-image-department-id) — the Add Task page, before the task
//     exists; POSTs to /pending-task-images.
// An editor root with neither pair set has no image support wired up (not
// expected for anything in this app today, but the module doesn't assume
// it) — the toolbar button is hidden rather than a dead click.
function buildImageUpload(editor, root, onBusyChange) {
    const input = el('input', 'rte-image-input', { type: 'file', accept: 'image/*', tabindex: '-1', 'aria-hidden': 'true' });

    const status = el('div', 'rte-upload-status');
    status.hidden = true;

    let busy = false;

    function target() {
        const taskId = root.dataset.imageTaskId;
        if (taskId) {
            return { url: '/tasks/' + taskId + '/images', fields: { context: root.dataset.imageContext || 'comment' } };
        }

        const pendingId = root.dataset.imagePendingId;
        if (pendingId) {
            return {
                url: '/pending-task-images',
                fields: {
                    pending_id: pendingId,
                    project_id: root.dataset.imageProjectId || '',
                    department_id: root.dataset.imageDepartmentId || '',
                },
            };
        }

        return null;
    }

    function setStatus(message, isError) {
        status.hidden = ! message;
        status.textContent = message || '';
        status.classList.toggle('is-error', !! isError);
    }

    function setBusy(next) {
        busy = next;
        status.classList.toggle('is-busy', next);
        if (onBusyChange) onBusyChange(next);
    }

    function upload(file) {
        const destination = target();
        if (! destination) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const body = new FormData();
        body.append('file', file);
        Object.keys(destination.fields).forEach(function (key) { body.append(key, destination.fields[key]); });

        setBusy(true);
        setStatus('Uploading image…', false);

        fetch(destination.url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
            body: body,
        })
            .then(function (response) {
                return response.json().catch(function () { return null; }).then(function (data) {
                    if (! response.ok) throw new Error((data && data.message) || 'Failed to upload image.');

                    return data;
                });
            })
            .then(function (data) {
                editor.chain().focus().setImage({ src: data.url }).run();
                setStatus('', false);
            })
            .catch(function (error) {
                setStatus(error.message, true);
            })
            .finally(function () {
                setBusy(false);
                input.value = '';
            });
    }

    input.addEventListener('change', function () {
        const file = input.files[0];
        if (file) upload(file);
    });

    return {
        element: status,
        input: input,
        isWired: function () { return target() !== null; },
        isBusy: function () { return busy; },
        trigger: function () {
            if (busy || ! target()) return;
            input.click();
        },
    };
}

// Audio upload (task #4 phase 4) — same shape as buildImageUpload() above,
// reusing the exact same pattern rather than a shared abstraction: two
// upload targets read fresh from `root.dataset` (data-audio-task-id/
// data-audio-context for an existing task; data-audio-pending-id/
// data-audio-project-id/data-audio-department-id for the Add Task page),
// matching RichTextAudioController's two actions the same way the image
// button matches RichTextImageController's.
function buildAudioUpload(editor, root, onBusyChange) {
    const input = el('input', 'rte-audio-input', { type: 'file', accept: 'audio/*', tabindex: '-1', 'aria-hidden': 'true' });

    const status = el('div', 'rte-upload-status');
    status.hidden = true;

    let busy = false;

    function target() {
        const taskId = root.dataset.audioTaskId;
        if (taskId) {
            return { url: '/tasks/' + taskId + '/audio', fields: { context: root.dataset.audioContext || 'comment' } };
        }

        const pendingId = root.dataset.audioPendingId;
        if (pendingId) {
            return {
                url: '/pending-task-audio',
                fields: {
                    pending_id: pendingId,
                    project_id: root.dataset.audioProjectId || '',
                    department_id: root.dataset.audioDepartmentId || '',
                },
            };
        }

        return null;
    }

    function setStatus(message, isError) {
        status.hidden = ! message;
        status.textContent = message || '';
        status.classList.toggle('is-error', !! isError);
    }

    function setBusy(next) {
        busy = next;
        status.classList.toggle('is-busy', next);
        if (onBusyChange) onBusyChange(next);
    }

    function upload(file) {
        const destination = target();
        if (! destination) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const body = new FormData();
        body.append('file', file);
        Object.keys(destination.fields).forEach(function (key) { body.append(key, destination.fields[key]); });

        setBusy(true);
        setStatus('Uploading audio…', false);

        fetch(destination.url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
            body: body,
        })
            .then(function (response) {
                return response.json().catch(function () { return null; }).then(function (data) {
                    if (! response.ok) throw new Error((data && data.message) || 'Failed to upload audio.');

                    return data;
                });
            })
            .then(function (data) {
                editor.chain().focus().setAudio({ src: data.url }).run();
                setStatus('', false);
            })
            .catch(function (error) {
                setStatus(error.message, true);
            })
            .finally(function () {
                setBusy(false);
                input.value = '';
            });
    }

    input.addEventListener('change', function () {
        const file = input.files[0];
        if (file) upload(file);
    });

    return {
        element: status,
        input: input,
        isWired: function () { return target() !== null; },
        isBusy: function () { return busy; },
        trigger: function () {
            if (busy || ! target()) return;
            input.click();
        },
    };
}

// Places a fixed-position popup just below a caret rectangle ({left, top,
// bottom}), or above it when there isn't room below, and keeps it inside the
// viewport. Shared by the @mention and :emoji: dropdowns.
function placePopup(dropdown, caret) {
    const margin = 4;
    const spaceBelow = window.innerHeight - caret.bottom - margin - 8;
    const spaceAbove = caret.top - margin - 8;
    const above = spaceBelow < dropdown.scrollHeight && spaceAbove > spaceBelow;

    dropdown.style.maxHeight = Math.max(80, above ? spaceAbove : spaceBelow) + 'px';
    dropdown.style.left = Math.max(8, Math.min(caret.left, window.innerWidth - dropdown.offsetWidth - 8)) + 'px';
    dropdown.style.top = above
        ? (caret.top - margin - dropdown.offsetHeight) + 'px'
        : (caret.bottom + margin) + 'px';
}

// :emoji: support. Emoji render as the browser's NATIVE glyphs — no image
// set, no extra assets. The extension's bundled data attaches a
// cdn.jsdelivr.net Apple PNG as `fallbackImage` to most emoji and renders
// that <img> instead of the character whenever the browser doesn't detect
// native support; stripping the field here guarantees the character is
// always what gets rendered (and stored — the sanitizer would otherwise let
// that <img> through now that task #4 phase 3 allows the tag, and an image
// would silently replace the emoji on save). Regional-indicator letters are
// left out of the list (they exist only to compose flags).
const NATIVE_EMOJIS = defaultEmojis
    .filter(function (item) { return item.emoji && ! /^regional_indicator/.test(item.name); })
    .map(function (item) {
        const copy = Object.assign({}, item);
        delete copy.fallbackImage;

        return copy;
    });

// The extension decides "supported" per Unicode version by testing one emoji
// of that version, which misses flags: Windows draws them as two letters
// ("LT"), not a flag. Country flags (a pair of regional indicators) and
// subdivision flags (a black flag + tag characters) are therefore offered
// only when a real flag renders as a single glyph on this device.
const FLAG_EMOJI = /^[\u{1F1E6}-\u{1F1FF}]{2}$|^\u{1F3F4}[\u{E0020}-\u{E007F}]+$/u;
let flagSupport = null;

function flagsRenderable() {
    if (flagSupport === null) flagSupport = isEmojiSupported('\u{1F1FA}\u{1F1F8}');

    return flagSupport;
}

// Only the emoji this device can actually draw are ever offered (by the ":"
// autocomplete or the picker) — an unsupported one would show as an empty
// box. If detection says nothing at all is supported (canvas blocked, etc.)
// fall back to offering everything. The skin-tone/hair swatches in the
// "components" group aren't standalone emoji to insert. Computed once: device
// support doesn't change while the page is open.
let availableCache = null;

function availableEmojis(editor) {
    if (availableCache === null) {
        const supported = NATIVE_EMOJIS.filter(function (item) {
            return item.group !== 'components'
                && editor.storage.emoji.isSupported(item)
                && (flagsRenderable() || ! FLAG_EMOJI.test(item.emoji));
        });
        availableCache = supported.length > 0 ? supported : NATIVE_EMOJIS.filter(function (item) { return item.group !== 'components'; });
    }

    return availableCache;
}

const EMOJI_MIN_QUERY = 2;
const EMOJI_MAX_RESULTS = 8;

// Each emoji is matched on its aliases and its full name, and the label that
// matched is the one shown (so typing ":smi" offers ":smile:", not an
// unrelated alias of a long-named emoji). Tiers: a label starting with the
// query, then containing it, then a keyword tag starting with it; within a
// tier the shortest label wins ("smile" before "smile_cat").
function searchEmojis(editor, query, limits) {
    const min = limits && limits.min !== undefined ? limits.min : EMOJI_MIN_QUERY;
    const max = limits && limits.max !== undefined ? limits.max : EMOJI_MAX_RESULTS;
    const needle = query.toLowerCase();
    if (needle.length < min) return [];

    const pool = availableEmojis(editor);

    const ranked = [];
    pool.forEach(function (item) {
        const labels = (item.shortcodes || []).concat([item.name]);
        const shortest = function (list) {
            return list.reduce(function (best, n) { return best === null || n.length < best.length ? n : best; }, null);
        };

        const prefixed = shortest(labels.filter(function (n) { return n.indexOf(needle) === 0; }));
        const containing = shortest(labels.filter(function (n) { return n.indexOf(needle) !== -1; }));
        const tagged = (item.tags || []).some(function (t) { return t.indexOf(needle) === 0; });

        if (prefixed !== null) ranked.push({ item: item, label: prefixed, tier: 0 });
        else if (containing !== null) ranked.push({ item: item, label: containing, tier: 1 });
        else if (tagged) ranked.push({ item: item, label: labels[0], tier: 2 });
    });

    ranked.sort(function (a, b) {
        return a.tier - b.tier || a.label.length - b.label.length || a.label.localeCompare(b.label);
    });

    const top = ranked.slice(0, max);
    const counts = {};
    top.forEach(function (entry) { counts[entry.label] = (counts[entry.label] || 0) + 1; });

    // Two different emoji can share an alias in the dataset (":satellite:");
    // when that happens the full, unique name tells them apart.
    return top.map(function (entry) {
        return Object.assign({}, entry.item, { label: counts[entry.label] > 1 ? entry.item.name : entry.label });
    });
}

// Suggestion-plugin renderer: same look and keyboard behavior as the @mention
// list (up/down move, Enter/Tab pick) and it reuses its styles.
function emojiSuggestionRenderer() {
    let dropdown = null;
    let current = null;
    let activeIndex = 0;

    function highlight() {
        Array.prototype.forEach.call(dropdown.children, function (child, i) {
            child.classList.toggle('is-active', i === activeIndex);
            child.setAttribute('aria-selected', i === activeIndex ? 'true' : 'false');
        });
    }

    function reposition() {
        const rect = dropdown && current.clientRect && current.clientRect();
        if (rect) placePopup(dropdown, rect);
    }

    function render(props) {
        current = props;
        activeIndex = 0;
        if (dropdown) dropdown.remove();

        if (props.items.length === 0) {
            dropdown = null;
            return;
        }

        dropdown = el('div', 'rte-mentions rte-emoji', { role: 'listbox', 'aria-label': 'Emoji suggestions' });
        props.items.forEach(function (item, i) {
            const option = el('button', 'rte-mention-option rte-emoji-option', { type: 'button', role: 'option' });
            const glyph = el('span', 'rte-emoji-glyph', { 'aria-hidden': 'true' });
            glyph.textContent = item.emoji;
            const label = el('span', 'rte-emoji-name');
            label.textContent = ':' + item.label + ':';
            option.append(glyph, label);
            // mousedown, not click: fires before the editor blurs.
            option.addEventListener('mousedown', function (event) {
                event.preventDefault();
                current.command({ name: item.name });
            });
            option.addEventListener('mousemove', function () {
                activeIndex = i;
                highlight();
            });
            dropdown.appendChild(option);
        });
        document.body.appendChild(dropdown);
        highlight();
        reposition();
    }

    return {
        onStart: render,
        onUpdate: render,
        onKeyDown: function (props) {
            if (! dropdown || current.items.length === 0) return false;
            const event = props.event;

            if (event.key === 'ArrowDown') {
                activeIndex = (activeIndex + 1) % current.items.length;
            } else if (event.key === 'ArrowUp') {
                activeIndex = (activeIndex - 1 + current.items.length) % current.items.length;
            } else if (event.key === 'Enter' || event.key === 'Tab') {
                current.command({ name: current.items[activeIndex].name });
                return true;
            } else {
                // Escape (and everything else) is left to the suggestion
                // plugin, which closes the list itself.
                return false;
            }

            highlight();

            return true;
        },
        onExit: function () {
            if (dropdown) dropdown.remove();
            dropdown = null;
            current = null;
        },
    };
}

// Emoji picker (the toolbar button next to "code block"): a popover with a
// search box, category tabs and a "recent" tab. It draws from the same
// availableEmojis() pool as the ":" autocomplete, so it only ever offers
// native glyphs this device can render, and inserts through the emoji
// extension's own setEmoji command (which keeps the surrounding bold/italic).
const EMOJI_CATEGORIES = [
    { key: 'recent', label: 'Recently used', icon: '🕘' },
    // The dataset's unnamed group is "smileys & emotion".
    { key: 'smileys', label: 'Smileys & emotion', icon: '😀', group: '' },
    { key: 'people', label: 'People & body', icon: '👋', group: 'people & body' },
    { key: 'animals', label: 'Animals & nature', icon: '🐵', group: 'animals & nature' },
    { key: 'food', label: 'Food & drink', icon: '🍇', group: 'food & drink' },
    { key: 'travel', label: 'Travel & places', icon: '🌍', group: 'travel & places' },
    { key: 'activities', label: 'Activities', icon: '🎃', group: 'activities' },
    { key: 'objects', label: 'Objects', icon: '💡', group: 'objects' },
    { key: 'symbols', label: 'Symbols', icon: '❤️', group: 'symbols' },
    { key: 'flags', label: 'Flags', icon: '🏁', group: 'flags' },
];

const EMOJI_RECENT_KEY = 'solava.emoji.recent';
const EMOJI_RECENT_MAX = 24;
const EMOJI_GRID_COLUMNS = 8;
const EMOJI_SEARCH_MAX = 96;

// Per-browser convenience only — every read/write is guarded, since storage can
// be blocked or full and the picker must work without it.
function readRecentEmojiNames() {
    try {
        const stored = JSON.parse(window.localStorage.getItem(EMOJI_RECENT_KEY) || '[]');

        return Array.isArray(stored) ? stored.filter(function (name) { return typeof name === 'string'; }) : [];
    } catch (error) {
        return [];
    }
}

function rememberEmoji(name) {
    try {
        const next = [name].concat(readRecentEmojiNames().filter(function (n) { return n !== name; })).slice(0, EMOJI_RECENT_MAX);
        window.localStorage.setItem(EMOJI_RECENT_KEY, JSON.stringify(next));
    } catch (error) {
        // Not persisted; nothing else depends on it.
    }
}

let openEmojiPicker = null;

function emojiPickerIsOpen() {
    return openEmojiPicker !== null;
}

function buildEmojiPicker(editor) {
    let panel = null;
    let anchor = null;
    let searchInput = null;
    let tabs = null;
    let title = null;
    let grid = null;
    let preview = null;
    let activeKey = null; // null while search results are showing
    let lastKey = null; // the category to return to when the search is cleared
    let cleanup = [];

    const byName = new Map();
    NATIVE_EMOJIS.forEach(function (item) { byName.set(item.name, item); });

    function categories() {
        return EMOJI_CATEGORIES.filter(function (c) {
            if (c.key === 'recent') return readRecentEmojiNames().length > 0;
            if (c.key === 'flags') return flagsRenderable();

            return true;
        });
    }

    function itemsFor(key) {
        const pool = availableEmojis(editor);
        if (key === 'recent') {
            return readRecentEmojiNames()
                .map(function (name) { return byName.get(name); })
                .filter(function (item) { return item && pool.indexOf(item) !== -1; });
        }

        const category = EMOJI_CATEGORIES.find(function (c) { return c.key === key; });

        return pool.filter(function (item) { return (item.group || '') === category.group; });
    }

    function cells() {
        return Array.prototype.slice.call(grid.querySelectorAll('.rte-emoji-cell'));
    }

    function showPreview(item) {
        preview.textContent = '';
        if (! item) return;
        const glyph = el('span', 'rte-emoji-glyph', { 'aria-hidden': 'true' });
        glyph.textContent = item.emoji;
        const name = el('span', 'rte-emoji-name');
        name.textContent = ':' + (item.label || item.shortcodes[0] || item.name) + ':';
        preview.append(glyph, name);
    }

    function pick(item) {
        rememberEmoji(item.name);
        close();
        editor.chain().focus().setEmoji(item.name).run();
        // TipTap's focus() finishes on the next animation frame; closing the
        // popover already dropped focus, so put it back immediately too.
        editor.view.focus();
    }

    function renderGrid(items, heading) {
        title.textContent = heading;
        grid.textContent = '';

        if (items.length === 0) {
            const empty = el('p', 'rte-emoji-empty');
            empty.textContent = 'No emoji found.';
            grid.appendChild(empty);
            showPreview(null);
            return;
        }

        items.forEach(function (item, index) {
            const cell = el('button', 'rte-emoji-cell', {
                type: 'button',
                title: ':' + (item.label || item.shortcodes[0] || item.name) + ':',
                'aria-label': item.name.replace(/_/g, ' '),
                tabindex: index === 0 ? '0' : '-1',
            });
            cell.textContent = item.emoji;
            cell.addEventListener('click', function () { pick(item); });
            const enter = function () { showPreview(item); };
            cell.addEventListener('mouseenter', enter);
            cell.addEventListener('focus', function () {
                cells().forEach(function (other) { other.tabIndex = other === cell ? 0 : -1; });
                enter();
            });
            grid.appendChild(cell);
        });

        grid.scrollTop = 0;
        showPreview(null);
    }

    function renderTabs() {
        tabs.textContent = '';
        categories().forEach(function (category) {
            const tab = el('button', 'rte-emoji-tab', { type: 'button', role: 'tab', title: category.label, 'aria-label': category.label, 'aria-selected': category.key === activeKey ? 'true' : 'false' });
            tab.textContent = category.icon;
            tab.classList.toggle('is-active', category.key === activeKey);
            tab.addEventListener('click', function () {
                searchInput.value = '';
                select(category.key);
            });
            tabs.appendChild(tab);
        });
    }

    function select(key) {
        activeKey = key;
        lastKey = key;
        renderTabs();
        const category = EMOJI_CATEGORIES.find(function (c) { return c.key === key; });
        renderGrid(itemsFor(key), category.label);
    }

    function runSearch() {
        const query = searchInput.value.trim();
        if (query === '') {
            select(lastKey);
            return;
        }

        activeKey = null;
        renderTabs();
        renderGrid(searchEmojis(editor, query, { min: 1, max: EMOJI_SEARCH_MAX }), 'Search results');
    }

    function reposition() {
        if (! panel || ! anchor) return;
        const rect = anchor.getBoundingClientRect();
        placePopup(panel, { left: rect.left, top: rect.top, bottom: rect.bottom });
    }

    function onGridKey(event) {
        const list = cells();
        const index = list.indexOf(document.activeElement);
        if (index === -1) return;

        let next = null;
        if (event.key === 'ArrowRight') next = index + 1;
        else if (event.key === 'ArrowLeft') next = index - 1;
        else if (event.key === 'ArrowDown') next = index + EMOJI_GRID_COLUMNS;
        else if (event.key === 'ArrowUp') next = index - EMOJI_GRID_COLUMNS;
        else return;

        event.preventDefault();
        if (next < 0) {
            searchInput.focus();
        } else if (next < list.length) {
            list[next].focus();
        }
    }

    function open(button) {
        if (openEmojiPicker) openEmojiPicker.close();
        anchor = button;

        panel = el('div', 'rte-emoji-picker', { role: 'dialog', 'aria-label': 'Emoji picker' });
        searchInput = el('input', 'rte-emoji-search', { type: 'text', placeholder: 'Search emoji', 'aria-label': 'Search emoji', autocomplete: 'off' });
        tabs = el('div', 'rte-emoji-tabs', { role: 'tablist' });
        title = el('div', 'rte-emoji-title');
        grid = el('div', 'rte-emoji-grid');
        preview = el('div', 'rte-emoji-preview', { 'aria-live': 'polite' });
        panel.append(searchInput, tabs, title, grid, preview);
        document.body.appendChild(panel);

        searchInput.addEventListener('input', runSearch);
        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowDown') {
                const first = cells()[0];
                if (first) {
                    event.preventDefault();
                    first.focus();
                }
            } else if (event.key === 'Enter') {
                // Enter in the search box inserts the best match.
                event.preventDefault();
                const first = cells()[0];
                if (first) first.click();
            }
        });
        grid.addEventListener('keydown', onGridKey);
        panel.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                close();
                editor.commands.focus();
                editor.view.focus();
            }
        });

        // Outside click closes; page scroll/resize keeps the popover attached to its button.
        const onOutside = function (event) {
            if (! panel.contains(event.target) && ! anchor.contains(event.target)) close();
        };
        document.addEventListener('mousedown', onOutside, true);
        window.addEventListener('resize', reposition);
        window.addEventListener('scroll', reposition, true);
        cleanup = [
            function () { document.removeEventListener('mousedown', onOutside, true); },
            function () { window.removeEventListener('resize', reposition); },
            function () { window.removeEventListener('scroll', reposition, true); },
        ];

        openEmojiPicker = api;
        select(categories()[0].key);
        reposition();
        searchInput.focus({ preventScroll: true });
    }

    function close() {
        if (! panel) return;
        cleanup.forEach(function (fn) { fn(); });
        cleanup = [];
        panel.remove();
        panel = null;
        if (anchor) {
            anchor.classList.remove('is-active');
            anchor.setAttribute('aria-expanded', 'false');
        }
        anchor = null;
        if (openEmojiPicker === api) openEmojiPicker = null;
    }

    const api = {
        toggle: function (button) {
            if (panel) {
                close();
                return;
            }
            open(button);
            button.classList.add('is-active');
            button.setAttribute('aria-expanded', 'true');
        },
        close: close,
    };

    return api;
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
        placePopup(dropdown, editor.view.coordsAtPos(range.to));
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
    let imageUpload = null;
    let audioUpload = null;

    // Trailing empty paragraphs (the editor keeps one after a final code
    // block, or after a block image, so the cursor can leave it) aren't
    // content worth saving. Emoji are editor nodes that serialize as
    // <span data-type="emoji">😀</span>; they're stored as the bare Unicode
    // character instead, so saved content is plain text plus the same
    // allowlisted tags as before (the wrapper carries nothing the character
    // doesn't).
    function serialize() {
        if (editor.isEmpty) return '';

        return editor.getHTML()
            .replace(/<span\b[^>]*\bdata-type="emoji"[^>]*>([^<]*)<\/span>/g, '$1')
            .replace(/(?:<p><\/p>)+$/, '');
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
            Emoji.configure({
                emojis: NATIVE_EMOJIS,
                enableEmoticons: false,
                suggestion: {
                    items: function (props) { return searchEmojis(props.editor, props.query); },
                    render: emojiSuggestionRenderer,
                },
            }),
            // allowBase64: false (the default) — this app's images always
            // come from the upload endpoint as an R2/MinIO URL; a base64
            // <img src> could only arrive via a paste or a crafted request,
            // and the server-side sanitizer rejects "data:" too either way.
            //
            // resize (task #4, image resize): native TipTap resize (see
            // resizable-image.js for the one gap it fills — max size — over
            // the stock extension). maxWidth roughly matches the content
            // column's own max-width (.rich-text img's inline-thumbnail
            // cap, resources/css/app.css) — resizing past what the column
            // can show wouldn't do anything visually useful anyway.
            // alwaysPreserveAspectRatio: true — dragging any single handle
            // (an edge or the corner) scales both dimensions together, so
            // an image can't be squashed or stretched out of shape.
            ResizableImage.configure({
                inline: false,
                resize: {
                    enabled: true,
                    directions: ['bottom', 'right', 'bottom-right'],
                    minWidth: 100,
                    maxWidth: 800,
                    minHeight: 60,
                    maxHeight: 800,
                    alwaysPreserveAspectRatio: true,
                },
            }),
            // task #4 phase 4 — see audio-extension.js for why this is a
            // plain custom node rather than a NodeView or third-party
            // package.
            Audio,
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

    const emojiPicker = buildEmojiPicker(editor);
    imageUpload = buildImageUpload(editor, root, function (busy) {
        const button = toolbar.button('image');
        if (button) button.disabled = busy;
    });
    audioUpload = buildAudioUpload(editor, root, function (busy) {
        const button = toolbar.button('audio');
        if (button) button.disabled = busy;
    });
    toolbar = buildToolbar(editor, {
        link: function () { linkBar.open(); },
        emoji: function (button) { emojiPicker.toggle(button); },
        image: function () { imageUpload.trigger(); },
        audio: function () { audioUpload.trigger(); },
    });
    linkBar = buildLinkBar(editor);
    if (users.length > 0) mentions = setupMentions(editor, users);

    if (! imageUpload.isWired()) {
        const imageButton = toolbar.button('image');
        if (imageButton) imageButton.hidden = true;
    }

    if (! audioUpload.isWired()) {
        const audioButton = toolbar.button('audio');
        if (audioButton) audioButton.hidden = true;
    }

    root.prepend(toolbar.element, linkBar.element, imageUpload.element, imageUpload.input, audioUpload.element, audioUpload.input);
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
            emojiPicker.close();
            editor.destroy();
        },
    };
}
