import intlTelInput from 'intl-tel-input/intlTelInputWithUtils';
import 'intl-tel-input/dist/css/intlTelInput.css';
import Gantt from 'frappe-gantt';

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

// Frappe Gantt's own "Week"/"Month" view modes are timeline-granularity
// settings (each column = one week, or each column = one whole month), not
// paginated calendar views — "Month" mode doesn't show individual days at
// all. Both toggle states here render at day-level granularity instead, only
// varying column_width, so "Week" reads as ~7 comfortably-wide day columns
// and "Month" reads as ~31 narrower day columns filling the same container
// width — anything beyond that scrolls horizontally rather than being
// clipped or squeezed.
const CALENDAR_VIEW_DAYS = { Week: 7, Month: 31 };
const CALENDAR_MIN_COLUMN_WIDTH = 30;

// Week starts on the Monday of the current week; Month starts on the 1st of
// the current month — Frappe's own gantt_start is just "earliest task date
// minus padding", with no concept of calendar-boundary alignment, so the
// view is explicitly scrolled to a computed boundary date instead of
// defaulting to wherever the task data happens to begin. periodOffset shifts
// that boundary by whole weeks/months for the prev/next buttons — e.g.
// offset -1 in Week view means "the Monday of last week", not "7 days
// earlier than today" (which would drift off calendar-week boundaries after
// a few clicks).
function getCalendarViewStart(view, periodOffset) {
    const now = new Date();

    if (view === 'Month') {
        return new Date(now.getFullYear(), now.getMonth() + periodOffset, 1);
    }

    const isoWeekday = now.getDay() === 0 ? 7 : now.getDay(); // Mon=1 .. Sun=7
    const monday = new Date(now.getFullYear(), now.getMonth(), now.getDate() - (isoWeekday - 1) + periodOffset * 7);
    monday.setHours(0, 0, 0, 0);
    return monday;
}

const DAY_ABBREVIATIONS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

// A custom Day view mode (cloned from Frappe's built-in one) so each date
// column's label is "17\nMon" instead of just "17" — innerText (which is
// how Frappe sets this) turns the \n into a real line break, no extra
// markup needed, just the taller lower_header_height below to fit it.
const DAY_VIEW_MODE_WITH_WEEKDAY = {
    name: 'Day',
    padding: '7d',
    date_format: 'YYYY-MM-DD',
    step: '1d',
    lower_text: function (date) {
        return date.getDate() + '\n' + DAY_ABBREVIATIONS[date.getDay()];
    },
    upper_text: function (date, prevDate) {
        return (! prevDate || date.getMonth() !== prevDate.getMonth()) ? date.toLocaleDateString('en-US', { month: 'long' }) : '';
    },
    thick_line: function (date) {
        return date.getDay() === 1;
    },
};

function formatLocalDate(date) {
    return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
}

