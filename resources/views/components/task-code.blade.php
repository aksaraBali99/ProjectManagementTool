{{--
    task #118 phase 3: a task's code (BGS-0012), muted, before its title:
    <x-task-code :task="$task" />{{ $task->title }}. The one place the
    code's look lives. Renders nothing for a task without a code. The
    trailing space is inside the no-wrap span, so the code never sits on
    a line of its own and the title follows after one space.

    Wherever tasks are listed, give them their company first:
    Task::useOrganization() when the page already has it, else eager-load
    'organization:id,code'.
--}}
@props(['task'])
@if ($task->code !== null)<span {{ $attributes->class('task-code whitespace-nowrap font-normal tabular-nums text-gray-500') }}>{{ $task->code }} </span>@endif
