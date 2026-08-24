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

    Passing content as a slot (rather than using this self-closing) also
    wraps it in a pale wash of the active company's own accent color — the
    same "folder body" the tab and seam already use — so whatever's inside
    (a table, a Kanban board, ...) reads as a sheet of paper sitting inside
    a colored folder rather than a colored tab floating over a plain white
    page. That wrapper is flex-1 and stretches to the bottom of the page
    (not just hugging its content's height) — this only actually reaches
    the page bottom if every ancestor between it and <main> in
    layouts/authenticated.blade.php is also a flex column with flex-1 on
    whichever child needs to grow, which is why each page's own outermost
    <div> is `flex flex-1 flex-col` rather than a plain block div. Self-
    closing usage (no slot) is unaffected, for any caller not migrated to
    this yet.
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

@if ($slot->isNotEmpty())
    @if ($active)
        <div class="flex min-h-0 flex-1 flex-col rounded-b-lg p-3" style="background-color: {{ $active->badgeBackground() }};">
            {{ $slot }}
        </div>
    @else
        <div class="min-h-0 flex-1">{{ $slot }}</div>
    @endif
@endif
