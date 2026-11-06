{{--
    task #124: Settings > Release notes. Shows resources/release-notes.json
    (App\Support\ReleaseNotes) as it is in the code this server runs. All
    text is plain: printed with {{ }} (escaped), never as HTML; bullets and
    line breaks come from ReleaseNotes::blocks() (release-notes._blocks).

    Follow-up: the side-by-side layout. One card per release; sections
    "New and improved" (Enhancement), "Fixed" (Bugfix) and, folded, "Behind
    the scenes" (Chore); each task's Before and Now side by side.
--}}
@extends('layouts.authenticated')

@section('title', 'Release notes — Solava')

@php
    // Section order, label and dot colour per task type.
    $sections = [
        'Enhancement' => ['label' => 'New and improved', 'dot' => 'bg-brand-600'],
        'Bugfix' => ['label' => 'Fixed', 'dot' => 'bg-amber-500'],
        'Chore' => ['label' => 'Behind the scenes', 'dot' => 'bg-gray-400'],
    ];
@endphp

@section('content')
<div class="min-w-0">
    <h1 class="text-[14px] font-medium text-[#1F2937]">Release notes</h1>
    <p class="mt-1 text-[11px] text-gray-500"
       title="Each server lists the notes of the code it runs, so DEV can show a release that isn't on PROD yet.">What changed in each release, newest first.</p>

    @if ($error)
        <div class="mt-4 rounded-md bg-red-50 p-3 text-[12px] text-red-700">The release notes file is invalid: {{ $error }}</div>
    @elseif ($releases === [])
        <div class="mt-4 rounded-xl border border-gray-200 bg-white p-4 text-[12px] text-gray-500 shadow-sm">No releases yet.</div>
    @else
        <div class="mt-4 space-y-6">
            @foreach ($releases as $release)
                <section class="min-w-0" data-release="{{ $release['version'] }}">
                    <header class="flex flex-wrap items-center gap-x-2 gap-y-1">
                        <h2 class="text-[13px] font-semibold text-[#1F2937]">{{ $release['version'] }}</h2>
                        @if ($loop->first)
                            <span class="rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-medium text-brand-700 ring-1 ring-brand-600/30">Latest</span>
                        @endif
                        <span class="text-[11px] text-gray-500">{{ \Illuminate\Support\Carbon::parse($release['date'])->format('M j, Y') }}</span>
                        <a href="{{ config('solava.github_repo_url') }}/releases/tag/{{ $release['version'] }}"
                           title="Exists once {{ $release['version'] }} is released to PROD"
                           class="ml-auto text-[11px] text-brand-700 hover:underline" target="_blank" rel="noopener noreferrer">GitHub release ↗</a>
                    </header>

                    <div class="mt-2 min-w-0 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="max-w-[90ch] space-y-2 break-words text-[12px] leading-[1.7] text-gray-700">
                            @include('release-notes._blocks', ['text' => $release['summary']])
                        </div>

                        @foreach ($sections as $type => $section)
                            @php $tasks = array_values(array_filter($release['tasks'], fn ($task) => $task['type'] === $type)); @endphp
                            @continue ($tasks === [])

                            @if ($type === 'Chore')
                                {{-- Folded by default: what changed for the team, not for people using the app. --}}
                                <details class="group mt-5 rounded-lg bg-gray-50 px-3 py-2.5" data-section="{{ $type }}">
                                    {{-- A flex summary hides the browser's own marker, so the arrow is drawn here. --}}
                                    <summary class="flex cursor-pointer select-none list-none items-center gap-2 text-[11px] font-semibold text-gray-700">
                                        <span class="text-[9px] text-gray-500 transition-transform group-open:rotate-90" aria-hidden="true">▶</span>
                                        <span class="h-1.5 w-1.5 rounded-full {{ $section['dot'] }}"></span>
                                        {{ $section['label'] }}
                                        <span class="font-normal text-gray-400">{{ count($tasks) }}</span>
                                    </summary>
                                    <ul class="mt-2 space-y-2">
                                        @foreach ($tasks as $task)
                                            <li>
                                                @if ($task['before'] !== null || $task['after'] !== null)
                                                    @include('release-notes._task', ['task' => $task])
                                                @else
                                                    <p class="flex flex-wrap items-baseline gap-x-2 gap-y-1 text-[12px] text-gray-700">
                                                        <span class="font-mono text-[10px] text-gray-500">#{{ $task['number'] }}</span>
                                                        <span>{{ $task['title'] }}</span>
                                                        @foreach ($task['prs'] as $pr)
                                                            <a href="{{ config('solava.github_repo_url') }}/pull/{{ $pr }}"
                                                               class="text-[11px] text-brand-700 hover:underline" target="_blank" rel="noopener noreferrer">#{{ $pr }}</a>
                                                        @endforeach
                                                    </p>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </details>
                            @else
                                <div class="mt-5" data-section="{{ $type }}">
                                    <h3 class="flex items-center gap-2 text-[11px] font-semibold text-[#1F2937]">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $section['dot'] }}"></span>
                                        {{ $section['label'] }}
                                        <span class="font-normal text-gray-400">{{ count($tasks) }}</span>
                                    </h3>
                                    <div class="mt-2 space-y-3">
                                        @foreach ($tasks as $task)
                                            @include('release-notes._task', ['task' => $task])
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</div>
@endsection
