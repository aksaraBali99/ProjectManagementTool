import intlTelInput from 'intl-tel-input/intlTelInputWithUtils';
import 'intl-tel-input/dist/css/intlTelInput.css';
import { highlightMentions } from './mention-highlight.js';

function initPhoneInputs() {
    document.querySelectorAll('[data-phone-input]').forEach(function (input) {
        if (input.dataset.itiInitialized) return;
        input.dataset.itiInitialized = 'true';

        const iti = intlTelInput(input, {
            initialCountry: 'id',
            separateDialCode: true,
            strictMode: true,
            countrySearch: true,
            // The visible input intentionally has no `name` — only the hidden
            // input the library creates (E.164-formatted) gets submitted.
            hiddenInputs: function () {
                return { phone: input.dataset.hiddenName };
            },
        });

        const errorEl = document.getElementById(input.dataset.errorTarget);

        function showError(message) {
            if (! errorEl) return;
            errorEl.textContent = message;
            errorEl.style.display = '';
            input.closest('.iti').classList.add('iti--invalid');
        }

        function clearError() {
            if (! errorEl) return;
            errorEl.style.display = 'none';
            input.closest('.iti').classList.remove('iti--invalid');
        }

        function messageForValidationError(code) {
            switch (code) {
                case intlTelInput.VALIDATION_ERROR.INVALID_COUNTRY_CODE:
                    return 'Please enter a valid country code.';
                case intlTelInput.VALIDATION_ERROR.TOO_SHORT:
                    return 'Phone number is too short.';
                case intlTelInput.VALIDATION_ERROR.TOO_LONG:
                    return 'Phone number is too long.';
                default:
                    return 'Please enter valid phone number';
            }
        }

        function validate(force) {
            if (input.value.trim() === '') {
                if (force) showError('Please fill out this field.');
                else clearError();
                return;
            }

            if (iti.isValidNumberPrecise()) {
                clearError();
            } else if (force) {
                showError(messageForValidationError(iti.getValidationError()));
            }
        }

        input.addEventListener('blur', function () { validate(true); });
        input.addEventListener('input', function () {
            validate(!! (errorEl && errorEl.style.display !== 'none'));
        });
        input.addEventListener('countrychange', function () {
            validate(!! (errorEl && errorEl.style.display !== 'none'));
        });

        const form = input.closest('form');
        if (form) {
            form.addEventListener('submit', function (event) {
                validate(true);
                if (errorEl && errorEl.style.display !== 'none') {
                    event.preventDefault();
                }
            });
        }
    });
}

const CHART_PALETTE = ['#1D9E75', '#534AB7', '#2563EB', '#D97706', '#DB2777', '#0891B2'];

// Chart.js is loaded on demand, not bundled into every page's initial
// script — only the Analytics page has canvas[data-chart-type] elements,
// so this dynamic import keeps its ~150KB (gzipped) out of every other
// page's load. Selective registration (not chart.js/auto, which pulls in
// every chart type/plugin) further trims it to just what's used here.
async function initAnalyticsCharts() {
    const canvases = document.querySelectorAll('canvas[data-chart-type]');
    if (canvases.length === 0) return;

    const {
        Chart,
        BarController,
        DoughnutController,
        CategoryScale,
        LinearScale,
        BarElement,
        ArcElement,
        Legend,
        Tooltip,
    } = await import('chart.js');
    Chart.register(BarController, DoughnutController, CategoryScale, LinearScale, BarElement, ArcElement, Legend, Tooltip);

    canvases.forEach(function (canvas) {
        if (canvas.dataset.chartInitialized) return;
        canvas.dataset.chartInitialized = 'true';

        const type = canvas.dataset.chartType;
        const labels = JSON.parse(canvas.dataset.chartLabels || '[]');
        const values = JSON.parse(canvas.dataset.chartValues || '[]');
        const suffix = canvas.dataset.chartSuffix || '';
        const horizontal = canvas.dataset.chartHorizontal === '1';
        // Status/priority breakdown charts pass their own per-slice colors
        // (the same admin-configurable Status & Priority Colors settings
        // every badge elsewhere reads) — anything without that attribute
        // (completion-by-company, staff workload) still cycles the generic
        // palette by index, since those aren't a fixed enum of colored
        // categories.
        const explicitColors = canvas.dataset.chartColors ? JSON.parse(canvas.dataset.chartColors) : null;

        new Chart(canvas, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: explicitColors || labels.map(function (_, i) { return CHART_PALETTE[i % CHART_PALETTE.length]; }),
                }],
            },
            options: {
                indexAxis: horizontal ? 'y' : 'x',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: type === 'doughnut' },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.formattedValue + suffix;
                            },
                        },
                    },
                },
                scales: type === 'bar' ? {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true },
                } : undefined,
            },
        });
    });
}

