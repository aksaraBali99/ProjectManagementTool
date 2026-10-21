{{-- task #72 phase 2: the Tasks list row preview — READ ONLY, and
     deliberately so.

     This replaces the two interactive partials (_subtasks, _comments) the
     list used to render inline for every task whether it was opened or
     not. Commenting, replying, reacting and subtask editing all live on
     the task page now, which keeps those partials unchanged; nothing here
     has an input, a select, a textarea or a save button, and there is no
     polling.

     Rendered with no layout and inserted into the drilldown row by
     index.blade.php's fetch, which then calls
     window.solavaRichText.highlight() over the container so mentions,
     code blocks, video thumbnails, file chips and link previews get the
     same treatment they would on a full page load. Click behaviour
     (image/video lightbox, opening a chip or a link preview) needs no
     wiring at all — app.js delegates those on document.

     Classes, sizes and colours match the sections this replaces. --}}
<div class="text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Description</div>
<x-rich-text :value="$task->description" empty="No description." class="mt-1 text-[12px] text-gray-700" />

<div class="mt-3 text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Subtasks</div>
<div class="mt-1">
    @forelse ($task->subtasks as $subtask)
        <div class="flex flex-wrap items-center gap-2 py-0.5 text-[12px] text-gray-700">
            {{-- A glyph, not a disabled checkbox: a checkbox (even
                 disabled) reads as something you could act on here. --}}
            <span class="w-3 shrink-0 text-[11px] {{ $subtask->is_done ? 'text-brand-600' : 'text-gray-300' }}" aria-hidden="true">{{ $subtask->is_done ? '✓' : '○' }}</span>
            <span class="min-w-[140px] flex-1 {{ $subtask->is_done ? 'text-gray-400 line-through' : '' }}">{{ $subtask->title }}</span>
            <span class="shrink-0 text-[11px] text-gray-500">
                {{ $subtask->assignee ? \Illuminate\Support\Str::before($subtask->assignee->name, ' ') : 'Unassigned' }}
            </span>
            <span class="shrink-0 text-[10px] text-gray-400">
                {{ $subtask->start_date?->format('M j') ?? '—' }} To {{ $subtask->due_date?->format('M j') ?? '—' }}
            </span>
        </div>
    @empty
        {{-- "No subtasks yet." verbatim from _subtasks.blade.php, not a
             shortened "No subtasks." — the list and the task page should not
             word the same empty state two different ways. --}}
        <p class="text-[11px] text-gray-500">No subtasks yet.</p>
    @endforelse
</div>

<div class="mt-3 text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Comments</div>
<div class="mt-1 space-y-2">
    @forelse ($comments as $comment)
        <div class="rounded-md border border-gray-200 bg-white px-3 py-2">
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center gap-1.5">
                    <x-avatar :user="$comment->user" size="18px" />
                    <span class="text-[10px] font-medium text-[#1F2937]">{{ $comment->user->name }}</span>
                </span>
                {{-- Relative, with the exact timestamp on hover — the task
                     page shows the absolute form in the same slot. --}}
                <span class="text-[10px] text-gray-400" title="{{ $comment->created_at->format('M j, Y g:ia') }}">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            {{-- Same component and the same data-mentioned-users payload the
                 task page emits, so highlightMentions() finds what it
                 expects once this is inserted. No data-body-hash: that
                 exists for the task page's 7s poll, and nothing polls here. --}}
            <x-rich-text :value="$comment->body" class="comment-body-text mt-1 text-[12px] text-gray-700" :data-mentioned-users="$comment->mentionedUsers->map(fn ($user) => ['id' => $user->id, 'name' => $user->name])->values()->toJson()" />
            @if ($comment->replies_count > 0)
                <div class="mt-1 text-[10px] text-gray-400">{{ $comment->replies_count }} {{ \Illuminate\Support\Str::plural('reply', $comment->replies_count) }}</div>
            @endif
        </div>
    @empty
        <p class="text-[11px] text-gray-500">No comments yet.</p>
    @endforelse

    @if ($remainingCommentCount > 0)
        <p class="text-[10px] text-gray-500">{{ $remainingCommentCount }} more {{ \Illuminate\Support\Str::plural('comment', $remainingCommentCount) }}</p>
    @endif
</div>

<div class="mt-3 flex flex-wrap items-center gap-2">
    <a href="{{ route('tasks.edit', ['task' => $task, 'return_to' => $returnTo['url'], 'return_label' => $returnTo['label']]) }}" class="text-[11px] font-medium text-brand-600 hover:underline">Open task</a>
    <span class="text-[10px] text-gray-400">To comment or edit subtasks, open the task.</span>
</div>
