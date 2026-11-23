{{--
    SOL-0028: the version under "Solava" in the sidebar, from
    config('solava.version') (App\Support\AppVersion, frozen per deploy by
    config:cache).

    - production: the release number (v2026.11.2); nothing at all without
      one.
    - anything else: the environment in capitals, in brackets, and the
      version ([DEV] v2026.11.2, [LOCAL] v2026.11.2), or just [DEV] without
      a version, in a small amber pill so DEV is never mistaken for PROD.

    For the owner and super_admin (who can open Settings > Release notes) it
    links there; for everyone else it is plain text. Never cut off: it sits
    on its own line under the name.
--}}
@php
    $version = config('solava.version');
    $version = is_string($version) && $version !== '' ? $version : null;
    $isProduction = app()->environment('production');

    if ($isProduction) {
        $label = $version;
    } else {
        $environment = '['.strtoupper((string) app()->environment()).']';
        $label = $version !== null ? $environment.' '.$version : $environment;
    }

    $classes = 'self-start whitespace-nowrap text-[10px] font-normal tabular-nums '
        .($isProduction ? 'text-gray-400' : 'rounded bg-amber-50 px-1 text-amber-700');
    // The link's hover keeps the pill amber, so "not PROD" never fades.
    $hover = $isProduction ? 'hover:text-gray-700' : 'hover:text-amber-900';
@endphp
@if ($label !== null && $label !== '')
    @if (auth()->user()?->can('release-notes.view'))
        <a href="{{ route('release-notes.index') }}" class="{{ $classes }} {{ $hover }}" data-app-version>{{ $label }}</a>
    @else
        <span class="{{ $classes }}" data-app-version>{{ $label }}</span>
    @endif
@endif
