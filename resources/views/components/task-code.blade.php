{{--
    task #118 phase 3: a task's code (BGS-0012), muted, before its title:
    <x-task-code :task="$task" />{{ $task->title }}. The one place the
    code's look lives. Renders nothing for a task without a code. The
    trailing space is inside the no-wrap span, so the code never sits on
    a line of its own and the title follows after one space.

    The task's organization should be eager-loaded ('organization:id,code')
    wherever tasks are listed.
--}}
@props(['task'])
@if ($task->code !== null)<span {{ $attributes->class('task-code whitespace-nowrap font-normal tabular-nums text-gray-500') }}>{{ $task->code }} </span>@endif
