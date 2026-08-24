<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Solava')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    @php
        $navItems = [
            ['label' => 'Dashboard', 'icon' => 'ti-layout-dashboard', 'route' => 'dashboard', 'matches' => ['dashboard']],
            ['label' => 'Kanban', 'icon' => 'ti-layout-kanban', 'route' => 'kanban', 'matches' => ['kanban']],
            ['label' => 'Calendar', 'icon' => 'ti-calendar', 'route' => 'calendar', 'matches' => ['calendar']],
            ['label' => 'Analytics', 'icon' => 'ti-chart-bar', 'route' => 'analytics.index', 'matches' => ['analytics.*'], 'can' => ['analytics.view']],
            ['label' => 'Projects', 'icon' => 'ti-folder', 'route' => 'projects.index', 'matches' => ['projects.*']],
            ['label' => 'Tasks', 'icon' => 'ti-checklist', 'route' => 'tasks.index', 'matches' => ['tasks.*', 'subtasks.*', 'comments.*'], 'can' => ['viewAny', \App\Models\Task::class]],
            ['label' => 'Documents', 'icon' => 'ti-files', 'route' => 'documents.index', 'matches' => ['documents.*']],
            ['label' => 'Department Access', 'icon' => 'ti-shield-lock', 'route' => 'access-control.index', 'matches' => ['access-control.*'], 'can' => ['access-control.view']],
            [
                'label' => 'Settings',
                'icon' => 'ti-settings',
                'route' => 'settings.index',
                'matches' => ['settings.index'],
                'children' => [
                    ['label' => 'Notifications', 'route' => 'notification-settings.index', 'matches' => ['notification-settings.*']],
                    ['label' => 'Users', 'route' => 'users.index', 'matches' => ['users.*'], 'can' => ['viewAny', \App\Models\User::class]],
                    ['label' => 'Companies', 'route' => 'organizations.index', 'matches' => ['organizations.*'], 'can' => ['viewAny', \App\Models\Organization::class]],
                    ['label' => 'Departments', 'route' => 'departments.index', 'matches' => ['departments.*'], 'can' => ['viewAny', \App\Models\Department::class]],
                    ['label' => 'Roles', 'route' => 'roles.index', 'matches' => ['roles.*'], 'can' => ['viewAny', \App\Models\Role::class]],
                    ['label' => 'Audit trail', 'route' => 'audit-trail.index', 'matches' => ['audit-trail.*'], 'can' => ['viewAny', \App\Models\AuditLog::class]],
                ],
            ],
        ];

        // Every company-scoped page (Dashboard, Kanban, Calendar, Tasks,
        // Documents, Access control) binds this same {organization?} route
        // parameter, so the topbar can read "current company" directly from
        // the route instead of every controller having to pass it in.
        $topbarOrganization = request()->route('organization');
        $topbarOrganization = $topbarOrganization instanceof \App\Models\Organization ? $topbarOrganization : null;
        $topbarTeamMembers = $topbarOrganization
            ? $topbarOrganization->members()->where('is_active', true)->orderBy('name')->get()
            : collect();
    @endphp

    <div class="flex min-h-screen">
        {{-- Below md, the sidebar becomes a fixed off-canvas drawer (closed
             by default, translated fully off-screen) toggled by the header's
             hamburger button; at md and up it reverts to the normal static
             in-flow sidebar exactly as before, so desktop is unaffected. --}}
        <div id="sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-black/40 md:hidden"></div>

        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-[220px] -translate-x-full flex-col border-r border-gray-200 bg-white transition-transform duration-200 ease-in-out md:static md:z-auto md:w-[180px] md:translate-x-0">
            <div class="flex h-12 shrink-0 items-center justify-between border-b border-gray-200 px-4">
                <span class="text-sm font-medium text-gray-900">Solava</span>
                <button type="button" id="sidebar-close" class="text-gray-400 hover:text-gray-600 md:hidden" aria-label="Close menu">
                    <i class="ti ti-x text-[16px]"></i>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto py-2">
                @foreach ($navItems as $item)
                    @php
                        $active = collect($item['matches'])->contains(fn ($pattern) => request()->routeIs($pattern));
                        $itemAllowed = ! isset($item['can']) || auth()->user()->can(...$item['can']);
                        $children = collect($item['children'] ?? [])->filter(
                            fn ($child) => ! isset($child['can']) || auth()->user()->can(...$child['can'])
                        );
                        $hasChildren = $children->isNotEmpty();
                        $anyChildActive = $children->contains(
                            fn ($child) => collect($child['matches'])->contains(fn ($pattern) => request()->routeIs($pattern))
                        );
                    @endphp

                    @if ($hasChildren && $itemAllowed)
                        {{-- A group toggles its children open/closed rather than
                             navigating — collapsed by default, except when one
                             of its own children is the current page, so the
                             active item is never hidden behind a closed group
                             the visitor would have no reason to think to open. --}}
                        <button type="button"
                           class="nav-group-toggle flex w-full items-center justify-between gap-2 border-r-2 px-4 py-2 text-xs {{ $active || $anyChildActive ? 'border-[#1D9E75] bg-gray-50 font-medium text-gray-900' : 'border-transparent text-gray-500 hover:bg-gray-50' }}">
                            <span class="flex items-center gap-2">
                                <i class="ti {{ $item['icon'] }}"></i>
                                {{ $item['label'] }}
                            </span>
                            <i class="ti ti-chevron-down nav-group-chevron text-[12px] transition-transform {{ $anyChildActive ? 'rotate-180' : '' }}"></i>
                        </button>
                    @elseif ($item['route'] && $itemAllowed)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-2 border-r-2 px-4 py-2 text-xs {{ $active ? 'border-[#1D9E75] bg-gray-50 font-medium text-gray-900' : 'border-transparent text-gray-500 hover:bg-gray-50' }}">
                            <i class="ti {{ $item['icon'] }}"></i>
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="flex cursor-not-allowed items-center gap-2 border-r-2 border-transparent px-4 py-2 text-xs text-gray-300">
                            <i class="ti {{ $item['icon'] }}"></i>
                            {{ $item['label'] }}
                        </span>
                    @endif

                    @if ($hasChildren)
                        <div class="nav-group-children" style="{{ $anyChildActive ? '' : 'display: none;' }}">
                            @foreach ($children as $child)
                                @php $childActive = collect($child['matches'])->contains(fn ($pattern) => request()->routeIs($pattern)) @endphp
                                <a href="{{ route($child['route']) }}"
                                   class="flex items-center border-r-2 py-2 pl-10 pr-4 text-xs {{ $childActive ? 'border-[#1D9E75] bg-gray-50 font-medium text-gray-900' : 'border-transparent text-gray-500 hover:bg-gray-50' }}">
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex h-12 shrink-0 items-center justify-between border-b border-gray-200 bg-white px-4">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" id="sidebar-open" class="text-gray-500 hover:text-gray-900 md:hidden" aria-label="Open menu">
                        <i class="ti ti-menu-2 text-[18px]"></i>
                    </button>
                    <span class="truncate text-[13px] font-medium text-gray-900">@yield('title', 'Solava')</span>
                </div>
                <div class="flex shrink-0 items-center gap-3 text-sm text-gray-600">
                    @if ($topbarTeamMembers->isNotEmpty())
                        <div class="hidden items-center sm:flex" aria-label="Team members in {{ $topbarOrganization->name }}">
                            @foreach ($topbarTeamMembers->take(5) as $member)
                                <x-avatar :user="$member" size="28px" class="-ml-2 border-2 border-white first:ml-0" />
                            @endforeach
                            @if ($topbarTeamMembers->count() > 5)
                                <span class="-ml-2 flex h-[28px] w-[28px] items-center justify-center rounded-full border-2 border-white bg-gray-100 text-[10px] font-medium text-gray-600">
                                    +{{ $topbarTeamMembers->count() - 5 }}
                                </span>
                            @endif
                        </div>
                    @endif
                    @php $unreadCount = auth()->user()->unreadNotifications()->count() @endphp
                    <a href="{{ route('notifications.index') }}" class="relative flex items-center hover:text-gray-900" aria-label="Notifications">
                        <i class="ti ti-bell text-[16px]"></i>
                        @if ($unreadCount > 0)
                            <span class="absolute -right-1.5 -top-1.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-[#DC2626] px-1 text-[9px] font-medium leading-none text-white">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        @endif
                    </a>
                    <span class="max-w-[100px] truncate sm:max-w-none">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-1 hover:text-gray-900" aria-label="Log out">
                            <i class="ti ti-logout text-[16px]"></i>
                            <span class="hidden sm:inline">Log out</span>
                        </button>
                    </form>
                </div>
            </header>

            <main class="min-w-0 flex-1 px-5 py-4">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const openBtn = document.getElementById('sidebar-open');
            const closeBtn = document.getElementById('sidebar-close');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }

            document.querySelectorAll('.nav-group-toggle').forEach(function (toggle) {
                const children = toggle.nextElementSibling;
                const chevron = toggle.querySelector('.nav-group-chevron');

                toggle.addEventListener('click', function () {
                    const isOpen = children.style.display !== 'none';
                    children.style.display = isOpen ? 'none' : '';
                    chevron.classList.toggle('rotate-180', ! isOpen);
                });
            });

            openBtn.addEventListener('click', openSidebar);
            closeBtn.addEventListener('click', closeSidebar);
            backdrop.addEventListener('click', closeSidebar);
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') closeSidebar();
            });
        })();
    </script>
</body>
</html>
