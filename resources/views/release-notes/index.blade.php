{{--
    task #124: Settings > Release notes. Shows resources/release-notes.json
    (App\Support\ReleaseNotes) as it is in the code this server runs. All
    text is plain: printed with {{ }} (escaped), line breaks kept by
    whitespace-pre-line, never as HTML.
--}}
@extends('layouts.authenticated')

@section('title', 'Release notes — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <h1 class="text-[14px] font-medium text-[#1F2937]">Release notes</h1>
    <p class="mt-1 text-[11px] text-gray-500">
        What changed in each release, newest first. Each server lists the notes of the code it runs, so DEV can show a release that isn't on PROD yet.
    </p>

    @if ($error)
        <div class="mt-4 rounded-md bg-red-50 p-3 text-[12px] text-red-700">The release notes file is invalid: {{ $error }}</div>
    @elseif ($releases === [])
        <p class="mt-6 text-[12px] text-gray-500">No releases yet.</p>
    @else
        <div class="mt-6 space-y-4">
            @foreach ($releases as $release)
                <section class="min-w-0 rounded-md border border-gray-200 bg-white px-4 py-3" data-release="{{ $release['version'] }}">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-[13px] font-medium text-[#1F2937]">{{ $release['version'] }}</h2>
                        @if ($loop->first)
                            <span class="rounded-sm bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">Latest</span>
                        @endif
                        <span class="text-[11px] text-gray-500">{{ \Illuminate\Support\Carbon::parse($release['date'])->format('M j, Y') }}</span>
                        <a href="https://github.com/aksaraBali99/ProjectManagementTool/releases/tag/{{ $release['version'] }}"
                           class="ml-auto text-[11px] text-brand-600 hover:underline" target="_blank" rel="noopener noreferrer">GitHub release</a>
                    </div>
                    <p class="mt-1 whitespace-pre-line break-words text-[12px] text-gray-700">{{ $release['summary'] }}</p>

                    @foreach ($types as $type)
                        @php $tasksOfType = array_values(array_filter($release['tasks'], fn ($task) => $task['type'] === $type)); @endphp
                        @if ($tasksOfType !== [])
                            <h3 class="mt-4 text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">{{ $type }}</h3>
                            <ul class="mt-1 space-y-3">
                                @foreach ($tasksOfType as $task)
                                    <li class="min-w-0 border-t border-gray-100 pt-2">
                                        <p class="break-words text-[12px] text-[#1F2937]">
                                            <span class="text-gray-500">task #{{ $task['number'] }}</span>
                                            <span class="font-medium">{{ $task['title'] }}</span>
                                        </p>
                                        @if ($task['before'] !== null)
                                            <p class="mt-1 text-[10px] uppercase tracking-[0.05em] text-gray-500">Before</p>
                                            <p class="whitespace-pre-line break-words text-[12px] text-gray-700">{{ $task['before'] }}</p>
                                        @endif
                                        @if ($task['after'] !== null)
                                            <p class="mt-1 text-[10px] uppercase tracking-[0.05em] text-gray-500">After</p>
                                            <p class="whitespace-pre-line break-words text-[12px] text-gray-700">{{ $task['after'] }}</p>
                                        @endif
                                        @if ($task['prs'] !== [])
                                            <p class="mt-1 flex flex-wrap gap-2 text-[11px]">
                                                @foreach ($task['prs'] as $pr)
                                                    <a href="https://github.com/aksaraBali99/ProjectManagementTool/pull/{{ $pr }}"
                                                       class="text-brand-600 hover:underline" target="_blank" rel="noopener noreferrer">PR #{{ $pr }}</a>
                                                @endforeach
                                            </p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @endforeach
                </section>
            @endforeach
        </div>
    @endif
</div>
@endsection
