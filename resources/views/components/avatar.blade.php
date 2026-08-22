{{--
    Initials avatar — the shared circular identity marker for a user
    anywhere their name appears in the UI. Colour is a deterministic hash
    (User::avatarBackground()/avatarText(), keyed on id % 8) rather than
    hardcoded per person, so it stays correct as users are added. Size is a
    prop rather than a fixed value since the spec calls for different sizes
    per context (28px standard, 18px compact table rows, 30px staff cards,
    16px document owner names).

    $user may be null (an unassigned task) — renders as an empty grey
    circle instead of initials, so callers don't have to wrap every usage
    in an @if($task->assignee) just to place the slot.
--}}
@props(['user', 'size' => '28px'])

@if ($user)
    <span
        {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center rounded-full font-medium leading-none']) }}
        style="width: {{ $size }}; height: {{ $size }}; background-color: {{ $user->avatarBackground() }}; color: {{ $user->avatarText() }}; font-size: calc({{ $size }} * 0.43);"
        title="{{ $user->name }}"
    >{{ $user->initials() }}</span>
@else
    <span
        {{ $attributes->merge(['class' => 'inline-flex shrink-0 rounded-full bg-gray-300']) }}
        style="width: {{ $size }}; height: {{ $size }};"
        title="Unassigned"
    ></span>
@endif