// TipTap + highlight.js are a large chunk, so like Chart.js they're loaded on
// demand and only once an editor is actually on screen — the Task List page
// renders one (hidden) comment editor per row inside collapsed drilldowns,
// and none of those should cost anything until a row is expanded. An editor
// root (`[data-rich-text]`) waits, via IntersectionObserver, until it's
// displayed near the viewport, then the chunk loads and it mounts.
//
// mount() is idempotent per element and resolves to the editor controller
// (getHTML / isEmpty / clear / focus / getMentionedUserIds / destroy), so
// inline page scripts (the comment box) can call it too without racing the
// auto-init below.
const richTextMounts = new WeakMap();

function whenNearViewport(element) {
    return new Promise(function (resolve) {
        if (! ('IntersectionObserver' in window)) {
            resolve();
            return;
        }

        const observer = new IntersectionObserver(function (entries) {
            if (entries.some(function (entry) { return entry.isIntersecting; })) {
                observer.disconnect();
                resolve();
            }
        }, { rootMargin: '200px' });
        observer.observe(element);
    });
}

function mountRichText(root) {
    if (! richTextMounts.has(root)) {
        richTextMounts.set(root, whenNearViewport(root)
            .then(function () { return import('./rich-text-editor.js'); })
            .then(function (module) { return module.createRichTextEditor(root); }));
    }

    return richTextMounts.get(root);
}

// Read-only rich text render-time enhancements — saved code blocks are
// plain <pre><code class="language-x">, colored client-side; (task #4,
// video resize + lightbox) a saved <video> is turned into the same
// click-to-expand thumbnail the live editor shows; (task #4, document
// upload + embedding) a saved <file-chip> gets its icon added the same
// way the live editor's own NodeView shows one; (task #4, Smart Links) a
// saved <link-preview> gets its image/title/domain card built the same
// way. Each only loads its own chunk when there's actually something of
// that kind to enhance, and re-running this over content that hasn't
// changed (the comment poll calls it on every row) is a no-op past each
// one's own first pass.
function highlightRichText(scope) {
    const target = scope || document;
    const passes = [];

    // task #70 phase 3: @mention highlighting. No dynamic import — unlike
    // the enhancements below, this has no external library behind it and
    // applies to nearly every comment body, so lazy-loading it as its own
    // chunk would cost more than it saves. Safe to call unconditionally;
    // it's a no-op over content with no data-mentioned-users at all
    // (Task Description, which doesn't support @mentions — see its own
    // docblock in mention-highlight.js).
    highlightMentions(target);

    if (target.querySelector('[data-rich-text-content] pre code:not([data-highlighted])')) {
        passes.push(import('./code-highlight.js').then(function (module) { module.highlightCodeBlocks(target); }));
    }

    if (target.querySelector('[data-rich-text-content] video:not([data-video-enhanced])')) {
        passes.push(import('./video-thumbnail.js').then(function (module) { module.enhanceEmbeddedVideos(target); }));
    }

    if (target.querySelector('[data-rich-text-content] file-chip:not([data-chip-enhanced])')) {
        passes.push(import('./file-chip-thumbnail.js').then(function (module) { module.enhanceFileChips(target); }));
    }

    if (target.querySelector('[data-rich-text-content] link-preview:not([data-preview-enhanced])')) {
        passes.push(import('./link-preview-thumbnail.js').then(function (module) { module.enhanceLinkPreviews(target); }));
    }

    return passes.length ? Promise.all(passes) : Promise.resolve();
}