function formatPopupDate(isoDate) {
    return new Date(isoDate + 'T00:00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

// Explicitly pinned (rather than left to Frappe's defaults) so the task-list
// column built alongside the chart (see renderTaskListColumn) can compute
// matching row/header heights in plain CSS without reading them back out of
// the library at runtime.
const GANTT_BAR_HEIGHT = 30;
const GANTT_ROW_PADDING = 18;
const GANTT_UPPER_HEADER_HEIGHT = 45;
const GANTT_LOWER_HEADER_HEIGHT = 44;
const GANTT_HEADER_HEIGHT = GANTT_UPPER_HEADER_HEIGHT + GANTT_LOWER_HEADER_HEIGHT + 10; // +10 matches Frappe's own header_height formula
const GANTT_ROW_HEIGHT = GANTT_BAR_HEIGHT + GANTT_ROW_PADDING;

const GANTT_TASK_LIST_DEFAULT_WIDTH = 220;
const GANTT_TASK_LIST_MIN_WIDTH = 150;
const GANTT_TASK_LIST_MAX_WIDTH = 420;

// Frappe Gantt has no native parent/child row hierarchy or task-list column
// (it's a flat list of bars filling the whole widget) — both are built here
// instead. buildDisplayRows expands expandedTaskIds (keyed by the real
// numeric task id, persisted across view/prev/next re-renders) into a flat
// row list; renderTaskListColumn renders that same list as plain HTML rows
// to the left of the chart, with the (+)/(−) toggle button living there
// instead of on the bar. The Gantt itself only ever sees blank-named bars
// (see renderGanttView) — it draws date range + progress, nothing else.
function buildDisplayRows(tasks, subtasksByTask, expandedTaskIds) {
    const rows = [];

    tasks.forEach(function (task) {
        const subtaskCount = task.subtaskCount || 0;
        const isExpanded = expandedTaskIds.has(String(task.id));

        rows.push(Object.assign({}, task, {
            isSubtask: false,
            hasToggle: subtaskCount > 0,
            isExpanded: isExpanded,
        }));

        if (isExpanded) {
            (subtasksByTask[task.id] || []).forEach(function (subtask) {
                rows.push(Object.assign({}, subtask, { isSubtask: true, hasToggle: false }));
            });
        }
    });

    return rows;
}

// One row per display row, height-matched to GANTT_ROW_HEIGHT so it lines up
// with the bar drawn in the same position in the chart; a trailing blank
// spacer row accounts for the chart's own invisible bounds-phantom row (see
// renderGanttView) so the two columns' total heights still match exactly.
//
// Rows are built inside an inner wrapper sized `width: max-content; min-width:
// 100%` rather than directly in the (overflow-x: auto) column element: with
// nowrap labels, that lets the wrapper grow past the column's own width when
// a name is too long to fit, which is what gives `el` something to scroll —
// a plain 100%-width wrapper would just clip the overflowing text instead.
function renderTaskListColumn(el, rows, onToggle) {
    el.innerHTML = '';

    const inner = document.createElement('div');
    inner.style.width = 'max-content';
    inner.style.minWidth = '100%';

    const header = document.createElement('div');
    header.className = 'flex items-end border-b border-gray-200 px-3 pb-2 text-[11px] font-medium text-gray-500';
    header.style.height = GANTT_HEADER_HEIGHT + 'px';
    header.textContent = 'Task';
    inner.appendChild(header);

    rows.forEach(function (row) {
        const rowEl = document.createElement('div');
        rowEl.className = 'flex items-center gap-1 border-b border-gray-100 px-2 text-[11px] '
            + (row.isSubtask ? 'pl-8 text-gray-500' : 'text-gray-700');
        rowEl.style.height = GANTT_ROW_HEIGHT + 'px';

        if (row.hasToggle) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'flex h-[18px] w-[18px] shrink-0 items-center justify-center rounded border border-gray-300 text-[11px] leading-none text-gray-600 hover:bg-gray-50';
            btn.textContent = row.isExpanded ? '−' : '+';
            btn.setAttribute('aria-label', row.isExpanded ? 'Hide subtasks' : 'Show subtasks');
            btn.addEventListener('click', function () {
                onToggle(String(row.id));
            });
            rowEl.appendChild(btn);
        } else if (! row.isSubtask) {
            const spacer = document.createElement('span');
            spacer.className = 'inline-block h-[18px] w-[18px] shrink-0';
            rowEl.appendChild(spacer);
        }

        const label = document.createElement('span');
        label.className = 'whitespace-nowrap';
        label.title = row.name;
        label.textContent = row.name;
        rowEl.appendChild(label);

        inner.appendChild(rowEl);
    });

    const boundsSpacer = document.createElement('div');
    boundsSpacer.style.height = GANTT_ROW_HEIGHT + 'px';
    inner.appendChild(boundsSpacer);

    el.appendChild(inner);
}

// Drag-to-resize for the task-list column, clamped to [MIN, MAX]. The
// column's own width is plain CSS (updates live, no re-render needed), but
// the Gantt's day-column pixel width is computed once per render from
// #gantt-container's clientWidth (see renderGanttChart) — onResize re-runs
// that computation so the chart fills whatever space the column leaves
// behind. rAF-throttled during the drag itself so a fast mousemove burst
// doesn't queue up more full chart rebuilds than the browser can paint.
function setupTaskListResizer(handle, taskListEl, onResize) {
    let startX = 0;
    let startWidth = 0;
    let rafId = null;

    function scheduleResize() {
        if (rafId) return;
        rafId = requestAnimationFrame(function () {
            rafId = null;
            onResize();
        });
    }

    function resizeTo(clientX) {
        const newWidth = Math.min(GANTT_TASK_LIST_MAX_WIDTH, Math.max(GANTT_TASK_LIST_MIN_WIDTH, startWidth + (clientX - startX)));
        taskListEl.style.width = newWidth + 'px';
        scheduleResize();
    }

    function onMouseMove(e) {
        resizeTo(e.clientX);
    }

    // Touch has no hover/cursor concept and delivers coordinates via
    // e.touches rather than e.clientX directly; preventDefault here is what
    // stops the page/task-list from scrolling underneath the drag instead of
    // resizing (the listener is registered non-passive for exactly this).
    function onTouchMove(e) {
        if (e.touches.length !== 1) return;
        e.preventDefault();
        resizeTo(e.touches[0].clientX);
    }

    function endDrag() {
        // A throttled resize from the last move can still be queued when
        // the drag ends (mouseup/touchend isn't itself rAF-gated) — left
        // pending, it would fire after this function's own render call and
        // read a scrollLeft/columnWidth pairing from mid-render, landing the
        // final scroll position on the wrong date. Canceling it guarantees
        // this is the last render for the drag.
        if (rafId) {
            cancelAnimationFrame(rafId);
            rafId = null;
        }
        document.body.style.removeProperty('cursor');
        document.body.style.removeProperty('user-select');
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', endDrag);
        document.removeEventListener('touchmove', onTouchMove);
        document.removeEventListener('touchend', endDrag);
        document.removeEventListener('touchcancel', endDrag);
        onResize();
    }

    handle.addEventListener('mousedown', function (e) {
        startX = e.clientX;
        startWidth = taskListEl.getBoundingClientRect().width;
        document.body.style.cursor = 'col-resize';
        document.body.style.userSelect = 'none';
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', endDrag);
        e.preventDefault();
    });

    handle.addEventListener('touchstart', function (e) {
        if (e.touches.length !== 1) return;
        startX = e.touches[0].clientX;
        startWidth = taskListEl.getBoundingClientRect().width;
        document.addEventListener('touchmove', onTouchMove, { passive: false });
        document.addEventListener('touchend', endDrag);
        document.addEventListener('touchcancel', endDrag);
    }, { passive: true });
}

function renderGanttView(container, taskListEl, tasks, subtasksByTask, expandedTaskIds, view, periodOffset, preserveScroll) {
    const days = CALENDAR_VIEW_DAYS[view] || CALENDAR_VIEW_DAYS.Week;
    const columnWidth = Math.max(CALENDAR_MIN_COLUMN_WIDTH, Math.floor(container.clientWidth / days));
    const viewStart = getCalendarViewStart(view, periodOffset);

    // Frappe's infinite_padding (on by default) auto-extends gantt_start and
    // re-renders the instant scrollLeft lands near the edge of the
    // originally-rendered range — which jumping straight to next/prev
    // week/month often does. That auto-extend can cascade into a runaway
    // loop of re-renders (reproduced: the header duplicating itself into
    // thousands of stale date labels) when the jump is large. Disabling it
    // and instead adding one invisible zero-height "bounds" task spanning
    // 14 days before/after the requested window guarantees gantt_start/end
    // cover it up front — a fixed range computed once, nothing dynamic left
    // to loop.
    const windowStart = new Date(viewStart);
    windowStart.setDate(windowStart.getDate() - 14);
    const windowEnd = new Date(viewStart);
    windowEnd.setDate(windowEnd.getDate() + days + 14);
    const boundsTask = {
        id: '__calendar_bounds__',
        name: '',
        start: formatLocalDate(windowStart),
        end: formatLocalDate(windowEnd),
        custom_class: 'gantt-bounds-phantom',
    };

    // Captured as a day offset (scrollLeft / column_width), not a raw pixel
    // value: view/periodOffset (so gantt_start) stay the same across a
    // preserveScroll re-render, but column_width doesn't — the task-list
    // resizer changes container.clientWidth, which changes column_width
    // above. Reapplying a stale pixel scrollLeft against a new column_width
    // would visibly shift which dates are on screen; multiplying the day
    // offset back out by the new column_width lands on the same date either
    // way (a no-op when column_width hasn't changed, e.g. on subtask
    // expand/collapse).
    const scrollElBefore = container.querySelector('.gantt-container');
    const oldColumnWidth = scrollElBefore ? parseFloat(getComputedStyle(scrollElBefore).getPropertyValue('--gv-column-width')) : 0;
    const dayOffsetBefore = scrollElBefore && oldColumnWidth ? scrollElBefore.scrollLeft / oldColumnWidth : 0;

    const rows = buildDisplayRows(tasks, subtasksByTask, expandedTaskIds);

    function toggle(taskId) {
        if (expandedTaskIds.has(taskId)) expandedTaskIds.delete(taskId);
        else expandedTaskIds.add(taskId);
        renderGanttView(container, taskListEl, tasks, subtasksByTask, expandedTaskIds, view, periodOffset, true);
    }

    renderTaskListColumn(taskListEl, rows, toggle);

    // The chart itself never shows names on the bar — only date range +
    // progress — now that the task-list column owns the label; `label`
    // carries the real name through for the hover popup below instead.
    const ganttBars = rows.map(function (row) {
        return Object.assign({}, row, { name: '', label: row.name });
    });

    container.innerHTML = '';
    new Gantt(container, ganttBars.concat([boundsTask]), {
        view_mode: 'Day',
        view_modes: [DAY_VIEW_MODE_WITH_WEEKDAY],
        view_mode_select: false,
        column_width: columnWidth,
        bar_height: GANTT_BAR_HEIGHT,
        padding: GANTT_ROW_PADDING,
        upper_header_height: GANTT_UPPER_HEADER_HEIGHT,
        lower_header_height: GANTT_LOWER_HEADER_HEIGHT,
        infinite_padding: false,
        // Frappe's default popup_on is 'click' — pointless here since a
        // click already navigates (see on_click below), so the popup would
        // only ever flash for an instant before the page unloads. 'hover'
        // is what actually makes it useful. The default popup formatter
        // reads task.name for its title, which is always '' on these bars
        // (see ganttBars above), so it's overridden here to use task.label
        // instead; the date-range/progress body is otherwise the same
        // information the default formatter shows, just built from the
        // plain ISO strings/number already on hand rather than reaching
        // into Frappe's own internal Date objects.
        popup_on: 'hover',
        popup: function (opts) {
            opts.set_title(opts.task.label || '');
            opts.set_subtitle('');
            opts.set_details(formatPopupDate(opts.task.start) + ' - ' + formatPopupDate(opts.task.end) + '<br/>Progress: ' + opts.task.progress + '%');
        },
        on_click: function (task) {
            if (task.editUrl) window.location = task.editUrl;
        },
    });

    const scrollEl = container.querySelector('.gantt-container') || container;

    if (preserveScroll) {
        // Applied synchronously, not deferred to requestAnimationFrame like
        // the initial-load path below — Gantt's constructor already builds
        // the SVG (and this scrollable element) synchronously, so the frame
        // isn't needed here, and a resize drag can fire this render many
        // times in quick succession (once per mousemove). Deferring it let a
        // later render's rebuild — a fresh .gantt-container with scrollLeft
        // reset to 0 — start, and read that 0 as "before", before an earlier
        // render's own deferred restore ever got a turn to run.
        scrollEl.scrollLeft = dayOffsetBefore * columnWidth;
        return;
    }

    // Gantt's own scroll_to option leaves a ~1/6-column sliver of the
    // previous day visible (it centers the target slightly right of the
    // viewport edge) — measuring viewStart's own grid-column element
    // directly and scrolling to exactly where it renders avoids that. Safe
    // to do in one pass now that infinite_padding can't shift the range
    // out from under this measurement mid-correction.
    const viewStartClass = 'date_' + formatLocalDate(viewStart);

    requestAnimationFrame(function () {
        const marker = container.querySelector('.' + viewStartClass);
        if (! marker) return;
        const targetLeft = marker.getBoundingClientRect().left - scrollEl.getBoundingClientRect().left + scrollEl.scrollLeft;
        scrollEl.scrollLeft = Math.max(0, targetLeft);
    });
}

function initGanttChart() {
    const container = document.getElementById('gantt-container');
    const taskListEl = document.getElementById('gantt-task-list');
    if (! container || ! taskListEl || container.dataset.ganttInitialized) return;
    container.dataset.ganttInitialized = 'true';

    const tasks = JSON.parse(container.dataset.tasks || '[]');
    if (tasks.length === 0) return;

    const subtasksByTask = JSON.parse(container.dataset.subtasks || '{}');
    const expandedTaskIds = new Set();

    const viewSelect = document.getElementById('calendar-view-select');
    const prevBtn = document.getElementById('calendar-prev');
    const nextBtn = document.getElementById('calendar-next');
    const resizeHandle = document.getElementById('gantt-task-list-resizer');
    let periodOffset = 0;

    function render() {
        renderGanttView(container, taskListEl, tasks, subtasksByTask, expandedTaskIds, viewSelect ? viewSelect.value : 'Week', periodOffset, false);
    }

    taskListEl.style.width = GANTT_TASK_LIST_DEFAULT_WIDTH + 'px';
    render();

    if (resizeHandle) {
        setupTaskListResizer(resizeHandle, taskListEl, function () {
            renderGanttView(container, taskListEl, tasks, subtasksByTask, expandedTaskIds, viewSelect ? viewSelect.value : 'Week', periodOffset, true);
        });
    }

    if (viewSelect) {
        viewSelect.addEventListener('change', function () {
            periodOffset = 0;
            render();
        });
    }
    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            periodOffset -= 1;
            render();
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            periodOffset += 1;
            render();
        });
    }
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

        new Chart(canvas, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: labels.map(function (_, i) { return CHART_PALETTE[i % CHART_PALETTE.length]; }),
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

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        initPhoneInputs();
        initGanttChart();
        initAnalyticsCharts();
    });
} else {
    initPhoneInputs();
    initGanttChart();
    initAnalyticsCharts();
}
