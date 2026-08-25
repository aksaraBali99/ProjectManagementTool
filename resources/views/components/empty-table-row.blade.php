{{--
    Shared "no rows yet" state for a responsive index table's @empty branch —
    the row itself stays block-level below md so it doesn't render as a bare
    cell next to the stacked-card rows above it.
--}}
@props(['colspan', 'py' => 4])

<tr class="block md:table-row">
    <td colspan="{{ $colspan }}" class="block px-3 py-{{ $py }} text-center text-[12px] text-gray-500 md:table-cell">{{ $slot }}</td>
</tr>
