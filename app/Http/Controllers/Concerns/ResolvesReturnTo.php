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
     * task #121: the Add/Edit Task forms and the Deactivate/Activate
     * forms carry the same pair as hidden inputs, so it's read from the
     * query string or the form body (request()->input()), and survives a
     * save (returnToParams()).
     * return_to is validated same-origin (isSafeReturnUrl()) before use,
     * since it arrives from the request and this is otherwise a textbook
     * open-redirect: a crafted link could point return_to at an external
     * site and ride a click of "Cancel"/"← Back" there.
     * return_label is checked against a fixed allow-list for the same
     * reason - a stray or tampered value falls back to $defaultLabel
     * instead of rendering whatever was passed. A label without a kept
     * return_to is dropped too, so "← Kanban" never links to the Tasks
     * list.
     *
     * 'params' is the kept pair (returnToParams()), for the page's hidden
     * inputs (tasks/_return-to-inputs).
     *
     * @return array{url: string, label: string, params: array{return_to?: string, return_label?: string}}
     */
    private function resolveReturnTo(string $defaultUrl, string $defaultLabel = 'Tasks'): array
    {
        $params = $this->returnToParams();

        return [
            'url' => $params['return_to'] ?? $defaultUrl,
            'label' => $params['return_label'] ?? $defaultLabel,
            'params' => $params,
        ];
    }

    /**
     * task #121: the return_to / return_label pair this request carried,
     * if it passes the checks, as route parameters, so a redirect (after
     * a save or a Deactivate) keeps the page's back link. Empty when
     * nothing valid was sent, so the URL stays clean: the Tasks fallback
     * is never added as an explicit parameter.
     *
     * When the request carries nothing, a page shown after a validation
     * error falls back to the values submitted with the failed form
     * (old()), through the same checks: the back link and the hidden
     * inputs always agree, and nothing unchecked reaches either.
     *
     * @return array{return_to?: string, return_label?: string}
     */
    private function returnToParams(): array
    {
        $params = $this->checkedReturnTo(request()->input('return_to'), request()->input('return_label'));

        if ($params === [] && request()->isMethod('GET')) {
            $params = $this->checkedReturnTo(old('return_to'), old('return_label'));
        }

        return $params;
    }

    /**
     * return_to if it's a safe same-origin URL, and return_label only with
     * it and only from the allow-list.
     *
     * @return array{return_to?: string, return_label?: string}
     */
    private function checkedReturnTo(mixed $returnTo, mixed $returnLabel): array
    {
        if (! is_string($returnTo) || ! $this->isSafeReturnUrl($returnTo)) {
            return [];
        }

        $allowedLabels = ['Tasks', 'Kanban', 'Calendar', 'Dashboard', 'Projects'];

        return in_array($returnLabel, $allowedLabels, true)
            ? ['return_to' => $returnTo, 'return_label' => $returnLabel]
            : ['return_to' => $returnTo];
    }

    /**
     * Accepts either a plain relative path ("/kanban/1") or an absolute
     * url() pointing back at THIS app's own host (what url()->full()
     * actually produces) - rejects anything pointing at a different host,
     * plus the protocol-relative ("//host/...") and backslash-variant
     * ("/\host/...", which some browsers also treat as protocol-relative)
     * open-redirect tricks that a naive "starts with /" check alone
     * wouldn't catch.
     *
     * task #121: an absolute URL must also use this request's own scheme.
     * The host alone isn't enough: "javascript://<this host>/%0Aalert(1)"
     * parses with this host, and as a link's href it runs script. Requiring
     * the request's scheme also stops an https page redirecting to http.
     * A relative path may not carry a scheme at all.
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
            return str_starts_with($url, '/') && ! isset($parsed['scheme']);
        }

        return strtolower($parsed['scheme'] ?? '') === request()->getScheme()
            && $parsed['host'] === request()->getHost();
    }
}
