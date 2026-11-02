<?php

namespace App\Http\Controllers\Concerns;

/**
 * The return_to / return_label pair every link into the task page carries.
 * Moved here unchanged from TaskManagementController (task #72 phase 9) so
 * the Projects drilldown endpoint validates the same way, not with a copy.
 */
trait ResolvesReturnTo
{
    /**
     * Where the Add/Edit Task page's "← Back" and "Cancel" links should
     * go — the page that actually linked here (Tasks list, Kanban,
     * Calendar, Dashboard, or a project's own task list), never a
     * hardcoded default like Projects, which has no Add Task button and
     * so can never legitimately be where a Create flow started.
     *
     * Every entry-point link passes return_to (its own url()->full(), an
     * ABSOLUTE url() carrying whatever filters/scroll-state it had) and
     * return_label (a name from the fixed list below) as plain route()
     * query params - see tasks/index.blade.php, kanban.blade.php,
     * calendar.blade.php, dashboard.blade.php, projects/_tasks.blade.php
     * (the Projects page passes its URL to the drilldown endpoint, which
     * runs it through here before building the task links).
     * return_to is validated same-origin (isSafeReturnUrl()) before use,
     * since it arrives via the query string and this is otherwise a
     * textbook open-redirect: a crafted link could point return_to at an
     * external site and ride a click of "Cancel"/"← Back" there.
     * return_label is checked against a fixed allow-list for the same
     * reason - a stray or tampered value falls back to $defaultLabel
     * instead of rendering whatever was passed.
     *
     * @return array{url: string, label: string}
     */
    private function resolveReturnTo(string $defaultUrl, string $defaultLabel = 'Tasks'): array
    {
        $allowedLabels = ['Tasks', 'Kanban', 'Calendar', 'Dashboard', 'Projects'];

        $returnTo = request()->query('return_to');
        $returnLabel = request()->query('return_label');

        return [
            'url' => is_string($returnTo) && $this->isSafeReturnUrl($returnTo) ? $returnTo : $defaultUrl,
            'label' => in_array($returnLabel, $allowedLabels, true) ? $returnLabel : $defaultLabel,
        ];
    }

    /**
     * Accepts either a plain relative path ("/kanban/1") or an absolute
     * url() pointing back at THIS app's own host (what url()->full()
     * actually produces) - rejects anything pointing at a different host,
     * plus the protocol-relative ("//host/...") and backslash-variant
     * ("/\host/...", which some browsers also treat as protocol-relative)
     * open-redirect tricks that a naive "starts with /" check alone
     * wouldn't catch.
     */
    private function isSafeReturnUrl(string $url): bool
    {
        if (str_starts_with($url, '//') || str_starts_with($url, '/\\')) {
            return false;
        }

        $parsed = parse_url($url);
        if ($parsed === false) {
            return false;
        }

        if (! isset($parsed['host'])) {
            return str_starts_with($url, '/');
        }

        return $parsed['host'] === request()->getHost();
    }
}
