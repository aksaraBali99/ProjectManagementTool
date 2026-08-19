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
            ['label' => 'Projects', 'icon' => 'ti-folder', 'route' => 'projects.index', 'matches' => ['projects.*']],
            ['label' => 'Documents', 'icon' => 'ti-files', 'route' => null, 'matches' => []],
            ['label' => 'Access control', 'icon' => 'ti-shield-lock', 'route' => 'access-control.index', 'matches' => ['access-control.*'], 'can' => ['access-control.view']],
            ['label' => 'Staff', 'icon' => 'ti-users', 'route' => null, 'matches' => []],
            [
                'label' => 'Settings',
                'icon' => 'ti-settings',
                'route' => 'settings.index',
                'matches' => ['settings.index'],
                'children' => [
                    ['label' => 'Users', 'route' => 'users.index', 'matches' => ['users.*'], 'can' => ['viewAny', \App\Models\User::class]],
                    ['label' => 'Companies', 'route' => 'organizations.index', 'matches' => ['organizations.*'], 'can' => ['viewAny', \App\Models\Organization::class]],
                    ['label' => 'Departments', 'route' => 'departments.index', 'matches' => ['departments.*'], 'can' => ['viewAny', \App\Models\Department::class]],
                    ['label' => 'Roles', 'route' => 'roles.index', 'matches' => ['roles.*'], 'can' => ['viewAny', \App\Models\Role::class]],
                ],
            ],
        ];
    @endphp

    <div class="flex min-h-screen">
        <aside class="flex w-[180px] shrink-0 flex-col border-r border-gray-200 bg-white">
            <div class="flex h-12 items-center border-b border-gray-200 px-4">
                <span class="text-sm font-medium text-gray-900">Solava</span>
            </div>
            <nav class="flex-1 py-2">
                @foreach ($navItems as $item)
                    @php
                        $active = collect($item['matches'])->contains(fn ($pattern) => request()->routeIs($pattern));
                        $itemAllowed = ! isset($item['can']) || auth()->user()->can(...$item['can']);
                        $children = collect($item['children'] ?? [])->filter(
                            fn ($child) => ! isset($child['can']) || auth()->user()->can(...$child['can'])
                        );
                    @endphp

                    @if ($item['route'] && $itemAllowed)
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

                    @foreach ($children as $child)
                        @php $childActive = collect($child['matches'])->contains(fn ($pattern) => request()->routeIs($pattern)) @endphp
                        <a href="{{ route($child['route']) }}"
                           class="flex items-center border-r-2 py-2 pl-10 pr-4 text-xs {{ $childActive ? 'border-[#1D9E75] bg-gray-50 font-medium text-gray-900' : 'border-transparent text-gray-500 hover:bg-gray-50' }}">
                            {{ $child['label'] }}
                        </a>
                    @endforeach
                @endforeach
            </nav>
        </aside>

        <div class="flex flex-1 flex-col">
            <header class="flex h-12 items-center justify-between border-b border-gray-200 bg-white px-4">
                <span class="text-[13px] font-medium text-gray-900">@yield('title', 'Solava')</span>
                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <span>{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hover:text-gray-900">Log out</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 px-[18px] py-[14px]">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
