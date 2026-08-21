{{--
    Initials avatar — the shared circular identity marker for a user
    anywhere their name appears in the UI. Colour is a deterministic hash
    (User::avatarBackground()/avatarText(), keyed on id % 8) rather than
    hardcoded per person, so it stays correct as users are added. Size is a
    prop rather than a fixed value since the spec calls for different sizes
    per context (28px standard, 18px compact table rows, 30px staff cards,
    16px document owner names).
--}}
@props(['user', 'size' => '28px'])

<span
    {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center rounded-full font-medium leading-none']) }}
    style="width: {{ $size }}; height: {{ $size }}; background-color: {{ $user->avatarBackground() }}; color: {{ $user->avatarText() }}; font-size: calc({{ $size }} * 0.43);"
    title="{{ $user->name }}"
>{{ $user->initials() }}</span>
