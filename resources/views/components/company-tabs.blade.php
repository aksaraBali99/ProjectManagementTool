{{--
    Manila-folder-style company tab bar, shared by every page that scopes
    its content to one of a few companies (Dashboard, Kanban, Calendar,
    Projects, Tasks, Documents, Access control, Departments) — one
    implementation so the trapezoid shape, overlap/z-index layering, and
    active/inactive color logic stay identical everywhere instead of
    drifting across 8 copies.

    $active can be null (e.g. a super admin who hasn't picked a company
    tab yet) — every tab then renders inactive and no connector strip
    shows.
--}}
@props(['organizations', 'active', 'route', 'query' => ''])

<div class="mt-4">
    <div class="relative flex">
        @foreach ($organizations as $tab)
            @php
                $isActiveTab = $active && $active->id === $tab->id;
                $href = route($route, $tab).($query ? '?'.$query : '');
            @endphp
            <a href="{{ $href }}"
               class="folder-tab {{ $isActiveTab ? 'folder-tab--active font-medium' : '' }} {{ ! $loop->first ? '-ml-2.5' : '' }} px-4 py-2 text-[12px]"
               style="
                   z-index: {{ $isActiveTab ? 10 : $loop->iteration }};
                   --tab-accent-bg: {{ $tab->badgeBackground() }};
                   --tab-accent-text: {{ $tab->badgeText() }};
               ">
                {{ $tab->name }}
            </a>
        @endforeach
    </div>

    {{-- The "seam": same background as the active tab, zero gap on either
         side, so the tab visually keeps going into the content below
         instead of stopping at a border. --}}
    @if ($active)
        <div class="h-1" style="background-color: {{ $active->badgeBackground() }};"></div>
    @else
        <div class="h-px bg-gray-200"></div>
    @endif
</div>
