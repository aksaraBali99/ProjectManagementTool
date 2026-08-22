{{--
    A small colour swatch dot — priority group headers, company/department
    identifiers wherever a full pill would be too heavy (tab sections, list
    dividers). Default 6px per the UIX spec's "Task row: 6px dot".
--}}
@props(['color', 'size' => '6px'])

<span
    {{ $attributes->merge(['class' => 'inline-block shrink-0 rounded-full align-middle']) }}
    style="background-color: {{ $color }}; width: {{ $size }}; height: {{ $size }}"
></span>
