@props(['align' => 'left'])

<th {{ $attributes->merge(['class' => "px-3 py-2 text-$align text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500"]) }}>{{ $slot }}</th>
