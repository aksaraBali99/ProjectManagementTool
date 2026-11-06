{{--
    task #124 follow-up: one task of a release: its number tag and title,
    Before and Now side by side (stacked on a phone), and its PR links
    folded away. Expects $task (App\Support\ReleaseNotes).
--}}
<article class="min-w-0 rounded-lg border border-gray-100 p-3" data-task="{{ $task['number'] }}">
    <h4 class="flex flex-wrap items-baseline gap-x-2 gap-y-1">
        <span class="rounded bg-gray-100 px-1.5 py-0.5 font-mono text-[10px] text-gray-500">#{{ $task['number'] }}</span>
        <span class="text-[12px] font-semibold text-[#1F2937]">{{ $task['title'] }}</span>
    </h4>

    @if ($task['before'] !== null || $task['after'] !== null)
        <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
            @if ($task['before'] !== null)
                <div class="min-w-0 rounded-lg bg-gray-50 px-3 py-2">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-400">Before</p>
                    <div class="mt-1 max-w-[100ch] space-y-2 break-words text-[12px] leading-[1.6] text-gray-600">
                        @include('release-notes._blocks', ['text' => $task['before']])
                    </div>
                </div>
            @endif
            @if ($task['after'] !== null)
                <div class="min-w-0 rounded-lg bg-brand-50/50 px-3 py-2 ring-1 ring-brand-600/20">
                    {{-- "Now" on the page; the file's field is "after". --}}
                    <p class="text-[10px] font-semibold uppercase tracking-[0.05em] text-brand-700">Now</p>
                    <div class="mt-1 max-w-[100ch] space-y-2 break-words text-[12px] leading-[1.6] text-gray-800">
                        @include('release-notes._blocks', ['text' => $task['after']])
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if ($task['prs'] !== [])
        <details class="mt-2 text-[11px] text-gray-500">
            <summary class="cursor-pointer select-none">{{ count($task['prs']) }} {{ count($task['prs']) === 1 ? 'pull request' : 'pull requests' }}</summary>
            <p class="mt-1 flex flex-wrap gap-x-2 gap-y-1">
                @foreach ($task['prs'] as $pr)
                    <a href="{{ config('solava.github_repo_url') }}/pull/{{ $pr }}"
                       class="text-brand-700 hover:underline" target="_blank" rel="noopener noreferrer">#{{ $pr }}</a>
                @endforeach
            </p>
        </details>
    @endif
</article>