// task #70 phase 4: on-demand access to the SAME emoji dataset/search/
// picker pipeline the rich-text toolbar's own :emoji: button uses (see
// rich-text-editor.js's buildEmojiPicker() docblock) — for the comment
// reaction button (tasks/_comments.blade.php), which has no live editor
// of its own to mount, so this doesn't go through mountRichText()'s
// viewport-lazy path at all, just the same dynamic import already used
// there. Returns a promise (the module load), not the picker itself.
function buildEmojiPicker(options) {
    return import('./rich-text-editor.js').then(function (module) { return module.buildEmojiPicker(options); });
}

window.solavaRichText = { mount: mountRichText, highlight: highlightRichText, buildEmojiPicker: buildEmojiPicker };

function initRichText() {
    document.querySelectorAll('[data-rich-text]').forEach(mountRichText);
    highlightRichText(document);
}

// Click-to-enlarge on an embedded image, or click-to-expand-and-play on a
// video thumbnail (task #4, video resize + lightbox) — scoped to
// READ-ONLY rendered rich text (task drilldown, comment list, the
// non-editable task view: anywhere carrying `data-rich-text-content`), not
// the live editing surface, where a click is normally placing the cursor
// or selecting the node, not asking to preview it. One delegated listener
// covers every current image/video and any a comment row adds later
// (buildCommentRow/syncComments in tasks/_comments.blade.php), so nothing
// needs re-wiring per row.
function initLightboxDelegation() {
    document.addEventListener('click', function (event) {
        const img = event.target.closest('[data-rich-text-content] img');
        if (img) {
            import('./lightbox.js').then(function (module) {
                module.openLightbox({ type: 'image', src: img.currentSrc || img.src, alt: img.alt });
            });
            return;
        }

        // .video-thumb is the wrapper video-thumbnail.js builds around the
        // actual <video> (pointer-events: none on the video itself, see
        // app.css, so the click always lands on this wrapper) — matches
        // resizable-video.js's own editor-side markup, so both read the
        // same way here even though only the read-only one opens anything.
        const videoThumb = event.target.closest('[data-rich-text-content] .video-thumb');
        if (videoThumb) {
            const video = videoThumb.querySelector('video');
            if (! video) return;

            import('./lightbox.js').then(function (module) {
                module.openLightbox({ type: 'video', src: video.currentSrc || video.src });
            });
        }
    });
}

// Click-to-open on a file-chip (task #4, document upload + embedding) or
// a link-preview card (task #4, Smart Links) — same READ-ONLY-only scoping
// as image/video's own delegation above and for the same reason (the live
// editor's copy just selects the atom node on click), but its own
// listener rather than folded into initLightboxDelegation: neither ever
// opens a lightbox — a document always opens in a new tab, exactly like
// the existing Documents page/tab already does, and a link preview is
// just a nicer-looking hyperlink, which already means "opens in a new
// tab" everywhere else in this app's rich text (RichText's sanitizer
// forces every plain <a> to target="_blank" too).
//
// A file-chip's href is routed through /file-downloads (DocumentController
// ::download) rather than opened directly — the raw storage URL a file-chip
// carries is a bare tasks/{id}/documents/{uuid}.ext key, so opening it
// directly would save under that key, not the file's real name; the
// download route resolves it back to its Document row and streams it
// with the real name in Content-Disposition instead (task #4 fix, same
// motivation as documents/index.blade.php's and tasks/_documents.blade.php's
// own links). A link-preview's href is always an external URL, never one
// of our own uploads, so it keeps opening exactly as given.
function initExternalReferenceDelegation() {
    document.addEventListener('click', function (event) {
        const fileChip = event.target.closest('[data-rich-text-content] file-chip');
        const linkPreview = event.target.closest('[data-rich-text-content] link-preview');
        const reference = fileChip || linkPreview;
        if (! reference) return;

        const href = reference.getAttribute('href');
        if (! href) return;

        const target = fileChip ? '/file-downloads?url=' + encodeURIComponent(href) : href;
        window.open(target, '_blank', 'noopener,noreferrer');
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        initPhoneInputs();
        initAnalyticsCharts();
        initRichText();
        initLightboxDelegation();
        initExternalReferenceDelegation();
    });
} else {
    initPhoneInputs();
    initAnalyticsCharts();
    initRichText();
    initLightboxDelegation();
    initExternalReferenceDelegation();
}
