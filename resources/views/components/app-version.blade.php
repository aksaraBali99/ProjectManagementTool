{{--
    SOL-0028: the version next to "Solava" in the sidebar, from
    config('solava.build') (App\Support\AppVersion, frozen per deploy by
    config:cache).

    - production: the release number (v2026.11.2), or the commit if there is
      none; nothing at all when neither is known.
    - anything else (dev, local, testing): "<env> · <commit>" in a small
      amber pill, so DEV is never mistaken for PROD.

    The tooltip has the full detail. For the owner and super_admin (who can
    open Settings > Release notes) it links there; for everyone else it is
    plain text.
--}}
@php
    $build = config('solava.build');
    $version = is_array($build) && is_string($build['version'] ?? null) ? $build['version'] : null;
    $commit = is_array($build) && is_string($build['commit'] ?? null) ? $build['commit'] : null;
    $environment = (string) app()->environment();
    $isProduction = app()->environment('production');

    if ($isProduction) {
        $label = $version ?? $commit;
        $title = implode(' · ', array_filter([
            $version !== null ? 'Release '.$version : null,
            $commit !== null ? 'commit '.$commit : null,
        ]));
    } else {
        $label = $commit !== null ? $environment.' · '.$commit : $environment;
        $title = implode(' · ', array_filter([
            $version !== null ? 'Newest release notes entry: '.$version : null,
            $commit !== null ? 'commit '.$commit : null,
            'APP_ENV '.$environment,
        ]));
    }

    $classes = 'min-w-0 truncate text-[11px] font-normal tabular-nums '
        .($isProduction ? 'text-gray-400' : 'rounded bg-amber-50 px-1.5 text-amber-700');
@endphp
@if ($label !== null && $label !== '')
    @if (auth()->user()?->can('release-notes.view'))
        <a href="{{ route('release-notes.index') }}" class="{{ $classes }} hover:text-gray-700" title="{{ $title }}" data-app-version>{{ $label }}</a>
    @else
        <span class="{{ $classes }}" title="{{ $title }}" data-app-version>{{ $label }}</span>
    @endif
@endif
