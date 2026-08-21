{{--
    Generic colour-tinted pill — the shared shape behind every status,
    priority, department/category, and company badge in the app, so each
    of those only has to supply its own background/text pair (from its
    enum/model) rather than repeating the pill markup everywhere it's used.
    Radius token "sm" (4-5px) per the spacing/border-radius spec table.
--}}
@props(['background', 'text', 'weight' => 'font-medium'])

<span
    {{ $attributes->merge(['class' => "inline-flex items-center rounded-sm px-2 py-0.5 text-[10px] $weight"]) }}
    style="background-color: {{ $background }}; color: {{ $text }}"
>{{ $slot }}</span>
