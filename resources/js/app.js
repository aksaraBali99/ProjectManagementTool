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

function renderGanttView(container, tasks, view, periodOffset) {
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

    container.innerHTML = '';
    new Gantt(container, tasks.concat([boundsTask]), {
        view_mode: 'Day',
        view_modes: [DAY_VIEW_MODE_WITH_WEEKDAY],
        view_mode_select: false,
        column_width: columnWidth,
        lower_header_height: 44,
        infinite_padding: false,
        on_click: function (task) {
            if (task.editUrl) window.location = task.editUrl;
        },
    });

    // Gantt's own scroll_to option leaves a ~1/6-column sliver of the
    // previous day visible (it centers the target slightly right of the
    // viewport edge) — measuring viewStart's own grid-column element
    // directly and scrolling to exactly where it renders avoids that. Safe
    // to do in one pass now that infinite_padding can't shift the range
    // out from under this measurement mid-correction.
    const scrollEl = container.querySelector('.gantt-container') || container;
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
    if (! container || container.dataset.ganttInitialized) return;
    container.dataset.ganttInitialized = 'true';

    const tasks = JSON.parse(container.dataset.tasks || '[]');
    if (tasks.length === 0) return;

    const viewSelect = document.getElementById('calendar-view-select');
    const prevBtn = document.getElementById('calendar-prev');
    const nextBtn = document.getElementById('calendar-next');
    let periodOffset = 0;

    function render() {
        renderGanttView(container, tasks, viewSelect ? viewSelect.value : 'Week', periodOffset);
    }

    render();

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
