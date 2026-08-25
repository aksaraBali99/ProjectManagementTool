@extends('layouts.authenticated')

@section('title', 'Permissions — Solava')

@section('content')
<div class="mx-auto max-w-4xl">
    <a href="{{ route('roles.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Roles</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Permissions</h1>
    <p class="mt-1 text-[11px] text-gray-500">
        What each role can do. Super Admin and Owner always have every permission — locked here to prevent
        accidental lockout.
    </p>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-brand-50 p-3 text-[12px] text-brand-800">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('roles.permissions.update') }}" class="mt-6">
        @csrf
        @method('PUT')

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="sticky left-0 z-10 bg-gray-50 px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Permission</th>
                        @foreach ($roles as $role)
                            <th class="px-3 py-2 text-center text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">
                                {{ $role->name }}
                                @if (! in_array($role->slug, $editableRoleSlugs, true))
                                    <span class="ml-1 rounded-full bg-gray-100 px-1.5 py-0.5 text-[9px] normal-case text-gray-500">Locked</span>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($permissionGroups as $group => $permissions)
                        <tr class="bg-gray-50">
                            <td colspan="{{ $roles->count() + 1 }}" class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">
                                {{ $group }}
                            </td>
                        </tr>
                        @foreach ($permissions as $permission)
                            <tr>
                                <td class="sticky left-0 z-10 bg-white px-3 py-2.5 text-[12px] text-[#1F2937]">{{ $permission->name }}</td>
                                @foreach ($roles as $role)
                                    @php $isEditable = in_array($role->slug, $editableRoleSlugs, true); @endphp
                                    <td class="px-3 py-2.5 text-center">
                                        @if ($isEditable)
                                            <input type="checkbox"
                                                name="role_permissions[{{ $role->id }}][]"
                                                value="{{ $permission->id }}"
                                                class="rounded border-gray-300 text-brand-600 focus:ring-brand-600"
                                                {{ in_array($permission->id, $grants[$role->id], true) ? 'checked' : '' }}>
                                        @else
                                            <input type="checkbox" checked disabled
                                                class="rounded border-gray-200 text-gray-400">
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <button type="submit" class="rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
                Save changes
            </button>
            <a href="{{ route('roles.index') }}" class="text-[12px] text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</div>
@endsection
