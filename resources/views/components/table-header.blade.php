{{--
    Shared responsive <thead> wrapper for index tables that collapse to
    stacked cards below md — hidden there since each <td> carries its own
    inline label instead. Column cells go through <x-th> in the slot.
--}}
@props(['bg' => 'gray-50'])

<thead class="hidden bg-{{ $bg }} md:table-header-group">
    <tr>
        {{ $slot }}
    </tr>
</thead>
