{{-- task #72 phase 9: one project's drilldown, fetched by the Projects page
     (projects/index.blade.php) when the row is first opened. $returnTo is
     the page's own URL, validated, so "← Back" lands on the same filters,
     sort and mode. --}}
@if ($tasks->isEmpty() && $hasOlderTasks)
    <p class="mt-1 text-[12px] text-gray-500">No recent tasks — every task on this project was completed more than {{ $organization->completed_window_days }} days ago. Use “Show all” above to see them.</p>
@elseif ($tasks->isEmpty())
    <p class="mt-1 text-[12px] text-gray-500">No tasks on this project yet.</p>
@else
    <div class="mt-2 overflow-hidden rounded-md border border-gray-200 bg-white">
        <table class="w-full">
            <x-table-header bg="gray-100">
                <x-th>Title</x-th>
                <x-th>Department</x-th>
                <x-th>Assignee</x-th>
                <x-th>Priority</x-th>
                <x-th>Status</x-th>
                <x-th>Due</x-th>
            </x-table-header>
            <tbody class="block divide-y divide-gray-100 md:table-row-group">
                @foreach ($tasks as $task)
                    <tr class="block px-3 py-2 md:table-row md:px-0 md:py-0">
                        <td class="flex items-center justify-between gap-2 py-1 text-[11px] font-medium text-[#1F2937] md:table-cell md:px-3 md:py-2">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Title</span>
                            <a href="{{ route('tasks.edit', ['task' => $task, 'return_to' => $returnTo['url'], 'return_label' => $returnTo['label']]) }}" class="hover:underline">{{ $task->title }}</a>
                        </td>
                        <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Department</span>
                            <x-badge :background="$task->department->badgeBackground()" :text="$task->department->badgeText()">{{ $task->department->name }}</x-badge>
                        </td>
                        <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Assignee</span>
                            {{ $task->assignee->name ?? 'Unassigned' }}
                        </td>
                        <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Priority</span>
                            <x-badge :background="$task->priority->badgeBackground()" :text="$task->priority->badgeText()">{{ $task->priority->label() }}</x-badge>
                        </td>
                        <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Status</span>
                            <x-badge :background="$task->status->badgeBackground()" :text="$task->status->badgeText()">{{ $task->status->label() }}</x-badge>
                        </td>
                        <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Due</span>
                            {{ $task->due_date?->format('M j') ?? '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@if ($tasksLink)
    <div class="mt-2 text-right">
        <a href="{{ $tasksLink['url'] }}" class="project-tasks-link text-[11px] text-brand-700 hover:underline">{{ $tasksLink['label'] }}</a>
    </div>
@endif
