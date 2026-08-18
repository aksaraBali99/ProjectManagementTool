<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'FounderOS')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    @php
        $navItems = [
            ['label' => 'Dashboard', 'icon' => 'ti-layout-dashboard', 'route' => 'dashboard', 'matches' => ['dashboard']],
            ['label' => 'Projects', 'icon' => 'ti-folder', 'route' => null, 'matches' => []],
            ['label' => 'Documents', 'icon' => 'ti-files', 'route' => null, 'matches' => []],
            ['label' => 'Access control', 'icon' => 'ti-shield-lock', 'route' => null, 'matches' => []],
            ['label' => 'Staff', 'icon' => 'ti-users', 'route' => null, 'matches' => []],
            ['label' => 'Settings', 'icon' => 'ti-settings', 'route' => 'settings.index', 'matches' => ['settings.*', 'users.*']],
        ];
    @endphp

    <div class="flex min-h-screen">
        <aside class="flex w-[180px] shrink-0 flex-col border-r border-gray-200 bg-white">
            <div class="flex h-12 items-center border-b border-gray-200 px-4">
                <span class="text-sm font-medium text-gray-900">FounderOS</span>
            </div>
            <nav class="flex-1 py-2">
                @foreach ($navItems as $item)
                    @php $active = collect($item['matches'])->contains(fn ($pattern) => request()->routeIs($pattern)) @endphp
                    @if ($item['route'])
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
                @endforeach
            </nav>
        </aside>

        <div class="flex flex-1 flex-col">
            <header class="flex h-12 items-center justify-between border-b border-gray-200 bg-white px-4">
                <span class="text-[13px] font-medium text-gray-900">@yield('title', 'FounderOS')</span>
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
