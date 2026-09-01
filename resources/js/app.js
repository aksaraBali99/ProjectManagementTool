import intlTelInput from 'intl-tel-input/intlTelInputWithUtils';
import 'intl-tel-input/dist/css/intlTelInput.css';

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

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        initPhoneInputs();
        initAnalyticsCharts();
    });
} else {
    initPhoneInputs();
    initAnalyticsCharts();
}
